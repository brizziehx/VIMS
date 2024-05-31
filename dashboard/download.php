<?php
    session_start();

    if(!isset($_SESSION['administrator']) && !isset($_SESSION['vmanager'])) {
        header('location: ../login.php');
    }

    if(isset($_SESSION['change'])) {
        header('location: ../changepassword.php');
    }

    try {
        require('../db/pdo.php');
        $stmt = $dbCnx->prepare('SELECT * FROM fuel WHERE fuelID = :id');
        $stmt->bindValue(':id', $_REQUEST['id'], PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();

        $img_path = '../images/'.$row['image'];
        
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($img_path).'"');
        header('Content-Length: ' .filesize($img_path));

        readfile($img_path);
    }catch(PDOException $e) {
        echo $e->getMessage();
    }