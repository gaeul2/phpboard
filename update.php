<?php
include './dbconnect.php';
include './create_check.php';


$post_pk = $_GET['index'];
$sql = "SELECT * FROM board WHERE pk = '$post_pk'";
$result= mysqli_query($conn, $sql);
$post = mysqli_fetch_assoc($result);
mysqli_close($conn);


if ($_SERVER['REQUEST_METHOD'] == "POST") {
    //보여주기
    show_form(0,$post);
    ?>

<?php } else {?>
    <script>
        alert("잘못된 접근입니다.");
        location.href="./index.php";
    </script>
<?php
}

