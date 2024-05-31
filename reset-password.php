<?php
    session_start();

    if(isset($_SESSION['uniqueID'])) {
        header("location: dashboard/");
    }

    $token = $_GET["token"];

    $token_hash = hash("sha256", $token);

    try {
        require('db/pdo.php');

        $stmt = $dbCnx->prepare("SELECT * FROM users WHERE reset_token_hash = :hash");
        $stmt->bindValue(":hash", $token_hash);
        $stmt->execute();

        $row = $stmt->fetch();

        if ($row === null) {
            $_SESSION['msg'] = "<script>tokenNotFound()</script>";
        }

        // var_dump($row["reset_token_expires_at"]);exit;

        if (strtotime($row["reset_token_expires_at"]) <= time()) {
            $_SESSION['msg'] = "<script>expiredToken()</script>";
        }

    } catch(PDOException $e) {
        echo $e->getMessage();
    }


    if(isset($_SESSION['change'])) {
        header("Location: changepaassword.php");
    }

    $errors = ['pass'=>'','pass2'=>''];
    $password = $rpassword = "";


    if(isset($_POST['reset'])) {
        $password = $_POST['password'];
        $rpassword = $_POST['rpassword'];
        $token1 = $_GET["token"];
        $token_hash = hash("sha256", $token1);

        try {
            require('db/pdo.php');
    
            $stmt = $dbCnx->prepare("SELECT * FROM users WHERE reset_token_hash = :hash");
            $stmt->bindValue(":hash", $token_hash);
            $stmt->execute();
    
            $row = $stmt->fetch();
    
            if ($row === null) {
                $_SESSION['msg'] = "<script>tokenNotFound()</script>";
            }
    
            if (strtotime($row["reset_token_expires_at"]) <= time()) {
                $_SESSION['msg'] = "<script>expiredToken()</script>";
            }
    
        } catch(PDOException $e) {
            echo $e->getMessage();
        }

        $token = $_POST["token"];

        $token_hash = hash("sha256", $token);

        if(empty($password)) {
            $errors['pass'] = "New password is required";
        } elseif(!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^\da-zA-Z]).{8,}$/', $password)) {
            $errors['pass'] = "Please choose a strong password";
        }

        if(empty($rpassword)) {
            $errors['pass2'] = "Confirmation password is required";
        } elseif($rpassword != $password) {
            $errors['pass2'] = "Passwords doesn't match";
        }

        if(!array_filter($errors)) {
            try {
                require('db/pdo.php');
                date_default_timezone_set('Africa/Nairobi');
                $password = password_hash($password, PASSWORD_BCRYPT);

                $stmt = $dbCnx->prepare("UPDATE users SET password = :pw, reset_token_hash = NULL, reset_token_expires_at = NULL WHERE userID = :userID");
                $stmt->bindValue(':pw', $password, PDO::PARAM_STR);
                $stmt->bindValue(':userID', $row['userID'], PDO::PARAM_INT);
                if($stmt->execute()) {
                    $password = "";
                    $rpassword = "";
                    $_SESSION['msg'] = "<script>success()</script>";
                }
            } catch(PDOException $e) {
                echo $e->getMessage();
            }
        }
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | Vehicle Information Management System</title>
    <link rel="stylesheet" href="./css/styles.css">
    <script src="swal/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="swal/sweetalert2.min.css">
    <style>
        ::-webkit-scrollbar {
            width: 0;
        }
    </style>
</head>
<body style="background: #fff">
<script>
        function success() {
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Password Reseted Successfully \nYou can now login',
                showConfirmButton: true,
            }).then(() => {
                location.href = 'login.php';
            })
        }

        function expiredToken() {
            Swal.fire({
                position: 'center',
                icon: 'error',
                title: 'Link has expired, Please reset password again',
                showConfirmButton: true,
            }).then(() => {
                location.href = 'forgot.php';
            })
        }

        function tokenNotFound() {
            Swal.fire({
                position: 'center',
                icon: 'error',
                title: 'Token not found',
                showConfirmButton: true,
            })
        }
    </script>
    <?php if(isset($_SESSION['msg'])) {
        echo $_SESSION['msg'];
    }
    unset($_SESSION['msg']);
    ?>
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
                <h3>Reset Your Password</h3>
                <div class="input">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                    <label>New Password</label>
                    <input type="password" id="password" class="password" name="password" value="<?=$password ?? ''?>">
                    <span id="show">show</span>
                    <p><?=$errors['pass'] ?? ''?></p>
                </div>
                <div class="input">
                    <label>Repeat Password</label>
                    <input type="password" id="rpassword" class="password" name="rpassword" value="<?=$rpassword ?? ''?>">
                    <span id="confirm">...</span>
                    <p><?=$errors['pass2'] ?? ''?></p>
                </div>
                
                <div style="margin-bottom: 20px;" class="input button">
                    <input type="submit" name="reset" value="Reset Password">
                </div>
                <div class="suc" style="display: none;">
                    <?php
                        if(isset($success['updated'])) {
                            echo $success['updated'];
                            echo "<script> setTimeout(()=> location.href = 'dashboard/index.php', 2100)</script>";
                        }
                    ?>
                </div>
            </form>
        </div>
    </div>
    <script>

        const show = document.querySelector('#show')
        const passwordField = document.querySelector('#password')
        const rpasswordField = document.querySelector('#rpassword')
        const matched = document.querySelector('#confirm')

        show.addEventListener('click', () => {
            if(passwordField.type === 'password') {
                passwordField.type = 'text'
                show.textContent = 'hide'
            } else {
                passwordField.type = 'password'
                show.textContent = 'show'
            }
        });

        rpasswordField.addEventListener('keyup', () => {
            (rpasswordField.value.length >= 5) ? (passwordField.value == rpasswordField.value && passwordField.value !== '') ? matched.textContent  = 'matched' : matched.textContent  = '' :  matched.textContent  = ''
        })

        passwordField.addEventListener('keyup', () => {
            (rpasswordField.value.length >= 5) ? (passwordField.value == rpasswordField.value && passwordField.value !== '') ? matched.textContent  = 'matched' : matched.textContent  = '' :  matched.textContent  = ''
        })
    </script>
</body>
</html>