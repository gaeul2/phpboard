<?php
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