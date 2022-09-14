<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Comments;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    //
    const PER_PAGE = 5;
    function get($product)
    {
        $comments =  Comments::where("product_id", $product)->orderBy("created_at", 'desc')->paginate(5);
        foreach ($comments as $item) {
            $item->userInfo = $item->user;
        }
        return $comments;
    }
    function getAll(Request $request)
    {
        $comments = Comments::orderBy('created_at', 'desc');
        if ($request->query('query') && $request->query('query') != "") {
            $keyword = $request->query('query');
            $comments = $comments->where(function ($query) use ($keyword) {
                $query->orWhere('comment', 'like', '%' . $keyword . '%');
            });
        }
        $comments =  $comments->paginate(self::PER_PAGE);
        foreach ($comments as $item) {
            $item->user = $item->user;
        }
        return $comments;
    }
    function add(Request $request)
    {
        $comment = new Comments();
        $comment->comment = $request->comment;
        $comment->user_id = $request->user_id;
        $comment->product_id = $request->product_id;
        $comment->rating = $request->rating;
        $comment->save();
        return $comment;
    }
    function caculatorComment(Request $request)
    {
        $check = false;
        $bills = Bill::where('user_id', $request->user_id)->where('status', 2)->get();
        if (count($bills) > 0) {
            foreach ($bills as $bill) {
                if ($check == false) {

                    $detailBill = $bill->detailBill;
                    if (count($detailBill) > 0) {
                        foreach ($detailBill as $item) {
                            if ($item->id_pro == $request->product_id) {
                                $check = true;
                                break;
                            }
                        }
                    }
                } else {
                    break;
                }
            }
        }
        if ($check) {
            return response()->json([
                'status' => 'success',
                'check' => 'yes'
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'check' => 'no'
            ]);
        }
    }
    function delete($id)
    {
        $result = Comments::where('id', $id)->delete();
        if ($result) {
            return response()->json(
                [
                    'status' => 'sucesss',
                    'data' => $result,
                ]
            );
        }
    }
}
