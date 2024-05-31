<?php
    session_start(); 
    if(!isset($_SESSION['uniqueID'])) {
        header('location: ../login.php');
    }

    if(isset($_SESSION['change'])) {
        header('location: ../changepassword.php');
    }

    require('nots.php');

    try {
        require('../db/pdo.php');
        $stmt = $dbCnx->prepare('SELECT * FROM users WHERE userID = :userID');
        $stmt->bindValue(':userID', $_SESSION['uniqueID'], PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();

        // all users
        $stmt2 = $dbCnx->prepare('SELECT * FROM users');
        $stmt2->execute();
        $rows = $stmt2->rowCount();

        $stmtc = $dbCnx->prepare('SELECT * FROM users WHERE usertype = \'driver\'');
        $stmtc->execute();
        $rowsD = $stmtc->rowCount();

        // all vehicles
        $stmt4 = $dbCnx->prepare('SELECT * FROM vehicle');
        $stmt4->execute();
        $vehicleRows = $stmt4->rowCount();

        // trips driver
        $stmt5 = $dbCnx->prepare('SELECT * FROM trips WHERE driverID = :driverID');
        $stmt5->bindValue(':driverID', $_SESSION['uniqueID']);
        $stmt5->execute();
        $trow = $stmt5->rowCount();

        // trips admin & manager
        $stmt6 = $dbCnx->prepare('SELECT * FROM trips');
        $stmt6->execute();
        $trowA = $stmt6->rowCount();

        //maintenance 2
        $stmt7 = $dbCnx->prepare('SELECT maintenance.*, vehicle.registration_no FROM maintenance INNER JOIN vehicle ON vehicle.vehicleID = maintenance.vehicleID ORDER BY mainID DESC LIMIT 2');
        $stmt7->execute();
        $mrows = $stmt7->fetchAll();

        //maintenance all
        $stmt8 = $dbCnx->prepare('SELECT * FROM maintenance');
        $stmt8->execute();

        // services
        $stmt9 = $dbCnx->prepare('SELECT * FROM oil');
        $stmt9->execute();

    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    // switch()
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Vehicle Information Management System</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../boxicons/css/boxicons.min.css">
    <script src="../swal/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="../swal/sweetalert2.css">
    <script src="../js//jquery.js"></script>
    <style>

        table {
            border-top: 1px solid #444;
            width: 80%;
            margin-left: 20px;
            border-collapse: collapse;
            /* margin-top: 5px; */
            padding: 20px !important;
        }

        table tr {
            height: 30px;
        }

        table th {
            font-weight: 500;
            text-align: center;
            border-bottom: 1px solid #444;
        }

        table td {
            border-bottom: 1px solid #444;
            color: #555;
            text-align:center
        }

    </style>
</head>
<body>
    <script>
        function deleteOK() {
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Notification deleted successfully',
                showConfirmButton: false,
                timer: 2000
            })
        }
        
        function deleteError(error) {
            Swal.fire({
                position: 'center',
                icon: 'error',
                title: error,
                showConfirmButton: false,
                timer: 2000
            })

        }
    </script>
    <?php
        if(isset($_SESSION['msg'])) {
            echo $_SESSION['msg'];
        }
       unset($_SESSION['msg']); 
    ?>
    <div class="container">
        <aside>
            <header>
                <!-- <h1>VIMS</h1> -->
                <img src="../assets/bus.png" alt="">
                <span>Vehicle Information Management System</span>
            </header>
            <!-- <div class="something">Something will go here...</div> -->
            <nav class="navbar">
                <ul>
                    <?php if(isset($_SESSION['administrator']) || isset($_SESSION['vmanager'])): ?>
                    <li class="active">
                        <a href="index.php"><i class="bx bx-grid-alt"></i>Dashboard</a>
                    </li>
                    <li>
                        <a href="users.php"><i class="bx bx-user"></i>Users</a>
                    </li>
                    <li>
                        <a href="vehicle.php"><i class="bx bx-bus"></i>Vehicles</a>
                    </li>
                    <li>
                        <a href="maintanance.php"><i class="bx bxs-car-mechanic"></i>Maintenacne</a>
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
                            <li class="active">
                                <a href="index.php"><i class="bx bx-grid-alt"></i>Dashboard</a>
                            </li>
                            <li>
                                <a href="my_routes.php"><i class="bx bx-trip"></i>Routes and Trips</a>
                            </li>
                            <li>
                                <a href="settings.php"><i class="bx bx-cog"></i>Settings</a>
                            </li>
                            <li>
                                <a href="logout.php?logout_id=<?=$_SESSION['uniqueID']?>"><i class="bx bx-log-out"></i>Logout</a>
                            </li>
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
                <div class="top_card">
                    <div class="bread-cumb">
                        <p href="index.php">Dashboard</p>
                    </div>
                </div>
                <?php if(isset($_SESSION['administrator']) || isset($_SESSION['vmanager'])): ?>
                    <a href="users.php" class="card">
                        <p><span>Users</span> <i style="background-color: #FFF4D7;" class="bx bx-user"></i></p>
                        <h1><?=$rows?></h1>
                    </a>
                    <!-- <a href="insurance.php" class="card"> -->
                        <!-- <p><span>Insurance</span> <i style="background-color: #ddf2dc;" class="bx bx-donate-heart"></i></p>  -->
                        <!-- <div class="flex"> -->
                            <!-- <h1 class="ins"><?=$insuRows?></h1> -->
                            <!-- <div class="row">
                                <p>Active 4</p>
                                <p>Not Active 6</p>
                            </div> -->
                        <!-- </div> -->
                    <!-- </a> -->
                    <a href="trips.php" class="card">
                        <p><span>Trips</span> <i style="background-color: #FFF4D7;" class="bx bx-trip"></i></p>
                        <h1><?=$trowA?></h1>
                    </a>
                    <a href="vehicle.php" class="card">
                        <p><span>Vehicles</span> <i style="background-color: #D3F4EA;" class="bx bx-bus"></i></p> 
                        <h1><?=$vehicleRows?></h1>
                    </a>
                    <div class="card-row">
                    <?php 
                            try {
                                require('../db/pdo.php');
                                $stmt = $dbCnx->prepare("SELECT * FROM notifications WHERE flag <> 1 ORDER BY notID DESC");
                                $stmt->execute();
                                $rows = $stmt->fetchAll(); 
                                 ?>
                        <p class="notification-heading"><span>Notifications</span> <i class="bx bx-bell"></i><span class="counter"><?=$stmt->rowCount()?></span></p> 
                        <div class="card-row_content">
                        <?php if($stmt->rowCount() > 0):
                                foreach($rows as $row):
                                    switch($row['type']):
                                        case 'insurance':
                                            $icon = "<i class='bx bx-donate-heart not-icon'></i>";
                                        break;
                                        case 'maintenance':
                                            $icon = "<i class='bx bxs-car-mechanic not-icon'></i>";
                                        break;
                                        case 'service':
                                            $icon = "<i class='bx bxs-wrench not-icon'></i>";
                                        break;
                                        default:
                                            $icon = "";
                                    endswitch;
                        ?>
                            <div class="new-notification">
                                <h5 class="not-header" style="text-transform: capitalize;"><?=$icon?><?=$row['type']?> Notification</h5>
                                <div class="selected-notification">
                                    <span class='not-item service-alert'><?=$row['notification']?> <span class="time"><?=' - '.date('jS F Y', strtotime($row['created_at'])).' - '?></span></span>
                                    <div class="del-not-btn">
                                        <div class="btn-container">
                                            <a href="delete-not.php?id=<?=$row['notID']?>"><i class="bx bx-trash"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php
                                endforeach;
                            else:
                                echo "<p style='font-weight: 500; font-size: 15px;color: #777; justify-content: center; align-items: center; '>There's no notification";
                            endif;
                            } catch(PDOException $e) {
                                echo $e->getMessage();
                            }
                        ?>
                        </div>
                    </div>
                    <a href="maintanance.php" class="card">
                        <p><span>Maintenance</span> <i class="bx bxs-car-mechanic"></i></p>
                        <h1><?=$stmt8->rowCount()?></h1>
                    </a>
                    <a href="all_services.php" class="card">
                        <p><span>Services</span> <i class="bx bxs-wrench"></i></p>
                        <h1><?=$stmt9->rowCount()?></h1>
                    </a>
                    <a href="drivers.php" class="card">
                        <p><span>Drivers</span> <i style="background-color: #FFF4D7;" class="bx bx-user"></i></p>
                        <h1><?=$rowsD?></h1>
                    </a>
                <?php elseif(isset($_SESSION['driver'])): ?>
                    <a href="my_routes.php" class="card">
                        <p><span>My Trips</span> <i class="bx bx-trip"></i></p>
                        <h1><?=$trow?></h1>
                    </a>
                <?php endif; ?>
                <!-- <footer>Vehicle Information Management System &copy; <?=date('Y')?>. All Righst Reserved.</footer> -->
                <footer style="position: fixed; bottom:0; display:flex; justify-content: center;margin-right: 10px; width: 78%; margin-bottom:10px;">Copyright &copy; <?=date('Y')?>. All Righst Reserved.</footer>
            </div>
        </main>
    </div>
    <script src="../js/main.js"></script>
</body>
</html>