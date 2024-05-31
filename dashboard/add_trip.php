<?php
    session_start();
    date_default_timezone_set('Africa/Nairobi');
    
    if(!isset($_SESSION['driver'])) {
        header('location: ../login.php');
    }

    if(isset($_SESSION['change'])) {
        header('location: ../changepassword.php');
    }

    $errors = [];
    date_default_timezone_set('Africa/Nairobi');
    $departure_time = date('Y-m-d H:i:s');

    if(isset($_POST['submit'])) {
        $depart_from = $_POST['depart_from'] ?? '';
        $arrive_in = $_POST['arrive_in'] ?? '';
        $departure_time = $_POST['departure_time'];
        // $arrival_time = $_POST['arrival_time'] ?? ''; 
        $odometer_before = trim($_POST['odometer_before']);
        // $odometer_after = $_POST['odometer_after'] ?? '';
        date_default_timezone_set('Africa/Nairobi');
        $created_at = date('Y-m-d H:i:s');

        if(empty($depart_from)) {
            $errors['depart_from'] = 'Depart from is required';
        } else {
            try {
                require('../db/pdo.php');

                $stmt2 = $dbCnx->prepare('SELECT * FROM trips WHERE driverID = :driverID ORDER BY tripID DESC');
                $stmt2->bindValue(':driverID', $_SESSION['uniqueID'], PDO::PARAM_INT);
                $stmt2->execute();

                if($stmt2->rowCount() > 0) {
                    $row2 = $stmt2->fetch();
                    if($depart_from == $row2['depart_from']) {
                        $errors['depart_from'] = "Please select a valid region! You're in {$row2['arrive_in']} now";
                    }
                }

                
        
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }

        if(empty($arrive_in)) {
            $errors['arrive_in'] = 'Arrive in is required';
        } else {
            if($depart_from == $arrive_in) {
                $errors['arrive_in'] = 'Depart from and arrive in can\'t be the same';
            }
        }

        if(empty($departure_time)) {
            $errors['departure_time'] = 'departure date and time is required';
        }

        if(empty($odometer_before)) {
            $errors['odometer_before'] = 'Odometer reading before trip is required';
        } else {
            
            if(!preg_match("/^[0-9]+$/", $odometer_before)) {
                $errors['odometer_before'] = 'Please enter valid Kilometres';
            } else {
                try {
                    require('../db/pdo.php');
    
                    $stmt2 = $dbCnx->prepare('SELECT * FROM trips WHERE driverID = :driverID ORDER BY tripID DESC');
                    $stmt2->bindValue(':driverID', $_SESSION['uniqueID'], PDO::PARAM_INT);
                    $stmt2->execute();
    
                    if($stmt2->rowCount() > 0) {
                        $row2 = $stmt2->fetch();
                        if($odometer_before < $row2['km_after']) {
                            $errors['odometer_before'] = "Please enter a valid odometer reading! Last recorded KMs was {$row2['km_after']}";
                        }
                    }
    
                    
            
                } catch (PDOException $e) {
                    echo $e->getMessage();
                }
            }
        }

        // if(empty($arrival_time)) {
        //     $errors['arrival_time'] = "Arrival date and time is required";
        // }

        

        if(!array_filter($errors)) {
            try {
                require('../db/pdo.php');

                $query = $dbCnx->query("SELECT vehicleID, driverID from vehicle WHERE driverID = {$_SESSION['uniqueID']}");
                $row = $query->fetch();

                $stmt = $dbCnx->prepare('INSERT INTO trips(depart_from, arrive_in, km_before, departure_time, created_at, vehicleID, driverID) VALUES (:df, :ai, :kb, :dt, :ca, :vid, :did)');
                $stmt->bindValue(':df', $depart_from, PDO::PARAM_STR);
                $stmt->bindValue(':ai', $arrive_in, PDO::PARAM_STR);
                $stmt->bindValue(':kb', $odometer_before, PDO::PARAM_STR);
                $stmt->bindValue(':dt', $departure_time, PDO::PARAM_STR);
                $stmt->bindValue(':ca', $created_at, PDO::PARAM_STR);
                $stmt->bindValue(':vid', $row['vehicleID'], PDO::PARAM_INT);
                $stmt->bindValue(':did', $row['driverID'], PDO::PARAM_INT);

                if($stmt->execute()) {
                    $depart_from = $arrive_in = $odometer_before = $departure_time = "";
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
    
    <title>Trip Registration | Vehicle Information Management System</title>
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
                title: 'Trip Details Added Successfully! \nHave A Safe Journey',
                showConfirmButton: false,
                timer: 4000
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
                        <a href="index.php">Dashboard</a> > <a href="my_routes.php" id="sec">Routes and Trips</a> > <span>New Trip</span>
                    </div>
                </div>
                <div class="bottom-card">
                    <form action="" method="post" enctype="multipart/form-data">
                    <p style="border-bottom: 1px #eee solid; padding-bottom:5px;margin-bottom:5px">Trip Registration</p>
                        <div class="input_row">
                            <div class="inputBox">
                                <span>Depart From</span>
                                <select name="depart_from">
                                    <option disabled selected>Select depart from..</option>
                                    <?php
                                        try {
                                            require('../db/pdo.php');

                                            $stmt = $dbCnx->prepare('SELECT vehicle.*, routes.* FROM routes INNER JOIN vehicle ON vehicle.routeID = routes.routeID WHERE vehicle.driverID = :driverID');
                                            $stmt->bindValue(':driverID', $_SESSION['uniqueID'], PDO::PARAM_INT);
                                            $stmt->execute();
                                            $rows = $stmt->fetchAll();

                                            foreach($rows as $row): ?>
                                            <option value="<?=$row['start']?>"><?=$row['start']?></option>
                                            <option value="<?=$row['to_']?>"><?=$row['to_']?></option>

                                        <?php endforeach;
                                        } catch(PDOException $e) {
                                            echo $e->getMessage();
                                        }
                                    ?>
                                </select>
                                <p><?=$errors['depart_from'] ?? ''?></p>
                            </div>
                            <div class="inputBox">
                                <span>Arrive In</span>
                                <select name="arrive_in">
                                    <option disabled selected>Select arrive in..</option>
                                    <?php
                                        try {
                                            require('../db/pdo.php');

                                            $stmt = $dbCnx->prepare('SELECT vehicle.*, routes.* FROM routes INNER JOIN vehicle ON vehicle.routeID = routes.routeID WHERE vehicle.driverID = :driverID');
                                            $stmt->bindValue(':driverID', $_SESSION['uniqueID'], PDO::PARAM_INT);
                                            $stmt->execute();
                                            $rows = $stmt->fetchAll();

                                            foreach($rows as $row): ?>
                                            <option value="<?=$row['start']?>"><?=$row['start']?></option>
                                            <option value="<?=$row['to_']?>"><?=$row['to_']?></option>

                                        <?php endforeach;
                                        } catch(PDOException $e) {
                                            echo $e->getMessage();
                                        }
                                    ?>
                                </select>
                                <p><?=$errors['arrive_in'] ?? ''?></p>
                            </div>
                            <div class="inputBox">
                                <span>Departure Time</span>
                                <input type="datetime-local" readonly name="departure_time" value="<?=$departure_time ?? ''?>">
                                <p><?=$errors['departure_time'] ?? ''?></p>
                            </div>
                        </div>
                        <div class="input_row">
                            <div class="inputBox">
                                <span>Arrival Time</span>
                                <input type="datetime-local" name="arrival_time" disabled>
                                <p style="color: #999;"><?=$errors['arrival_time'] ?? 'This option will be available after you arrive'?></p>
                            </div>
                            <div class="inputBox">
                                <span>Odometer Reading Before Trip</span>
                                <input type="text" name="odometer_before">
                                <p><?=$errors['odometer_before'] ?? ''?></p>
                            </div>
                            <div class="inputBox">
                                <span>Odometer Reading After Trip.</span>
                                <input type="text" disabled name="odometer_after">
                                <p style="color: #999;"><?=$errors['odometer_after'] ?? 'This option will be available after you arrive'?></p>
                            </div>
                        </div>
                        <div class="input_row" style="justify-content: flex-start;">
                            <div class="inputBoxText">
                                <span>Trip Details</span>
                                <textarea name="details" cols="40" rows="5" disabled></textarea>
                                <p style="color: #999; font-size: small;margin-top:5px;margin-bottom:10px"><?=$errors['arrival_time'] ?? 'This option will be available after you arrive'?></p>
                            </div>
                        </div>
                        <div class="input_row">
                            <div class="inputBox">
                                <input type="submit" name="submit" value="Register Trip"> 
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