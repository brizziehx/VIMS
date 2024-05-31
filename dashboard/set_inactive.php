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

    $stmt = $dbCnx->prepare("UPDATE vehicle SET status = :status, driverID = :NULL WHERE vehicleID = :vehicleID");
    $stmt->bindValue(':status', 'inactive', PDO::PARAM_STR);
    $stmt->bindValue(':NULL', NULL, PDO::PARAM_NULL);
    $stmt->bindValue(':vehicleID', $id, PDO::PARAM_INT);
    if($stmt->execute()) {
        $_SESSION['msg'] = "<script>inactive()</script>";
        header('Location:vehicle.php');
    }
    
} catch(PDOException $e) {
    $msg = json_encode($e->getMessage());
    $_SESSION['msg'] = "<script>error($msg)</script>";
}
