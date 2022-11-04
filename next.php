

<?php
include_once './create_update_service.php';


function form($input, $status){?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= ($status)? "글수정" : "글작성" ?> </title>
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<div class="container">
<form method="post" action="<?=($status =='update')? '/update.php?index=<?=$status[pk]' : '';?>" enctype="multipart/form-data">
    <table class="create-or-update-table">
        <tr>
            <th>구분</th>
            <td>
                <select name="category" class="select-box">
                    <option value="unselect">선택해주세요</option>
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
                <?= make_tag($input, 'detail_option')?>
            </td>
        </tr>
        <tr>
            <th>고객유형</th>
            <td>
                <?= make_tag($input, 'question_type')?>
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
            </td>
        </tr>
    </table>
    <input type="submit" value="저장">
    <a href="./index.php"><input type="button" value="취소"></a>
</form>
</div>
</body>
</html>
<?php }?>