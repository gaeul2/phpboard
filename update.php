<?php
include './dbconnect.php';
include './create_update_service.php';
include './sql_service.php';

$post_pk = $_GET['index'];

$post = find_pk_one($conn,$post_pk);


if ($_SERVER['REQUEST_METHOD'] == "GET") {
    //보여주기
    show_form($post, 'update','글 수정');
} else {
    list($input, $errors)= validate_form();
    
    if ($errors) {
        show_form($input, $errors,'오류 수정');
    } else {
        process_form($input, 'update');
    }
}

