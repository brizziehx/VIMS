<?php
    session_start();

    if(!isset($_SESSION['administrator']) && !isset($_SESSION['vmanager'])) {
        header('location: ../login.php');
    }

    if(isset($_SESSION['change'])) {
        header('location: ../changepassword.php');
    }

    $errors = [];

    if(isset($_POST['submit'])) {
       $litres = $_POST['litres'];
       $cost = $_POST['cost'];
       $purchased = $_POST['purchased'];
       $vehicleID = $_POST['vehicleID'] ?? '';
       $imageName = $_FILES['image']['name'];
        date_default_timezone_set('Africa/Nairobi');
        $created_at = date('Y-m-d H:i:s');


        if(empty($litres)) {
            $errors['litres'] = 'Litres are required';
        } else {
            if(!preg_match("/^\d+\.?\d*$/", $litres)) {
                $errors['litres'] = "Please enter a valid litres";
            }
        }

        if(empty($cost)) {
            $errors['cost'] = 'Cost is required';
        } else {
            if(!preg_match("/^[0-9]{4,10}$/", $cost)) {
                $errors['cost'] = "Please enter a valid fuel cost";
            }
        }

        if(empty($purchased)) {
            $errors['purchased'] = 'Purchased date is required';
        } else {
            if($purchased > date('Y-m-d')){
                $errors['purchased'] = 'Purchased date is not valid';
            }
        }

        if(empty($vehicleID)) {
            $errors['vehicleID'] = "Vehicle reg. number is required";
        } 

        if(empty($imageName)) {
            $errors['image'] = "An image of receipt is required";
        } else {
            $tmpName = $_FILES['image']['tmp_name'];
            $extensions = ['png','jpg','jpeg'];
            $image_explode = explode('.', $imageName);
            $exp_extension = strtolower(end($image_explode));
            if(!in_array($exp_extension, $extensions)) {
                $errors['image'] = "Please insert a valid image";
            }
        }
    
        if(!array_filter($errors)) {
            try {
                require('../db/pdo.php');

                $newImgName = time().$imageName;
                $stmt = $dbCnx->prepare('INSERT INTO fuel(litres, cost, purchased_on, vehicleID, image, created_at) VALUES (:litres, :cost, :purchased, :vehicleID, :image, :created_at)');
                $stmt->bindValue(':litres', $litres, PDO::PARAM_STR);
                $stmt->bindValue(':cost', $cost, PDO::PARAM_STR);
                $stmt->bindValue(':purchased', $purchased, PDO::PARAM_STR);
                $stmt->bindValue(':vehicleID', $vehicleID, PDO::PARAM_INT);
                $stmt->bindValue(':image', $newImgName, PDO::PARAM_STR);
                $stmt->bindValue(':created_at', $created_at, PDO::PARAM_STR);

                if($stmt->execute()) {
                    $litres = $cost = $purchases = $vehicleID = $phone = "";
                    move_uploaded_file($tmpName, '../images/'.$newImgName);
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

        $stmt2 = $dbCnx->prepare('SELECT * FROM fuel WHERE fuelID = :fuelID');
        $stmt2->bindValue(':fuelID', $_REQUEST['id'], PDO::PARAM_INT);
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
    
    <title>Update Fuel Details | Vehicle Information Management System</title>
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
                title: 'Fuel details has been updated Successfully',
                showConfirmButton: false,
                timer: 2500
            })
        }
        
        function error(error) {
            Swal.fire({
                position: 'center',
                icon: 'error',
                title: error,
                showConfirmButton: true
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
                    <li class="active">
                        <a href="fuel.php"><i class="bx bx-gas-pump"></i>Fuel</a>
                    </li>
                    <li>
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
                        <a href="index.php">Dashboard</a> > <a href="fuel.php" id="sec">Fuel</a> > <span>Edit Fuel Details</span>
                    </div>
                </div>
                <div class="bottom-card">
                    <form action="" method="post" enctype="multipart/form-data">
                    <p style="border-bottom: 1px #eee solid; padding-bottom:5px;margin-bottom:5px">Edit Details</p>
                        <div class="input_row">
                            <div class="inputBox">
                                <span>Fuel in Litres</span>
                                <input type="text" name="litres" value="<?=$row2['litres'] ?? ''?>" placeholder="Eg. 12.5">
                                <p><?=$errors['litres'] ?? ''?></p>
                            </div>
                            <div class="inputBox">
                                <span>Fuel Cost</span>
                                <input type="text" name="cost" value="<?=$row2['cost'] ?? ''?>" placeholder="Eg. 120000">
                                <p><?=$errors['cost'] ?? ''?></p>
                            </div>
                            <div class="inputBox">
                                <span>Purchased on</span>
                                <input type="date" name="purchased" value="<?=$row2['purchased_on'] ?? ''?>">
                                <p><?=$errors['purchased'] ?? ''?></p>
                            </div>
                        </div>
                        <div class="input_row" style="justify-content:left;">
                            <div class="inputBox">
                                <span>Vehicle Registration No.</span>
                                <select name="vehicleID" id="vehicleID">
                                    <!-- <option selected disabled>Select vehicle registration no...</option> -->
                                    <?php
                                        try {
                                            require('../db/pdo.php');
                                            $stmt = $dbCnx->prepare('SELECT * FROM vehicle');
                                            $stmt->execute();
                                            $rows = $stmt->fetchAll();

                                            foreach($rows as $row): 
                                               if($row2['vehicleID'] == $row['vehicleID']) {
                                                $vid = 'selected';
                                               } else {
                                                $vid = '';
                                               }
                                            ?>
                                                
                                                <option <?=$vid?> value="<?=$row['vehicleID']?>"><?=$row['registration_no']?></option>
                                            <?php endforeach;
                                        } catch (PDOException $e) {
                                            echo $e->getMessage();
                                        }
                                    ?>
                                </select>
                                <p><?=$errors['vehicleID'] ?? ''?></p>
                            </div>
                            <div class="inputBox" style="margin-left: 22px;">
                                <span>Upload Receipt Image</span>
                                <input type="file" name="image">
                                <p><?=$errors['image'] ?? ''?></p>
                            </div>
                            
                        </div>
                        
                        <div class="input_row">
                            <div class="inputBox">
                                <input type="submit" name="submit" value="Register Fuel"> 
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