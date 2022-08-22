<?php

namespace App\Components;

use App\Models\Category;

class Recusive
{
    private $data, $htmlSelect = '';
    public function __construct($data)
    {
        $this->data = $data;
    }
    public function categoryRecusive($idSelected = 0, $id = 0, $text = '')
    {

        if ($this->data->count() > 0) {
            foreach ($this->data as $value) {
                if ($value->parent_id == $id) {
                    if (!empty($idSelected) && $value->id == $idSelected) {
                        $this->htmlSelect .= '<option selected value="' . $value->id . '">' . $text . $value->name . '</option>';
                    } else {

                        $this->htmlSelect .= '<option value="' . $value->id . '">' . $text . $value->name . '</option>';
                    }
                    $this->categoryRecusive($idSelected, $value->id, $text . '-');
                }
            }
        }
        return $this->htmlSelect;
    }
}
