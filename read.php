<?php
include './dbconnect.php';

$index = $_GET['index'];
$sql = "SELECT * FROM board WHERE pk = '".$index."'";
$result = mysqli_query($conn, $sql);
$post = mysqli_fetch_assoc($result);
mysqli_close($conn);
?>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>글 작성</title>
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <div class="container">
        <form method="post" action="./update.php?index=<?=$post['pk']?>">
            <table class="list-table-for-read-and-update">
                <tr>
                    <th>구분</th>
                    <td> <?= $post['category']?></td>
                </tr>
                <tr>
                    <th>작성자</th>
                    <td> <?= $post['writer']?></td>
                </tr>
                <tr>
                    <th>분류</th>
                    <td> <?= $post['detail_option']?></td>
                </tr>
                <tr>
                    <th>고객유형</th>
                    <td> <?= $post['question_type']?></td>
                </tr>
                <tr>
                    <th>제목</th>
                    <td><?= $post['title']?></td>
                </tr>
                <tr>
                    <th>내용(필수)</th>
                    <td> <?= nl2br($post['content'])?></td>
                </tr>
                <tr>
                    <th>첨부파일</th>
                    <td><?= (($post['userfile'] == ""))? 
                        ''
                        :"<div> $post[userfile] <a href='files/".$post['userfile']."' download><button type='button'>다운로드</button></a>";?>   
                    </td>
                </tr>
            </table>
            <button type="submit"> 수정 </button>
            <a href="./delete.php?index=<?=$post['pk']?>"><input type="button" value="삭제"></a>
            <a href="./index.php"><input type="button" value="목록"></a>
        </form>
    </div>
</body>



