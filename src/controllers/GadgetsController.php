<?php



namespace hcolab\cms\controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use hcolab\cms\traits\ApiTrait;


class GadgetsController extends Controller
{

    use ApiTrait;



    public function render(){

        $config_gadgets = config('pages.gadgets');
        $gadgets = [];
        foreach($config_gadgets as $gadget){
            $gadgets [] = new $gadget;
        }

        return view('CMSViews::gadgets.index' , compact('gadgets'))->render();
    }

    public function countElement($count , $unit = ""){

        return [
            'count' => number_format($count , 0, '.' , ',') ." <span class='unit'>". $unit."</span>",
        ];
    }


    public function tableElement($rows , $columns , $display_header = true ,$display_title = false){

        return [
            'columns' => $columns,
            'rows' => $rows,
            'display_header' => $display_header,
            'display_title' => $display_title
        ];

    }

    public function pieGraphElement($labels , $values , $colors){
   
        return [
            'id' => uniqid(),
            'labels' => implode(",",$labels),
            'values' => implode(",",$values),
            'colors' => implode("," , $colors)
        ];
    }

    public function barGraphElement($labels , $values , $colors){
   
        return [
            'id' => uniqid(),
            'labels' => implode(",",$labels),
            'values' => implode(",",$values),
            'colors' => implode("," , $colors)
        ];
    }

    public function lineGraphElement($labels , $values , $colors){
   
        return [
            'id' => uniqid(),
            'labels' => implode(",",$labels),
            'values' => implode(",",$values),
            'colors' => implode("," , $colors)
        ];
    }

    

}