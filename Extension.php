<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>延長ページ</title>
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
        function get_timer($value){
            preg_match("/[0-9]*:[0-9]*/u", $value, $res, PREG_OFFSET_CAPTURE);
            preg_match("/<p hidden>[0-9]*<\/p>/u", $value, $timer, PREG_OFFSET_CAPTURE);
            if (count($res) != 0 && count($timer)){
                $start_time = strtotime($res[0][0]);
                $timer_val = substr($timer[0][0], 10, -4);
                $fin_time = $start_time +  60 * $timer_val;
                $now_timer = ($fin_time - strtotime('now')) / 60;
                if($now_timer > 360){
                    $now_timer -= 1440;
                }
                echo "<h2>";
                echo floor($now_timer);
                echo "分";
                echo "</h2>";
            }
            return;
        }
        function update_comment($ex_time){
            global $board_name, $id, $date, $pdo;
            $comment = get_comment_time_edit($ex_time);
            $edit_sql = "UPDATE $board_name SET comment=:comment WHERE id=:id";
            $stmt = $pdo->prepare($edit_sql);
            $stmt ->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt ->bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt ->execute();
            header("Location: input_only_page.php?date=$date");
        }
        function get_comment_time_edit($ex_time){
            global $results;
            preg_match("/<p hidden>[0-9]*<\/p>/u", $results, $timer, PREG_OFFSET_CAPTURE);
            if (count($timer)){
                $timer_val = substr($timer[0][0], 10, -4);
                $total_comment = str_replace($timer_val, $timer_val+$ex_time, $results);
                return $total_comment;
            }
        }
        require_once("pdo.php");
        $pdo = pdo_connect();
        init();
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
                get_timer($results);
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
        if(isset($_POST["30"]))
        {
            update_comment($_POST["30"]);
        }
        if(isset($_POST["60"]))
        {
            update_comment($_POST["60"]);
        }
    ?>
    <form action="" method="POST">
        <input type="submit" name="30" value="30">
        <input type="submit" name="60" value="60">
    </form>
    <br>
    <a href="input_only_page.php?date=<?=$date?>">戻る</a>
</body>
</html>