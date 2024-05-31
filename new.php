<?php
    session_start();
    require_once('conn/conn.php');
    error_reporting(0);

    if(isset($_SESSION['uid'])) {
        header("location: dashboard/index.php");
    }

    $token = $_GET["token"];

    $token_hash = hash("sha256", $token);

    $sql = "SELECT * FROM user WHERE reset_token_hash = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("s", $token_hash);

    $stmt->execute();

    $result = $stmt->get_result();

    $user = $result->fetch_assoc();

    if ($user === null) {
        $_SESSION['msg'] = "<script>tokenNotFound()</script>";
    }

    if (strtotime($user["reset_token_expires_at"]) <= time()) {
        $_SESSION['msg'] = "<script>expiredToken()</script>";
    }

    $errors = [];


if(isset($_POST['submit'])) {
    $password = $_POST["password"];
    $cpassword = $_POST['password_confirmation'];
    $token1 = $_GET["token"];

    $token_hash = hash("sha256", $token1);

    $sql = "SELECT * FROM user WHERE reset_token_hash = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("s", $token_hash);

    $stmt->execute();

    $result = $stmt->get_result();

    $user = $result->fetch_assoc();

    if ($user === null) {
        $_SESSION['msg'] = "<script>tokenNotFound()</script>";
    }

    if (strtotime($user["reset_token_expires_at"]) <= time()) {
        $_SESSION['msg'] = "<script>expiredToken()</script>";
    }

    $token = $_POST["token"];

    $token_hash = hash("sha256", $token);

    if(empty($password)) {
        $errors['pass'] = "Enter new password";
    } else {
        if(!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^\da-zA-Z]).{8,}$/', $password)) {
            $errors['pass'] = "Please choose a strong password";
        }
    }

    if(empty($cpassword)) {
        $errors['cpass'] = "Enter confirmation password";
    } else {
        if($password !== $cpassword) {
            $errors['cpass'] = "Passwords do not match";
        }
    }

    if(!array_filter($errors)) {
        $password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);

        $sql = "UPDATE users SET password = :pw, reset_token_hash = NULL, reset_token_expires_at = NULL WHERE userID = :userID";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param("si", $password_hash, $user["userID"]);

        $stmt->execute();

        $_SESSION['msg'] = "<script>success()</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | UMS</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="swal/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="swal/sweetalert2.css">

</head>
<body>
    <script>
        function success() {
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Password updated. You can now login',
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
    <div class="login-container">
        <?php if(isset($_SESSION['msg'])) {
                echo $_SESSION['msg'];
            }
            unset($_SESSION['msg']);
        ?>
        <form action="" method="post" autocomplete="off">
            <h3>Reset Password | SUMS</h3>
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
    
            <label for="password">New password</label>
            <input type="password" value="<?=$password ?? ''?>" name="password">
            <div class="err"><?=$errors['pass'] ?? ''?></div>
            <label for="password_confirmation">Confirm New password</label>
            <input type="password" value="<?=$cpassword ?? ''?>" name="password_confirmation">
            <div class="err"><?=$errors['cpass'] ?? ''?></div>
            <input type="submit" class="login-btn" value="RESET" name="submit">
        </form>
    </div>
</body>
</body>
</html>