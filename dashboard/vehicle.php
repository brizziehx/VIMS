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
    
    <title>Vehicles | Vehicle Information Management System</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../boxicons/css/boxicons.min.css">
    <script src="../swal/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="../swal/sweetalert2.css">
</head>
<body>
<script>
        function deleteOK() {
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Vehicle deleted successfully',
                showConfirmButton: false,
                timer: 2000
            })
        }

        function active() {
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Vehicle is now active',
                showConfirmButton: false,
                timer: 2000
            })
        }

        function inactive() {
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Vehicle is now inactive',
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
    <?php
        if(isset($_SESSION['msg'])) {
            echo $_SESSION['msg'];
        }
       unset($_SESSION['msg']); 
    ?>
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
                        <a href="index.php">Dashboard</a> > <span>Vehicles</span>
                    </div>
                    <a href="add_vehicle.php" class="add-btn"><i class="bx bx-bus"></i>new vehicle</a>
                </div>
                <div class="top_card" style="align-items: center;">
                    <p style="color:black">All Vehicles</p>
                    <input type="text" name="searchInput" id="searchInput" placeholder="Search Vehicles..">
                </div>
                <div class="bottom-card">
                    <table id="myTable">
                        <tr>
                            <th>#</th>
                            <th>Reg. no</th>
                            <th>Model</th>
                            <th>Make</th>
                            <th>Chassis no.</th>
                            <th>Transmission</th>
                            <th>Engine no</th>
                            <th>CC</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        <?php 
                            try {
                                require('../db/pdo.php');
                                $stmt = $dbCnx->prepare('SELECT * FROM vehicle');
                                $stmt->execute();

                                $rows = $stmt->fetchAll();
                                $sn = 1;
                                foreach($rows as $row): 
                                switch($row['status']){
                                    case 'active':
                                        $status = "<p class=active>Active</p>";
                                    break;
                                    case 'inactive':
                                        $status = "<p class=expired>Inactive</p>";
                                    break;
                                    default:
                                    $status = "";
                                }
                                if($row['status'] == 'active') {
                                    $action = "<a href=set_inactive.php?id=".$row['vehicleID']."><i class='bx bx-x icon res'></i></a>";
                                } else {
                                    $action = "<a href=set_active.php?id=".$row['vehicleID']."><i class='bx bx-check-double icon res'></i></a>";
                                }
                                ?>
                                    
                                    <tr id="trow">
                                        <td><?=$sn++?></td>
                                        <td><?=$row['registration_no']?></td>
                                        <td><?=$row['model']?></td>
                                        <td><?=$row['make']?></td>
                                        <td><?=$row['chassis_no']?></td>
                                        <td><?=$row['transmission']?></td>
                                        <td><?=$row['engine_no']?></td>
                                        <td><?=$row['cc']?></td>
                                        <td><?=$status?></td>
                                        <td><a href="view_vehicle.php?id=<?=$row['vehicleID']?>"><i class="bx bx-file icon update"></i></a><?=$action?><a href="edit_vehicle.php?id=<?=$row['vehicleID']?>"><i class="bx bx-edit icon update"></i></a><a href="delete_vehicle.php?id=<?=$row['vehicleID']?>"><i class="bx bx-trash icon del"></i></a></td>
                                    </tr>
                        <?php
                                endforeach;
                            } catch(PDOException $e) {
                                echo $e->getMessage();
                            }
                        ?>
                        
                    </table>
                </div>
                <footer>Copyright &copy; <?=date('Y')?>. All Righst Reserved.</footer>
            </div>
        </main>
    </div>
    <script src="../js/jquery.js"></script>
    <script src="../js/main.js"></script>
</body>
</html>