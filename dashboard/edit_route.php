<?php
    session_start();
    date_default_timezone_set('Africa/Nairobi');

    if(!isset($_SESSION['uniqueID'])) {
        header('location: ../login.php');
    }

    if(isset($_SESSION['change'])) {
        header('location: ../changepassword.php');
    }

    $errors = [];

    if(isset($_POST['submit'])) {
        $start = trim($_POST['start']);
        $to = trim($_POST['to']);
        $end = trim($_POST['end']);
        date_default_timezone_set('Africa/Nairobi');
        $updated_at = date('Y-m-d H:i:s');
    
    
        if(empty($start)) {
            $errors['start'] = "From is required";
        } elseif(!preg_match('/^[a-zA-z ]+$/', $start)) {
            $errors['start'] = "Region is invalid";
        } elseif($start == $to) {
            $errors['start'] = "This regions can't be the same";
        }
    
        if(empty($to)) {
            $errors['to'] = "To is required";
        } elseif(!preg_match('/^[a-zA-z ]+$/', $to)) {
            $errors['to'] = "Region is invalid";
        }  elseif($end == $to) {
            $errors['end'] = "This regions can't be the same";
        }
    
        if(empty($end)) {
            $errors['end'] = "End is required";
        } elseif(!preg_match('/^[a-zA-z ]+$/', $end)) {
            $errors['end'] = "Region is invalid";
        } elseif($start != $end) {
            $errors['start'] = "From and End regions must be the same";
        }
    
        if(!array_filter($errors)) {
    
            try {
                require('../db/pdo.php');

                    $stmt = $dbCnx->prepare("UPDATE routes SET start = :start, to_ = :to, end = :end, updated_at = :updated_at WHERE routeID = :routeID");
                    $stmt->bindValue(':start', $start, PDO::PARAM_STR);
                    $stmt->bindValue(':to', $to, PDO::PARAM_STR);
                    $stmt->bindValue(':end', $end, PDO::PARAM_STR);
                    $stmt->bindValue(':updated_at', $updated_at, PDO::PARAM_STR);
                    $stmt->bindValue(':routeID', $_REQUEST['id'], PDO::PARAM_INT);
                    if($stmt->execute()) {
                        $start = $to = $end = "";
                        $errors['success'] = "<script>success()</script>";
                    }
            }catch(PDOException $e) {
                echo $e->getMessage();
            }
        }
    }



    try {
        require('../db/pdo.php');
        $stmt = $dbCnx->prepare('SELECT * FROM users WHERE userID = :userID');
        $stmt->bindValue(':userID', $_SESSION['uniqueID'], PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();

        $stmt2 = $dbCnx->prepare('SELECT * FROM routes WHERE routeID = :routeID');
        $stmt2->bindValue(':routeID', $_REQUEST['id'], PDO::PARAM_INT);
        $stmt2->execute();
        $row2= $stmt2->fetch();

    } catch (PDOException $e) {
        echo $e->getMessage();
    }


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Edit route | Vehicle Information Management System</title>
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
                title: 'Route Has Been Updated Successfully',
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
                    <li>
                        <a href="all_services.php"><i class="bx bx-wrench"></i>Service</a>
                    </li>
                    <!-- <li>
                        <a href="#"><i class="bx bx-gas-pump"></i>Fuel</a>
                    </li>
                    <li>
                        <a href="insurance.php"><i class="bx bx-donate-heart"></i>Insurance</a>
                    </li> -->
                    <li class="active">
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
                        <li>
                            <a href="index.php"><i class="bx bx-grid-alt"></i>Dashboard</a>
                        </li>
                        <li>
                            <a href="my_routes.php"><i class="bx bx-trip"></i>Routes and Trips</a>
                        </li>
                        <li class="active">
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
                <!-- <div class="top_card"></div> -->
                <div class="top_card">
                    <div class="bread-cumb">
                        <a href="index.php">Dashboard</a> > <a href="routes.php" id="sec">Routes</a> > <span>Edit Route</span>
                    </div>
                </div>
                <div class="bottom-card">
                    <form action="" method="post">
                        <p style="border-bottom: 1px #eee solid; padding-bottom:5px;margin-bottom:5px">Edit Route</p>
                        <div class="input_row">
                            <div class="inputBox">
                                <span>Start From</span>
                                <input type="start" name="start" value="<?=$row2['start'] ?? ''?>">
                                <p><?=$errors['start'] ?? ''?></p>
                            </div>
                            <div class="inputBox">
                                <span>To</span>
                                <input type="text" name="to" value="<?=$row2['to_'] ?? ''?>">
                                <p><?=$errors['to'] ?? ''?></p>
                            </div>
                            <div class="inputBox">
                                <span>End</span>
                                <input type="text" name="end" value="<?=$row2['end'] ?? ''?>">
                                <p><?=$errors['end'] ?? ''?></p>
                            </div>
                        </div>
                        
                        <div class="input_row">
                            <div class="inputBox">
                                <input type="submit" name="submit" value="Update Route"> 
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