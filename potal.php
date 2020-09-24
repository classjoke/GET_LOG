
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POTAL</title>
    <link rel="icon" type="image/x-icon" href="./favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="./apple-touch-icon-180x180.png">
</head>
<body>
    <?php
        require_once("pdo.php");
        $pdo = pdo_connect();

        $sql = "SHOW TABLES";
        $result = $pdo -> query($sql);
            foreach ($result as $row){
            if(strpos($row[0],'zoo') !== false)
            {
                $date = substr($row[0], 10);
                echo "<a href='input_only_page.php?date=$date'>".$row[0]."</a>"
                ."<a href='table_drop.php?board_name=$row[0]'>削除</a>";
                echo "<hr>";
            }
            }
    ?>

</body>
</html>