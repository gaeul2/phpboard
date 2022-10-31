<?php
include './dbconnect.php';

$pk= $_GET['index'];
$update_sql = "UPDATE board SET userfile = '' WHERE pk = $pk";
mysqli_query($conn, $update_sql);?>
<script>
    alert("파일만 삭제함");
    location.href='update.php?index=<?=$pk?>';
</script>
<?php 