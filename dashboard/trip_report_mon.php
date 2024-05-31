<?php error_reporting(0);

session_start();

if(!isset($_SESSION['administrator']) && !isset($_SESSION['vmanager'])) {
    header('location: ../login.php');
}

if(isset($_SESSION['change'])) {
    header('location: ../changepassword.php');
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
?>
<!DOCTYPE html>
<html lang="en, id">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>
      Vims | Report
    </title>
    <link rel="stylesheet" href="../css/invoice.css" />
    <link rel="stylesheet" href="../css/print.css" />
    <link rel="stylesheet" href="../boxicons/css/boxicons.min.css">
    <style>

        .container-r {
            width: 90%;
        }
        .wrapper-invoice .invoice {
            max-width: 200vh
        }
    </style>
  </head>
  <body>
    <section class="wrapper-invoice container-r">
      <div class="invoice print">
        <div class="invoice-information">
          <p><b>Report Date</b>: <?=date('F, jS Y')?></p>
        </div>
        <div class="invoice-logo-brand">
          <img src="../assets/ico.png" alt="" />
        </div>
        <!-- invoice head -->
        <div class="invoice-head">
          <div class="head client-data">
            <p>-</p>
            <p>Vehicle Information Management System</p>
            <p>Monthly Trip Report</p>
            <p><?=$month.' '.date('Y')?></p> 
          </div>
        </div>
        <!-- invoice body-->
        <div class="invoice-body">
          <table class="table" border="1">
            <thead>
              <tr>
                <th>SN</th>
                <th>Bus No.</th>
                <th>From</th>
                <th>To</th>
                <th>Departure Time</th>
                <th>Arrive Time</th>
                <th>KM Before</th>
                <th>KM After</th>
                <!-- <th>Next Service KM</th> -->
            </tr>
            </thead>
            <tbody>
              <?php 
                try {
                  require('../db/pdo.php');
                  // require('checkService.php');
                  $start_date = $_REQUEST['sdate'];
                  $end_date = $_REQUEST['edate'];
                  $vehicleID = $_REQUEST['vehicleID'];
                  

                  $stmt = $dbCnx->prepare("SELECT trips.*, vehicle.* FROM trips INNER JOIN vehicle ON trips.vehicleID = vehicle.vehicleID WHERE month(trips.created_at) = :month");
                  $stmt->bindValue(':month', $_GET['month'], PDO::PARAM_INT);
                //   $stmt->bindValue(':start_date', $start_date, PDO::PARAM_STR);
                //   $stmt->bindValue(':end_date', $end_date, PDO::PARAM_STR);
                  $stmt->execute();

                  if($stmt->rowCount() > 0): 
                    $rows = $stmt->fetchAll();
                    $sn = 1; foreach($rows as $row):
                ?>
              <tr>
                  <td><?=$sn++?></td>
                  <td><?=$row['registration_no']?></td>
                  <td><?=$row['depart_from']?></td>
                  <td><?=$row['arrive_in']?></td> 
                  <td><?=date('d-m-Y H:i:s', strtotime($row['departure_time']))?></td>
                  <td><?=date('d-m-Y H:i:s', strtotime($row['arrival_time']))?></td>
                  <td><?=$row['km_before'].' KM'?></td>
                  <td><?=$row['km_after'].' KM'?></td>
              </tr>
              <?php endforeach; endif; 
                } catch(PDOException $e) {
                  echo $e->getMessage();
                }
              ?>
            </tbody>
          </table>
          <div class="flex-table">
            <div class="flex-column"></div>
            <div class="flex-column">
              <table class="table-subtotal">
                <tbody>
                  <tr>
                    <!-- <td>Total</td> -->
                    <!-- <td class="subtotal"><?=number_format($amount,0,'.',',').'/-';?></td> -->
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <!-- total  -->
          <div class="invoice-total-amount">
          <?php 
                // try {
                //   require('../db/pdo.php');
                //   $query = $dbCnx->query("SELECT oil_type, COUNT(*) AS count FROM oil WHERE created_at BETWEEN '{$start_date}' AND '{$end_date}' GROUP BY oil_type ORDER BY count DESC LIMIT 1");
                //   if($query->rowCount() > 0):
                //     $row = $query->fetch();  
                //     if($row['count'] > 1):
                //       $used = 'times';
                //     else:
                //       $used = 'time';
                //     endif;
          ?>
                    <!-- <p id="invoice-total-amount">Most used oil:  <?=$row['oil_type']?><span></span></p>
                    <p id="invoice-total-amount">Time used:  <?=$row['count'].' '.$used?><span></span></p> -->
          <?php  //endif;
                //   } catch(PDOException $e) {
                //   echo $e->getMessage();
                // }
          ?>
          </div>
        </div>
      </div>
      <div class="buttons">
        <a href="report.php" class="backBtn"><i class="bx bx-undo"></i>Go Back</a>
        <a href="javascript:void()" class="printBtn"><i class="bx bx-printer"></i>Print</a>
      </div>
    </section>

    <script>
      const printBtn = document.querySelector('.printBtn')
        printBtn.addEventListener('click', function() {
          print()
        });
    </script>
  </body>
</html>
