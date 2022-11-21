<?php

function find_pk_one($conn, $pk){
    $select_one_sql = "SELECT * FROM board Where pk = $pk";
    $select_one_result = mysqli_query($conn, $select_one_sql);
    $target = mysqli_fetch_assoc($select_one_result);
    return $target; 
}

function select_all($conn){
    $select_all_sql = "SELECT * FROM board";
    $select_all_result = mysqli_query($conn, $select_all_sql);
    $total = mysqli_num_rows($select_all_result);
    return $total;
}