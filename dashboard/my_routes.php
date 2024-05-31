<?php
    session_start();

    if(!isset($_SESSION['driver'])) {
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
    
    <title>Route and Trips | Vehicle Information Management System</title>
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
                title: 'Account deleted successfully',
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
                    <?php if(isset($_SESSION['driver'])) : ?>
                    <li>
                        <a href="index.php"><i class="bx bx-grid-alt"></i>Dashboard</a>
                    </li>
                    <li class="active">
                        <a href="my_routes.php"><i class="bx bx-trip"></i>Routes and Trips</a>
                    </li>
                    <li>
                        <a href="settings.php"><i class="bx bx-cog"></i>Settings</a>
                    </li>
                    <li>
                        <a href="logout.php?logout_id=<?=$_SESSION['uniqueID']?>"><i class="bx bx-log-out"></i>Logout</a>
                    </li>
                    
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
                        <a href="index.php">Dashboard</a> > <span>Routes And Trips</span>
                    </div>
                    <?php
                        try {
                            require('../db/pdo.php');
                            $stmtx = $dbCnx->prepare('SELECT trips.*, vehicle.* FROM trips INNER JOIN vehicle ON trips.vehicleID = vehicle.vehicleID WHERE trips.driverID = :driverID ORDER BY trips.created_at DESC');
                            $stmtx->bindValue(':driverID', $_SESSION['uniqueID'], PDO::PARAM_INT);
                            $stmtx->execute();

                            $rowx = $stmtx->fetch();
                            if($stmtx->rowCount() > 0):
                                if(!$rowx['km_after'] == ''): ?>
                                    <a href="add_trip.php" class="add-btn"><i class="bx bx-trip"></i>new trip</a>
                    <?php endif; else: ?>
                        <a href="add_trip.php" class="add-btn"><i class="bx bx-trip"></i>new trip</a>
                    <?php endif;;
                        }catch(PDOException $e) {
                            echo $e->getMessage();
                        }
                    ?>
                </div>
                <div class="top_card" style="align-items: center;">
                    <p style="color:black">My Route</p>
                    <input type="text" name="searchInput" id="searchInput" placeholder="Search trips..">
                </div>
                <div class="bottom-card">
                    <table>
                        <tr>
                            <th>SN</th>
                            <th>Start</th>
                            <th>To</th>
                            <th>End</th>
                            <th>Vehicle No.</th>
                        </tr>
                        <?php 
                            try {
                                require('../db/pdo.php');
                                $stmt = $dbCnx->prepare('SELECT vehicle.*, routes.* FROM routes INNER JOIN vehicle ON routes.routeID = vehicle.routeID WHERE vehicle.driverID = :driverID');
                                $stmt->bindValue(':driverID', $row['userID'], PDO::PARAM_INT);
                                $stmt->execute();

                                $rows = $stmt->fetchAll();
                                $sn = 1;

                                foreach($rows as $row): ?>

                                    <tr id="trow">
                                        <td><?=$sn++?></td>
                                        <td><?=$row['start']?></td>
                                        <td><?=$row['to_']?></td>
                                        <td><?=$row['end']?></td> 
                                        <td><?=$row['registration_no']?></td> 
                                    </tr>
                        <?php
                                endforeach;
                            } catch(PDOException $e) {
                                echo $e->getMessage();
                            }
                        ?>
                        
                    </table>
                </div>
                
                <div class="top_card" style="align-items: center;">
                    <p style="color:black">My Trips</p>
                    <!-- <input type="text" name="searchInput" id="searchInput" placeholder="Search trips.."> -->
                </div>
                <div class="bottom-card">
                    <table id="myTable">
                        <tr>
                            <th>SN</th>
                            <th>Depart From</th>
                            <th>Arrive in</th>
                            <th>Departure Time</th>
                            <th>Arrival Time</th>
                            <th>Odometer before</th>
                            <th>Odometer after</th>
                            <th>Actions</th>
                        </tr>
                        <?php 
                            try {
                                require('../db/pdo.php');
                                $stmt = $dbCnx->prepare('SELECT * FROM trips WHERE driverID = :driverID ORDER BY tripID DESC');
                                $stmt->bindValue(':driverID', $_SESSION['uniqueID'], PDO::PARAM_INT);
                                $stmt->execute();

                                $rows = $stmt->fetchAll();
                                $sn = 1;
                                foreach($rows as $row): ?>

                                    <tr id="trow">
                                        
                                        <td><?=$sn++?></td>
                                        <td><?=$row['depart_from']?></td>
                                        <td><?=$row['arrive_in']?></td>
                                        <td><?=date('d-m-Y H:i:s', strtotime($row['departure_time']))?></td> 
                                        <td><?=($row['arrival_time'] != '') ? date('d-m-Y H:i:s', strtotime($row['arrival_time'])) : 'Update after you arrive'?></td> 
                                        <td><?=$row['km_before']. ' '. 'KM'?></td> 
                                        <td><?=($row['km_after'] != "") ? $row['km_after']. ' '. 'KM': 'Update after you arrive'?></td> 
                                        <td><a href="edit_trip.php?id=<?=$row['tripID']?>"><i class="bx bx-edit icon update"></i></a></td>
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