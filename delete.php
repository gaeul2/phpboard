<?php
include './dbconnect.php';
include './sql_service.php';

$pk= $_GET['index'];
$delete_target= find_pk_one($conn, $pk);
$file_name = $delete_target['userfile'];


$select_end_pk_sql = "SELECT pk FROM board ORDER BY pk desc LIMIT 1";
$select_end_pk_result= mysqli_query($conn, $select_end_pk_sql);
$last_num = mysqli_fetch_assoc($select_end_pk_result);

//마지막 pk값 보다다 큰 값으로 접근하여 삭제하려고 하는것 방지
if($last_num['pk'] < $pk){?>
    <script>
        alert("잘못된 접근입니다.");
        location.href='./index.php';
        </script>
<?php }

$delete_sql = "DELETE FROM board WHERE pk = $pk";
$delete_result = mysqli_query($conn, $delete_sql);
mysqli_close($conn);

if (!$delete_result) { ?>
    <script>
        alert("삭제에 실패했습니다. 같은문제가 계속 발생한다면 관리자에게 문의하세요.");
        location.href='./index.php';
    </script>
<?php } else {
    unlink("./files/".$file_name); ?>
    <script>
        alert("게시글이 삭제되었습니다.");
        location.href='./index.php';
    </script>
<?php }?> 