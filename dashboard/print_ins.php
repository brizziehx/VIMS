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

        $stmt = $dbCnx->prepare("SELECT insurance.*, vehicle.registration_no FROM insurance INNER JOIN vehicle ON insurance.vehicleID = vehicle.vehicleID WHERE year(insurance.start) = year(curdate())");
        $stmt->execute();
        $rows = $stmt->fetchAll();
    }

    catch(PDOException $e) {
        echo $e->getMessage();
    }

    $amount = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insurance Report | VIMS</title>
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
            <p>Annual Insurance Report</p>
            <p>Jan - Dec 2024</p>
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
                    <td style="font-weight: bold;"><?=number_format($amount,0,'.',',').'/-';?></td>
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