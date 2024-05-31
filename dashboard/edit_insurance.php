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

        $stmt2 = $dbCnx->prepare('SELECT insurance.*, vehicle.vehicleID, vehicle.model, vehicle.chassis_no, vehicle.engine_no, vehicle.registration_no, vehicle.make, vehicle.cc FROM insurance INNER JOIN vehicle ON insurance.vehicleID = vehicle.vehicleID WHERE insurance.insuranceID = :insuranceID');
        $stmt2->bindValue(':insuranceID', $_REQUEST['id'], PDO::PARAM_INT);
        $stmt2->execute();
        $row2 = $stmt2->fetch();

        switch($row2['type']) {
            case 'Comprehensive':
                $comp = "selected";
                $third = "";
            break;
            case 'Third-Party':
                $comp = "";
                $third = "selected";
            break;
            default:
            $comp = "";
            $third = "";
        }

    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    $errors = ['insurer'=>'','start'=>'','end'=>'','cost'=>'','cover'=>'','vehicleID'=>''];
    $insurer = $start = $end = $cost = $cover = $vehicleID = "";
    if(isset($_POST['submit'])) {
        $insurer = $_POST['insurer'];
        $start = $_POST['start'];
        $end = $_POST['end'];
        $cost = $_POST['cost']; 
        $cover = $_POST['coverage'] ?? '';
        $vehicleID = $_POST['vehicleID'] ?? '';
        date_default_timezone_set('Africa/Nairobi');
        $updated_at = date('Y-m-d H:i:s');

        if(empty($insurer)) {
            $errors['insurer'] = 'Insurer name is required';
        } else {
            if(!preg_match("/^[A-Z][a-zA-Z' ]{5,30}$/", $insurer)) {
                $errors['insurer'] = "Insurer name is not valid";
            }
        }

        if(empty($start)) {
            $errors['start'] = 'Starting date is required';
        }

        if(empty($end)) {
            $errors['end'] = 'Ending date is required';
        }

        if(empty($cost)) {
            $errors['cost'] = "Insurance cost is required";
        } else {
            if(!preg_match("/^[0-9]{6,8}$/", $cost)) {
                $errors['cost'] = "Please enter a valid insurance cost";
            }
        }

        if(empty($cover)) {
            $errors['cover'] = "Insurance coverage is required";
        }
        
        switch($cover) {
            case 'Comprehensive':
                $comp = "selected";
                $third = "";
            break;
            case 'Third-Party':
                $comp = "";
                $third = "selected";
            break;
            default:
            $comp = "";
            $third = "";
        }

        
        if(empty($vehicleID)) {
            $errors['vehicleID'] = "Vehicle No. is required";
        }
        

        if(!array_filter($errors)) {
            try {
                require('../db/pdo.php');

                $stmt = $dbCnx->prepare('UPDATE insurance SET insure = :insure, vehicleID = :vehicleID, start = :start, end = :end, type = :type, amount = :amount, updated_at = :updated_at WHERE insuranceID = :insuranceID');
                $stmt->bindValue(':insure', $insurer, PDO::PARAM_STR);
                $stmt->bindValue(':vehicleID', $vehicleID, PDO::PARAM_INT);
                $stmt->bindValue(':start', $start, PDO::PARAM_STR);
                $stmt->bindValue(':end', $end, PDO::PARAM_STR);
                $stmt->bindValue(':type', $cover, PDO::PARAM_STR);
                $stmt->bindValue(':amount', $cost, PDO::PARAM_STR);
                $stmt->bindValue(':updated_at', $updated_at, PDO::PARAM_STR);
                $stmt->bindValue(':insuranceID', $_REQUEST['id'], PDO::PARAM_INT);

                if($stmt->execute()) {
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

        $stmt2 = $dbCnx->prepare('SELECT insurance.*, vehicle.vehicleID, vehicle.model, vehicle.chassis_no, vehicle.engine_no, vehicle.registration_no, vehicle.make, vehicle.cc FROM insurance INNER JOIN vehicle ON insurance.vehicleID = vehicle.vehicleID WHERE insurance.insuranceID = :insuranceID');
        $stmt2->bindValue(':insuranceID', $_REQUEST['id'], PDO::PARAM_INT);
        $stmt2->execute();
        $row2 = $stmt2->fetch();

        switch($row2['type']) {
            case 'Comprehensive':
                $comp = "selected";
                $third = "";
            break;
            case 'Third-Party':
                $comp = "";
                $third = "selected";
            break;
            default:
            $comp = "";
            $third = "";
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
    
    <title>Edit Insurance | Vehicle Information Management System</title>
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
                title: 'Insurance Details Has Been Updated Successfully',
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
                    <li>
                        <a href="fuel.php"><i class="bx bx-gas-pump"></i>Fuel</a>
                    </li>
                    <li class="active">
                        <a href="insurance.php"><i class="bx bx-donate-heart"></i>Insurance</a>
                    </li>
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
                        <a href="index.php">Dashboard</a> > <a href="insurance.php" id="sec">Insurance</a> > <span>Edit Insurance Details</span>
                    </div>
                </div>
                <div class="bottom-card">
                    <form action="" method="post" enctype="multipart/form-data">
                    <p style="border-bottom: 1px #eee solid; padding-bottom:5px;margin-bottom:5px">Edit Insurance</p>
                        <div class="input_row">
                            <div class="inputBox">
                                <span>Insurer</span>
                                <input type="text" name="insurer" value="<?=$row2['insure']?>" placeholder="Eg. Britam Insurance">
                                <p><?=$errors['insurer']?></p>
                            </div>
                            <div class="inputBox">
                                <span>Starting Date</span>
                                <input type="date" name="start" value="<?=$row2['start']?>"> 
                                <p><?=$errors['start']?></p>
                            </div>
                            <div class="inputBox">
                                <span>Ending Date</span>
                                <input type="date" name="end" value="<?=$row2['end']?>">
                                <p><?=$errors['end']?></p>
                            </div>
                        </div>
                        <div class="input_row">
                            <div class="inputBox">
                                <span>Insurance Cost</span>
                                <input type="text" name="cost" value="<?=$row2['amount']?>" placeholder="Eg. 118000">
                                <p><?=$errors['cost']?></p>
                            </div>
                            <div class="inputBox">
                                <span>Insurance Coverage</span>
                                <select name="coverage">
                                    <option selected disabled>Select insurance cover...</option>
                                    <option <?=$comp ?? ''?> value="Comprehensive">Comprehensive</option>
                                    <option <?=$third ?? ''?> value="Third-Party">Third-Party</option>
                                </select>
                                <p><?=$errors['cover']?></p>
                            </div>
                            <div class="inputBox">
                                <span>Vehicle No. | <?=$row2['registration_no']?></span>
                                <select name="vehicleID">
                                    <option  disabled>Select Vehicle..</option>

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
                                                <option <?=$vid?> value="<?=$row['vehicleID']?>"><?=$row['registration_no'].' - '.$row['make'].' '.$row['model']?></option>
                                    <?php
                                            endforeach;
                                        } catch(PDOException $e) {
                                            echo $e->getMessage();
                                        }
                                    ?>

                                </select>
                                <p><?=$errors['vehicleID']?></p>
                            </div>
                        </div>
                        <div class="input_row" style="justify-content: flex-start;">
                            
                        </div>
                        <div class="input_row">
                            <div class="inputBox">
                                <input type="submit" name="submit" value="Save changes"> 
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