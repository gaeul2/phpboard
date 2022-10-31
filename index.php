<?php include './dbconnect.php';

// board 테이블에서 pk값을 기준으로 내림차순하여 10개까지 표시
$sql = "SELECT * FROM board ORDER BY pk desc limit 0,5";
$result = mysqli_query($conn, $sql);

//총 레코드 수 반환
$total = mysqli_num_rows($result);

//겟요청으로 'page'가 있다면 그 파라미터값, 없으면 1
$page = isset($_GET['page']) ? $_GET['page'] : 1;


function pagenation($total, $page, $post_per_page=10){
    /* $post_per_page는 한페이지에 보여줄 글의 개수
    $page_button_num 은 페이지 써진 걸 몇개씩 보여줄지 정하는것  */
    $page_button_num = 3;
    // 1page -> 0 / 2page -> 10 / 3page -> 20 \\ 10개씩 끊어 볼 수 있는기준
    $offset = $post_per_page*($page-1);

    //글을 10개씩 묶었을때 생기는 총페이지수
    $total_page_num = ceil($total/$post_per_page);
    // 현재글번호로 글하나를 삭제하더라도 글 번호의 건너뜀없이 번호순서 유지하게 해둠.
    $current_num = $total - $offset;

    //전체 블록개수
    $total_block = ceil($total_page_num/$page_button_num);
    //현재블록
    $current_block = ceil($page/$page_button_num);

    //페이지 블록이 시작하는 첫 페이지
    $first = ($current_block-1)*$page_button_num;
    $last = $current_block * $page_button_num;

    if ($current_block >= $total_block){
        $last= $total_page_num;
    }
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