<?php
    session_start();
    error_reporting(0);
    require_once('db/pdo.php');

    if(isset($_SESSION['uid'])) {
        header("location: dashboard/index.php");
    }

    $errors = [];

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require 'PHPMailer/Exception.php';
    require 'PHPMailer/PHPMailer.php';
    require 'PHPMailer/SMTP.php';

if(isset($_POST['submit'])) {

    $email = trim($_POST["email"]);

    if(empty($email)) {
        $errors['email'] = "Email is required";
    } else {
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Please enter a valid email";
        } else {
            $users = $conn->prepare("SELECT * FROM user WHERE email = :email");
            $users->bindValue(':email', $email, PDO::PARAM_STR);
            $users->execute();
            $row = $users->fetch(PDO::FETCH_ASSOC);

            if($email !== $row['email']) {
                $errors['email'] = "Email does't exists";
            }
        }
    }

    

    if(!array_filter($errors)) {
        $token = bin2hex(random_bytes(16));

        $token_hash = hash("sha256", $token);

        $expiry = date("Y-m-d H:i:s", time() + 60 * 30);

        $sql = "UPDATE user SET reset_token_hash = :reset_token_hash, reset_token_expires_at = :reset_token_expires_at WHERE email = :email";

        $stmt = $conn->prepare($sql);

        $stmt->bindValue(":reset_token_hash", $token_hash, PDO::PARAM_STR);
        $stmt->bindValue(':reset_token_expires_at', $expiry, PDO::PARAM_STR);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);

        $stmt->execute();

        if ($stmt->rowCount()) {
            $mail = new PHPMailer(true);

            $mail->isSMTP();
            $mail->SMTPAuth = true;
        
            $mail->Host = "smtp.gmail.com";
            $mail->SMTPAuth = true;
            $mail->Username = "sumstz@gmail.com";
            $mail->Password = "ukqoortwjqeaivmh";
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;
        
            $mail->setFrom("noreply@sums.co.tz");
            $mail->addAddress($email);
            $mail->isHtml(true);
            $mail->Subject = "Password Reset";
            $mail->addEmbeddedImage('inc/SUMS.png', 'sums');
            $mail->Body = "
                
                <!doctype html>
                <html>
                <head>
                    <title>Reset Password Email Template</title>
                    <meta name=\"description\" content=\"Reset Password.\">
                    <style type=\"text/css\">
                        a:hover {text-decoration: underline !important;}
                    </style>
                </head>

                <body marginheight=\"0\" topmargin=\"0\" marginwidth=\"0\" style=\"margin: 0px; background-color: #f2f3f8;\" leftmargin=\"0\">
                    <!--100% body table-->
                    <table cellspacing=\"0\" border=\"0\" cellpadding=\"0\" width=\"100%\" bgcolor=\"#f2f3f8\"
                        style=\"@import url(https://fonts.googleapis.com/css?family=Rubik:300,400,500,700|Open+Sans:300,400,600,700); font-family: 'Open Sans', sans-serif;\">
                        <tr>
                            <td>
                                <table style=\"background-color: #f2f3f8; max-width:670px;  margin:0 auto;\" width=\"100%\" border=\"0\"
                                    align=\"center\" cellpadding=\"0\" cellspacing=\"0\">
                                    <tr>
                                        <td style=\"height:80px;\">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td style=\"text-align:center;\">
                                        <a href=\"http://localhost/ums/\" title=\"logo\" target=\"_blank\">
                                            <img src='cid:sums' width=\"200\" title=\"logo\" alt=\"logo\">
                                        </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style=\"height:20px;\">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <table width=\"95%\" border=\"0\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\"
                                                style=\"max-width:670px;background:#fff; border-radius:3px; text-align:center;-webkit-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);-moz-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);box-shadow:0 6px 18px 0 rgba(0,0,0,.06);\">
                                                <tr>
                                                    <td style=\"height:40px;\">&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td style=\"padding:0 35px;\">
                                                        <h1 style=\"color:#1e1e2d; font-weight:500; margin:0;font-size:32px;font-family:'Rubik',sans-serif;\">You have
                                                            requested to reset your password</h1>
                                                        <span
                                                            style=\"display:inline-block; vertical-align:middle; margin:29px 0 26px; border-bottom:1px solid #cecece; width:100px;\"></span>
                                                        <p style=\"color:#455056; font-size:15px;line-height:24px; margin:0;\">
                                                            We cannot simply send you your old password. A unique link to reset your
                                                            password has been generated for you. To reset your password, click the
                                                            following link and follow the instructions.
                                                        </p>
                                                        <a href=\"http://localhost/ums/reset-password.php?token=$token\"
                                                            style=\"background:#20e277;text-decoration:none !important; font-weight:500; margin-top:35px; color:#fff;text-transform:uppercase; font-size:14px;padding:10px 24px;display:inline-block;border-radius:50px;\">Reset Password</a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style=\"height:40px;\">&nbsp;</td>
                                                </tr>
                                            </table>
                                        </td>
                                    <tr>
                                        <td style=\"height:20px;\">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td style=\"text-align:center;\">
                                            <p style=\"font-size:14px; color:rgba(69, 80, 86, 0.7411764705882353); line-height:18px; margin:0 0 0;\">&copy; <strong>Staff Uniform Management System</strong></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style=\"height:80px;\">&nbsp;</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <!--/100% body table-->
                </body>

                </html>
            ";
        
            try {
        
                $mail->send();
                $email = "";
                $success['msg'] = "<script>sentOkay()</script>";
        
            } catch (Exception $e) {
                $msg = json_encode($e->getMessage());

                $success['msg'] = "<script>error('Server Error! Make sure you\'re connected to the internet, and try again')</script>";
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | UMS</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="swal/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="swal/sweetalert2.css">

</head>
<body>
    <script>
        function sentOkay() {
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Message sent, please check your inbox!',
                showConfirmButton: true,
            })
        }

        function error(msg) {
            Swal.fire({
                position: 'center',
                icon: 'error',
                title: msg,
                showConfirmButton: true,
            })
        }
    </script>
    <div class="login-container">
        <?php if(isset($success['msg'])) {
                echo $success['msg'];
            }
        ?>
        <form action="" method="post" autocomplete="off">
            <h3>Forgot Password | SUMS</h3>
            <label>Email</label>
            <input type="text" name="email" value="<?=$email ?? ''?>" placeholder="Enter your email">
            <div class="err"><?=$errors['email'] ?? ''?></div>
            <input type="submit" class="login-btn" value="Submit" name="submit">
        </form>
    </div>
</body>
</body>
</html>