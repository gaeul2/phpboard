<?php include './dbconnect.php';
include './pagenation.php';

//겟요청으로 'page'가 있다면 그 파라미터값, 없으면 1
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$post_per_page= 10;
list($total_page, $start_page_num, $end_page_num, $offset_result, $total)= pagenation($conn, $page,$post_per_page);
$cnt = $total-(($page-1)*$post_per_page); 
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
        <div id ="status">Total : <?= $total?> Page : <?= $page.'/'.$total_page?> </div>
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
            while ($post = mysqli_fetch_assoc($offset_result)) {?>
            <tbody>
                <tr>
                    <td class="num"><?= $cnt;?></td>
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
            <?php $cnt--;
            }; ?>
        </table>
        <p class = "pager">
            <?php 
            // 이전페이지
            if($page > 1) {?>
                <a href="./index.php?page=1"><<</a>
                <a href="./index.php?page=<?=($page -1);?>"><</a>
            <?php } 
            
            //페이지 번호 출력
            for ($print_page = $start_page_num; $print_page <= $end_page_num; $print_page++){?>
                <a href="./index.php?page=<?= $print_page;?>"><?=$print_page."</a>";}?>
            
            <!-- 다음페이지 -->
            <?php
            if ($page < $total_page){?>
                <a href="./index.php?page"<?=($page +1) ?>>></a>
                <a href="./index.php?page=<?= $total_page;?>">>></a>
            <?php }?>
            


        <div class = "btn">
            <a href="./create_post.php"><button>글쓰기</button></a>
        </div>
    </div>
</body>

</html>