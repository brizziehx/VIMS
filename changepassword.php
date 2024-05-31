<?php
    session_start();

    try {
        require('db/pdo.php');
        // $user = $conn->query("SELECT * FROM user WHERE userID = {$_SESSION['uid']}");
        // $row = $user->fetch_assoc();
        // $name = $row['firstname'].' '.$row['lastname'];
        // // $gen = ($row['gender'] == 'male') ? 'his' : 'her';

        $stmt = $dbCnx->prepare("SELECT * FROM users WHERE userID = :uid");
        $stmt->bindValue(':uid', $_SESSION['uniqueID'], PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();

    } catch(PDOException $e) {
        echo $e->getMessage();
    }


    if(!isset($_SESSION['change'])) {
        header("Location: login.php");
    }

    $errors = ['pass'=>'','pass2'=>''];
    $password = $rpassword = "";
    $reseted_pass = strtoupper($row['lastname']);

    // print_r($reseted_pass);exit;

    if(isset($_POST['update'])) {
        $password = $_POST['password'];
        $rpassword = $_POST['rpassword'];

        if(empty($password)) {
            $errors['pass'] = "New password is required";
        } elseif($password == $reseted_pass){
            $errors['pass'] = "Please choose another password";
        } elseif(!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^\da-zA-Z]).{8,}$/', $password)) {
            $errors['pass'] = "Please choose a strong password";
        }

        if(empty($rpassword)) {
            $errors['pass2'] = "Repeat password is required";
        } elseif($rpassword != $password) {
            $errors['pass2'] = "Passwords doesn't match";
        }

        if(!array_filter($errors)) {
            try {
                require('db/pdo.php');

                $password = password_hash($password, PASSWORD_BCRYPT);

                $stmt = $dbCnx->prepare("UPDATE users SET password = :pw WHERE userID = :uid");
                $stmt->bindValue(':pw', $password, PDO::PARAM_STR);
                $stmt->bindValue(':uid', $_SESSION['uniqueID'], PDO::PARAM_INT);
                if($stmt->execute()) {
                    unset($_SESSION['change']);
                    $password = "";
                    $rpassword = "";
                    // $success['updated'] = "Password Updated Successfully!";
                    $success['updated'] = "<script>updated();</script>!";
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
    <title>Update Password | Vehicle Information Management System</title>
    <link rel="stylesheet" href="./css/styles.css">
    <link rel="stylesheet" href="swal/sweetalert2.min.css">
    <script src="swal/sweetalert2.min.js"></script>
    <style>
        ::-webkit-scrollbar {
            width: 0;
        }
    </style>
</head>
<body style="background: #fff">
    <script>
            function updated() {
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: 'Password Updated',
                    showConfirmButton: false,
                    timer: 2000
                });
            }
        </script>
    <div class="login-container">
        <?php 
            if(isset($_SESSION['msg'])) {
                echo $_SESSION['msg'];
            }
            unset($_SESSION['msg']);
        ?>
        <div class="left">
            <img src="./assets/Cyberpunk2077_Love_this_town_RGB-en.jpg" alt="">
            <h1>VIMS</h1>
        </div>
        <div class="right">
            <header>
                <h1>VIMS</h1>
            </header>
            <form action="" method="post">
                <h3>Update Password</h3>
                <div class="input">
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
                    <input type="submit" name="update" value="Update Password">
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