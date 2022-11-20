<?php
include_once './dbconnect.php';
include './create_update_service.php';
 

if ($_SERVER['REQUEST_METHOD']=="POST"){
    //validate_form()으로 검사
    list($input, $errors) = validate_form();
    // validation 통과못할시.
    if ($errors) {
        show_form($input ,$errors,'오류 수정');
    } else {
        process_form($input, 'create');
    }
} else {
    show_form(0, 0, '글 작성');
}
?>
