<?php include './dbconnect.php';

// board 테이블에서 pk값을 기준으로 내림차순하여 10개까지 표시
$sql = "SELECT * FROM board ORDER BY pk desc limit 0,5";
$result = mysqli_query($conn, $sql);

//총 레코드 수 반환
$total = mysqli_num_rows($result);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/style.css">
    <title>게시판입니다.</title>
</head>

<body>
    <div class="container">
        <!-- 이건 일단 나중에
            <div class ="search-box">
            </div> -->
        <div id ="status">Total : <?= $total?> Page : n/n </div>
        <table class="list-table">
            <thead>
                <tr>
                    <!--행-->
                    <th class="num">번호</th>
                    <th class="category">구분</th>
                    <th class="title">제목</th>
                    <th class="file">첨부</th>
                    <th class="date">작성일</th>
                    <th class="writer">작성자</th>
                    <th class="hit">조회수</th>
                </tr>
            </thead>

            <?php
            
            while ($post = mysqli_fetch_assoc($result)) {
            ?>
            <tbody>
                <tr>
                    <td class="num"><?= $post['pk'];?></td>
                    <td class="category"><?= $post['category'];?></td>
                    <td class="title"><a href="/board/read.php?index=<?=$post['pk'];?>"><?= $post['title'] ?></a></td>
                    <?php if (strlen($post['userfile'])>1)
                        {echo "<td class='file'><a href='files/$post[userfile]' download><img src='./img/save-file.png' width='10px'></img></a></td>";} 
                        else{ echo "<td class='file'></td>";}?>
                    <td class="date"><?= $post['created_date'];?></td>
                    <td class="writer"><?= $post['writer'];?></td>
                    <td class="hit"><?= $post['pk'];?></td>
                </tr>
            </tbody>
            <?php } mysqli_close($conn); ?>
        </table>
        <div class = "btn">
            <a href="./create_post.php"><button>글쓰기</button></a>
        </div>
    </div>
</body>

</html>