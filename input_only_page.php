
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>入力ページ</title>
</head>
<body>
<?php
    function create_table()
    {
        global $pdo, $board_name;

        $sql="CREATE TABLE IF NOT EXISTS $board_name"
        ."("
        ."id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,"
        ."comment VARCHAR(128),"
        ."name VARCHAR(128),"
        ."date DATETIME NOT NULL"
        .")"
        ."ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;";
        $stmt = $pdo->query($sql);
        return;
    }
    function get_comment()
    {   
        $comment = "none";
        if(isset($_POST["sousin"])){
            if($_POST["in_out"] == "in"){
                $comment = "(".$_POST["table"].$_POST["table_num"].")".$_POST["type"].$_POST["name"]."  ".$_POST["time"]."-";
            }
            elseif($_POST["in_out"] == "out"){
                $comment = "(".$_POST["table"].$_POST["table_num"].")".$_POST["type"].$_POST["name"]."     "."-".$_POST["time"];
            }
        }
        return $comment;
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
    $comment = get_comment();
    // echo $comment;
    $edit_number=NULL;
    $errors = Array();
    $modified_flag= 0;
    $date=get_date();
    echo "<h1>".$date."</h1>";
    $board_name = "zoo_board_".$date;
    create_table();
    $html_time_value=date('H:i');
    if(isset($_POST["sousin"]))
    {
        // echo "check1";
        // $name = $_SESSION["NAME"];
        $name = "ginga";
        if($comment != "none")
        {
            // 普通の投稿
            $insert_tmp = "INSERT INTO $board_name(name, comment, date) VALUES (:name, :comment, now())";
            $new_writeing = $pdo -> prepare($insert_tmp);
            $new_writeing -> bindParam (":name", $name, PDO::PARAM_STR);
            $new_writeing -> bindParam(":comment", $comment, PDO::PARAM_STR);
            $new_writeing -> execute();
            $modified_flag = 1;
        }
    }
    $select = "SELECT * FROM $board_name";
    // echo "ID,  username,   コメント,   コメント日<br>";
    $stmt = $pdo->query($select);
    $results = $stmt->fetchAll();
    foreach ($results as $row)
    {
        echo $row['id'].",";
        if(substr($row["comment"], -1) == "-"){
            $ids = $row['id'];
            $jo =  $row['comment']
            ."<a href='leave.php?date=$date&id=$ids'>離席</a>";
            // ."<input type='submit' name='leave' value='離席' form='leave'>"
            // ."<input type = 'hidden' name = 'id' form='leave' value ='".$row['id']."'>"
            // ."<input type = 'hidden' name = 'date' form='leave' value='".$date.">";
            echo $jo;
        }else{
            echo $row['comment'];
        }
        echo "<hr>";
    }

?>
    <form action="" method="POST">
        <tr>
            <td align="right"><b>テーブルの種類：</b></td>
            <td>
                <input type="radio" name="table" value="T" checked="checked">通常席
                <input type="radio" name="table" value="V">VIP
                <input type="radio" name="table" value="S">Servis VIP
            </td>
        </tr>
        <br>
        <tr>
            <td align="right"><b>テーブル番号：</b></td>
            <td>
            <select name="table_num">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
                <option value="11">11</option>
                <option value="12">12</option>
                <option value="13">13</option>
                <option value="14">14</option>
                <option value="15">15</option>
            </select>
            </td>
        </tr>
        <br>
        <tr>
            <td align="right"><b>Type：</b></td>
            <td>
            <input type="radio" name="type" value="A" checked="checked">Ａ指名
            <input type="radio" name="type" value="B">Ｂ指名
            <input type="radio" name="type" value="F">フリー
            <input type="radio" name="type" value="H">ヘルプ
            </td>
        </tr>
        <br>
        <tr>
            <td align="right"><b>name：</b></td>
            <td>
            <input type="text" name="name" value="" checked="checked">
            </td>
        </tr>
        <br>
        <tr>
            <td align="right"><b>time：</b></td>
            <td>
            <input type="time" name="time" value="<?php echo $html_time_value?>">
            </td>
        </tr>
        <br>
        <tr>
            <td align="right"><b>INOUT：</b></td>
            <td>
                <input type="radio" name="in_out" value="in" checked="checked">入
                <input type="radio" name="in_out" value="out">出
            </td>
        </tr>
        <br>
        <input type="submit" name="sousin" value="送信">
    </form>
    <br>
    <br>
    <a href="input_only_page.php?date=<?php echo $date?>">更新</a>
    <br>
    <br>
    <a href="EDITpage.php?date=<?php echo $date?>">編集用ページへ</a>
    <br>
    <br>
    <a href="potal.php">ポータルへ</a>
    <br>
    <a href="display.php?date=<?php echo $date?>">表示用ページへ</a>
</body>
</html>
