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
        
        $stmt2 = $dbCnx->prepare('SELECT * FROM vehicle WHERE vehicleID = :vehicleID');
        $stmt2->bindValue(':vehicleID', $_REQUEST['id'], PDO::PARAM_INT);
        $stmt2->execute();
        $row2 = $stmt2->fetch();

        $regno = $row2['registration_no'];
        $make = $row2['make']; 
        $type = $row2['type']; 
        $year =  $row2['year'];
        $chassisno = $row2['chassis_no'];
        $model = $row2['model'];
        $cc = $row2['cc'];
        $fuel = $row2['fuel'];
        $engine_no = $row2['engine_no'];
        $transmission = $row2['transmission'];
        $current_km = $row2['current_km'];
        $driverID = $row2['driverID'];
        $routeID = $row2['routeID'];

        if($driverID == '') {
            $userRow = "This vehicle has no driver";
        } else {
            $query = $dbCnx->query("SELECT * FROM users WHERE userID = '$driverID'");
            $row3 = $query->fetch();
            $userRow = $row3['firstname'].' '.$row3['lastname'];
        }
        
        $query2 = $dbCnx->query("SELECT * FROM routes WHERE routeID = '$routeID'");
        $routeRow = $query2->fetch();


        if($query2->rowCount() > 0) {
            $route = $routeRow['start'].' - '.$routeRow['to_'].' - '.$routeRow['end'];
        } else {
            $route = "This vehicle has not been assigned yet";
        }
        
        switch($row2['status']){
            case 'active':
                $status = "<span class=active>Active</span>";
            break;
            case 'inactive':
                $status = "<span class=expired>Inactive</span>";
            break;
            default:
            $status = "";
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
    
    <title><?=$regno?> | Vehicle Information Management System</title>
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
    <script>
        function success() {
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Vehicle Details Updated Successfully',
                showConfirmButton: false,
                timer: 2000
            })
        }
        
        function error(error) {
            Swal.fire({
                position: 'center',
                icon: 'error',
                title: error,
                showConfirmButton: true
            })
        }
    </script>
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
                    <li class="active">
                        <a href="vehicle.php"><i class="bx bx-bus"></i>Vehicles</a>
                    </li>
                    <li>
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
                        <a href="index.php">Dashboard</a> > <a href="vehicle.php" id="sec">vehicles</a> > <span>View Vehicle</span>
                    </div>
                </div>
                <div class="bottom-card">
                    <form action="" method="post" enctype="multipart/form-data">
                        <p style="display: flex; justify-content:space-between; border-bottom: 1px #eee solid; padding-bottom:5px;margin-bottom:5px;">Vehicle Details <span style="margin-right: 10px;text-transform:capitalize">Status: <?=$status?></span></p>
                        <div class="input_row">
                            <div class="inputBox">
                                <span>Registered Number | Letters</span>
                                <input type="text" name="regno" value="<?=$regno?>" placeholder="eg. T101 DSM" disabled>
                            </div>
                            <div class="inputBox">
                                <span>Make</span>
                                <input type="text" name="make" value="<?=$make?>" placeholder="eg. Yutong" disabled>
                            </div>
                            <div class="inputBox">
                                <span>Type of Body</span>
                                <input type="text" name="type" value="<?=$type?>" placeholder="Eg. Bus, saloon" disabled>
                            </div>
                        </div>
                        <div class="input_row">
                            <div class="inputBox">
                                <span>Year of Manufacture</span>
                                <input type="text" name="year" value="<?=$year?>" placeholder="Eg. 2022" disabled>
                                
                            </div>
                            <div class="inputBox">
                                <span>Chassis Number</span>
                                <input type="text" name="chassisno" value="<?=$chassisno?>" placeholder="Eg. 2H2XA59BWDY987665" disabled>
                            </div>
                            <div class="inputBox imae-upload">
                                <span>Engine Capacity Cc</span>
                                <input type="text" name="cc" value="<?=$cc?>" placeholder="Eg. 1990" disabled>
                            </div>
                        </div>
                        <div class="input_row">
                            <div class="inputBox">
                                <span>Engine Number</span>
                                <input type="text" name="engine_no" value="<?=$engine_no?>" placeholder="Eg. 52WBVC10338" disabled>
                                
                            </div>
                            <div class="inputBox">
                                <span>Fuel Type</span>
                                <input value="<?=$fuel?>" disabled>
                            </div>
                            <div class="inputBox">
                                <span>Transmission</span>
                                <input value="<?=$transmission?>" disabled>
                            </div>
                        </div>
                        <div class="input_row">
                            <div class="inputBox">
                                <span>Model</span>
                                <input type="text" name="model" value="<?=$model?>" disabled>
                            </div>
                            <div class="inputBox">
                                <span>Route</span>
                                <input value="<?=$route?>" disabled>
                            </div>
                            <div class="inputBox">
                                <span>Driver</span>
                                <input value="<?=$userRow?>" disabled>
                            </div>
                        </div>
                        <div class="input_row">
                            <div class="inputBox">
                                <span>Kilometers | Odometer Reading</span>
                                <input type="text" name="model" value="<?=$current_km.' KM'?>" disabled>
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