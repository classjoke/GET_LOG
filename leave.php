<?php
    // echo $_GET["date"];
    // echo "<br>";
    // echo $_GET["id"];
    require_once("pdo.php");
    $pdo = pdo_connect();


    if(isset($_GET['date'])){
        if(isset($_GET['id'])){
            $date = $_GET['date'];
            $board_name = "zoo_board_".$date;
            $id = $_GET['id'];
            $select = "SELECT comment FROM $board_name WHERE id=:id";
            $stmt = $pdo->prepare($select);
            $stmt ->bindValue(':id', $id);
            $stmt ->execute();
            $results = $stmt->fetch(PDO::FETCH_COLUMN);
            $comment = $results.date('H:i');
            // echo $comment;
            $edit_sql = "UPDATE $board_name SET comment=:comment WHERE id=:id";
            $stmt = $pdo->prepare($edit_sql);
            $stmt ->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt ->bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt ->execute();
            header("Location: input_only_page.php?date=$date");
        }
    }
    else{
        header("Location: input_only_page.php");
    }
    
?>