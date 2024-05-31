<?php 

    $errors = ['firstname'=>'','lastname'=>'','email'=>'','password'=>'','error'=>'', 'success'=>''];

    if(isset($_POST['submit'])) {
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $created_at = date('Y-m-d H:i:s');

        if(empty($firstname)) {
            $errors['firstname'] = 'Firstname is required';
        }

        if(empty($lastname)) {
            $errors['lastname'] = 'Lastname is required';
        }

        if(empty($email)) {
            $errors['email'] = 'Email is required';
        } else {
            try {
                require('./db/pdo.php');
                
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

        if(empty($password)) {
            $errors['password'] = 'Firstname is required';
        }

        if(!array_filter($errors)) {
            try {
                require('./db/pdo.php');

                $password = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $dbCnx->prepare('INSERT INTO users(firstname, lastname, email, password, created_at) VALUES (:firstname, :lastname, :email, :password, :created_at)');
                $stmt->bindValue(':firstname', $firstname, PDO::PARAM_STR);
                $stmt->bindValue(':lastname', $lastname, PDO::PARAM_STR);
                $stmt->bindValue(':email', $email, PDO::PARAM_STR);
                $stmt->bindValue(':password', $password, PDO::PARAM_STR);
                $stmt->bindValue(':created_at', $created_at, PDO::PARAM_STR);

                if($stmt->execute()) {
                    $firstname = '';
                    $lastname = '';
                    $email = '';
                    $password = '';
                    // echo "success";
                    $errors['success'] = '<script>success()</script>';
                }
                
            } catch(PDOException $e) {
                $msg = json_encode($e->getMessage());
                $errors['error'] = '<script>error($msg)</script>';
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup | Vehicle Information Management System</title>
    <link rel="stylesheet" href="./css/styles.css">
    <link rel="stylesheet" href="./boxicons/css/boxicons.min.css">
    <script src="swal/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="swal/sweetalert2.css">
    <style>
        ::-webkit-scrollbar {
            width: 0;
        }
    </style>
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
    <div class="login-container">
        <div class="left">
            <img src="./assets/Cyberpunk2077_Love_this_town_RGB-en.jpg" alt="">
            <!-- <h1>VIMS</h1> -->
        </div>
        <div class="right">
            <header style="height: 50px; padding: 10px">
                <h1>VIMS</h1>
            </header>
            <form action="" method="post">
                <h3>Signup</h3>
                <div class="input">
                    <label>Firstname</label>
                    <input type="text" name="firstname">
                    <p><?=$errors['firstname']?></p>
                </div>
                <div class="input">
                    <label>Lastname</label>
                    <input type="text" name="lastname">
                    <p><?=$errors['lastname']?></p>
                </div>
                <div class="input">
                    <label>Email Address</label>
                    <input type="text" name="email">
                    <p><?=$errors['email']?></p>
                </div>
                <div class="input">
                    <label>Password</label>
                    <input type="password" id="password" class="password" name="password">
                    <span id="show">show</span>
                    <p><?=$errors['password']?></p>
                </div>
                <div class="input button">
                    <input type="submit" name="submit" value="Signup">
                    <?=$errors['error']?>
                    <?=$errors['success']?>
                </div>
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