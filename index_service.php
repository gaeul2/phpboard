<?php
include './dbconnect.php';
/*  pagination() : index.php페이지에서 페이지를 나눠 보여줄수 있도록 값들을 반환하는 함수
 *  search_validation() : 입력된 검색어가 빈값인지, html entity가 있는지 확인,
 *                       선택된 날짜들을 유효하게 바꿔주어
 *                       검색어가 없다면 0을, 있다면 조건들을 배열에 담아 넘겨주는 함수
 *  make_where() : search_validation()함수가 넘겨준 조건들이 담긴 배열을 SQL조건문에 맞는 형식으로 
 *                 변환하여 한문장으로 만들어 $where_sentence로 반환하는 함수
 */



/* index.php 페이지에서 페이지네이션을 위한 함수*/
function pagination($conn, $page, $post_per_page, $condition){
    /* $post_per_page는 한페이지당 데이터 개수
        $page_num 한 블럭에 보일 페이지 수, $page는 현재 페이지  */

    $page_num = 3;
    if (!$condition){
        $select_all_sql = "SELECT * FROM board";
        $select_all_result = mysqli_query($conn, $select_all_sql);
        $total = mysqli_num_rows($select_all_result);
    } else {
        //입력받은 검색조건을 문장화
        $where_sentence = make_where($condition);
        $search_sql = "SELECT * FROM board WHERE $where_sentence";
        $search_sql_result = mysqli_query($conn, $search_sql);
        $total = mysqli_num_rows($search_sql_result);
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

    if(!isset($where_sentence)){
        $offset_sql = "SELECT * FROM board ORDER BY pk desc LIMIT $start_post_num, $post_per_page ";
        $offset_result = mysqli_query($conn, $offset_sql);    
    } else {
        $offset_sql = "SELECT * FROM board WHERE $where_sentence ORDER BY pk desc LIMIT $start_post_num, $post_per_page ";
        $offset_result = mysqli_query($conn, $offset_sql);
    }
    
    return array($total_page, $start_page_num, $end_page_num, $offset_result, $total);

}


/* index.php 페이지에서 검색어 유효성검사 위한 함수*/
function search_validation($post_data){
    $today = date('Y-m-d');
    $title_search = $writer_search= $start_date = $end_date = "";

    //제목 검색어 처리 
    if (isset($post_data['title_search'])){
        $title_search=htmlentities(trim($post_data['title_search']));
    } 
    //작성자 검색어 처리
    if (isset($post_data['writer_search'])){
        $writer_search= htmlentities(trim($post_data['writer_search']));
    } 
    //작성일 검색 시작기준 날짜
    if (isset($post_data['start_date'])){
        $start_date = $post_data['start_date'];
        if($start_date > $today){
            $start_date = date('Y-m-d H:i:s');
        }
    } 
    //작성일 검색 기간 끝부분 날짜
    if (isset($post_data['end_date'])){
        $end_date = $post_data['end_date'];
    }

    $before_validation = array($title_search, $writer_search, $start_date, $end_date);
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

function make_where($condition){
    $sql_where_array = [];
    if ((array_key_exists("start_date",$condition)) && (array_key_exists("end_date", $condition))){
        $sql_where_array[] = "created_date between ". "'$condition[start_date]"." 00:00:00'". 
                            " and ". "'$condition[end_date]"." 23:59:59'";
    } elseif (array_key_exists("start_date", $condition)){
        $sql_where_array[] = "created_date between ". "'$condition[start_date]"." 00:00:00'". 
                            " and ". "'".date("Y-m-d")." 23:59:59"."'";
    } elseif (array_key_exists("end_date", $condition)){
        $sql_where_array[] = "created_date between ". "'"."0000-00-00 00:00:00."."'". 
                            " and ". "'$condition[end_date]"." 23:59:59'";
    }
    foreach ($condition as $key => $value){
        if ($key== "title"|| $key == "writer"){
            $sql_where_array[] = $key." like "."'%".$value."%'";
        }             
    }
    if (count($sql_where_array) == 1){
        $where_sentence = $sql_where_array[0];
        return $where_sentence;
    } elseif (count($sql_where_array) <= 3){
        $where_sentence = implode('and ', $sql_where_array);
        return $where_sentence;
    }
}