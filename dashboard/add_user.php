<?php
    session_start();

    if(!isset($_SESSION['administrator']) && !isset($_SESSION['vmanager'])) {
        header('location: ../login.php');
    }

    if(isset($_SESSION['change'])) {
        header('location: ../changepassword.php');
    }

    $errors = ['firstname'=>'','middlename'=>'','lastname'=>'','email'=>'','phone'=>'','usertype'=>'','gender'=>'','image'=>''];
    $firstname = $middlename = $lastname = $email = $phone = "";
    if(isset($_POST['submit'])) {
        $firstname = trim($_POST['firstname']);
        $middlename = trim($_POST['middlename']);
        $lastname = trim($_POST['lastname']);
        $email = trim($_POST['email']);
        $imageName = $_FILES['image']['name'];
        $gender = $_POST['gender'] ?? '';
        $phone = trim($_POST['phone']);
        $usertype = $_POST['usertype'] ?? '';
        date_default_timezone_set('Africa/Nairobi');
        $created_at = date('Y-m-d H:i:s');


        if(empty($firstname)) {
            $errors['firstname'] = 'Firstname is required';
        } else {
            if(!preg_match("/^[a-zA-Z']{3,30}$/", $firstname)) {
                $errors['firstname'] = "Firstname is not valid name";
            }
        }

        if(empty($middlename)) {
            $errors['middlename'] = 'Middlename is required';
        } else {
            if(!preg_match("/^[a-zA-Z']{3,30}$/", $middlename)) {
                $errors['middlename'] = "Middlename is not valid name";
            }
        }

        if(empty($lastname)) {
            $errors['lastname'] = 'Lastname is required';
        } else {
            if(!preg_match("/^[a-zA-Z']{3,30}$/", $lastname)) {
                $errors['lastname'] = "Lastname is not valid name";
            }
        }

        if(empty($phone)) {
            $errors['phone'] = "Phone number is required";
        } else {
            if(!preg_match("/^[0-9]{10}$/", $phone)) {
                $errors['phone'] = "Please enter a valid number";
            }
        }

        if(empty($usertype)) {
            $errors['usertype'] = "Usertype is required";
        }

        if(empty($email)) {
            $errors['email'] = 'Email is required';
        } else {
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = "Email is not valid";
            } else {
                try {
                    require('../db/pdo.php');
                    
                    $stmt = $dbCnx->prepare('SELECT * FROM users WHERE email = :email');
                    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
                    $stmt->execute();
    
                    if($stmt->rowCount() > 0) {
                        $errors['email'] = 'Email already exists';
                    }
                } catch(PDOException $e) {
                    $errors['email'] = $e->getMessage();
                }
            }
        }

        if(empty($imageName)) {
            $errors['image'] = "An image is required";
        } else {
            $tmpName = $_FILES['image']['tmp_name'];
            $extensions = ['png','jpg','jpeg'];
            $image_explode = explode('.', $imageName);
            $exp_extension = strtolower(end($image_explode));
            if(!in_array($exp_extension, $extensions)) {
                $errors['image'] = "Please insert a valid image";
            }
        }
    
        if(empty($gender)) {
            $errors['gender'] = "Gender is required";
        }

        switch($gender) {
            case 'Male':
                $male = "selected";
                $female = "";
            break;
            case 'Female':
                $female = "selected";
                $male = "";
            break;
            default:
                $male = "";
                $female = "";
        }
        
        switch($usertype) {
            case 'administrator':
                $administrator = "selected";
                $driver = "";
                $vmanager = "";
            break;
            case 'driver':
                $driver = "selected";
                $vmanager = "";
                $administrator = "";
            break;
            case 'vehicle manager':
                $vmanager = "selected";
                $driver = "";
                $administrator = "";
            break;
            default:
                $vmanager = "";
                $driver = "";
                $administrator = "";
        }

        if(!array_filter($errors)) {
            try {
                require('../db/pdo.php');

                $password = password_hash(strtoupper($lastname), PASSWORD_BCRYPT);
                $newImgName = time().$imageName;
                $stmt = $dbCnx->prepare('INSERT INTO users(firstname, middlename, lastname, email, password, phone, image, gender, usertype, created_at) VALUES (:firstname, :middlename, :lastname, :email, :password, :phone, :image, :gender, :usertype, :created_at)');
                $stmt->bindValue(':firstname', $firstname, PDO::PARAM_STR);
                $stmt->bindValue(':middlename', $middlename, PDO::PARAM_STR);
                $stmt->bindValue(':lastname', $lastname, PDO::PARAM_STR);
                $stmt->bindValue(':email', $email, PDO::PARAM_STR);
                $stmt->bindValue(':password', $password, PDO::PARAM_STR);
                $stmt->bindValue(':phone', $phone, PDO::PARAM_STR);
                $stmt->bindValue(':image', $newImgName, PDO::PARAM_STR);
                $stmt->bindValue(':gender', $gender, PDO::PARAM_STR);
                $stmt->bindValue(':usertype', $usertype, PDO::PARAM_STR);
                $stmt->bindValue(':created_at', $created_at, PDO::PARAM_STR);

                if($stmt->execute()) {
                    $firstname = $middlename = $lastname = $email = $phone = $male = $female = $vmanager = $administrator = $driver = "";
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

    } catch (PDOException $e) {
        echo $e->getMessage();
    }


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>New user | Vehicle Information Management System</title>
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
                title: 'Account created Successfully',
                showConfirmButton: false,
                timer: 2000
            })
        }
        
        function error(error) {
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: error,
                showConfirmButton: false,
                timer: 2000
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
                        <a href="index.php">Dashboard</a> > <a href="users.php" id="sec">users</a> > <span>New User</span>
                    </div>
                </div>
                <div class="bottom-card">
                    <form action="" method="post" enctype="multipart/form-data">
                    <p style="border-bottom: 1px #eee solid; padding-bottom:5px;margin-bottom:5px">User Registration</p>
                        <div class="input_row">
                            <div class="inputBox">
                                <span>First Name</span>
                                <input type="text" name="firstname" value="<?=$firstname?>">
                                <p><?=$errors['firstname']?></p>
                            </div>
                            <div class="inputBox">
                                <span>Middle Name</span>
                                <input type="text" name="middlename" value="<?=$middlename?>">
                                <p><?=$errors['middlename']?></p>
                            </div>
                            <div class="inputBox">
                                <span>Last Name</span>
                                <input type="text" name="lastname" value="<?=$lastname?>">
                                <p><?=$errors['lastname']?></p>
                            </div>
                        </div>
                        <div class="input_row">
                            <div class="inputBox">
                                <span>Email Address</span>
                                <input type="text" name="email" value="<?=$email?>">
                                <p><?=$errors['email']?></p>
                            </div>
                            <div class="inputBox">
                                <span>Phone Number</span>
                                <input type="text" name="phone" value="<?=$phone?>">
                                <p><?=$errors['phone']?></p>
                            </div>
                            <div class="inputBox imae-upload">
                                <span>Image</span>
                                <input type="file" name="image" class="img-upload">
                                <p><?=$errors['image']?></p>
                            </div>
                        </div>
                        <div class="input_row" style="justify-content: flex-start;">
                            <div class="inputBox" style="margin-right: 20px;">
                                <span>Gender</span>
                                <select name="gender">
                                    <option selected disabled>Select gender...</option>
                                    <option <?=$male ?? ''?> value="Male">Male</option>
                                    <option <?=$female ?? ''?> value="Female">Female</option>
                                </select>
                                <p><?=$errors['gender']?></p>
                            </div>
                            <div class="inputBox">
                                <span>User Type</span>
                                <select name="usertype">
                                    <option selected disabled>Select usertype...</option>
                                    <?php if(isset($_SESSION['administrator'])): ?>
                                        <option <?=$administrator ?? '' ?> value="administrator">Administrator</option>
                                        <option <?=$vmanager ?? ''?> value="vehicle manager">Vehicle Manager</option>
                                        <option <?=$driver ?? ''?> value="driver">Driver</option>
                                    <?php else: ?>
                                        <!-- <option <?=$vmanager ?? ''?> value="vehicle manager">Vehicle Manager</option> -->
                                        <option <?=$driver ?? ''?> value="driver">Driver</option>
                                    <?php endif; ?>
                                </select>
                                <p><?=$errors['usertype']?></p>
                            </div>
                        </div>
                        <div class="input_row">
                            <div class="inputBox">
                                <input type="submit" name="submit" value="Register user"> 
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