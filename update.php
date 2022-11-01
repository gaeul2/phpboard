<?php
include './dbconnect.php';
include './create_check.php';


$post_pk = $_GET['index'];
$sql = "SELECT * FROM board WHERE pk = '$post_pk'";
$result= mysqli_query($conn, $sql);
$post = mysqli_fetch_assoc($result);



if ($_SERVER['REQUEST_METHOD'] == "GET") {
    //보여주기
    show_form(0,$post);

    } else {
        list($input, $errors)= validate_form();
            if ($errors) {
                show_form($input, $errors);
            } else {
                process_form($input, 'update');
            }
}

