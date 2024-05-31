<?php
session_start();

$id = $_REQUEST['id'];

if(!isset($_SESSION['administrator']) && !isset($_SESSION['vmanager'])) {
    header('location: ../login.php');
}

if(isset($_SESSION['change'])) {
    header('location: ../changepassword.php');
}

try {
    require('../db/pdo.php');

    $stmt = $dbCnx->prepare("DELETE FROM fuel WHERE fuelID = :fuelID");
    $stmt->bindValue(':fuelID', $id, PDO::PARAM_INT);
    if($stmt->execute()) {
        unlink('../images/'.$urow['image']);
        $_SESSION['msg'] = "<script>deleteOK()</script>";
        header('Location:fuel.php');
    }
} catch(PDOException $e) {
    $msg = json_encode($e->getMessage());
    $_SESSION['msg'] = "<script>deleteError($msg)</script>";
}
