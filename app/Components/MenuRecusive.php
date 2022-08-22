<?php

namespace App\Components;

use App\Models\Menu;

class MenuRecusive
{
    private $html;
    private $arr;

    public function __construct()
    {

        $this->html = '<option value="0">Root</option>';
    }

    public function menuRecusiveAdd($idSelected, $deff = "", $parent_id = 0, $subMark = '')
    {
        $data = Menu::where('parent_id', $parent_id)->get();
        if ($data->count() > 0) {
            foreach ($data as $dataItem) {
                if ($dataItem->id == $deff) {
                    continue;
                }

                if (!empty($idSelected) && $dataItem->id == $idSelected) {
                    $this->html .= '<option selected value="' . $dataItem->id . '" >' . $subMark . $dataItem->name . '</option>';
                } else {

                    $this->html .= '<option value="' . $dataItem->id . '" >' . $subMark . $dataItem->name . '</option>';
                }
                $this->menuRecusiveAdd($idSelected, $deff, $dataItem->id, $subMark . '--');
            }
        }
        return $this->html;
    }
}
