
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">
    <meta http-equiv="refresh" content="5; URL=">
    <title>指名係</title>
    <link rel="stylesheet" href="css/input.css"/>

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
                echo $value;
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
    $date=get_date();
    
    $html_time_value=date('H:i');
    echo "<h1>".$date.
    "  <a href='simeigakari.php?date=$date'>非表示</a>".
    "  <a href='simeigakari.php?date=$date&dis=t'>表示</a>  ".
    "<span style= color:blue>".$html_time_value."</span>".
    "</h1>";
    $board_name = "zoo_board_".$date;
    display();

?>
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
