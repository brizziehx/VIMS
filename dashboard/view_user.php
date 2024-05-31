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
        
        $stmt2 = $dbCnx->prepare('SELECT * FROM users WHERE userID = :userID');
        $stmt2->bindValue(':userID', $_REQUEST['id'], PDO::PARAM_INT);
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
    
    <title>View User | Vehicle Information Management System</title>
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
        #uploadedAvatar {
            width: 150px;
            height: 150px;
            object-fit: cover;
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
                    <li class="active">
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
                        <a href="index.php">Dashboard</a> > <a href="users.php" id="sec">Users</a> > <span>User Details</span>
                    </div>
                </div>
                <div class="bottom-card">
                    <form action="" method="post" enctype="multipart/form-data">
                        <p style="display: flex; justify-content:space-between; border-bottom: 1px #eee solid; padding-bottom:5px;margin-bottom:5px;">User Details</p>
                        <div class="img" style="margin-bottom: 30px;">
                            <img src="../images/<?=$row2['image']?>" id="uploadedAvatar">
                        </div>
                        <div class="input_row">
                            <div class="inputBox">
                                <span>Firstname</span>
                                <input value="<?=$row2['firstname']?>" disabled>
                            </div>
                            <div class="inputBox">
                                <span>Middlename</span>
                                <input value="<?=$row2['middlename']?>" disabled>
                            </div>
                            <div class="inputBox">
                                <span>Lastname</span>
                                <input  value="<?=$row2['lastname']?>" disabled>
                            </div>
                        </div>
                        <div class="input_row">
                            <div class="inputBox">
                                <span>Email Address</span>
                                <input  value="<?=$row2['email']?>" disabled>
                                
                            </div>
                            <div class="inputBox">
                                <span>Gender</span>
                                <input  value="<?=$row2['gender']?>" disabled>
                            </div>
                            <div class="inputBox imae-upload">
                                <span>Phone Number</span>
                                <input  value="<?=$row2['phone']?>"  disabled>
                            </div>
                        </div>
                        <div class="input_row">
                            <div class="inputBox">
                                <span>User Role | Type</span>
                                <input style="text-transform: capitalize;" value="<?=$row2['usertype']?>" disabled>
                                
                            </div>
                            <div class="inputBox">
                                <span>Created At</span>
                                <input value="<?=date('jS F Y - H:i:s', strtotime($row2['created_at']))?>" disabled>
                            </div>
                            <div class="inputBox">
                                <span>Updated At</span>
                                <input value="<?=($row2['updated_at'] != '') ? date('jS F Y - H:i:s', strtotime($row2['updated_at'])): 'NIL'?>" disabled>
                            </div>
                        </div>
                        <!--  -->
                        
                    </form>
                </div>
                <footer>Copyright &copy; <?=date('Y')?>. All Righst Reserved.</footer>
            </div>
        </main>
    </div>
    <script src="../js/main.js"></script>
</body>
</html>