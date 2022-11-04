<?php
include_once './dbconnect.php';
include './validate.php';

function validate_form(){
    $input = array();
    $status = array();
    
    $keywords = array('category' => "구분", 'writer' => "작성자", 'title' => "제목" ,'content'=>"내용");

    foreach ($keywords as $keyword => $value){
        $validated_result = validate($_REQUEST, $keyword);
        if ($validated_result != "error") {
            $input[$keyword] = $validated_result;
        } else {
            $status[$keyword] = "$value(을)를 입력해주세요";
        }
    }

    //분류
    if (array_key_exists("detail_option", $_REQUEST)){
        $detail_option = $_POST['detail_option'];
        $input['detail_option'] = $detail_option;
    } else {
        $status['detail_option'] = "분류를 선택해주세요";
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
        $status['question_type'] = "고객 유형을 선택해주세요";
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
        if( !move_uploaded_file($_FILES['userfile']['tmp_name'], $upload_file)){
            $status[] = "파일이 정상적으로 업로드 되지 않았습니다.";
            exit;
        }
        $input['userfile'] = $name;
    }
    return array($input, $status);
};

function process_form($validate_input, $status){
    global $conn;
    $input = $validate_input;
    if(isset($input['userfile'])){
        $userfile = $input['userfile'];
    }else{
        $userfile = '';
    }
    //날짜 지금 저장시간으로 맞추고
    $datetime = date('Y-m-d H:i:s', time());

    if ($status == 'create'){
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
        var_dump($validate_input);
        $pk= $_GET['index'];
        $sql = "UPDATE board 
                SET category = '$input[category]',
                    writer = '$input[writer]',
                    detail_option = '$input[detail_option]',
                    question_type = '$input[question_type]',
                    title = '$input[title]',
                    content = '$input[content]',
                    userfile = '$userfile',
                    updated_date = '$datetime'
                WHERE pk = '$pk'";
        mysqli_query($conn, $sql);
        ?>
        <script>
            alert("게시글이 수정되었습니다.");
            location.replace("./read.php?index=<?=$pk?>");
        </script>
    <?php
    }
} 

function show_form($input, $status){
    if ($status) { //pk키가 있으면 업데이트에서 넘어온것이고, 없으면 errors가 넘어온것임
        if (array_key_exists('pk',$status)){ ?> 
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>글 수정 </title>
                <link rel="stylesheet" href="./css/style.css">
            </head>
            <body>
            <div class="container">
            <form method="post" action="./update.php?index=<?=$status['pk']?>" enctype="multipart/form-data">
                <table class="create-or-update-table">
                    <tr>
                        <th>구분</th>
                        <td>
                            <select name="category" class="select-box">
                                <option value="unselect">선택해주세요</option>
                                <option value="유지보수" <?= ($status['category'])? "selected" : ""?>>유지보수</option>
                                <option value="문의사항" <?= ($status['category'])? "selected" : ""?>>문의사항</option>                            
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>작성자</th>
                        <td>
                            <input type="text" width="100px" value="<?= $status['writer']?>" name="writer">
                        </td>
                    </tr>
                    <tr>
                        <th>분류</th>
                        <td>
                            <input type="radio" name="detail_option" value="홈페이지" <?= ($status['detail_option']== "홈페이지")? "checked" : "" ;?>>홈페이지
                            <input type="radio" name="detail_option" value="네트워크" <?= ($status['detail_option']== "네트워크")? "checked" : "" ;?>>네트워크
                            <input type="radio" name="detail_option" value="서버" <?= ($status['detail_option']== "서버")? "checked" : "" ;?>>서버
                        </td>
                    </tr>
                    <tr>
                        <th>고객유형</th>
                        <td>
                            <input type="checkbox" name="question_type[]" value="호스팅" <?=(strpos($status['question_type'], "호스팅") !==false)? "checked" : ""; ?>>호스팅
                            <input type="checkbox" name="question_type[]" value="유지보수"<?=(strpos($status['question_type'], "유지보수")!==false)? "checked" : ""; ?>>유지보수
                            <input type="checkbox" name="question_type[]" value="서버임대"<?=(strpos($status['question_type'], "서버임대")!==false)? "checked" : ""; ?>>서버임대
                            <input type="checkbox" name="question_type[]" value="기타"<?=(strpos($status['question_type'], "기타")!==false)? "checked" : ""; ?>>기타
                        </td>
                    </tr>
                    <tr>
                        <th>제목</th>
                        <td>
                            <input type="text" name="title" value="<?= $status['title'];?>">
                        </td>
                    </tr>
                    <tr>
                        <th>내용</th>
                        <td>
                            <textarea name="content" cols=75 rows=15 ><?= $status['content'] ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th>첨부파일</th>
                        <td>
                            <?= (($status['userfile'] == "")) ? '<input type="file" name="userfile">' 
                            :'<div class="filename"> <input type="file" name="userfile">'.$status['userfile'].'
                            <a href="./delete_file.php?index='.$status['pk'].'"><button type="button">삭제</button></a>
                            </div>'; ?>                      
                        </td>
                    </tr>
                </table>
                <input type="submit" value="저장">
                <a href="./index.php"><input type="button" value="취소"></a>
            </form>
        </div>
    </body>
</html>
<?php
        } else {// errors가 넘어왔을때 

            $validate_errors = $status;//헷갈리지 않게 validate_errors로 이름변경
            ?> 
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>글 수정 </title>
                <link rel="stylesheet" href="./css/style.css">
            </head>
            <body>
            <div class="container">
            <form method="post" action="<?= $_SERVER['PHP_SELF']?>" enctype="multipart/form-data">
                <table class="create-or-update-table">
                    <tr>
                        <th>구분</th>
                        <td>
                            <select name="category">
                                <option value="unselect">선택해주세요</option>
                                <?= (array_key_exists('category',$input) && $input['category']=='유지보수')? 
                                "<option value='유지보수' selected> 유지보수</option>" :
                                "<option value='유지보수'>유지보수</option>"?>
                                <?= (array_key_exists('category',$input) && $input['category']=='문의사항')? 
                                "<option value='문의사항' selected >문의사항</option> " :
                                "<option value='문의사항'>문의사항</option>"?>                            
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>작성자</th>
                        <td>
                            <input type="text" width="100px" value="<?= (array_key_exists('writer',$input))? $input['writer'] : '';?>" name="writer">
                        </td>
                    </tr>
                    <tr>
                        <th>분류</th>
                        <td>
                            <?= ((!array_key_exists('detail_option',$input)) || array_key_exists('detail_option',$input) && $input['detail_option']=='홈페이지')? 
                                '<input type="radio" name="detail_option" value="홈페이지" checked>홈페이지' :
                                '<input type="radio" name="detail_option" value="홈페이지">홈페이지'?>
                            <?= ((!array_key_exists('detail_option',$input)) || array_key_exists('detail_option',$input) && $input['detail_option']=='네트워크')? 
                            '<input type="radio" name="detail_option" value="네트워크" checked>네트워크' :
                            '<input type="radio" name="detail_option" value="네트워크">네트워크'?>
                            <?= ((!array_key_exists('detail_option',$input)) || array_key_exists('detail_option',$input) && $input['detail_option']=='서버')? 
                            '<input type="radio" name="detail_option" value="서버" checked>서버' :
                            '<input type="radio" name="detail_option" value="서버">서버'?>

                        </td>
                    </tr>
                    <tr>
                        <th>고객유형</th>
                        <td>
                            <?= ((array_key_exists('question_type',$input) && strpos($input['question_type'], "호스팅") !==false))? 
                                '<input type="checkbox" name="question_type[]" value="호스팅" checked>호스팅' :
                                '<input type="checkbox" name="question_type[]" value="호스팅">호스팅' ?>
                            <?= ((array_key_exists('question_type',$input) && strpos($input['question_type'], "유지보수") !==false))? 
                                '<input type="checkbox" name="question_type[]" value="유지보수" checked>유지보수' :
                                '<input type="checkbox" name="question_type[]" value="유지보수">유지보수' ?>
                            <?= ((array_key_exists('question_type',$input) && strpos($input['question_type'], "서버임대") !==false))? 
                                '<input type="checkbox" name="question_type[]" value="서버임대" checked>서버임대' :
                                '<input type="checkbox" name="question_type[]" value="서버임대">서버임대' ?>
                            <?= ((array_key_exists('question_type',$input) && strpos($input['question_type'], "기타") !==false))? 
                                '<input type="checkbox" name="question_type[]" value="기타" checked>기타' :
                                '<input type="checkbox" name="question_type[]" value="기타">기타' ?>    
                        </td>
                    </tr>
                    <tr>
                        <th>제목</th>
                        <td>
                            <input type="text" name="title" value="<?= (array_key_exists('title',$input))? $input['title'] : '';?>">
                        </td>
                    </tr>
                    <tr>
                        <th>내용</th>
                        <td>
                            <textarea name="content" cols=75 rows=15 ><?= (array_key_exists('content',$input))? $input['content'] : ''; ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th>첨부파일</th>
                        <td>
                            <input type="file" name="userfile">
                        </td>
                    </tr>
                </table>
                <input type="submit" value="저장">
                <a href="./index.php"><input type="button" value="취소"></a>
            </form>
            <div class=error-message>
                <?= implode('<br>', $validate_errors); ?>
            </div>
        </div>
    </body>
</html>


        <?php }
    } else {?>
        
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>글 작성</title>
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <div class="container">
        <form method="post" action="<?php $_SERVER["PHP_SELF"]; ?>" enctype="multipart/form-data">
            <table class="create-or-update-table">
                <tr>
                    <th>구분(필수)</th>
                    <td>
                        <select name="category" class="select-box">
                            <option value="unselect">선택해주세요</option>
                            <option value="유지보수">유지보수</option>
                            <option value="문의사항">문의사항</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>작성자(필수)</th>
                    <td>
                        <input type="text" width="100px" value="" name="writer" >
                    </td>
                </tr>
                <tr>
                    <th>분류(필수)</th>
                    <td>
                        <input type="radio" name="detail_option" value="홈페이지" checked >홈페이지
                        <input type="radio" name="detail_option" value="네트워크">네트워크
                        <input type="radio" name="detail_option" value="서버">서버
                    </td>
                </tr>
                <tr>
                    <th>고객유형</th>
                    <td>
                        <input type="checkbox" name="question_type[]" value="호스팅" >호스팅
                        <input type="checkbox" name="question_type[]" value="유지보수">유지보수
                        <input type="checkbox" name="question_type[]" value="서버임대">서버임대
                        <input type="checkbox" name="question_type[]" value="기타">기타
                    </td>
                </tr>
                <tr>
                    <th>제목(필수)</th>
                    <td>
                        <input type="text" name="title" value="" >
                    </td>
                </tr>
                <tr>
                    <th>내용(필수)</th>
                    <td>
                        <textarea name="content" cols=75 rows=15 ></textarea>
                    </td>
                </tr>
                <tr>
                    <th>첨부파일</th>
                    <td>
                        <input type="file" name="userfile">
                    </td>
                </tr>
            </table>
            <input type="submit" value="저장">
            <a href="./index.php"><input type="button" value="취소"></a>
        </form>
        
    </div>
</body>
</html>

<?php    }
}

define('VALUE',['category' => ['유지보수' =>'유지보수', '문의사항'=>'문의사항'],
                'detail_option' => ['홈페이지' , '네트워크' , '서버'],
                'question_type' => ['호스팅','유지보수','서버임대','기타']]);

function make_tag($input,$name){
    $tag = "";
    foreach (VALUE[$name] as $key){
        $end = ""; 
        if ($input && $input[$name]==$key){
            $end = "ed";  
        }
        switch ($name){
            case 'category' : $made_tag = "<option value='$key' select$end>$key</option>";
                break;
            case 'detail_option' : $made_tag = "<input type='radio' name='detail_option' value='$key' check$end>$key";
                break;
            case 'question_type' : $made_tag = "<input type='checkbox' name='question_type[]' value='$key' check$end>$key";
        };
        $tag.= $made_tag;
    }
    return $tag;
}

function text_input($input,$name){
    //글수정시 작성자 잘 변하는지 확인!
    $input_value ="";
    if ($input && array_key_exists($name,$input)){
        $input_value=$input[$name];
    }
    if ($name == 'content'){
        $text_input = "<textarea name=$name cols=75 rows=15 > $input_value</textarea>";
    } else {
        $text_input= "<input type='text' value='$input_value' name='$name'>";
    }
    return $text_input;
}


function file_input($input,$name){
    //여기부터 
    "<?= (($status['userfile'] == "")) ? '<input type="file" name="userfile">' 
    :'<div class="filename"> <input type="file" name="userfile">'.$status['userfile'].'
    <a href="./delete_file.php?index='.$status['pk'].'"><button type="button">삭제</button></a>
    </div>'; ?>    "
}