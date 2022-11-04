<?php 
include_once './dbconnect.php';
include './index_service.php';

$page = isset($_GET['page']) ? $_GET['page'] : 1;
//페이지당 보여줄 게시글 수
$post_per_page= 10;
//검색어 아무것도 입력하지 않았을때 처리위한 변수
$show_message = "";

if($_SERVER['REQUEST_METHOD']=="GET"){ 
    //겟요청으로 'page'가 있다면 그 파라미터값, 없으면 1
    list($total_page, $start_page_num, $end_page_num, $offset_result, $total)
    = pagenation($conn, $page, $post_per_page, "GET", 0);
    $cnt = $total-(($page-1)*$post_per_page);
} else{
    if (!array_filter($_POST)){ 
        $show_message = 1;
    }
    $validate_result = search_validation($_POST);
    list($total_page, $start_page_num, $end_page_num, $offset_result, $total)
    = pagenation($conn, $page, $post_per_page, "POST", $validate_result);
    $cnt = $total-(($page-1)*$post_per_page); 
}

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
        <!--검색기능-->
        <div class ="search-box">
            <form method="post" action="<?=$_SERVER['PHP_SELF']?>">
                제목 <input type="text" name="title_search">
                작성자 <input type="text" name="writer_search">
                작성일<input type="date" name="start_date">
                ~ <input type="date" name="end_date">
                <button>검색</button> 
            </form>
        </div>
        <div class="message-box">
            <?php if ($show_message){echo '<p>검색어를 입력하거나 날짜를 선택해 주세요.</p>';}  ''; ?>
        </div>
        <div class=status-wrapper>
            <div class = "total"> Total : <?= $total?></div>
            <div class = "page"> Page : <?= $page.'/'.$total_page?></div></div>
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
            while ($post = mysqli_fetch_assoc($offset_result)) {
                $pk = $post['pk']?>
                <tbody>
                    <tr>
                        <td class="num"><?= $cnt;?></td>
                        <td class="category"><?= $post['category'];?></td>
                        <td style="padding: 0 8px 0 8px;" class="title" id= "post-title" onclick="location.href='read.php?index=<?=$pk?>'" ><?= $post['title'] ?></td>
                        <?php if (strlen($post['userfile'])>1)
                            {echo "<td class='file'><a href='files/$post[userfile]' download><img src='./img/save-file.png' width='10px'></img></a></td>";} 
                            else{ echo "<td class='file'></td>";}?>
                        <td class="date"><?= $post['created_date'];?></td>
                        <td class="writer"><?= $post['writer'];?></td>
                        <td class="hit"><?= $post['hit'];?></td>
                    </tr>
                </tbody>
                <?php $cnt--;                
            } ?>
        </table>
        <div class= "lower-wrapper">
            <div class = "pager">
                <?php 
                // 이전페이지
                if($page > 1) {?>
                    <a href="./index.php?page=1"><<</a>
                    <a href="./index.php?page=<?=($page -1);?>"><</a>
                <?php } 
                
                //페이지 번호 출력
                for ($print_page = $start_page_num; $print_page <= $end_page_num; $print_page++){?>
                    <a href="./index.php?page=<?= $print_page;?>"<?=($page==$print_page)? 'id="here"':"";?>><?=$print_page."</a>";}?>
                
                <!-- 다음페이지 -->
                <?php
                if ($page < $total_page){?>
                    <a href="./index.php?page"<?=($page +1) ?>>></a>
                    <a href="./index.php?page=<?= $total_page;?>">>></a>
                <?php }?>
            </div>
            <div>
                <a href="./create_post.php"><button class="create-btn">글쓰기</button></a>
            </div>
        </div>
    </div>
</body>

</html>
<?php mysqli_close($conn);?>
