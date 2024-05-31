<?php
    session_start();

    if(!isset($_SESSION['administrator']) && !isset($_SESSION['vmanager'])) {
        header('location: ../login.php');
    }

    if(isset($_SESSION['change'])) {
        header('location: ../changepassword.php');
    }
    

    try {
        require('../db/pdo.php');

        $stmt = $dbCnx->prepare("SELECT insurance.*, vehicle.registration_no FROM insurance INNER JOIN vehicle ON insurance.vehicleID = vehicle.vehicleID WHERE month(insurance.start) = :month");
        $stmt->bindValue(':month', $_REQUEST['month']);
        $stmt->execute();
        $rows = $stmt->fetchAll();
    }

    catch(PDOException $e) {
        echo $e->getMessage();
    }

    $amount = 0; $litres = 0; 

    $month = '';
    switch($_REQUEST['month']) {
        case 1:
            $month = 'January';
        break;
        case 2:
            $month = 'February';
        break;
        case 3:
            $month = 'March';
        break;
        case 4:
            $month = 'April';
        break;
        case 5:
            $month = 'May';
        break;
        case 6:
            $month = 'June';
        break;
        case 7:
            $month = 'July';
        break;
        case 8:
            $month = 'August';
        break; 
        case 9:
            $month = 'September';
        break;
        case 10:
            $month = 'October';
        break;
        case 11:
            $month = 'November';
        break;
        case 12:
            $month = 'December';
        break;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insurance | VIMS</title>
    <link rel="stylesheet" href="../css/print.css">
    <link rel="stylesheet" href="../boxicons/css/boxicons.min.css">
    <script src="../js/jquery.js"></script>
</head>
<body>
    <div class="container-r print">
        <div class="header">
            <img src="../assets/buss.png" alt="">
            <p>VIMS</p>
            <!-- <p>Vehicle Information Management System</p> -->
        </div>
        <section>
            <div class="flex">
                <p>Vehicle Information Management System</p>
                <p><?=date('jS F Y')?></p>
            </div>
            <p>Monthly Insurance Report</p>
            <p><?=$month.', '.date('Y');?></p>
        </section>

        <div class="main-body">
            <h4>INSURANCE REPORT</h4>
            <table>
                <tr>
                    <th>SN</th>
                    <th>Vehicle No.</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Insurer</th>
                    <th>Coverage</th>
                    <th>Cost</th>
                </tr>
                <?php $sn = 1; foreach($rows as $row): ?>
                <tr>
                    <td><?=$sn++?></td>
                    <td><?=$row['registration_no']?></td>
                    <td><?=date('d-m-Y', strtotime($row['start']))?></td>
                    <td><?=date('d-m-Y', strtotime($row['end']))?></td>
                    <td><?=$row['insure']?></td>
                    <td><?=$row['type']?></td>
                    <td><?=number_format($row['amount'],0,'.',',').'/-'; ?></td>
                </tr>
                <?php $amount = $amount + $row['amount']; endforeach; ?>
                <tr>
                    <td style="text-align: left; font-weight: 600;" colspan="6">Total Cost</td>
                    <td><?=number_format($amount,0,'.',',').'/-';?></td>
                </tr>
            </table>
        </div>
    </div>
    <div class="buttons">
        <a href="report.php" class="backBtn"><i class="bx bx-undo"></i>Go Back</a>
        <a href="javascript:void()" class="printBtn"><i class="bx bx-printer"></i>Print</a>
    </div>
    <script>
        $(document).ready(function() {
            $('.printBtn').click(function() {
                print()
            });
        });
    </script>
</body>
</html>