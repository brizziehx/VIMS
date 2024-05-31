<?php 
    session_start();
    if(isset($_SESSION['uniqueID'])) {
        header('location: dashboard/');
    }

    $errors = ['email'=>'','password'=>'','error'=>''];

    if(isset($_POST['submit'])) {
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $_SESSION['password'] = $password;

        if(empty($email)) {
            $errors['email'] = "email address is required";
        } else {
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = "Please enter a valid email";
            } else {
                try {
                    require('./db/pdo.php');

                    $stmt = $dbCnx->prepare('SELECT * FROM users WHERE email = :email');
                    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
                    $stmt->execute();

                    if(!$stmt->rowCount() > 0) {
                        $errors['email'] = "Email address doesn't exists";
                    }else {
                        $row = $stmt->fetch();
                        if(!password_verify($password, $row['password'])) {
                            $errors['password'] = "Password you entered is incorrect";
                        }
                    }
                } catch (Exception $e) {
                    $errors['error'] = $e->getMessage();
                } finally {
                    $dbCnx = null;
                }
            }
        }

        if(empty($password)) {
            $errors['password'] = "Password is required";
        }

        if(!array_filter($errors)) {
            try {
                require('./db/pdo.php');
                $stmt = $dbCnx->prepare('SELECT * FROM users WHERE email = :email');
                $stmt->bindValue(':email', $email, PDO::PARAM_STR);
                $stmt->execute();
                $row = $stmt->fetch();

                date_default_timezone_set('Africa/Nairobi');
                $login_time = date("Y-m-d H:i:s");
    
                $stmt2 = $dbCnx->prepare("UPDATE users SET login_time = :login_time WHERE userID = :userID");
                $stmt2->bindValue(':login_time', $login_time, PDO::PARAM_STR);
                $stmt2->bindValue(':userID', $row['userID']);
                $stmt2->execute();

                if($_SESSION['password'] === strtoupper($row['lastname'])) {
                    $_SESSION['change'] = $_SESSION['password'];
                } else {
                    unset($_SESSION['change']);
                    unset($_SESSION['password']);
                }

                if($row['usertype'] === 'administrator') {
                    $_SESSION['uniqueID'] = $row['userID'];
                    $_SESSION['administrator'] = $row['firstname'].' '.$row['lastname'];
                    header('location: ./dashboard/');
                }elseif($row['usertype'] === 'vehicle manager') {
                    $_SESSION['uniqueID'] = $row['userID'];
                    $_SESSION['vmanager'] = $row['firstname'].' '.$row['lastname'];
                    header('location: ./dashboard/');
                } elseif($row['usertype'] === 'driver') {
                    $_SESSION['uniqueID'] = $row['userID'];
                    $_SESSION['driver'] = $row['firstname'].' '.$row['lastname'];
                    header('location: ./dashboard/');
                }
            } catch (PDOException $e) {
                $errors['error'] = $e->getMessage();
            } finally {
                $dbCnx = null;
            }
        }
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Vehicle Information Management System</title>
    <link rel="stylesheet" href="./css/styles.css">
    <link rel="stylesheet" href="./boxicons/css/boxicons.min.css">
    <style>
        ::-webkit-scrollbar {
            width: 0;
        }
    </style>
</head>
<body style="background: #fff">
    <div class="login-container">
        <div class="left">
            <img src="./assets/Cyberpunk2077_Love_this_town_RGB-en.jpg" alt="">
            <h1>VIMS</h1>
        </div>
        <div class="right">
            <header>
                <h1>VIMS</h1>
            </header>
            <form action="" method="post">
                <h3>Login</h3>
                <div class="input">
                    <label>Email Address</label>
                    <input type="text" name="email" value="<?=$email ?? ''?>">
                    <p><?=$errors['email']?></p>
                </div>
                <div class="input">
                    <label>Password</label>
                    <input type="password" id="password" class="password" name="password" value="<?=$password ?? ''?>">
                    <span id="show">show</span>
                    <p><?=$errors['password']?></p>
                </div>
                <div style="margin-bottom: 20px;" class="input button">
                    <input type="submit" name="submit" value="Login">
                    <p><?=$errors['error']?></p>
                </div>
                <p>Forgot Password? Reset <a href="forgot.php">Here</a></p>
            </form>
        </div>
    </div>
    <script>
        const show = document.querySelector('#show');
        const passwordField = document.querySelector('#password');

        show.addEventListener('click', () => {
            if(passwordField.type === 'password') {
                passwordField.type = 'text'
                show.textContent = 'hide'
            } else {
                passwordField.type = 'password'
                show.textContent = 'show'
            }
        });
    </script>
</body>
</html>