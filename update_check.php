<?php
include './create_check.php';

if ($_SERVER['REQUEST_METHOD']=="POST"){
    //validate_form()으로 검사
    list($input, $errors)= validate_form();
    if ($errors) {
        show_form($input, $errors);
    } else {
        process_form($input, 'update');
    }
}