<?php
include './dbconnect.php';

/* index.php 페이지에서 페이지네이션을 위한 함수*/
function pagenation($conn, $page, $post_per_page, $mode, $condition=0){
    /* $post_per_page는 한페이지당 데이터 개수
        $page_num 한 블럭당 페이지 수, $page는 현재 페이지  */
    $page_num = 3;
    if ($mode == "GET"){
        $select_all_sql = "SELECT * FROM board";
        $select_all_result = mysqli_query($conn, $select_all_sql);
        $total = mysqli_num_rows($select_all_result);
    } else {
        echo "검색조건 전달";
        // $search_sql = "SELECT * FROM board";
    }
    
    //전체 페이지수 = 전체데이터 / 페이지당 데이터 개수, ceil: 올림값, floor: 내림값
    $total_page = ceil($total/$post_per_page);

    //전체 블록개수 = 전체페이지 수 / 블럭당 페이지 수
    $total_block = ceil($total_page/$page_num);

    //현재 블럭 번호 = 현재 페이지 번호 / 블럭 당 페이지 수
    $now_block = ceil($page / $page_num);

    //블럭 당 시작 페이지 번호 = (해당글 블럭번호 -1) * 블럭당 페이지 수 + 1;
    $start_page_num = ($now_block -1) * $page_num +1;

    //데이터가 0개인경우 페이지는 1이 꼭 있도록
    if($start_page_num <= 0){
        $start_page_num = 1;
    };

    //블럭당 마지막 페이지 번호 = 현재 블럭 번호 * 블럭 당 페이지 수 
    $end_page_num = $now_block * $page_num;

    //페이지번호의 마지막 번호가 전체 페이지를 넘지 않도록.
    if($end_page_num > $total_page){
        $end_page_num = $total_page;
    };

    //페이지별 게시글 첫번호
    $start_post_num = ($page -1) * $post_per_page;
    // 게시글 첫번호 ~ 페이지당 데이터 수로 개수 제한하여 조회

    $offset_sql = "SELECT * FROM board ORDER BY pk desc LIMIT $start_post_num, $post_per_page ";
    $offset_result = mysqli_query($conn, $offset_sql);
    mysqli_close($conn);

    return array($total_page, $start_page_num, $end_page_num, $offset_result, $total);

}

/* index.php 페이지에서 검색 위한 함수*/
function search_validation($before_validation){
    $name = array('title','writer','start_date','end_date');
    
    $condition = array();
    for ($i=0; $i<4; $i++){
        if ($before_validation[$i] != ""){
            $condition[$name[$i]] = $before_validation[$i];
        }
    }
    if (empty($condition)){
        return 0;
    } else {
        return $condition;
    }

}