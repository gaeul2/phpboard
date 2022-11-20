<?php
include './dbconnect.php';


$pk= $_GET['index'];
$target = find_pk_one($conn, $pk);


$update_sql = "UPDATE board SET userfile = '' WHERE pk = $pk";
mysqli_query($conn, $update_sql);
unlink('./files/'.$target['userfile']);?>
<script>
    alert("파일만 삭제하였습니다.");
    location.href='update.php?index=<?=$pk?>';
</script>
<?php 