<?php
    session_start();

    if(!isset($_SESSION['administrator']) && !isset($_SESSION['vmanager'])) {
        header('location: ../login.php');
    }

    if(isset($_SESSION['change'])) {
        header('location: ../changepassword.php');
    }

    $admin = '';


    try {
        require('../db/pdo.php');
        $stmt = $dbCnx->prepare('SELECT * FROM users WHERE userID = :userID');
        $stmt->bindValue(':userID', $_SESSION['uniqueID'], PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();

        $admin = $row['usertype'];

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
    
    <title>Insurance | Vehicle Information Management System</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../boxicons/css/boxicons.min.css">
    <script src="../swal/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="../swal/sweetalert2.css">
    <!-- <link href="DataTables/datatables.min.css" rel="stylesheet"> -->
</head>
<body>
    <script>
        function success() {
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Password upated successfully',
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

        function deleteOK() {
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Insurance details deleted successfully',
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
                    <?php elseif(isset($_SESSION['driver'])): ?>
                        nav coming soon
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
                        <a href="index.php">Dashboard</a> > <span>Insurance</span>
                    </div>
                    <a href="add_insurance.php" class="add-btn"><i class="bx bx-donate-heart"></i>new insurance</a>
                </div>
                <div class="top_card" style="align-items: center;">
                    <p style="color:black">Insurance Details</p>
                    <input type="text" name="searchInput" id="searchInput" placeholder="Search Insurance details..">
                </div>
                <div class="bottom-card">
                    <table id="myTable">
                        <tr>
                            <th>#</th>
                            <th>Vehicle No.</th>
                            <th>Insurer</th>
                            <!-- <th>Cover Note</th> -->
                            <th>Start</th>
                            <th>End</th>
                            <th>Status</th>
                            <th>Cost</th>
                            <th>Remains</th>
                            <th>Insurance Cover</th>
                            <th>Actions</th>
                        </tr>
                        <?php 
                            try {
                                require('../db/pdo.php');
                                $stmt = $dbCnx->prepare('SELECT vehicle.registration_no, insurance.insuranceID, insurance.insure, insurance.start, insurance.end, insurance.type, insurance.amount FROM insurance INNER JOIN vehicle ON vehicle.vehicleID = insurance.vehicleID ORDER BY insurance.start ASC');
                                $stmt->execute();

                                $rows = $stmt->fetchAll();
                                $sn = 1;

                                foreach($rows as $row): 
                                    date_default_timezone_set('Africa/Nairobi');
                                    $date1 = date_create();
                                    // $date2 = date_create('2024-03-22'); 
                                    $date2 = date_create($row['end']); 
                                    $diff = date_diff($date1, $date2);
                                    
                                    $days = ($diff->format('%a') == +0) ? 'day' : 'days';
                                    $insuredDays = "";
                                    if($diff->format('%R%a') > 0) {
                                        $status = "<p class=active>Active</p>";
                                        $insuredDays = $diff->format("%R%a") + 1;
                                    } elseif($diff->format('%R%a') == +0) {
                                        $insuredDays = $diff->format("%R%a") + 1;
                                        $status = "<p class=active>Active</p>";
                                    } else {
                                        $status = "<p class=expired>Expired</p>";
                                        $insuredDays = $diff->format("%R%a");
                                    }
                                ?>
                                    <tr id="trow">
                                        <td><?=$sn++?></td>
                                        <td><?=$row['registration_no']?></td>
                                        <td><?=$row['insure']?></td>
                                        <td><?=date('jS F Y', strtotime($row['start']))?></td>
                                        <td><?=date('jS F Y', strtotime($row['end']))?></td>
                                        <td><?=$status?></td>
                                        <td><?=number_format($row['amount'], 2, '.', ',')?></td>
                                        <td><?=$insuredDays." ".$days?></td>
                                        <td><?=$row['type']?></td>
                                        <td><a href="view_insurance.php?id=<?=$row['insuranceID']?>"><i class="bx bx-file icon res"></i></a><a href="edit_insurance.php?id=<?=$row['insuranceID']?>"><i class="bx bx-edit icon update"></i></a><a href="delete_insurance.php?id=<?=$row['insuranceID']?>"><i class="bx bx-trash icon del"></i></a></td>
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
    <!-- <script src="DataTables/datatables.min.js"></script> -->
</body>
</html>