<?php
    session_start();

    if(!isset($_SESSION['administrator']) && !isset($_SESSION['vmanager'])) {
        header('location: ../login.php');
    }

    if(isset($_SESSION['change'])) {
        header('location: ../changepassword.php');
    }

    $errors = [];
    $last_m = $next_m = $oil = $vehicleID = $actions = "";
    if(isset($_POST['submit'])) {
        $last_m = $_POST['last_m'];
        $next_m = $_POST['next_m'];
        // $oil = $_POST['oil'];
        $vehicleID = $_POST['vehicleID'] ?? '';
        if(isset($_POST['actions'])) {
            $actions = $_POST['actions'];
            $actions = implode(',', $actions);
        }
        
        // $battery = $_POST['battery'] ?? '';
        date_default_timezone_set('Africa/Nairobi');
        $created_at = date('Y-m-d H:i:s');


        if(empty($last_m)) {
            $errors['last_m'] = 'Last maintenance date is required';
        }

        if(empty($next_m)) {
            $errors['next_m'] = 'Next maintenance date is required';
        }

        // if(empty($oil)) {
        //     $errors['oil'] = 'Next oil change date is required';
        // }

        // if(empty($battery)) {
        //     $errors['battery'] = "Battery status is required";
        // }

        if(empty($vehicleID)) {
            $errors['vehicleID'] = "Vehicle number is required";
        }
        

        if(!array_filter($errors)) {
            try {
                require('../db/pdo.php');

                $stmt = $dbCnx->prepare('INSERT INTO maintenance(vehicleID, last_maintenance, next_maintenance, actions, created_at) VALUES (:vehicleID, :last_maintenance, :next_maintenance, :actions, :created_at)');
                $stmt->bindValue(':vehicleID', $vehicleID, PDO::PARAM_INT);
                $stmt->bindValue(':last_maintenance', $last_m, PDO::PARAM_STR);
                $stmt->bindValue(':next_maintenance', $next_m, PDO::PARAM_STR);
                // $stmt->bindValue(':oil_change', $oil, PDO::PARAM_STR);
                // $stmt->bindValue(':battery_check', $battery, PDO::PARAM_STR);
                $stmt->bindValue(':actions', $actions, PDO::PARAM_STR);
                $stmt->bindValue(':created_at', $created_at, PDO::PARAM_STR);

                if($stmt->execute()) {
                    $last_m = $next_m = $vehicleID = $actions = "";
                    $errors['success'] = '<script>success()</script>';
                }
                
            } catch(PDOException $e) {
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

    } catch (PDOException $e) {
        echo $e->getMessage();
    }


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Add Maintanance | Vehicle Information Management System</title>
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
                title: 'Maintenance Recorded Successfully',
                showConfirmButton: false,
                timer: 2000
            })
        }
        
        function error(error) {
            Swal.fire({
                position: 'center',
                icon: 'error',
                title: error,
                showConfirmButton: false,
                timer: 2000
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
                        <a href="index.php">Dashboard</a> > <a href="maintanance.php" id="sec">Maintenance</a> > <span>New Maintenance</span>
                    </div>
                </div>
                <div class="bottom-card">
                    <form action="" method="post" enctype="multipart/form-data">
                    <p style="border-bottom: 1px #eee solid; padding-bottom:5px;margin-bottom:5px">Maintanance</p>
                        <div class="input_row">
                            <div class="inputBox">
                                <span>Last Maintenance</span>
                                <input type="date" name="last_m" value="<?=$last_m ?? ''?>">
                                <p><?=$errors['last_m'] ?? ''?></p>
                            </div>
                            <div class="inputBox">
                                <span>Next Maintenance</span>
                                <input type="date" name="next_m" value="<?=$next_m ?? ''?>">
                                <p><?=$errors['next_m'] ?? ''?></p>
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
                                    ?>
                                                <option value="<?=$row['vehicleID']?>"><?=$row['registration_no'].' - '.$row['make'].' '.$row['model']?></option>
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
                        <div class="input_row" style="justify-content: start;">

                        </div>
                        <div class="input_row" style="justify-content: flex-start;">
                            <div class="input_check" style="margin-right: 20px;">
                                <span style="font-size: 14px;">Actions Peformed</span>
                                <div>
                                    <input type="checkbox" name="actions[]" value="Battery changed"> Battery changed 
                                </div>
                                <div>
                                    <input type="checkbox" name="actions[]" value="Oil filter changed"> Oil filter changed 
                                </div>
                                <div>
                                    <input type="checkbox" name="actions[]" value="Hydraulic changed"> Hydraulic changed 
                                </div>
                                <div>
                                    <input type="checkbox" name="actions[]" value="Bumper changed"> Bumper changed 
                                </div>
                                <div>
                                    <input type="checkbox" name="actions[]" value="Front left tyre changed"> Front left tyre changed 
                                </div>
                                <div>
                                    <input type="checkbox" name="actions[]" value="Front right tyre changed"> Front right tyre changed 
                                </div>
                                <div>
                                    <input type="checkbox" name="actions[]" value="Back left tyre changed"> Back left tyre changed 
                                </div>
                                <div>
                                    <input type="checkbox" name="actions[]" value="Back right tyre changed"> Back right tyre changed 
                                </div>
                                <!-- <input type="checkbox" name="actions[]" value="Battery changed">
                                <input type="checkbox" name="actions[]" value="Battery changed"> -->
                            </div>
                            
                        </div>
                        <div class="input_row">
                            <div class="inputBox">
                                <input type="submit" name="submit" value="Save Maintenance"> 
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