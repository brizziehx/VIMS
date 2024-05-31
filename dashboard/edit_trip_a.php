<?php
    session_start();

    if(!isset($_SESSION['administrator']) && !isset($_SESSION['vmanager'])) {
        header('location: ../login.php');
    }

    if(isset($_SESSION['change'])) {
        header('location: ../changepassword.php');
    }

    $errors = [];
    date_default_timezone_set('Africa/Nairobi');
    // $arrival_time = date('Y-m-d H:i:s'); 

    if(isset($_POST['submit'])) {
        $departure_time = $_POST['departure_time'];
        $arrival_time = $_POST['arrival_time']; 
        // echo $departure_time.' '.$arrival_time;exit;
        $details = $_POST['details']; 
        $odometer_before = trim($_POST['odometer_before']);
        $details = trim($details);
        $odometer_after = trim($_POST['odometer_after']);
        date_default_timezone_set('Africa/Nairobi');
        $updated_at = date('Y-m-d H:i:s');
        // $row = '';
        // try{
        //     require('../db/pdo.php');
        //     $query = $dbCnx->query("SELECT departure_time FROM trips WHERE tripID = {$_REQUEST['id']}");
        //     $row = $query->fetch();
        // } catch(PDOException $e) {
        //     echo $e->getMessage();
        // }

        // time after arrival
        // $datetime = explode('T', $arrival_time);
        // $date = $datetime[0];
        // $time = $datetime[1];
        // $hms =  explode(':', $time);
        // $h = $hms[0];
        

        // depart time
        // $datetime2 = explode(' ', $row['departure_time']);
        // $date2 = $datetime2[0];
        // $time2 = $datetime2[1];
        // $hms2 =  explode(':', $time2);
        // $h2 = $hms2[0];
        // print_r($h2); exit;

        
        

        // if(empty($depart_from)) {
        //     $errors['depart_from'] = 'Depart from is required';
        // }

        // if(empty($arrive_in)) {
        //     $errors['arrive_in'] = 'Arrive in is required';
        // } else {
        //     if($depart_from == $arrive_in) {
        //         $errors['arrive_in'] = 'Depart from and arrive in can\'t be the same';
        //     }
        // }

        if(empty($departure_time)) {
            $errors['departure_time'] = 'departure date and time is required';
        }

        if(empty($odometer_before)) {
            $errors['odometer_before'] = 'Odometer reading before trip is required';
        } else {
            
            if(!preg_match("/^[0-9]+$/", $odometer_before)) {
                $errors['odometer_before'] = 'Please enter valid Kilometers';
            }
        }

        if(empty($odometer_after)) {
            $errors['odometer_after'] = 'Odometer reading after trip is required';
        } else {
            
            if(!preg_match("/^[0-9]+$/", $odometer_after)) {
                $errors['odometer_after'] = 'Please enter valid Kilometers';
            } else {
                try {
                    require('../db/pdo.php');

                    $stmt2 = $dbCnx->prepare('SELECT * FROM trips WHERE tripID = :tripID');
                    $stmt2->bindValue(':tripID', $_REQUEST['id'], PDO::PARAM_INT);
                    $stmt2->execute();
                    $row2 = $stmt2->fetch();

                    if($odometer_after < $row2['km_before']) {
                        $errors['odometer_after'] = 'Odometer reading cant be less than Odometer before trip';
                    }
            
                } catch (PDOException $e) {
                    echo $e->getMessage();
                }
            }
        }

        if(empty($details)) {
            $errors['details'] = 'Trip details is required';
        } else {
            
            if(!preg_match("/^[A-Za-z ']+$/", $details)) {
                $errors['odometer_after'] = 'Please enter valid details';
            }
        }

        if(empty($arrival_time)) {
            $errors['arrival_time'] = "Arrival date and time is required";
        }

        // if($h <= $h2 || $h2 + 3 > $h) {
        //     $errors['arrival_time'] = "Please select a valid time";
        // }

        if($odometer_after < $odometer_before) {
            $errors['odometer_after'] = 'Odometer reading cant be less than Odometer before trip';
        }
        
        if(!array_filter($errors)) {
            try {
                require('../db/pdo.php');

                $stmt = $dbCnx->prepare('UPDATE trips SET km_before = :kb, departure_time = :dept, km_after = :ka, arrival_time = :at, updated_at = :ua, details = :dt WHERE tripID = :ti');
                $stmt->bindValue(':kb', $odometer_before, PDO::PARAM_STR);
                $stmt->bindValue(':dept', $departure_time, PDO::PARAM_STR);
                $stmt->bindValue(':ka', $odometer_after, PDO::PARAM_STR);
                $stmt->bindValue(':at', $arrival_time, PDO::PARAM_STR);
                $stmt->bindValue(':ua', $updated_at, PDO::PARAM_STR);
                $stmt->bindValue(':dt', $details, PDO::PARAM_STR);
                $stmt->bindValue(':ti', $_REQUEST['id'], PDO::PARAM_INT);

                if($stmt->execute()) {
                    $odometer_after = $arrival_time = $details = "";
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

        $stmt2 = $dbCnx->prepare('SELECT trips.*, vehicle.* FROM trips INNER JOIN vehicle ON trips.vehicleID = vehicle.vehicleID WHERE trips.tripID = :tripID');
        $stmt2->bindValue(':tripID', $_REQUEST['id'], PDO::PARAM_INT);
        $stmt2->execute();
        $row2 = $stmt2->fetch();



    } catch (PDOException $e) {
        echo $e->getMessage();
    }


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Update Trip | Vehicle Information Management System</title>
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
                title: 'Trip has been finished Successfully! \nGood Job!',
                showConfirmButton: false,
                timer: 3000
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
                    <li class="active">
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
                        <a href="index.php">Dashboard</a> > <a href="trips.php" id="sec">Trips</a> > <span>Update Trip</span>
                    </div>
                </div>
                <div class="bottom-card">
                    <form action="" method="post" enctype="multipart/form-data">
                    <p style="border-bottom: 1px #eee solid; padding-bottom:5px;margin-bottom:5px">Update Trip</p>
                        <div class="input_row">
                            <div class="inputBox">
                                <span>Depart From</span>
                                <input type="text" value="<?=$row2['depart_from']?>" readonly>
                                <p><?=$errors['depart_from'] ?? ''?></p>
                            </div>
                            <div class="inputBox">
                                <span>Arrive In</span>
                                <input type="text" value="<?=$row2['arrive_in']?>" readonly>
                                <p><?=$errors['arrive_in'] ?? ''?></p>
                            </div>
                            <div class="inputBox">
                                <span>Departure Time</span>
                                <input type="datetime-local" name="departure_time" value="<?=$row2['departure_time']?>">
                                <p><?=$errors['departure_time'] ?? ''?></p>
                            </div>
                        </div>
                        <div class="input_row">
                            <div class="inputBox">
                                <span>Arrival Time</span>
                                <input type="datetime-local" name="arrival_time" value="<?=$row2['arrival_time']?>">
                                <p><?=$errors['arrival_time'] ?? ''?></p>
                            </div>
                            <div class="inputBox">
                                <span>Odometer Reading Before Trip</span>
                                <input type="text" name="odometer_before" value="<?=$row2['km_before']?>">
                                <p><?=$errors['odometer_before'] ?? ''?></p>
                            </div>
                            <div class="inputBox">
                                <span>Odometer Reading After Trip.</span>
                                <input type="text"  name="odometer_after" value="<?=$row2['km_after']?>">
                                <p><?=$errors['odometer_after'] ?? ''?></p>
                            </div>
                        </div>
                        <div class="input_row" style="justify-content: flex-start;margin-bottom:10px">
                        <div class="inputBox" style="margin-right: 22px;">
                                <span>Vehicle No.</span>
                                <input type="text"  value="<?=$row2['registration_no']?>" readonly>
                            </div>
                            <div class="inputBoxText">
                                <span>Trip Details</span>
                                <textarea name="details" cols="40" rows="5"><?=($row2['details']) ?? ''?></textarea>
                                <p style="color: crimson; font-size: small; margin-top: 5px;"><?=$errors['details'] ?? ''?></p>
                            </div>
                        </div>
                        <div class="input_row">
                            <div class="inputBox">
                                <input type="submit"  name="submit" value="Finish Trip">
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