<?php 
    session_start();

    if(!isset($_SESSION['administrator']) && !isset($_SESSION['vmanager'])) {
        header('location: ../login.php');
    }

    if(isset($_SESSION['change'])) {
        header('location: ../changepassword.php');
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
            <p>Annual Service Report</p> 
          </div>
        </div>
        <!-- invoice body-->
        <div class="invoice-body">
          <table class="table" border="1">
            <thead>
              <tr>
                <th>SN</th>
                <th>Vehicle No.</th>
                <th>Service KM</th>
                <th>Oil Type</th>
                <th>Length</th>
                <th>Service Date</th>
                <!-- <th>Cost</th> -->
            </tr>
            </thead>
            <tbody>
              <?php 
                try {
                  require('../db/pdo.php');

                  $stmt = $dbCnx->prepare("SELECT oil.*, vehicle.* FROM oil INNER JOIN vehicle ON oil.vehicleID = vehicle.vehicleID WHERE oil.vehicleID = :vehicleID AND year(oil.created_at) = year(curdate())");
                  $stmt->bindValue(':vehicleID', $_GET['vehicleID'], PDO::PARAM_INT);
                  $stmt->execute();

                  if($stmt->rowCount() > 0): 
                    $rows = $stmt->fetchAll();
                    $sn = 1; foreach($rows as $row): ?>
              <tr>
                  <td><?=$sn++?></td>
                  <td><?=$row['registration_no']?></td>
                  <td><?=$row['current_km'].' KM'?></td>
                  <td><?=$row['oil_type']?></td>
                  <td><?=$row['length'].' KM'?></td>
                  <td><?=date('M, jS Y', strtotime($row['created_at']))?></td>
                  <!-- <td></td> -->
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
                try {
                  require('../db/pdo.php');
                  $query = $dbCnx->query("SELECT oil_type, COUNT(*) AS count FROM oil WHERE oil.vehicleID = {$_GET['vehicleID']} AND year(created_at) = year(curdate()) GROUP BY oil_type ORDER BY count DESC LIMIT 1");
                  if($query->rowCount() > 0):
                    $row = $query->fetch();  
                    if($row['count'] > 1):
                      $used = 'times';
                    else:
                      $used = 'time';
                    endif;
          ?>
                    <p id="invoice-total-amount">Most used oil:  <?=$row['oil_type']?><span></span></p>
                    <p id="invoice-total-amount">Time used:  <?=$row['count'].' '.$used?><span></span></p>
          <?php  endif;
                  } catch(PDOException $e) {
                  echo $e->getMessage();
                }
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
