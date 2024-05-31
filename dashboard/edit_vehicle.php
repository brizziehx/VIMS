<?php
    session_start();

    if(!isset($_SESSION['administrator']) && !isset($_SESSION['vmanager'])) {
        header('location: ../login.php');
    }

    if(isset($_SESSION['change'])) {
        header('location: ../changepassword.php');
    }

    $errors = ['regno'=>'','make'=>'','type'=>'','year'=>'','chassisno'=>'','cc'=>'','engine_no'=>'','fuel'=>'', 'transmission'=>'','model'=>'','driver'=>'','route'=>''];

    if(isset($_POST['submit'])) {
        $regno = $_POST['regno'];
        $make = $_POST['make'];
        $type = $_POST['type'];
        $year = $_POST['year'];
        $model = $_POST['model'];
        $chassisno = $_POST['chassisno'];
        $cc = $_POST['cc'];
        $engine_no = $_POST['engine_no'];
        $fuel = $_POST['fuel'] ?? '';
        $transmission = $_POST['transmission'] ?? '';
        $driverID = $_POST['driver'] ?? '';
        $routeID = $_POST['route'] ?? '';
        $current_km = $_POST['current_km'];
        date_default_timezone_set('Africa/Nairobi');
        $updated_at = date('Y-m-d H:i:s');


        if(empty($regno)) {
            $errors['regno'] = 'Registered number is required';
        } else {
            if(!preg_match("/^[T][0-9']{3} [A-Z]{3}$/", $regno)) {
                $errors['regno'] = "Registered number is not valid";
            } else {
                try {
                    require('../db/pdo.php');
                    $stmtX = $dbCnx->prepare('SELECT * FROM vehicle WHERE registration_no <> :rg AND vehicleID = :vehicleID');
                    $stmtX->bindValue(':rg', $regno, PDO::PARAM_STR);
                    $stmtX->bindValue(':vehicleID', $_REQUEST['id'], PDO::PARAM_INT);
                    $stmtX->execute();

                    // print_r($stmt->rowCount());exit;

                    if($stmtX->rowCount() > 0) {
                        $errors['regno'] = "Vehicle with this registered number already exists";
                    }
                } catch(PDOException $e) {
                    $msg = json_encode($e->getMessage());
                    $errors['error'] = '<script>error($msg)</script>';
                }
            }
        }

        if(empty($make)) {
            $errors['make'] = 'Make is required';
        } else {
            if(!preg_match("/^[A-Za-z ]{3,}$/", $make)) {
                $errors['make'] = "Make is not valid";
            }
        }

        if(empty($model)) {
            $errors['model'] = 'Model is required';
        } else {
            if(!preg_match("/^[\w\d -]{3,}$/", $model)) {
                $errors['model'] = "Model is not valid";
            }
        }

        if(empty($current_km)) {
            $errors['current_km'] = 'Current kilometers is required';
        } else {
            if(!preg_match("/^[0-9]{1,}$/", $current_km)) {
                $errors['current_km'] = "Current kilometers is not valid";
            }
        }

        if(empty($type)) {
            $errors['type'] = 'Vehicle type is required';
        } else {
            if(!preg_match("/^[A-Za-z ]{3,}$/", $type)) {
                $errors['type'] = "Type is not valid";
            }
        }

        if(empty($chassisno)) {
            $errors['chassisno'] = "Chassis number is required";
        } else {
            if(!preg_match("/^[A-Z0-9]{17}$/", $chassisno)) {
                $errors['chassisno'] = "Chassis number must have 17 characters";
            } else {
                try {
                    require('../db/pdo.php');
                    $stmt = $dbCnx->prepare('SELECT * FROM vehicle WHERE chassis_no <> :cn AND vehicleID = :vehicleID');
                    $stmt->bindValue(':cn', $chassisno);
                    $stmt->bindValue(':vehicleID', $_REQUEST['id']);
                    $stmt->execute();

                    if($stmt->rowCount() > 0) {
                        $errors['chassisno'] = "Vehicle with this chassis number already exists";
                    }
                } catch(PDOException $e) {
                    $msg = json_encode($e->getMessage());
                    $errors['error'] = '<script>error($msg)</script>';
                }
            }
        }

        if(empty($cc)) {
            $errors['cc'] = "Engine capacity is required";
        } else {
            if(!preg_match("/^[0-9]{3,4}$/", $cc)) {
                $errors['cc'] = "Engine capacity is not valid";
            }
        }

        if(empty($engine_no)) {
            $errors['engine_no'] = "Engine number is required";
        } else {
            if(!preg_match("/^[A-Z0-9]{11,}$/", $engine_no)) {
                $errors['engine_no'] = "Engine no. must have atleast 11 characters or more";
            } else {
                try {
                    require('../db/pdo.php');
                    $stmt = $dbCnx->prepare('SELECT * FROM vehicle WHERE engine_no <> :en AND vehicleID = :vehicleID');
                    $stmt->bindValue(':en', $engine_no);
                    $stmt->bindValue(':vehicleID', $_REQUEST['id']);
                    $stmt->execute();

                    if($stmt->rowCount() > 0) {
                        $errors['engine_no'] = "Vehicle with this engine number already exists";
                    }
                } catch(PDOException $e) {
                    $msg = json_encode($e->getMessage());
                    $errors['error'] = '<script>error($msg)</script>';
                }
            }
        }

        if(empty($year)) {
            $errors['year'] = 'Manufacture year is required';
        } else {
            if(!preg_match("/^[0-9]{4}$/", $year)) {
                $errors['year'] = "Manufacture year is not valid";
            }
        }

    
        if(empty($fuel)) {
            $errors['fuel'] = "Fuel type is required";
        }

        if(empty($transmission)) {
            $errors['transmission'] = "Transmission is required";
        }

        switch($fuel) {
            case 'gasoline':
                $gas = "selected";
                $diz = "";
            break;
            case 'diesel':
                $gas = "";
                $diz = "selected";
            break;
            default:
                $gas = "";
                $diz = "";
        }

        switch($transmission) {
            case 'automatic':
                $auto = "selected";
                $manual = "";
            break;
            case 'manual':
                $manual = "selected";
                $auto = "";
            break;
            default:
                $manual = "";
                $auto = "";
        }

        if(empty($driverID)) {
            // $errors['driver'] = "Please select a driver";
            try {
                require('../db/pdo.php');
                $stmt = $dbCnx->prepare('SELECT * FROM vehicle  WHERE vehicleID = :vehicleID');
                $stmt->bindValue(':vehicleID', $_GET['id'], PDO::PARAM_INT);
                $stmt->execute();
                $rowV = $stmt->fetch();
                // var_dump($rowV['driverID']);exit;
                
                $stmt2 = $dbCnx->prepare("SELECT * FROM users WHERE userID = :userID");
                $stmt2->bindValue(':userID', $rowV['driverID'], PDO::PARAM_INT);
                $stmt2->execute();
                if($stmt2->rowCount() > 0):
                    $row = $stmt2->fetch();
                    $driverID = $row['userID'];
                else:
                    $driverID = NULL;
                endif;
            } catch(PDOException $e) {
                $msg = json_encode($e->getMessage());
                $errors['error'] = "<script>error($msg)</script>";
                echo $e->getMessage();
            }
        }

        if(empty($routeID)) {
            $errors['route'] = "Please select a route";
        }
 
        if(!array_filter($errors)) {
            try {
                require('../db/pdo.php');

                $stmt = $dbCnx->prepare("UPDATE vehicle SET model = :model,type = :type, year = :year, make = :make, chassis_no = :chassis_no, registration_no = :registration_no, engine_no = :engine_no, cc = :cc, transmission = :transmission, fuel = :fuel, updated_at = :updated_at, driverID = :driverID, routeID = :routeID, current_km = :current_km WHERE vehicleID = :vehicleID");
                $stmt->bindValue(':model', $model, PDO::PARAM_STR);
                $stmt->bindValue(':type', $type, PDO::PARAM_STR);
                $stmt->bindValue(':year', $year, PDO::PARAM_STR);
                $stmt->bindValue(':make', $make, PDO::PARAM_STR);
                $stmt->bindValue(':chassis_no', $chassisno, PDO::PARAM_STR);
                $stmt->bindValue(':registration_no', $regno, PDO::PARAM_STR);
                $stmt->bindValue(':engine_no', $engine_no, PDO::PARAM_STR);
                $stmt->bindValue(':cc', $cc, PDO::PARAM_STR);
                $stmt->bindValue(':transmission', $transmission, PDO::PARAM_STR);
                $stmt->bindValue(':fuel', $fuel, PDO::PARAM_STR);
                $stmt->bindValue(':updated_at', $updated_at, PDO::PARAM_STR);
                $stmt->bindValue(':driverID', $driverID, PDO::PARAM_STR);
                $stmt->bindValue(':routeID', $routeID, PDO::PARAM_STR);
                $stmt->bindValue(':current_km', $current_km, PDO::PARAM_STR);
                $stmt->bindValue(':vehicleID', $_REQUEST['id'], PDO::PARAM_INT);

                if($stmt->execute()) {
                    $errors['success'] = '<script>success()</script>';
                }
                
            } catch(PDOException $e) {
                $msg = json_encode($e->getMessage());
                $errors['error'] = "<script>error($msg)</script>";
                // echo $e->getMessage();
            }
        }
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
        $current_km = $row2['current_km'];
        $engine_no = $row2['engine_no'];
        $transmission = $row2['transmission'];
        $driverID = $row2['driverID'];
        $routeID = $row2['routeID'];

        if($driverID != ''):
            $query = $dbCnx->query("SELECT * FROM users WHERE userID = $driverID");
            if($query->rowCount() > 0) {
                $rowUser = $query->fetch();
                $msg = $rowUser['firstname'].' '.$rowUser['lastname'];
            } else {
                $msg = '';
            }
        endif;
        switch($transmission) {
            case 'automatic':
                $auto = "selected";
                $manual = "";
            break;
            case 'manual':
                $manual = "selected";
                $auto = "";
            break;
            default:
                $manual = "";
                $auto = "";
        }

        switch($fuel) {
            case 'gasoline':
                $gas = "selected";
                $diz = "";
            break;
            case 'diesel':
                $gas = "";
                $diz = "selected";
            break;
            default:
                $gas = "";
                $diz = "";
        }

    } catch (PDOException $e) {
        echo $e->getMessage(). $e->getLine();
    }


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Edit vehicle | Vehicle Information Management System</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../boxicons/css/boxicons.min.css">
    <script src="../swal/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="../swal/sweetalert2.css">
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
                        <a href="index.php">Dashboard</a> > <a href="vehicle.php" id="sec">vehicles</a> > <span>Edit Vehicle</span>
                    </div>
                </div>
                <div class="bottom-card">
                    <form action="" method="post" enctype="multipart/form-data">
                        <p style="border-bottom: 1px #eee solid; padding-bottom:5px;margin-bottom:5px">Edit Vehicle</p>
                        <div class="input_row">
                            <div class="inputBox">
                                <span>Registered Number | Letters</span>
                                <input type="text" name="regno" value="<?=$regno?>" placeholder="eg. T101 DSM">
                                <p><?=$errors['regno']?></p>
                            </div>
                            <div class="inputBox">
                                <span>Make</span>
                                <input type="text" name="make" value="<?=$make?>" placeholder="eg. Yutong">
                                <p><?=$errors['make']?></p>
                            </div>
                            <div class="inputBox">
                                <span>Type of Body</span>
                                <input type="text" name="type" value="<?=$type?>" placeholder="Eg. Bus, saloon">
                                <p><?=$errors['type']?></p>
                            </div>
                        </div>
                        <div class="input_row">
                            <div class="inputBox">
                                <span>Year of Manufacture</span>
                                <input type="text" name="year" value="<?=$year?>" placeholder="Eg. 2022">
                                <p><?=$errors['year']?></p>
                            </div>
                            <div class="inputBox">
                                <span>Chassis Number</span>
                                <input type="text" name="chassisno" value="<?=$chassisno?>" placeholder="Eg. 2H2XA59BWDY987665">
                                <p><?=$errors['chassisno']?></p>
                            </div>
                            <div class="inputBox imae-upload">
                                <span>Engine Capacity Cc</span>
                                <input type="text" name="cc" value="<?=$cc?>" placeholder="Eg. 1990">
                                <p><?=$errors['cc']?></p>
                            </div>
                        </div>
                        <div class="input_row">
                            <div class="inputBox">
                                <span>Engine Number</span>
                                <input type="text" name="engine_no" value="<?=$engine_no?>" placeholder="Eg. 52WBVC10338">
                                <p><?=$errors['engine_no']?></p>
                            </div>
                            <div class="inputBox">
                                <span>Fuel Type</span>
                                <select name="fuel" id="fuel">
                                    <option selected disabled>Select Fuel type..</option>
                                    <option <?=$diz ?? ''?> value="diesel">Diesel</option>
                                    <option <?=$gas ?? ''?> value="gasoline">Gasoline</option>
                                </select>
                                <p><?=$errors['fuel']?></p>
                            </div>
                            <div class="inputBox">
                                <span>Transmission</span>
                                <select name="transmission">
                                    <option selected disabled>Select transmission..</option>
                                    <option <?=$auto ?? '' ?> value="automatic">Automatic</option>
                                    <option <?=$manual ?? '' ?> value="manual">Manual</option>
                                </select>
                                <p><?=$errors['transmission']?></p>
                            </div>
                        </div>
                        <div class="input_row">
                            <div class="inputBox">
                                <span>Model</span>
                                <input type="text" name="model" value="<?=$model?>">
                                <p><?=$errors['model']?></p>
                            </div>
                            <div class="inputBox">
                                <span>Driver <?=$msg ?? ''?></span>
                                <select name="driver">
                                    <option selected disabled>Select Driver..</option>

                                    <?php
                                        try {
                                            require('../db/pdo.php');

                                            $check_status_query = $dbCnx->query("SELECT * FROM vehicle WHERE status = 'active' AND vehicleID = {$_GET['id']}");
                                            if($check_status_query->rowCount() > 0):

                                                $stmt = $dbCnx->prepare('SELECT * FROM users WHERE usertype = :usertype AND NOT EXISTS(SELECT driverID FROM vehicle WHERE vehicle.driverID = users.userID AND vehicle.status = :active)');
                                                // $stmt = $dbCnx->prepare('SELECT users.*, vehicle.driverID, FROM users RIGHT JOIN vehicle ON vehicle.driverID = users.userID WHERE users.usertype = :usertype AND vehicle.status = :active');
                                                $stmt->bindValue(':usertype', 'driver');
                                                $stmt->bindValue(':active', 'active');
                                                $stmt->execute();
                                                $rows= $stmt->fetchAll();
                                                
                                                foreach($rows as $row) : 
                                    ?>
                                                    <option value="<?=$row['userID']?>"><?=$row['firstname'].' '.$row['lastname']?></option>
                                    <?php
                                                endforeach;
                                            endif;
                                        } catch(PDOException $e) {
                                            echo $e->getMessage();
                                        }
                                    ?>

                                </select>
                                <p><?=$errors['driver']?></p>
                            </div>
                            <div class="inputBox">
                                <span>Route</span>
                                <select name="route">
                                    <option disabled>Select a route..</option>

                                    <?php
                                        try {
                                            require('../db/pdo.php');

                                            $stmt = $dbCnx->prepare('SELECT * FROM routes');
                                            $stmt->execute();
                                            $rows= $stmt->fetchAll();
                                            
                                            foreach($rows as $row) : 
                                                if($row2['routeID'] == $row['routeID']) {
                                                    $vid = 'selected';
                                                } else {
                                                    $vid = '';
                                                }
                                    ?>
                                                <option <?=$vid?> value="<?=$row['routeID']?>"><?=$row['start'].' - '.$row['to_'].' - '.$row['end']?></option>
                                    <?php
                                            endforeach;
                                        } catch(PDOException $e) {
                                            echo $e->getMessage();
                                        }
                                    ?>

                                </select>
                                <p><?=$errors['route'] ?? ''?></p>
                            </div>
                        </div>
                        <div class="input_row">
                            <div class="inputBox">
                                <span>Current Kilometers</span>
                                <input type="text" name="current_km" value="<?=$current_km?>" placeholder="Eg. 120000">
                                <p><?=$errors['current_km'] ?? ''?></p>
                            </div>
                        </div>
                        <div class="input_row">
                            <div class="inputBox">
                                <input type="submit" name="submit" value="save changes"> 
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
    <script src="../js/main.js"></script>
</body>
</html>