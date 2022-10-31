<?php

$input = array();
$errors = array();

function validate($item, $keyword){
    if (array_key_exists($keyword, $item)){
        $input = $item["$keyword"];
        
        //구분 선택안했을때 error반환
        if ($input == "unselect") {
            return "error";
        }
            //
        if ($keyword == "writer" || $keyword == "title" || $keyword == "content"){
            if (strlen($input) > 0){
                $input = htmlentities($input);    
            } else {
                return "error";
            }
        }
        return $input;
    }
    //여기도 생각해 봐야함.
    
}
