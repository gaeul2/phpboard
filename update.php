<?php
include './dbconnect.php';
include './create_update_service.php';

$post_pk = $_GET['index'];
$sql = "SELECT * FROM board WHERE pk = '$post_pk'";
$result= mysqli_query($conn, $sql);
$post = mysqli_fetch_assoc($result);

include './next.php';

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    //보여주기
    form($post, 'update');
    // show_form(0,$post);

    } else {
        echo"이곳";
        category_html(0, "errors");
        // list($input, $errors)= validate_form();
        //     if ($errors) {
        //         // form($input, $errors);
        //         // show_form($input, $errors);
        //     } else {
        //         process_form($input, 'update');
        //     }
}

