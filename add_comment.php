<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>編集専用ページ</title>
</head>
<body>
    <?php
        function init(){
            global $date,$id,$board_name;
            if(isset($_GET['date']))
            {
                $date = $_GET['date'];
            }
            else
            {
                $date = date('Y_m_j');
            }
            if(isset($_GET['id']))
            {
                $id = $_GET['id'];
            }
            else
            {
                $id = 1;
            }
            $board_name = "zoo_board_".$date;
        }
        function get_comment(){
            $comment="";
            if(isset($_POST["hensyu"])){
                $comment = $_POST['type'].$_POST['name']." ".$_POST['time']."-";
            }
            return $comment;
        }
        require_once("pdo.php");
        $pdo = pdo_connect();
        init();
        $html_time_value=date('H:i');
        if(isset($_POST["hensyu"]))
        {
            $hid_comment = $_POST["hid_comment"];
            $res_comment = get_comment();
            $comment = $hid_comment.$res_comment;
            $edit_sql = "UPDATE $board_name SET comment=:comment WHERE id=:id";
            $stmt = $pdo->prepare($edit_sql);
            $stmt ->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt ->bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt ->execute();
            header("Location: input_only_page.php?date=$date");
        }

        if(isset($_GET['date']))
        {
            $board_name = "zoo_board_".$date;
            if(isset($_GET['id']))
            {
                $select = "SELECT comment FROM $board_name WHERE id=:id";
                $stmt = $pdo->prepare($select);
                $stmt ->bindValue(':id', $id);
                $stmt ->execute();
                $results = $stmt->fetch(PDO::FETCH_COLUMN);
                echo $results;
            }
            else
            {
                header("Location: input_only_page.php?date=$date");
            }
        }
        else
        {
            header("Location: input_only_page.php");
        }
    ?>
</body>
    <form action="" method="POST">
        <tr>
            <td align="right"><b>Type：</b></td>
            <td>
            <label><input type="radio" name="type" value="A">Ａ指名</label>
            <label><input type="radio" name="type" value="B">Ｂ指名</label>
            <label><input type="radio" name="type" value="F">フリー</label>
            <label><input type="radio" name="type" value="H"  checked="checked">ヘルプ</label>
            <label><input type="radio" name="type" value="J">フリー飛ばし</label>
            </td>
        </tr>
        <br>
        <tr>
            <label><td align="right"><b>name：</b></td>
            <td>
            <input type="text" name="name" required></label>
            </td>
        </tr>
        <br>
        <tr>
            <label><td align="right"><b>time：</b></td>
            <td>
            <input type="time" name="time" value="<?php echo $html_time_value?>"></label>
            </td>
        </tr>
        <input type="hidden" name="hid_comment" value="<?=$results?>">
        <input type="submit" name="hensyu">
    </form>
    <br>
    <a href="input_only_page.php?date=<?php echo $date?>">戻る</a>
</html>