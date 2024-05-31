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
    
    <title>Trips | Vehicle Information Management System</title>
    <link rel="stylesheet" href="../css/styles.css"> 
    <link rel="stylesheet" href="../boxicons/css/boxicons.min.css">
    <script src="../swal/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="../swal/sweetalert2.css">
    <!-- <link href="DataTables/datatables.min.css" rel="stylesheet"> -->
    <style>

        /* .hidden {
            visibility: hidden;
        } */     

        @media print {
            body * {
                visibility: hidden;
            }

            body .container aside {
                display: none;
            }

            .container main {
                margin-left: 0;
                width: calc(100% - 0%);
                height: 100vh;
            }
            
            .print, .print * {
                visibility: visible;
                position: relative !important;
                top: 0;
                left: 0;
            }

            .hidden, .hidden * {
                /* visibility: visible; */
                display: none;
            }

        }

    </style>
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
                title: 'Trip deleted successfully',
                showConfirmButton: false,
                timer: 2000
            })
        }
        
        function deleteError(error) {
            Swal.fire({
                position: 'center',
                icon: 'error',
                title: error,
                showConfirmButton: true,
                // timer: 2000
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
                        <a href="index.php">Dashboard</a> > <span>Trips</span>
                    </div>
                    <!-- <a href="add_trip_ad.php" class="add-btn"><i class="bx bx-trip"></i>new trip</a> -->
                </div>
                
                <div class="top_card" style="align-items: center;">
                    <p style="color:black">All Trips</p>
                    <input type="text" name="searchInput" id="searchInput" placeholder="Search trips..">
                </div>
                <div class="top_card" style="align-items: center; justify-content:end">
                    <a href="javascript:void(0)" class="add-btn printBtn"><i class="bx bx-printer"></i>Print</a>
                </div>
                <div class="bottom-card print">
                    <table id="myTable" class="print">
                        <tr>
                            <th>SN</th>
                            <th>Bus No.</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Departure Time</th>
                            <th>Arrival Time</th>
                            <th>KM before</th>
                            <th>KM after</th>
                            <th class="hidden">Actions</th>
                        </tr>
                        <?php 
                            try {
                                require('../db/pdo.php');
                                $stmt = $dbCnx->prepare('SELECT trips.*, vehicle.* FROM trips INNER JOIN vehicle ON vehicle.vehicleID = trips.vehicleID ORDER BY trips.tripID DESC');
                                $stmt->execute();

                                $rows = $stmt->fetchAll();
                                $sn = 1;
                                foreach($rows as $row): ?>

                                    <tr id="trow">
                                        
                                        <td><?=$sn++?></td>
                                        <td><?=$row['registration_no']?></td>
                                        <td><?=$row['depart_from']?></td>
                                        <td><?=$row['arrive_in']?></td>
                                        <td><?=date('d-m-Y H:i:s', strtotime($row['departure_time']))?></td> 
                                        <td><?=($row['arrival_time'] != '') ? date('d-m-Y H:i:s', strtotime($row['arrival_time'])) : 'Still ongoing'?></td> 
                                        <td><?=$row['km_before']. ' '. 'KM'?></td> 
                                        <td><?=($row['km_after'] != "") ? $row['km_after']. ' '. 'KM': 'Still ongoing'?></td> 
                                        <td class="hidden"><a href="view_trip.php?id=<?=$row['tripID']?>"><i class="bx bx-file icon res"></i></a><a href="edit_trip_a.php?id=<?=$row['tripID']?>"><i class="bx bx-edit icon update"></i></a><a href="delete_trip.php?id=<?=$row['tripID']?>"><i class="bx bx-trash icon del"></i></a></td>
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
    <script>
        const printBtn = document.querySelector('.printBtn');

        printBtn.addEventListener('click', () => print())
    </script>
    <script src="../js/jquery.js"></script>
    <script src="../js/main.js"></script>
    <!-- <script src="DataTables/datatables.min.js"></script> -->
</body>
</html>