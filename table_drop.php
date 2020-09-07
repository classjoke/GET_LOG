<?php
    require_once("pdo.php");
    $pdo = pdo_connect();

    $board_name=$_GET['board_name'];
    $drop_table = "DROP TABLE IF EXISTS $board_name";
    $stmt = $pdo -> query($drop_table);
    
    header("Location: potal.php");
?>