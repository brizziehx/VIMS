<?php
    error_reporting(0);
    session_start();

    if(!isset($_SESSION['administrator']) && !isset($_SESSION['vmanager'])) {
        header('location: ../login.php');
    }

    if(isset($_SESSION['change'])) {
        header('location: ../changepassword.php');
    }
    
    
    $errors = [];

    if(isset($_POST['submit'])) {
        $km = trim($_POST['km']);
        $oil = $_POST['oil'] ?? '';
        $vehicleID = $_POST['vehicleID'] ?? '';
        $lenth = '';

        if(empty($km)) {
            $errors['km'] = "Current vehicle KM is required";
        } else {
            if(!preg_match("/^[0-9']{1,}$/", $km)) {
                $errors['km'] = "Current kilometers must contain only digits";
            } else {
                if(!empty($vehicleID)) {
                    try {
                        require('../db/pdo.php');
                        $query = $dbCnx->query("SELECT * FROM oil WHERE oilD = '{$_REQUEST['id']}'");
                        $oilRow = $query->fetch();
                        $km_check = '';
                        
                        if($km > ($oilRow['current_km'] + $oilRow['length'])) {
                            $errors['km'] = "Please enter valid kilometers";
                        }

                    } catch (PDOException $e) {
                        $msg = json_encode($e->getMessage());
                        $errors['error'] = "<script>error($msg)</script>";
                    }
                }
            }
        }

        if(empty($oil)) {
            $errors['oil'] = "Please select the engine oil you used";
        }

        switch($oil) {
            case 'Conventional Oil':
                $length = 5000;
                $n1 = "selected";
                $n2 = "";
                $n3 = "";
            break;
            case 'Synthetic Oil':
                $length = 7500;
                $n1 = "";
                $n2 = "selected";
                $n3 = "";
            break;
            case 'Synthetic Blend Oil':
                $length = 5000;
                $n1 = "";
                $n2 = "";
                $n3 = "selected";
            break;

            default:
                $length = 0;
                $n1 = "";
                $n2 = "";
                $n3 = "";
        }

        if(empty($vehicleID)) {
            $errors['vehicleID'] = "Please select a vehicle";
        }

        if(!array_filter($errors)) {
            try {
                require('../db/pdo.php');
                $query = $dbCnx->query("SELECT * FROM oil WHERE vehicleID = {$vehicleID} ORDER BY created_at DESC");
                $rowOil = $query->fetch();

                $time = 0;
                if(!isset($rowOil['time'])) {
                    $time = $rowOil['time'] + 1;
                } elseif ($rowOil['time'] == 3) {
                    $time = 1;
                } else {
                    $time = $rowOil['time'] + 1;
                }

                $stmt = $dbCnx->prepare('UPDATE oil SET current_km = :ck, oil_type = :ot, vehicleID = :vI, time = :tm, length = :ln WHERE oilD = :oilID');
                $stmt->bindValue(":ck", $km, PDO::PARAM_STR);
                $stmt->bindValue(":ot", $oil, PDO::PARAM_STR);
                $stmt->bindValue(":vI", $vehicleID, PDO::PARAM_INT);
                $stmt->bindValue(":tm", $time, PDO::PARAM_STR);
                $stmt->bindValue(":ln", $length, PDO::PARAM_STR);
                $stmt->bindValue(":oilID", $_REQUEST['id'], PDO::PARAM_INT);

                if($stmt->execute()) {
                    $km = $oil = $vehicleID = $actions = "";
                    $errors['success'] = '<script>success()</script>';
                }
            } catch (PDOException $e) {
                $msg = json_encode($e->getMessage());
                $errors['error'] = "<script>error($msg)</script>";
            }
        }
    }

    try {
        require('../db/pdo.php');
        $stmt = $dbCnx->prepare('SELECT * FROM users WHERE userID = :userID');
        $stmt->bindValue(':userID', $_SESSION['uniqueID'], PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();

        $stmt2 = $dbCnx->prepare('SELECT * FROM oil WHERE oilD = :oilID');
        $stmt2->bindValue(':oilID', $_REQUEST['id'], PDO::PARAM_INT);
        $stmt2->execute();
        $row2 = $stmt2->fetch();
        $oil = $row2['oil_type'];

        switch($oil) {
            case 'Conventional Oil':
                $length = 5000;
                $n1 = "selected";
                $n2 = "";
                $n3 = "";
            break;
            case 'Synthetic Oil':
                $length = 7500;
                $n1 = "";
                $n2 = "selected";
                $n3 = "";
            break;
            case 'Synthetic Blend Oil':
                $length = 5000;
                $n1 = "";
                $n2 = "";
                $n3 = "selected";
            break;

            default:
                $length = 0;
                $n1 = "";
                $n2 = "";
                $n3 = "";
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
    
    <title>Service | Vehicle Information Management System</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../boxicons/css/boxicons.min.css">
    <script src="../swal/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="../swal/sweetalert2.css">
    <!-- <link href="DataTables/datatables.min.css" rel="stylesheet"> -->
    <style>
        table td {
            text-transform: capitalize
        }
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <script>
        function success() {
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Service Updated Successfully',
                showConfirmButton: false,
                timer: 2000
            })
        }
        
        function error(error) {
            Swal.fire({
                position: 'center',
                icon: 'error',
                title: error,
                showConfirmButton: true,
                // timer: 2000
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
                    <li>
                        <a href="vehicle.php"><i class="bx bx-bus"></i>Vehicles</a>
                    </li>
                    <li>
                        <a href="maintanance.php"><i class="bx bxs-car-mechanic"></i>Maintenance</a>
                    </li>
                    <li class="active">
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
                    
                    <?php endif;  ?>
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
                    <a href="index.php">Dashboard</a> > <a href="all_services.php" id="sec">All Services</a> > <span>New Service</span>
                    </div>
                </div>
               
                <div class="bottom-card">
    
                    <form action="" method="post">
                    <p style="border-bottom: 1px #eee solid; padding-bottom:5px;margin-bottom:5px">Service Registration</p>
                        <div class="input_row">
                            <div class="inputBox">
                                <span>Current KM</span>
                                <input type="text" name="km" value="<?=$row2['current_km']?>" id="km">
                                <p><?=$errors['km'] ?? ''?></p>
                            </div>
                            <div class="inputBox">
                                <span>Engine Oil</span>
                                <select name="oil" id="oil">
                                    <option selected disabled>Select engine oil type..</option>
                                    <option <?=$n1 ?? ''?> value="Conventional Oil">Conventional Oil - 5000 km</option>
                                    <option <?=$n2 ?? ''?> value="Synthetic Oil">Synthetic Oil - 7500 km</option>
                                    <option <?=$n3 ?? ''?> value="Synthetic Blend Oil">Synthetic Blend Oil - 5000 km</option>
                                </select>
                                <p><?=$errors['oil'] ?? ''?></p>
                            </div>
                            <div class="inputBox">
                                <span>Vehicle No.</span>
                                <select name="vehicleID">
                                    <option selected disabled>Select Vehicle..</option>

                                    <?php
                                        try {
                                            require('../db/pdo.php');

                                            $stmt = $dbCnx->prepare('SELECT * FROM vehicle');
                                            $stmt->execute();
                                            $rows= $stmt->fetchAll();
                                            
                                            foreach($rows as $row) : 
                                            if($row2['vehicleID'] == $row['vehicleID']) {
                                                $vid = 'selected';
                                            } else {
                                                $vid = '';
                                            }
                                    ?>
                                                <option <?=$vid ?? '' ?> value="<?=$row['vehicleID']?>"><?=$row['registration_no'].' - '.$row['make'].' '.$row['model']?></option>
                                    <?php
                                            endforeach;
                                        } catch(PDOException $e) {
                                            echo $e->getMessage();
                                        }
                                    ?>

                                </select>
                                <p><?=$errors['vehicleID'] ?? ''?></p>
                            </div>
                        </div>
                        
                        <div class="input_row">
                            <div class="inputBox">
                                <input type="submit" name="submit" value="UPDATE service"> 
                            </div>
                            <?=$errors['error'] ?? ''?>
                            <?=$errors['success'] ?? ''?>
                        </div>
                    </form>
                </div>
                <footer>Copyright &copy; <?=date('Y')?>. All Righst Reserved.</footer>
            </div>
        </main>
    </div>
    <script src="../js/jquery.js"></script>
    <script src="../js/main.js"></script>
    
</body>
</html>