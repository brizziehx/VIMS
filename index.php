<?php 
    session_start();

    if(!isset($_SESSION['uniqueID'])) {
        header('location: login.php');
    }

    if(isset($_SESSION['uniqueID'])) {
        header('location: ./dashboard/');
    }
?>