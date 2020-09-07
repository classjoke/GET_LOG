
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>editpage</title>
</head>
<body>
<?php
    function get_name(){
        $name = "ginga";
        return $name;
    }
    function get_date(){
        if(isset($_GET['date']))
        {
        $date = $_GET['date'];
        }
        else
        {
            $date = date('Y_m_j');
        }
        return $date;
    }
    require_once("pdo.php");
    $pdo =pdo_connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    session_start();
    $edit_flag = -1;
    $date=get_date();
    $comment ="";
    $board_name = "zoo_board_".$date;
    $html_time_value=date('H:i');
        if(isset($_POST["submit"]))
        {
            $name = get_name();
            $edit_flag = $_POST["edit_number_hidden"];
            if($edit_flag != -1)
            {
                $comment = $_POST["Edit_comment"];
                $id = $_POST["edit_number_hidden"];
                $edit_sql = "UPDATE $board_name SET comment=:comment, date=now() WHERE id=:id";
                $stmt = $pdo->prepare($edit_sql);
                $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                $stmt->bindValue(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $comment ="";
            }
        }
        if(!empty($_POST["remove"]))
        {
            $id = $_POST["rmnom"];
            $delete = "DELETE FROM $board_name WHERE id=:id";
            $stmt = $pdo->prepare($delete);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        }
        if(!empty($_POST["edit"]))
        {
            $edit_number = $_POST["ednom"];
            $edit_io = "SELECT comment FROM $board_name WHERE id=:id";
            $stmt = $pdo->prepare($edit_io);
            $stmt->bindValue(':id', $edit_number, PDO::PARAM_INT);
            $stmt->execute();
            $comment = $stmt->fetch(PDO::FETCH_COLUMN);
        }
        $select = "SELECT * FROM $board_name";
        // echo "ID,  username,   コメント,   コメント日<br>";
        $stmt = $pdo->query($select);
        $results = $stmt->fetchAll();
        foreach ($results as $row)
        {
            echo $row['id'].",";
            echo $row['comment'];
            // echo ",".$row['name'];
            echo "<hr>";
        }

?>

    <form action="" method="POST">
        コメント編集用:<input type = "text" name ="Edit_comment" value ="<?php echo $comment?>">
        <input type="submit" name="submit" value="送信">
        <br>
        削除番号:<input type = "number" name ="rmnom" min=1 max=9999>
        <input type= "submit" name= "remove" value = "削除">
        <br>
        編集番号:<input type = "number" name = "ednom" min=1 max=9999>
        <input type= "submit" name= "edit" value = "編集">
        <br>
        <input type = "hidden" name = "edit_number_hidden" value ="<?php echo $edit_number?>">
        <br>
    </form>
    <a href="EDITpage.php?date=<?php echo $date?>">更新</a>
    <br>
    <br>
    <a href="input_only_page.php?date=<?php echo $date?>">入力用ページへ</a>
</body>
</html>