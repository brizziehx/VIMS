<?php 
    $db_host = '127.0.0.1';
    $db_user = 'root';
    $db_pass = '';
    $db_name = 'vehicle_information_management_system';
    
    $dsn = 'mysql:host='.$db_host.';dbname='.$db_name;
    $db_options = [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

    $dbCnx = new PDO($dsn,$db_user,$db_pass,$db_options);

?>