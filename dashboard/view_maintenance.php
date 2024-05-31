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
        $stmt = $dbCnx->prepare('SELECT * FROM users WHERE userID = :userID');
        $stmt->bindValue(':userID', $_SESSION['uniqueID'], PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        
        $stmt2 = $dbCnx->prepare('SELECT maintenance.*, vehicle.vehicleID, vehicle.registration_no FROM maintenance INNER JOIN vehicle ON maintenance.vehicleID = vehicle.vehicleID WHERE maintenance.mainID = :mainID');
        $stmt2->bindValue(':mainID', $_REQUEST['id'], PDO::PARAM_INT);
        $stmt2->execute();
        $row2 = $stmt2->fetch();

        $last_m = $row2['last_maintenance'];
        $next_m = $row2['next_maintenance'];
        // $oil = $row2['oil_change'];
        // $battery = $row2['battery_check'];
        $reg_no = $row2['registration_no'];
        $actions= $row2['actions'];
        $actions = explode(',', $actions);

        // $regno = $row2['registration_no'];
        // $make = $row2['make']; 
        // $type = $row2['type']; 
        // $chassisno = $row2['chassis_no'];
        // $model = $row2['model'];
        // $engine_no = $row2['engine_no'];

        date_default_timezone_set('Africa/Nairobi');
        $date1 = date_create();
        // $date2 = date_create('2024-03-22'); 
        $date2 = date_create($next_m); 
        $diff = date_diff($date1, $date2);
        
        $days = ($diff->format('%a') == +0) ? 'day' : 'days';
        $next_m_days = "";
        if($diff->format('%R%a') > 0) {
            // $status = "<span class=active>Active</span>";
            $next_m_days = $diff->format("%R%a") + 1;
        } elseif($diff->format('%R%a') == +0) {
            $next_m_days = $diff->format("%R%a") + 1;
        } else {
            // $status = "<span class=expired>Expired</span>";
            $next_m_days = $diff->format("%R%a");
        }
        
    } catch (PDOException $e) {
        echo $e->getMessage();
    }


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>View Maintenance | Vehicle Information Management System</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../boxicons/css/boxicons.min.css">
    <script src="../swal/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="../swal/sweetalert2.css">
    <style>
        input {
            border: none !important;
            border-bottom: 1px solid #ddd !important;
            border-radius: 0px !important;
            padding: 0 !important;
            pointer-events: none;
            background: #fff;
        }
        .inputBox span {
            font-weight: bold;
        }

    </style>
</head>
<body>
    <div class="container">
        <aside>
            <header>
                <!-- <h3>VIMS</h3> -->
                <img src="../assets/bus.png" alt="">
                <span>Vehicle Information Management System</span>
            </header>
            <!-- <div class="something">Something will go here...</div> -->
            <nav class="navbar">
                <ul>
                    <?php if(isset($_SESSION['administrator']) || isset($_SESSION['vmanager'])): ?>
                    <li>
                        <a href="index.php"><i class="bx bx-grid-alt"></i>Dashboard</a>
                    </li>
                    <li>
                        <a href="users.php"><i class="bx bx-user"></i>Users</a>
                    </li>
                    <li>
                        <a href="vehicle.php"><i class="bx bx-bus"></i>Vehicles</a>
                    </li>
                    <li class="active">
                        <a href="maintanance.php"><i class="bx bxs-car-mechanic"></i>Maintenance</a>
                    </li>
                    <li>
                        <a href="all_services.php"><i class="bx bx-wrench"></i>Service</a>
                    </li>
                    <!-- <li>
                        <a href="fuel.php"><i class="bx bx-gas-pump"></i>Fuel</a>
                    </li>
                    <li>
                        <a href="insurance.php"><i class="bx bx-donate-heart"></i>Insurance</a>
                    </li> -->
                    <li>
                        <a href="routes.php"><i class="bx bx-trip"></i>Routes</a>
                    </li>
                    <li>
                        <a href="trips.php" ><i class="bx bx-trip"></i>Trips</a>
                    </li>
                    <li>
                        <a href="report.php"><i class="bx bx-receipt"></i>Report</a>
                    </li>
                    <li>
                        <a href="settings.php"><i class="bx bx-cog"></i>Settings</a>
                    </li>
                    <?php if(isset($_SESSION['administrator'])): ?>
                        <li>
                            <a href="logs.php"><i class="bx bx-cog"></i>User Logs</a>
                        </li>
                    <?php endif ?>
                    <li>
                        <a href="logout.php?logout_id=<?=$_SESSION['uniqueID']?>"><i class="bx bx-log-out"></i>Logout</a>
                    </li>
                    <?php elseif(isset($_SESSION['driver'])): ?>
                        nav coming soon
                    <?php endif ?>
                </ul>
            </nav>
        </aside>
        <main class="main-content">
        <header class="grid">
                <span class="logo">VIMS</span>
                <div class="user">
                    <div class="name">
                        <?=$row['firstname'].' '.$row['lastname']?>
                        <span id="role"><?=$row['usertype']?></span>
                    </div>
                    <img id="imgDropdown" src="../images/<?=$row['image']?>" alt="">
                    <div class="dropdown">
                        <ul>
                            <li><a href="settings.php"><i class="bx bx-user"></i>Profile</a></li>
                            <li><a href="logout.php?logout_id=<?=$row['userID']?>"><i class="bx bx-log-out"></i>Logout</a></li>
                        </ul>
                    </div>
                </div>
            </header>
            <div class="grid main">
                <!-- <div class="top_card"></div> -->
                <div class="top_card">
                    <div class="bread-cumb">
                        <a href="index.php">Dashboard</a> > <a href="maintanance.php" id="sec">Maintenance</a> > <span>View Maintenance</span>
                    </div>
                </div>
                <div class="bottom-card">
                    <form action="" method="post" enctype="multipart/form-data">
                        <p style="display: flex; justify-content:space-between; border-bottom: 1px #eee solid; padding-bottom:5px;margin-bottom:5px;">Maintenance History <!--<span style="margin-right: 10px;text-transform:capitalize">Insurance Status: <?=$status?></span></p> -->
                        <div class="input_row">
                            <div class="inputBox">
                                <span>Last Maintenance Date</span>
                                <input type="text" value="<?=date('D, jS F Y', strtotime($last_m))?>" disabled>
                            </div>
                            <div class="inputBox">
                                <span>Next Maintenance Date</span>
                                <input type="text" value="<?=date('D, jS F Y', strtotime($next_m))?>" disabled>
                            </div>
                            <div class="inputBox">
                                <span>Days Remain | Next Maintenance</span>
                                <input type="text"  value="<?=$next_m_days.' '.$days?>"  disabled>
                            </div>
                        </div>
                        <div class="input_row" style="justify-content: start;">
                            <div class="inputBox">
                                <span>Registered Number | Letters</span>
                                <input type="text" value="<?=$reg_no?>" disabled>
                                
                            </div>
                        </div>
                        <div class="input_row">
                            <div class="inputBox">
                                <?php if(array_filter($actions)): ?>
                                    <span>Services | Actions Performed</span>
                                    <ol>
                                        <?php $sn = 1; foreach($actions as $row): ?>
                                            <li style="font-size: 14px;"><?=$sn++.'. '.$row?></li>
                                        <?php endforeach; ?>
                                    </ol>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                    </form>
                </div>
                <footer>Copyright &copy; <?=date('Y')?>. All Righst Reserved.</footer>
            </div>
        </main>
    </div>
    <script src="../js/main.js"></script>
</body>
</html>