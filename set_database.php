<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>set</title>
</head>
<body>
    <?php
        function create_table()
        {
            global $pdo, $board_name;
    
            $sql="CREATE TABLE IF NOT EXISTS zoo_sendai_board"
            ."("
            ."id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,"
            ."board_name varchar(128) NOT NULL,"
            ."table_type int(1) NOT NULL,"
            ."table_number int(2) NOT NULL,"
            ."companion_type int(1) NOT NULL,"
            ."companion_name varchar(128) NOT NULL,"
            ."conect_id int(128) NOT NULL,"
            ."time TIME NOT NULL"
            .")"
            ."ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;";
            $stmt = $pdo->query($sql);
            return;
        }
        require_once("pdo.php");
        $pdo =pdo_connect();
    ?>
</body>
</html>