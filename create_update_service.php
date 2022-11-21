<?php
include_once './dbconnect.php';

function validate_input($input_data, $keyword){
    $input = "";
    
    if (array_key_exists($keyword, $input_data)){
        $input = $input_data["$keyword"];
        
        //구분 선택안했을때 error반환
        if ($input == "unselect") {
            return "error";
        }
            //
        if ($keyword == "writer" || $keyword == "title" || $keyword == "content"){
            if (strlen(trim($input)) > 1){
                $input = htmlentities(trim($input));    
            } else {
                return "error";
            }
        }
        return $input;
    }    
}

function validate_form(){
    $input = array();
    $errors = array();
    
    $keywords = array('category' => "구분", 'writer' => "작성자", 'title' => "제목" ,'content'=>"내용");

    foreach ($keywords as $keyword => $value){
        $validated_result = validate_input($_POST, $keyword);
        if ($validated_result != "error") {
            $input[$keyword] = $validated_result;
        } else {
            $errors[$keyword] = "$value(을)를 입력해주세요";
        }
    }

    //분류
    if (array_key_exists("detail_option", $_REQUEST)){
        $detail_option = $_POST['detail_option'];
        $input['detail_option'] = $detail_option;
    } else {
        $input['detail_option']="";
        $errors['detail_option'] = "분류를 선택해주세요";
    }
    
    //고객유형 
    if (array_key_exists("question_type", $_REQUEST)){
        $question_type= $_POST['question_type'];
        if (count($question_type)>=2){
            $input['question_type'] = implode(', ', $question_type);
        }
        else {
            $input['question_type'] = $question_type[0];
        }
    } else {
        $input['question_type'] = "";
        $errors['question_type'] = "고객 유형을 선택해주세요";
    }

    //파일
    $uploads_dir = "./files";
    // 확장자는 따로 지정하지 않음
    
    if ($_FILES['userfile']['full_path'] != ""){
        $name = $_FILES['userfile']['name'];
        $upload_file = $uploads_dir.'/'.$name; //저장될 디렉터리 및 파일명
        
        $fileinfo = pathinfo($upload_file); //첨부파일 정보를 pathinfo함수로 개별정보로 분리
        $ext = strtolower($fileinfo['extension']); //확장자 소문자로 바꿈
            
        $i = 1;
        while (is_file($upload_file)){//파일 중복 검사
            $name = $fileinfo['filename']."-{$i}.".$ext;
            $upload_file = $uploads_dir.'/'.$name;
            $i++;
        }
        
        /*move_uploaded_file()함수는 임시디렉터리에 저장된 파일을 새위치로 이동하는 함수.
            성공시 true반환 실패시 false 반환*/
        if(!move_uploaded_file($_FILES['userfile']['tmp_name'], $upload_file)){
            $errors[] = "파일이 정상적으로 업로드 되지 않았습니다.";
            exit;
        }
        
        $input['userfile'] = $name;    
    }
    return array($input, $errors);

};

function process_form($validated_input, $mode){
    global $conn;
    $input = $validated_input;
    if(isset($input['userfile'])){
        $userfile = $input['userfile'];
    }else{
        $userfile = '';
    }
    //날짜 지금 저장시간으로 맞추고
    $datetime = date('Y-m-d H:i:s', time());

    if ($mode == 'create'){
        $sql = "INSERT INTO board
                SET category = '$input[category]',
                    writer = '$input[writer]',
                    detail_option = '$input[detail_option]',
                    question_type = '$input[question_type]',
                    title = '$input[title]',
                    content = '$input[content]',
                    userfile = '$userfile',
                    created_date = '$datetime'";
        mysqli_query($conn, $sql);
        ?>
        <script>
            alert("게시글이 등록되었습니다.");
            location.replace("./index.php");
        </script>
<?php } else { // update일때 
        $pk= $_GET['index'];
        $select_this_sql = "SELECT * FROM board WHERE pk = $pk";
        $select_this_result = mysqli_query($conn, $select_this_sql);
        $update_target = mysqli_fetch_assoc($select_this_result);
        $file_name = $update_target['userfile'];
        $update_sql = "UPDATE board 
                SET category = '$input[category]',
                    writer = '$input[writer]',
                    detail_option = '$input[detail_option]',
                    question_type = '$input[question_type]',
                    title = '$input[title]',
                    content = '$input[content]',
                    userfile = '$userfile',
                    updated_date = '$datetime'
                WHERE pk = '$pk'";
        mysqli_query($conn, $update_sql);
        
        //기존에 있던 파일은 저장소에서 삭제
        unlink('./files/'.$file_name);
        ?>
        <script>
            alert("게시글이 수정되었습니다.");
            location.replace("./read.php?index=<?=$pk?>");
        </script>
    <?php
    }
} 

function show_form($input, $errors, $title){?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=$title?></title>
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <div class="container">
        <form method="post" action="<?=($errors =='update')? "/board/update.php?index=$input[pk]" : "";?>" enctype="multipart/form-data">
            <table class="create-or-update-table">
                <tr>
                    <th>구분</th>
                    <td> 
                        <select name="category" class="select-box">
                            <option value="unselect" id="unselect">선택해주세요</option>
                            <?= make_tag($input, 'category')?>                        
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>작성자</th>
                    <td>
                        <?=text_input($input,'writer')?>
                    </td>
                </tr>
                <tr>
                    <th>분류</th>
                    <td>
                        <div class="detail-option-wrapper"><?= make_tag($input, 'detail_option')?></div>
                    </td>
                </tr>
                <tr>
                    <th>고객유형</th>
                    <td>
                        <div class="question-type-wrapper"><?= make_tag($input, 'question_type')?></div>
                    </td>
                </tr>
                <tr>
                    <th>제목</th>
                    <td>
                        <?=text_input($input,'title')?>
                    </td>
                </tr>
                <tr>
                    <th>내용</th>
                    <td>
                        <?=text_input($input,'content')?>
                    </td>
                </tr>
                <tr>
                    <th>첨부파일</th>
                    <td> 
                        <?= file_input($input,$errors)?>               
                    </td>
                </tr>
            </table>
            <div class = "lower-btn">
            <input type="submit" value="저장" class="button">
            <a href=<?=($errors=='update')? "./read.php?index=$input[pk]" :"./index.php"?>><input type="button" value="취소" class="button"></a>
            </div>
        </form>
    <!--에러메세지 표시되는 곳 -->
    <?= (is_array($errors)) ? '<div class=error-message>'.implode('<br>', $errors).'</div>':'' ?>
    </div>
</body>
</html>
<?php }

define('VALUE',['category' => ['유지보수','문의사항'],
                'detail_option' => ['홈페이지' , '네트워크' , '서버'],
                'question_type' => ['호스팅','유지보수','서버임대','기타']]);

function make_tag($input,$name){
    $tag = "";
    foreach (VALUE[$name] as $key){
        $end = ""; 

        //input ={'category' = '유지보수'}
        if ($input && ($input[$name]==$key|| strpos($input[$name], $key) !==false)){
            $end = "ed";
        }
        if ($key == "서버"){
            $key = $key."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        }
        switch ($name){
            case 'category' : $made_tag = "<option value='$key' select$end>$key</option>";
                break;
            case 'detail_option' : $made_tag = "<div class='detail-option-area'><input type='radio' name='detail_option' value='$key' check$end >$key</div>";
                break;
            case 'question_type' : $made_tag = "<div class='question-type-area'><input type='checkbox' name='question_type[]' value='$key' check$end>&nbsp;$key</div>";
        };
        $tag.= $made_tag;
    }
    return $tag;
}

function text_input($input, $name){
    //글수정시 작성자 잘 변하는지 확인
    $input_value ="";
    
    if (!$input){
        $text_input= "<input type='text value='$input_value' name='$name' class ='$name-input'>";
        if ($name == 'content'){
            $text_input = "<textarea name=$name cols=75 rows=15 class ='$name-input'> $input_value</textarea>";
        } 
    } else {
        if (array_key_exists($name, $input)){
            $input_value = $input[$name];
        }
        $text_input= "<input type='text' value='$input_value' name='$name' class ='$name-input'>";
        
        if ($name == 'content'){
            $text_input = "<textarea name=$name cols=75 rows=15 class ='$name-input'> $input_value</textarea>";
        } 
    }
    return $text_input;
}

function file_input($input ,$errors){
    if ($errors == 'update'){
        $file_input = '<div class="filename"> <input type="file" name="userfile">'.$input['userfile'].'
                        <a href="./delete_file.php?index='.$input['pk'].'"><button type="button">삭제</button></a>
                        </div>';
    } else {
        $file_input= '<input type="file" name="userfile">';
    }
    return $file_input;
}
