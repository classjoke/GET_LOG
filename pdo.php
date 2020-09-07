<?php
    function pdo_connect(){
        $dsn = '****';
        $user = '****';
        $password = '****';

        try {
            $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
            return $pdo;
        }catch (PDOException $error){
            print("Error:".$error->getMessage());
            die();
        }
}
?>