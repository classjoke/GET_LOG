<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>表示</title>
</head>
<body>
    <?php
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
        function get_name(){
            if(isset($_POST["name"])){
                $name = $_POST["name"];
            }
            else
            {
                $name = "";
            }
            return $name;
        }
        require_once("pdo.php");
        $pdo = pdo_connect();
        $date=get_date();
        echo "<h1>".$date."</h1>";
        $board_name = "zoo_board_".$date;
        $name = get_name();
        $select = "SELECT * FROM $board_name";
        // echo "ID,  username,   コメント,   コメント日<br>";
        $stmt = $pdo->query($select);
        $results = $stmt->fetchAll();
        foreach ($results as $row)
        {
            if($name != "")
            {
                if(strpos($row["comment"], $name) !== false)
                {
                    echo $row['id'].",";
                    if(substr($row["comment"], -1) == "-"){
                        $ids = $row['id'];
                        $jo =  $row['comment']
                        ."<a href='leave.php?date=$date&id=$ids'>離席</a>";
                        echo $jo;
                    }else{
                        echo $row['comment'];
                    }
                    echo "<hr>";
                }
            }
            else
            {
                echo $row['id'].",";
                if(substr($row["comment"], -1) == "-"){
                    $ids = $row['id'];
                    $jo =  $row['comment']
                    ."<a href='leave.php?date=$date&id=$ids'>離席</a>";
                    echo $jo;
                }else{
                    echo $row['comment'];
                }
                echo "<hr>";
            }

        }
    ?>
    <form action="" method="POST">
        <tr>
            <td>
                <input type="text" name="name" value="">
                <input type="submit" name="submit" value="送信">
            </td>
        </tr>
    </form>
    <br>
    <br>
    <a href="input_only_page.php?date=<?php echo $date?>">入力用ページ</a>
    <br>
    <br>
    <a href="EDITpage.php?date=<?php echo $date?>">編集用ページへ</a>
    <br>
    <br>
    <a href="potal.php">ポータルへ</a>
    <br>
    <a href="display.php?date=<?php echo $date?>">更新</a>
</body>
</html>