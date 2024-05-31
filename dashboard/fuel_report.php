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

        $stmt = $dbCnx->prepare("SELECT fuel.*, vehicle.registration_no, vehicle.fuel FROM fuel INNER JOIN vehicle ON fuel.vehicleID = vehicle.vehicleID WHERE month(fuel.purchased_on) = :month");
        $stmt->bindValue(':month', $_REQUEST['month']);
        $stmt->execute();
        $rows = $stmt->fetchAll();
    }

    catch(PDOException $e) {
        echo $e->getMessage();
    }

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

    $amount = 0; $litres = 0; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fuel Report | VIMS</title>
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
            <p>Monthly Fuel Report</p>
            <p><?=$month.', '.date('Y');?></p>
        </section>

        <div class="main-body">
            <h4>FUEL REPORT</h4>
            <table>
                <tr>
                    <th>SN</th>
                    <th>Date</th>
                    <th>Vehicle No.</th>
                    <th>Fuel Type</th>
                    <th>Litres</th>
                    <th>Cost</th>
                </tr>
                <?php $sn = 1; foreach($rows as $row): ?>
                <tr>
                    <td><?=$sn++?></td>
                    <td><?=date('d-m-Y', strtotime($row['purchased_on']))?></td>
                    <td><?=$row['registration_no']?></td>
                    <td style="text-transform: capitalize;"><?=$row['fuel']?></td>
                    <td><?=$row['litres']?></td>
                    <td><?=number_format($row['cost'],0,'.',',').'/-'; ?></td>
                </tr>
                <?php $amount = $amount + $row['cost']; $litres = $litres + $row['litres'];  endforeach; ?>
                <tr>
                    <td style="text-align: left; font-weight: 600;" colspan="4">Total Cost and Litres</td>
                    <td style="font-weight: bold;"><?=$litres?></td>
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