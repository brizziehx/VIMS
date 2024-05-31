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

    $stmt = $dbCnx->prepare('SELECT * FROM users WHERE userID = :userID');
    $stmt->bindValue(':userID', $id, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch();

    $new_pass = password_hash(strtoupper($row['lastname']), PASSWORD_BCRYPT);
    $stmt = $dbCnx->prepare("UPDATE users SET password = :pw WHERE userID = :userID");
    $stmt->bindValue(':pw', $new_pass, PDO::PARAM_STR);
    $stmt->bindValue(':userID', $id, PDO::PARAM_INT);
    if($stmt->execute()) {
        $_SESSION['msg'] = "<script>success()</script>";
        header('Location:users.php');
    }
} catch(PDOException $e) {
    $msg = json_encode($e->getMessage());
    $_SESSION['msg'] = '<script>error($msg)</script>';
}
