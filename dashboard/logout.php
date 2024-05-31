<?php 
    session_start();

    if(isset($_SESSION['uniqueID'])) {
        $logout_id = htmlspecialchars($_GET['logout_id']);
        if(isset($logout_id)) {
            try {
                date_default_timezone_set('Africa/Nairobi');
                $logout_time = date("Y-m-d H:i:s");

                require('../db/pdo.php');
    
                $stmt = $dbCnx->prepare("UPDATE users SET logout_time = :logout_time WHERE userID = :userID");
                $stmt->bindValue(':logout_time', $logout_time, PDO::PARAM_STR);
                $stmt->bindValue(':userID', $_SESSION['uniqueID']);
                
                if($stmt->execute()) {
                    session_unset();
                    session_destroy();
                    header("Location: ../login.php");
                }
            } catch(PDOException $e) {
                echo $e->getMessage();
            }
        }  else {
            header("Location: ./dashboard/");
        }
    } else {
        header("Location: ../login.php");
    }
?>