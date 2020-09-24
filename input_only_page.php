
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>入力ページ</title>
    <link rel="stylesheet" href="css/input.css"/>

</head>
<body>
<?php
    function create_table()
    {
        global $pdo, $board_name;

        $sql="CREATE TABLE IF NOT EXISTS $board_name"
        ."("
        ."id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,"
        ."comment VARCHAR(1024)"
        .")"
        ."ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;";
        $stmt = $pdo->query($sql);
        return;
    }
    function get_comment($i)
    {   
        $comment = "none";
        if(isset($_POST["sousin"])){
            if($_POST["in_out"] == "in"){
                $comment = "(".$_POST["table"].$_POST["table_num"].")"."<p hidden>60</p>".$_POST["type"].$_POST["text_1"][$i]." ".$_POST["time"]."-";
            }
            elseif($_POST["in_out"] == "out"){
                $comment = "(".$_POST["table"].$_POST["table_num"].")"."<p hidden>60</p>".$_POST["type"].$_POST["text_1"][$i]." "."-".$_POST["time"];
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
    function get_dis(){
        if(isset($_GET['dis']))
        {
            return True;
        }
        return False;
    }
    function get_table($value){
        if(!preg_match("/\)/",substr($value , 2, 2)) && substr($value , 2 , 1) == "1" ){
            $buf = substr($value ,1,3);
        }
        else{
            $buf = substr($value ,1,2);
        }
        return $buf;
    }
    function highlight_name($value){
        preg_match("/[ABCFHJ][あ-んー]* [0-9][0-9]:[0-9][0-9]\-$/u", $value, $res, PREG_OFFSET_CAPTURE);
        if (count($res) != 0){
        $name = substr($res[0][0], 0, -6);
        $name_count = strlen($name);
        
        $color_name = '<span style = '."color:#ff0000;".'>'.$name."</span>";
        $value = substr_replace($value, $color_name, $res[0][1], $name_count);
        }
        return $value;
    }
    function cut_table($value){
        if(!preg_match("/\)/",substr($value , 2, 2)) && substr($value , 2 , 1) == "1" ){
            $table = substr($value, 0, 5);
            $value = str_replace($table, '', $value);
        }
        else{
            $table = substr($value, 0, 4);
            $value = str_replace($table, '', $value);
        }
        return $value;
    }
    function get_timer($value){
        preg_match("/[0-9]*:[0-9]*/u", $value, $res, PREG_OFFSET_CAPTURE);
        preg_match("/<p hidden>[0-9]*<\/p>/u", $value, $timer, PREG_OFFSET_CAPTURE);
        if (count($res) != 0 && count($timer)){
            $start_time = strtotime($res[0][0]);
            $timer_val = substr($timer[0][0], 10, -4);
            $fin_time = $start_time +  60 * $timer_val;
            $now_timer = ($fin_time - strtotime("now")) / 60 ;
            if($now_timer > 360){
                $now_timer -= 1440;
            }
            echo "<t1>";
            echo floor($now_timer);
            echo "分";
            echo "</t1>";
        }
        return;
        
    }
    function display(){
        global $board_name, $pdo, $date;
        $select_id = "SELECT id FROM $board_name";
        $stmt = $pdo->query($select_id);
        $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $select_com = "SELECT comment FROM $board_name";
        $stmt = $pdo->query($select_com);
        $comments = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $results = array_combine($ids, $comments);
        natsort($results);
        $prev_table = "";
        foreach($results as $key => $value)
        {
            if(substr($value, -1) == "-"){
                $table_data = get_table($value);
                if($table_data != $prev_table){
                    echo "<hr>";
                    echo "<h3>".$table_data;
                    // ここでタイマーを取得し表示
                    get_timer($value);
                    echo "</h3>";
                    $prev_table = $table_data;
                }
                $value = highlight_name($value);
                $value = cut_table($value);
                echo "<a href='edit.php?date=$date&id=$key'>編集</a>";
                echo $value
                ."<a href='add_comment.php?date=$date&id=$key'>追加</a>"
                ."  <a href='leave.php?date=$date&id=$key'>離席</a>"
                ."   <a href='Extension.php?date=$date&id=$key'>延長</a>";
                echo "<br>";
            }elseif(get_dis()){
                $table_data = get_table($value);
                if($table_data != $prev_table){
                    echo "<hr>";
                    echo "<h3>".$table_data."</h3>";
                    $prev_table = $table_data;
                }
                $value = cut_table($value);
                echo "<a href='edit.php?date=$date&id=$key'>編集</a>";
                echo $value; 
                echo "<br>";
            }
        }
        echo "<hr>";
    }
    require_once("pdo.php");
    $pdo =pdo_connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    session_start();
    // echo $comment;
    $edit_number=NULL;
    $errors = Array();
    $modified_flag= 0;
    $date=get_date();
    echo "<h1>".$date.
    "  <a href='input_only_page.php?date=$date'>非表示</a>".
    "  <a href='input_only_page.php?date=$date&dis=t'>表示</a>".
    "</h1>";
    $board_name = "zoo_board_".$date;
    create_table();
    $html_time_value=date('H:i');

    if(isset($_POST["sousin"]))
    {
        for($comment_num = 0 ; $comment_num < count($_POST["text_1"]);$comment_num++){
            $comment = get_comment($comment_num);
            if($comment != "none")
            {
                // 普通の投稿
                $insert_tmp = "INSERT INTO $board_name(comment) VALUES (:comment)";
                $new_writeing = $pdo -> prepare($insert_tmp);
                $new_writeing -> bindParam(":comment", $comment, PDO::PARAM_STR);
                $new_writeing -> execute();
                $modified_flag = 1;
            }
        }
    }
    display();

?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script type="text/javascript">
 
 $(function() {
  
   $('button#add').click(function(){
  
   var tr_form = '' +
   '<tr>' +
     '<td><input type="text" name="text_1[]" required></td>' +
   '</tr>';
  
   $(tr_form).appendTo($('table > tbody'));
  
    });
 });
 </script>
    <form action="" method="POST">
        <tr>
            <td align="right"><b>テーブルの種類：</b></td>
            <td>
                <label><input type="radio" name="table" value="T" checked="checked">通常席</label>
                <label><input type="radio" name="table" value="V">VIP</label>
                <label><input type="radio" name="table" value="S">Servis VIP</label>
            </td>
        </tr>
        <br>
        <tr>
        <label><td align="right"><b>テーブル番号：</b></td>
            <td>
            <input type="number" name="table_num" min=1 max=15 required onBlur="javascript:document.forms[0].elements['name'].focus()"></label>
            </td>
        </tr>
        <br>
        <tr>
            <td align="right"><b>Type：</b></td>
            <td>
            <label><input type="radio" name="type" value="A" checked="checked">Ａ指名</label>
            <label><input type="radio" name="type" value="B">Ｂ指名</label>
            <label><input type="radio" name="type" value="C">同伴</label>
            <label><input type="radio" name="type" value="F">フリー</label>
            <label><input type="radio" name="type" value="H">ヘルプ</label>
            <label><input type="radio" name="type" value="J">フリー飛ばし</label>
            </td>
        </tr>
        <table>
            <tbody>
                <tr>
                <td><input type="text" name="text_1[]" required></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                <td ><button id="add" type="button">追加</button></td>
                </tr>
            </tfoot>
        </table>
        <br>
        <tr>
            <label><td align="right"><b>time：</b></td>
            <td>
            <input type="time" name="time" value="<?php echo $html_time_value?>"></label>
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
    <a href="EDITpage.php?date=<?php echo $date?>">編集用ページへ</a>
    <br>
    <br>
    <a href="potal.php">ポータルへ</a>
    <br>
    <a href="display.php?date=<?php echo $date?>">表示用ページへ</a>
    <br>
    <a href="simeigakari.php?date=<?php echo $date?>">指名係用ページへ</a>
</body>
</html>
