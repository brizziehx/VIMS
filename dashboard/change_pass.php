<?php
    session_start();

    if(!isset($_SESSION['uniqueID'])) {
        header('location: ../login.php');
    }

    if(isset($_SESSION['change'])) {
        header('location: ../changepassword.php');
    }

    $errors = [];

    if(isset($_POST['submit'])) {
        $password = $_POST['password'];
        $npassword = $_POST['npassword'];
        $cpassword = $_POST['cpassword'];
    
    
        if(empty($password)) {
            $errors['password'] = "Current password is required";
        } else {
            try {
                require('../db/pdo.php');
                $sql = "SELECT password FROM users WHERE userID = {$_SESSION['uniqueID']}";
                $user = $dbCnx->query($sql);
                $row = $user->fetch();

                if(!password_verify($password, $row['password'])) {
                    $errors['password'] = "Current password is incorect!";
                }

            } catch(PDOException $e) {
                echo $e->getMessage();
            }
        }
    
        if(empty($npassword)) {
            $errors['npassword'] = "New password is required";
        } elseif(!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^\da-zA-Z]).{8,}$/', $npassword)) {
            $errors['npassword'] = "Choose a strong password";
        }
    
        if(empty($cpassword)) {
            $errors['cpassword'] = "Confirmation password is required";
        } elseif($npassword !== $cpassword) {
            $errors['cpassword'] = "Passwords doesn't match";
        }
    
        if(!array_filter($errors)) {
    
            try {
                require('../db/pdo.php');

                    $npassword = password_hash($npassword, PASSWORD_BCRYPT);
                    $stmt = $dbCnx->prepare("UPDATE users SET password = :pass WHERE userID = :id");
                    $stmt->bindValue(':pass', $npassword, PDO::PARAM_STR);

                    $stmt->bindValue(':id', $_SESSION['uniqueID'], PDO::PARAM_INT);
                    if($stmt->execute()) {
                        $password = $npassword = $cpassword = "";
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

    } catch (PDOException $e) {
        echo $e->getMessage();
    }


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Change password | Vehicle Information Management System</title>
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
                title: 'Password Updated Successfully',
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
                        <a href="#"><i class="bx bx-gas-pump"></i>Fuel</a>
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
                    <li class="active">
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
                            <li><a href="#"><i class="bx bx-user"></i>Profile</a></li>
                            <li><a href="logout.php?logout_id=<?=$row['userID']?>"><i class="bx bx-log-out"></i>Logout</a></li>
                        </ul>
                    </div>
                </div>
            </header>
            <div class="grid main">
                <!-- <div class="top_card"></div> -->
                <div class="top_card">
                    <div class="bread-cumb">
                        <a href="index.php">Dashboard</a> > <a href="settings.php" id="sec">Settings</a> > <span>New Password</span>
                    </div>
                </div>
                <div class="bottom-card">
                    <form action="" method="post">
                    <p style="border-bottom: 1px #eee solid; padding-bottom:5px;margin-bottom:5px">Update Password</p>
                        <div class="input_row">
                            <div class="inputBox">
                                <span>Current Password</span>
                                <input type="password" name="password" value="<?=$password ?? ''?>">
                                <p><?=$errors['password'] ?? ''?></p>
                            </div>
                            <div class="inputBox">
                                <span>New Password</span>
                                <input type="password" name="npassword" value="<?=$npassword ?? ''?>">
                                <p><?=$errors['npassword'] ?? ''?></p>
                            </div>
                            <div class="inputBox">
                                <span>Confirm New Password</span>
                                <input type="password" name="cpassword" value="<?=$cpassword ?? ''?>">
                                <p><?=$errors['cpassword'] ?? ''?></p>
                            </div>
                        </div>
                        
                        <div class="input_row">
                            <div class="inputBox">
                                <input type="submit" name="submit" value="Update Password"> 
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