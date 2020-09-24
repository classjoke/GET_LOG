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
        require_once("pdo.php");
        $pdo = pdo_connect();
        init();
        if(isset($_POST["hensyu"]))
        {
            $comment = $_POST["comment"];
            $edit_sql = "UPDATE $board_name SET comment=:comment WHERE id=:id";
            $stmt = $pdo->prepare($edit_sql);
            $stmt ->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt ->bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt ->execute();
            header("Location: input_only_page.php?date=$date");
        }
        elseif(isset($_POST["delete"]))
        {
            $delete = "DELETE FROM $board_name WHERE id=:id";
            $stmt = $pdo->prepare($delete);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
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
    <br><br><br><br><br><br><br><br><br>
    <form action="" method="POST">
        <textarea id="comment"  name="comment" rows="4" cols="50" style="vertical-align:middle;"><?=$results?></textarea> 
    <!--行数　row 文字数　cols cssコード：style=""　vertical-align 縦方向のそろえ方　middle　位置-->
        <br>
        <input type="submit" name="hensyu">
        <input type="submit" name="delete" value="削除">
    </form>

</html>