<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Cart;
use App\Models\DetailBill;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BillController extends Controller
{
    //
    const PER_PAGE = 4;
    function add(Request $request)
    {

        $bill = new  Bill();
        if ($request->pttt) {
            $bill = $bill->create([
                'tel' => $request->phone,
                'address' => $request->address,
                'total' => $request->total,
                'user_id' => $request->userId,
                'pttt' => $request->pttt,
                'status' => 0
            ]);
        } else {

            $bill = $bill->create([
                'tel' => $request->phone,
                'address' => $request->address,
                'total' => $request->total,
                'user_id' => $request->userId,
                'pttt' => 'Pay after recieve',
                'status' => 0

            ]);
        }

        $cartInfo = Cart::where('id_user', $request->userId)->first();

        $list = json_decode($cartInfo->carts);
        if ($list) {
            foreach ($list as $item) {
                $detailBill = new DetailBill();
                $detailBill->id_bill = $bill->id;
                $detailBill->id_pro = $item->id;
                $detailBill->number = (int)($item->number);
                $detailBill->total = $item->total;
                $detailBill->price = $item->price;
                $detailBill->image = $item->file_path;
                $detailBill->name_pro = $item->name;
                $detailBill->save();
            }
        }
        $cartInfo = Cart::where('id_user', $request->userId)->delete();

        return response()->json([
            'message' => "Đặt hàng thành công!",
            'id_bill' => $bill->id
        ]);
    }
    function vnPay_return()
    {
        return view('vnpay_return');
    }
    function vnPay(Request $request)
    {
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = "http://127.0.0.1:8000/vnpay_return";
        $vnp_TmnCode = "N8169UO7"; //Mã website tại VNPAY 
        $vnp_HashSecret = "VHDMVMERXAJSLPGFLTRLWCFLGAIGFQEB"; //Chuỗi bí mật

        $vnp_TxnRef = rand(); //Mã đơn hàng. Trong thực tế Merchant cần insert đơn hàng vào DB và gửi mã này sang VNPAY
        $vnp_OrderInfo = 'Thanh toán đơn hàng test';
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = $request->sum * 23000 * 100;
        $vnp_Locale = 'vn';
        $vnp_BankCode = 'NCB';
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
        //Add Params of 2.0.1 Version
        // $vnp_ExpireDate = $_POST['txtexpire'];
        //Billing


        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
            // "vnp_ExpireDate" => $vnp_ExpireDate

        );

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }
        if (isset($vnp_Bill_State) && $vnp_Bill_State != "") {
            $inputData['vnp_Bill_State'] = $vnp_Bill_State;
        }

        //var_dump($inputData);
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret); //  
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }
        $returnData = array(
            'code' => '00', 'message' => 'success', 'data' => $vnp_Url
        );
        if (isset($request->redirect)) {
            return  $vnp_Url;
            die();
        } else {
            return json_encode($returnData);
        }
        // vui lòng tham khảo thêm tại code demo
    }
    function detail($id, Request $request)
    {
        $bill = Bill::find($id);
        $bill->detailBill = $bill->detailBill;

        return $bill;
    }
    function list(Request $request)
    {
        $bills = Bill::where('user_id', $request->user_id);
        if ($request->query('status') > -1) {
            $status = $request->query('status');
            $bills = $bills->where(function ($query) use ($status) {
                $query->orWhere('status',   $status);
            });
        }
        return $bills->orderBy('created_at', 'desc')->get();
    }

    function listBillAdmin(Request $request)
    {
        $bills = Bill::orderBy('created_at', 'desc');
        if ($request->query('query') && $request->query('query') != "") {
            $keyword = $request->query('query');
            $bills = $bills->where(function ($query) use ($keyword) {
                $query->orWhere('tel', 'like', '%' . $keyword . '%');
                $query->orWhere('address', 'like', '%' . $keyword . '%');
            });
        }
        if ($request->query('status') > -1) {
            $status = $request->query('status');
            $bills = $bills->where(function ($query) use ($status) {
                $query->orWhere('status',   $status);
            });
        }
        $bills =  $bills->paginate(self::PER_PAGE);
        foreach ($bills as $item) {
            $item->user = $item->user;
        }
        return $bills;
    }

    function update($id, Request $request)
    {
        $bill = Bill::find($id);
        $bill->status = $request->status;
        $bill->save();
        return $bill;
    }

    //thống kê
    function getBillMonth()
    {
        $data = Bill::select('id', 'created_at', 'total')->orderBy('created_at', 'asc')->get()->groupBy(function ($data) {
            return Carbon::parse($data->created_at)->format('M');
        });
        // $data = Bill::select('months')->groupby('month')
        //     ->orderBy('months', 'ASC')
        //     ->get();
        return $data;
    }
}
