<?php 

    try {
        require('../db/pdo.php');

        // Notification for maintance
        $stmtx = $dbCnx->prepare('SELECT maintenance.*, vehicle.vehicleID, vehicle.registration_no FROM maintenance INNER JOIN vehicle ON maintenance.vehicleID = vehicle.vehicleID');
        $stmtx->execute();
        $mainRowss = $stmtx->fetchAll();
        date_default_timezone_set('Africa/Nairobi');
        $created_at = date('Y-m-d');

        foreach($mainRowss as $row):
            $last_m = $row['last_maintenance'];
            $next_m = $row['next_maintenance'];
            $_SESSION['vehicleID'] = $row['vehicleID'];
            
            $date1 = date_create();
            // $date2 = date_create('2024-03-22'); 
            $date2 = date_create($next_m); 
            $diff = date_diff($date1, $date2);

            $next_m_days = "";
            if($diff->format('%R%a') > 0) {
                $next_m_days = $diff->format("%R%a") + 1;
            } elseif($diff->format('%R%a') == +0) {
                $next_m_days = $diff->format("%R%a") + 1;
            } else {
                $next_m_days = $diff->format("%R%a");
            }

            // $next_m_days = 3;

            if($next_m_days == 7) {
                $type = "maintenance";
                $notification = "Maintenance of the vehicle no. {$row['registration_no']} will be within 7 days! Make sure the maintenance is done within a time";
                $select = $dbCnx->query("SELECT * FROM notidate WHERE type = '{$type}' AND date = curdate() AND reg_no = '{$row['registration_no']}'");
                if(!$select->rowCount() > 0) {
                    $dbCnx->query("INSERT INTO notifications(notification, vehicleID, created_at, type) VALUES ('$notification', {$_SESSION['vehicleID']}, '$created_at', '$type')");
                    $dbCnx->query("INSERT INTO notidate(date, type, reg_no) VALUES (curdate(), '{$type}', '{$row['registration_no']}')");
                }
            } elseif($next_m_days == 6) {
                $type = "maintenance";
                $notification = "Maintenance of the vehicle no. {$row['registration_no']} will be within 6 days! Make sure the maintenance is done within a time";
                $select = $dbCnx->query("SELECT * FROM notidate WHERE type = '{$type}' AND date = curdate() AND reg_no = '{$row['registration_no']}'");
                if(!$select->rowCount() > 0) {
                    $dbCnx->query("INSERT INTO notifications(notification, vehicleID, created_at, type) VALUES ('$notification', {$_SESSION['vehicleID']}, '$created_at', '$type')");
                    $dbCnx->query("INSERT INTO notidate(date, type, reg_no) VALUES (curdate(), '{$type}', '{$row['registration_no']}')");
                }
            } elseif($next_m_days == 5) {
                $type = "maintenance";
                $notification = "Maintenance of the vehicle no. {$row['registration_no']} will be within 5 days! Make sure the maintenance is done within a time";
                $select = $dbCnx->query("SELECT * FROM notidate WHERE type = '{$type}' AND date = curdate() AND reg_no = '{$row['registration_no']}'");
                if(!$select->rowCount() > 0) {
                    $dbCnx->query("INSERT INTO notifications(notification, vehicleID, created_at, type) VALUES ('$notification', {$_SESSION['vehicleID']}, '$created_at', '$type')");
                    $dbCnx->query("INSERT INTO notidate(date, type, reg_no) VALUES (curdate(), '{$type}', '{$row['registration_no']}')");
                }
            } elseif($next_m_days == 4) {
                $type = "maintenance";
                $notification = "Maintenance of the vehicle no. {$row['registration_no']} will be within 4 days! Make sure the maintenance is done within a time";
                $select = $dbCnx->query("SELECT * FROM notidate WHERE type = '{$type}' AND date = curdate() AND reg_no = '{$row['registration_no']}'");
                if(!$select->rowCount() > 0) {
                    $dbCnx->query("INSERT INTO notifications(notification, vehicleID, created_at, type) VALUES ('$notification', {$_SESSION['vehicleID']}, '$created_at', '$type')");
                    $dbCnx->query("INSERT INTO notidate(date, type, reg_no) VALUES (curdate(), '{$type}', '{$row['registration_no']}')");
                }
            } elseif($next_m_days == 3) {
                $type = "maintenance";
                $notification = "Maintenance of the vehicle no. {$row['registration_no']} will be within 3 days! Make sure the maintenance is done within a time";
                $select = $dbCnx->query("SELECT * FROM notidate WHERE type = '{$type}' AND date = curdate() AND reg_no = '{$row['registration_no']}'");
                if(!$select->rowCount() > 0) {
                    $dbCnx->query("INSERT INTO notifications(notification, vehicleID, created_at, type) VALUES ('$notification', {$_SESSION['vehicleID']}, '$created_at', '$type')");
                    $dbCnx->query("INSERT INTO notidate(date, type, reg_no) VALUES (curdate(), '{$type}', '{$row['registration_no']}')");
                }
            } elseif($next_m_days == 2) {
                $type = "maintenance";
                $notification = "Maintenance of the vehicle no. {$row['registration_no']} will be within 2 days! Make sure the maintenance is done within a time";
                $select = $dbCnx->query("SELECT * FROM notidate WHERE type = '{$type}' AND date = curdate() AND reg_no = '{$row['registration_no']}'");
                if(!$select->rowCount() > 0) {
                    $dbCnx->query("INSERT INTO notifications(notification, vehicleID, created_at, type) VALUES ('$notification', {$_SESSION['vehicleID']}, '$created_at', '$type')");
                    $dbCnx->query("INSERT INTO notidate(date, type, reg_no) VALUES (curdate(), '{$type}', '{$row['registration_no']}')");
                }
            } elseif($next_m_days == 1) {
                $type = "maintenance";
                $notification = "Maintenance of the vehicle no. {$row['registration_no']} will be within 1 day! Make sure the maintenance is done within a time";
                $select = $dbCnx->query("SELECT * FROM notidate WHERE type = '{$type}' AND date = curdate() AND reg_no = '{$row['registration_no']}'");
                if(!$select->rowCount() > 0) {
                    $dbCnx->query("INSERT INTO notifications(notification, vehicleID, created_at, type) VALUES ('$notification', {$_SESSION['vehicleID']}, '$created_at', '$type')");
                    $dbCnx->query("INSERT INTO notidate(date, type, reg_no) VALUES (curdate(), '{$type}', '{$row['registration_no']}')");
                }
            } elseif($next_m_days == 0) {
                $type = "maintenance";
                $notification = "Maintenance of the vehicle no. {$row['registration_no']} is today! Make sure everything goes as planned";
                $select = $dbCnx->query("SELECT * FROM notidate WHERE type = '{$type}' AND date = curdate() AND reg_no = '{$row['registration_no']}'");
                if(!$select->rowCount() > 0) {
                    $dbCnx->query("INSERT INTO notifications(notification, vehicleID, created_at, type) VALUES ('$notification', {$_SESSION['vehicleID']}, '$created_at', '$type')");
                    $dbCnx->query("INSERT INTO notidate(date, type, reg_no) VALUES (curdate(), '{$type}', '{$row['registration_no']}')");
                }
            }

        endforeach;
            
        } catch(PDOException $e) {
            echo $e->getMessage();
        }


        try {
            require('../db/pdo.php');
            require('checkService.php');
            $stmt = $dbCnx->prepare('SELECT oil.*, vehicle.registration_no, vehicle.vehicleID FROM oil INNER JOIN vehicle ON oil.vehicleID = vehicle.vehicleID ORDER BY oilD DESC');
            $stmt->execute();
            $rows = $stmt->fetchAll();
            date_default_timezone_set('Africa/Nairobi');
            $created_at = date('Y-m-d');


            foreach($rows as $row):
                $query = $dbCnx->query("SELECT * FROM trips WHERE vehicleID = {$row['vehicleID']} ORDER BY created_at DESC");
                $tripRow = $query->fetch();
                $curr_km = '';
                $_SESSION['vehicleID'] = $row['vehicleID'];

                if(isset($tripRow['km_after'])) {
                    $curr_km = $tripRow['km_after'];
                } elseif(isset($tripRow['km_before'])) { 
                    $curr_km = $tripRow['km_before'];
                } else {
                    $curr_km = 0;
                }

                $km_until = isServiceDue($curr_km, $row['current_km'] + $row['length']);

                if($km_until <= 600 && $km_until >= 401) {
                    $type = "service";
                    $notification = "Service of the vehicle no. {$row['registration_no']} is near, km remained before next service is $km_until KM";
                    $select = $dbCnx->query("SELECT * FROM notidate WHERE type = '{$type}' AND date = curdate() AND reg_no = '{$row['registration_no']}'");
                    if(!$select->rowCount() > 0) {
                        $dbCnx->query("INSERT INTO notifications(notification, vehicleID, created_at, type) VALUES ('$notification', {$_SESSION['vehicleID']}, '$created_at', '$type')");
                        $dbCnx->query("INSERT INTO notidate(date, type, reg_no) VALUES (curdate(), '{$type}', '{$row['registration_no']}')");
                    }
                } elseif($km_until <= 400 && $km_until >= 301) {
                    $type = "service";
                    $notification = "Service of the vehicle no. {$row['registration_no']} is near, km remained before next service is $km_until KM";
                    $select = $dbCnx->query("SELECT * FROM notidate WHERE type = '{$type}' AND date = curdate() AND reg_no = '{$row['registration_no']}'");
                    if(!$select->rowCount() > 0) {
                        $dbCnx->query("INSERT INTO notifications(notification, vehicleID, created_at, type) VALUES ('$notification', {$_SESSION['vehicleID']}, '$created_at', '$type')");
                        $dbCnx->query("INSERT INTO notidate(date, type, reg_no) VALUES (curdate(), '{$type}', '{$row['registration_no']}')");
                    }
                } elseif($km_until <= 300 && $km_until >= 101) {
                    $type = "service";
                    $notification = "Service of the vehicle no. {$row['registration_no']} is near, km remained before next service is $km_until KM";
                    $select = $dbCnx->query("SELECT * FROM notidate WHERE type = '{$type}' AND date = curdate() AND reg_no = '{$row['registration_no']}'");
                    if(!$select->rowCount() > 0) {
                        $dbCnx->query("INSERT INTO notifications(notification, vehicleID, created_at, type) VALUES ('$notification', {$_SESSION['vehicleID']}, '$created_at', '$type')");
                        $dbCnx->query("INSERT INTO notidate(date, type, reg_no) VALUES (curdate(), '{$type}', '{$row['registration_no']}')");
                    }
                } elseif($km_until <= 100 && $km_until >= 0) {
                    $type = "service";
                    $notification = "Service of the vehicle no. {$row['registration_no']} is almost there, km remained before next service is $km_until KM";
                    $select = $dbCnx->query("SELECT * FROM notidate WHERE type = '{$type}' AND date = curdate() AND reg_no = '{$row['registration_no']}'");
                    if(!$select->rowCount() > 0) {
                        $dbCnx->query("INSERT INTO notifications(notification, vehicleID, created_at, type) VALUES ('$notification', {$_SESSION['vehicleID']}, '$created_at', '$type')");
                        $dbCnx->query("INSERT INTO notidate(date, type, reg_no) VALUES (curdate(), '{$type}', '{$row['registration_no']}')");
                    }
                }
            endforeach;
            } catch(PDOException $e) {
                echo $e->getMessage();
            }

?>