<?php
    session_start();

    if(!isset($_SESSION['administrator']) && !isset($_SESSION['vmanager'])) {
        header('location: ../login.php');
    }

    if(isset($_SESSION['change'])) {
        header('location: ../changepassword.php');
    }

    $admin = '';

    $errors = [];

    try {
        require('../db/pdo.php');
        $stmt = $dbCnx->prepare('SELECT * FROM users WHERE userID = :userID');
        $stmt->bindValue(':userID', $_SESSION['uniqueID'], PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();

        $admin = $row['usertype'];

    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    // all vehicles
    if(isset($_POST['generate_all'])) {
        $type = $_POST['type_all'] ?? '';
        $length = $_POST['length_all'] ?? '';
        $month = $_POST['months_all'] ?? '';
        $start_date = $_POST['start_date_all'];
        $end_date = $_POST['end_date_all'];
        $routeID = $_POST['all_routes'] ?? '';
        $trip_for = $_POST['for'];
        // echo $month;exit;

        if(empty($type)) {
            $errors['type_all'] = "Type of report is required";
        }

        if(empty($length)) {
            $errors['length'] = "Length of report is required";
        }

        if($length == 'Monthly_all') {
            if(empty($month)) {
                $errors['months_all'] = "Month of report is required";
            }
        }

        if($length == 'Pick_all') {
            if(empty($start_date)) {
                $errors['start_date_all'] = "Start date is required";
            }
    
            if(empty($end_date)) {
                $errors['end_date_all'] = "Start date is required";
            }

            if($start_date > $end_date) {
                $errors['end_date_all'] = "End date must be greater than start date";
                $errors['start_date_all'] = "Start date must be less than end date";
            }
        }
        
        if($trip_for == 'single') {
            if(empty($routeID)) {
                $errors['route'] = "Route is required";
            }
        }
        

        switch($type) {
            case 'Maintenance':
                $maintance = "selected";
                $main = "";
                $trip = "";
            break;
            case 'Service':
                $maintance = "";
                $main = "selected";
                $trip = "";
                break;
            case 'Trip':
                $maintance = "";
                $main = "";
                $trip = "selected";
                break;
            default:
            $maintance = "";
            $main = "";
            $trip = "";
        }

        // vehicle
        if(isset($type) && $type == 'Vehicle' && isset($length) && $length == 'Pick_all' && $start_date !== '' && $end_date !== '') {
            header("location: vehicle_pick_report.php?sdate=$start_date&edate=$end_date");
            $type = $length = $start_date = $end_date = ''; 
            exit;
        }


        if(isset($type) && $type == 'Vehicle' && isset($length) && $length == 'Monthly_all' && isset($month) && $month !== '') {
            header("location: vehicle_mon_report.php?month=$month");
            $type = $length = $month = ''; 
            exit;
        }


        if(isset($type) && $type == 'Vehicle' && isset($length) && $length == 'Annually_all') {
            header("location: vehicle_an_report.php");
            exit;
        }

        //maintenance
        if(isset($type) && $type == 'Maintenance' && isset($length) && $length == 'Monthly_all' && isset($month) && $month !== '') {
            header("location: maintenance_mon_rep.php?month=$month");
            $type = $length = $month = ''; 
            exit;
        }

        if(isset($type) && $type == 'Maintenance' && isset($length) && $length == 'Annually_all') {
            header("location: maintenance_report_an.php");
            exit;
        }

        if(isset($type) && $type == 'Maintenance' && isset($length) && $length == 'Pick_all' && $start_date !== '' && $end_date !== '') {
            header("location: maintenance_pick_rep.php?sdate=$start_date&edate=$end_date");
            $type = $length = $start_date = $end_date = ''; 
            exit;
        }

        if(isset($type) && $type == 'Service' && isset($length) && $length == 'Pick_all' && $start_date !== '' && $end_date !== '') {
            header("location: service_rd.php?sdate=$start_date&edate=$end_date");
            $type = $length = $start_date = $end_date = ''; 
            exit;
        }


        if(isset($type) && $type == 'Service' && isset($length) && $length == 'Monthly_all' && isset($month) && $month !== '') {
            header("location: service_report.php?month=$month");
            $type = $length = $month = ''; 
            exit;
        }


        if(isset($type) && $type == 'Service' && isset($length) && $length == 'Annually_all') {
            header("location: service_an_report.php");
            exit;
        }

        // Single route
        if(isset($type) && $type == 'Trip' && isset($length) && $length == 'Annually_all' && isset($routeID) && $routeID != '') {
            header("location: trip_rep_s.php?routeID=$routeID");
            exit;
        }

        if(isset($type) && $type == 'Trip' && isset($length) && $length == 'Pick_all' && $start_date !== '' && $end_date !== ''  && $routeID != '') {
            header("location: trip_rep_pick.php?sdate=$start_date&edate=$end_date&routeID=$routeID");
            $type = $length = $start_date = $end_date = ''; 
            exit;
        }

        if(isset($type) && $type == 'Trip' && isset($length) && $length == 'Monthly_all' && isset($month) && $month !== ''  && $routeID != '') {
            header("location: trip_rep_mon.php?month=$month&routeID=$routeID");
            $type = $length = $month = ''; 
            exit;
        }

        //All routes
        if(isset($type) && $type == 'Trip' && isset($length) && $length == 'Annually_all' && isset($trip_for) && $trip_for = 'all') {
            header("location: trip_report_an.php");
            exit;
        }

        if(isset($type) && $type == 'Trip' && isset($length) && $length == 'Pick_all' && $start_date !== '' && $end_date !== '' && isset($trip_for) && $trip_for = 'all') {
            header("location: trip_report_pick.php?sdate=$start_date&edate=$end_date");
            $type = $length = $start_date = $end_date = ''; 
            exit;
        }

        if(isset($type) && $type == 'Trip' && isset($length) && $length == 'Monthly_all' && isset($month) && $month !== '' && isset($trip_for) && $trip_for = 'all') {
            header("location: trip_report_mon.php?month=$month");
            $type = $length = $month = ''; 
            exit;
        }


    }



    // Individual Vehicle
    if(isset($_POST['generate'])) {
        $type = $_POST['in_type'] ?? '';
        $length = $_POST['in_length'] ?? '';
        $month = $_POST['months'] ?? '';
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $vehicleID = $_POST['vehicleID'] ?? '';
        $trip_for = $_POST['for'];

        // echo $month;exit;


        // echo "Your report type is $type, length is $length, from: $start_date to $end_date on vehicle: $vehicleID";exit;

        if(empty($type)) {
            $errors['in_type'] = "Type of report is required";
        }

        if(empty($vehicleID)) {
            $errors['vehicleID'] = "Vehicle Number is required";
        }

        if(empty($length)) {
            $errors['in_length'] = "Length of report is required";
        }


        if($length == 'Monthly') {
            if(empty($month)) {
                $errors['in_months'] = "Month of report is required";
            }
        }

        if($length == 'Pick') {
            if(empty($start_date)) {
                $errors['start_date'] = "Start date is required";
            }
    
            if(empty($end_date)) {
                $errors['end_date'] = "Start date is required";
            }

            if($start_date > $end_date) {
                $errors['end_date'] = "End date must be greater than start date";
                $errors['start_date'] = "Start date must be less than end date";
            }
        }

        switch($type) {
            case 'Insurance':
                $maintance_in = "selected";
                $main_in = "";
                $trip_in = "";
            break;
            case 'Service':
                $maintance_in = "";
                $main_in = "selected";
                $trip_in = "";
                break;
            case 'Fuel':
                $maintance_in = "";
                $main_in = "";
                $trip_in = "selected";
                break;
            default:
            $maintance_in = "";
            $main_in = "";
            $trip_in = "";
        }

        switch($length) {
            case 'Monthly':
                $n1_in = "selected";
                $n2_in = "";
                $n3_in = "";
            break;
            case 'Annually':
                $n2_in = "selected";
                $n1_in = "";
                $n3_in = "";
                break;
            case 'Pick':
                $n1_in = "";
                $n2_in = "";
                $n3_in = "selected";
                break;
            default:
                $n3_in = "";
                $n2_in = "";
                $n1_in = "";
        }
        // service report
        if(isset($type) && $type == 'Service' && isset($length) && $length == 'Pick' && $start_date !== '' && $end_date !== '' && $vehicleID != '') {
            header("location: report_s.php?vehicleID=$vehicleID&sdate=$start_date&edate=$end_date");
            $type = $length = $start_date = $end_date = ''; 
            exit;
        }

        if(isset($type) && $type == 'Service' && isset($length) && $length == 'Monthly' && isset($month) && $month !== '' && $vehicleID != '') {
            header("location: service_report_in.php?vehicleID=$vehicleID&month=$month");
            $type = $length = $month = ''; 
            exit;
        }

        if(isset($type) && $type == 'Service' && isset($length) && $length == 'Annually' && $vehicleID != '') {
            header("location: service_an_report_in.php?vehicleID=$vehicleID");
            exit;
        }

        // maintenance report
        if(isset($type) && $type == 'Maintenance' && isset($length) && $length == 'Monthly' && isset($month) && $month !== '' && $vehicleID != '') {
            header("location: maintenance_mon.php?vehicleID=$vehicleID&month=$month");
            $type = $length = $month = ''; 
            exit;
        }

        if(isset($type) && $type == 'Maintenance' && isset($length) && $length == 'Annually' && $vehicleID != '') {
            header("location: maintenance_rep_an.php?vehicleID=$vehicleID");
            exit;
        }

        if(isset($type) && $type == 'Maintenance' && isset($length) && $length == 'Pick' && $start_date !== '' && $end_date !== '' && $vehicleID != '') {
            header("location: maintenance_pick_rep_i.php?vehicleID=$vehicleID&sdate=$start_date&edate=$end_date");
            $type = $length = $start_date = $end_date = ''; 
            exit;
        }
        
        // trip report
        if(isset($type) && $type == 'Trip' && isset($length) && $length == 'Monthly' && isset($month) && $month !== '' && $vehicleID != '') {
            header("location: trip_mon.php?vehicleID=$vehicleID&month=$month");
            $type = $length = $month = ''; 
            exit;
        }

        if(isset($type) && $type == 'Trip' && isset($length) && $length == 'Annually' && $vehicleID != '') {
            header("location: trip_rep_an.php?vehicleID=$vehicleID");
            exit;
        }

        if(isset($type) && $type == 'Trip' && isset($length) && $length == 'Pick' && $start_date !== '' && $end_date !== '' && $vehicleID != '') {
            header("location: trip_pick_rep_i.php?vehicleID=$vehicleID&sdate=$start_date&edate=$end_date");
            $type = $length = $start_date = $end_date = ''; 
            exit;
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Report | Vehicle Information Management System</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../boxicons/css/boxicons.min.css">
    <script src="../swal/sweetalert2.min.js"></script>
    <link rel="stylesheet" href="../swal/sweetalert2.css">
    <link rel="stylesheet" href="../css/select2.min.css" />
    <style>
        table td {
            text-transform: capitalize
        }

        :is(.input_row, .inputBox) .hidden {
            display: none;
        }

        .custom-select {
            height: 35px !important;
        }
    </style>
</head>
<body>
    
    <div class="container">
        <aside>
            <header>
                <!-- <h3>VIMS</h3> -->
                <img src="../assets/bus.png" alt="">
                <span>Vehicle Information Management System</span>
            </header>
            <!-- <div class="something">Something will go here...</div> -->
            <nav class="navbar">
                <ul>
                    <?php if(isset($_SESSION['administrator']) || isset($_SESSION['vmanager'])): ?>
                    <li>
                        <a href="index.php"><i class="bx bx-grid-alt"></i>Dashboard</a>
                    </li>
                    <li>
                        <a href="users.php"><i class="bx bx-user"></i>Users</a>
                    </li>
                    <li>
                        <a href="vehicle.php"><i class="bx bx-bus"></i>Vehicles</a>
                    </li>
                    <li>
                        <a href="maintanance.php"><i class="bx bxs-car-mechanic"></i>Maintenance</a>
                    </li>
                    <li>
                        <a href="all_services.php"><i class="bx bx-wrench"></i>Service</a>
                    </li>
                    <!-- <li>
                        <a href="trip.php"><i class="bx bx-gas-pump"></i>Fuel</a>
                    </li>
                    <li>
                        <a href="insurance.php"><i class="bx bx-donate-heart"></i>Insurance</a>
                    </li> -->
                    <li>
                        <a href="routes.php"><i class="bx bx-trip"></i>Routes</a>
                    </li>
                    <li>
                        <a href="trips.php" ><i class="bx bx-trip"></i>Trips</a>
                    </li>
                    <li class="active">
                        <a href="report.php"><i class="bx bx-receipt"></i>Report</a>
                    </li>
                    <li>
                        <a href="settings.php"><i class="bx bx-cog"></i>Settings</a>
                    </li>
                    <?php if(isset($_SESSION['administrator'])): ?>
                        <li>
                            <a href="logs.php"><i class="bx bx-cog"></i>User Logs</a>
                        </li>
                    <?php endif ?>
                    <li>
                        <a href="logout.php?logout_id=<?=$_SESSION['uniqueID']?>"><i class="bx bx-log-out"></i>Logout</a>
                    </li>
                    
                    <?php endif;  ?>
                </ul>
            </nav>
        </aside>
        <main class="main-content">
        <header class="grid">
                <span class="logo">VIMS</span>
                <div class="user">
                    <div class="name">
                        <?=$row['firstname'].' '.$row['lastname']?>
                        <span id="role"><?=$row['usertype']?></span>
                    </div>
                    <img id="imgDropdown" src="../images/<?=$row['image']?>" alt="">
                    <div class="dropdown">
                        <ul>
                            <li><a href="settings.php"><i class="bx bx-user"></i>Profile</a></li>
                            <li><a href="logout.php?logout_id=<?=$row['userID']?>"><i class="bx bx-log-out"></i>Logout</a></li>
                        </ul>
                    </div>
                </div>
            </header>
            <div class="grid main">
                <!-- <div class="top_card"></div> -->
                <div class="top_card">
                    <div class="bread-cumb">
                        <a href="index.php">Dashboard</a> > <span>Report</span>
                    </div>
                </div>
               
                <div class="bottom-card">
                    <!-- All Vehicles  -->
                    <form action="" method="post">
                    <p style="border-bottom: 1px #eee solid; padding-bottom:5px;margin-bottom:5px">Genarate Report</p>
                        <div class="input_row">
                            <div class="inputBox">
                                <span>Type of Report</span>
                                <select name="type_all" id="type_all">
                                    <option disabled selected>Select report type...</option>
                                    <option <?=$maintance ?? ''?> value="Maintenance">Maintenance Report</option>
                                    <option <?=$trip ?? ''?> value="Trip">Trip Report</option>
                                    <option <?=$main ?? ''?> value="Service">Service Report</option> 
                                    <option value="Vehicle">Vehicle Report</option> 
                                </select>
                                <p><?=$errors['type_all'] ?? ''?></p>
                            </div>
                            <div class="inputBox">
                                <span>Report Length</span>
                                <select name="length_all" id="length">
                                    <option selected disabled>Select length..</option>
                                    <option <?=$n1 ?? ''?> value="Monthly_all">Monthly Reports</option>
                                    <option <?=$n2 ?? ''?> value="Annually_all">Annual Report</option>
                                    <option <?=$n3 ?? ''?> value="Pick_all">Pick Date</option>
                                </select>
                                <p><?=$errors['length'] ?? ''?></p>
                            </div>
                            <div class="inputBox">
                                <div id="months_all" class="hidden">
                                    <span>Month</span>
                                    <select name="months_all" id="months">
                                        <option selected disabled>Select month...</option>
                                        <option value="1">January</option>
                                        <option value="2">February</option>
                                        <option value="3">March</option>
                                        <option value="4">April</option>
                                        <option value="5">May</option>
                                        <option value="6">June</option>
                                        <option value="7">July</option>
                                        <option value="8">August</option>
                                        <option value="9">September</option>
                                        <option value="10">October</option>
                                        <option value="11">November</option>
                                        <option value="12">December</option>
                                    </select>
                                    <p><?=$errors['months_all'] ?? ''?></p>
                                </div>
                                <div class="hidden" id="start_date_all"> 
                                    <span>Start Date</span>
                                    <input type="date" name="start_date_all">
                                    <p><?=$errors['start_date_all'] ?? ''?></p>
                                </div>
                            </div>
                        </div>
                        <div class="input_row" style="justify-content: start;">
                            <div class="inputBox hidden" style="margin-right: 22px" id="end_date_all">
                                <span>End Date</span>
                                <input type="date" name="end_date_all">
                                <p><?=$errors['end_date_all'] ?? ''?></p>
                            </div>
                            <div class="inputBox hidden" style="margin-right: 22px" id="routes">
                                <span>Trip For</span>
                                <select name="for" id="for_route">
                                    <option value="all" selected>All routes</option>
                                    <option value="single">Single Route</option>
                                </select>
                                <p><?=$errors['for'] ?? ''?></p>
                            </div>
                            <div class="inputBox hidden" style="margin-right: 22px" id="all_routes">
                            <span>All Routes</span>
                                <select name="all_routes">
                                    <option selected disabled>Select a route..</option>

                                    <?php
                                        try {
                                            require('../db/pdo.php');

                                            $stmt = $dbCnx->prepare('SELECT * FROM routes');
                                            $stmt->execute();
                                            $rows= $stmt->fetchAll();
                                            
                                            foreach($rows as $row) : 
                                    ?>
                                                <option value="<?=$row['routeID']?>"><?=$row['start'].' - '.$row['to_'].' - '.$row['end']?></option>
                                    <?php
                                            endforeach;
                                        } catch(PDOException $e) {
                                            echo $e->getMessage();
                                        }
                                    ?>

                                </select>
                                <p><?=$errors['route'] ?? ''?></p>
                            </div>
                        </div>
                        <div class="input_row">
                            <div class="inputBox">
                                <input type="submit" name="generate_all" value="Genearate Report"> 
                            </div>
                            <?=$errors['error'] ?? ''?>
                            <?=$errors['success'] ?? ''?>
                        </div>
                    </form>
                </div>
                <div class="bottom-card">
    
                    <!-- Individual Vehicle  -->
                    <form action="" method="post">
                    <p style="border-bottom: 1px #eee solid; padding-bottom:5px;margin-bottom:5px">Genarate Report - Individual</p>
                        <div class="input_row">
                        <div class="inputBox">
                                <span>Vehicle No.</span>
                                <select name="vehicleID" id="vehicle" class="custom-select">
                                    <option selected disabled>Select Vehicle..</option>

                                    <?php
                                        try {
                                            require('../db/pdo.php');

                                            $stmt = $dbCnx->prepare('SELECT * FROM vehicle');
                                            $stmt->execute();
                                            $rows= $stmt->fetchAll();
                                            
                                            foreach($rows as $row) : 
                                    ?>
                                                <option value="<?=$row['vehicleID']?>"><?=$row['registration_no'].' - '.$row['make'].' '.$row['model']?></option>
                                    <?php
                                            endforeach;
                                        } catch(PDOException $e) {
                                            echo $e->getMessage();
                                        }
                                    ?>

                                </select>
                                <p><?=$errors['vehicleID'] ?? ''?></p>
                            </div>
                            <div class="inputBox">
                                <span>Type of Report</span>
                                <select name="in_type">
                                    <option disabled selected>Select report type...</option>
                                    <option <?=$maintance_in ?? ''?> value="Maintenance">Maintenance Report</option>
                                    <option <?=$trip_in ?? ''?> value="Trip">Trip Report</option>
                                    <option <?=$main_in ?? ''?> value="Service">Service Report</option>
                                </select>
                                <p><?=$errors['in_type'] ?? ''?></p>
                            </div>
                            <div class="inputBox">
                                <span>Report Length</span>
                                <select name="in_length" id="length_individual">
                                    <option selected disabled>Select length..</option>
                                    <option <?=$n1_in ?? ''?> value="Monthly">Monthly Reports</option>
                                    <option <?=$n2_in ?? ''?> value="Annually">Annual Report</option>
                                    <option <?=$n3 ?? ''?> value="Pick">Pick date</option>
                                </select>
                                <p><?=$errors['in_length'] ?? ''?></p>
                            </div>
                        </div>
                        <div class="input_row" style="justify-content: start">
                            <div class="inputBox hidden" id="start_date"> 
                                <span>Start Date</span>
                                <input type="date" name="start_date">
                                <p><?=$errors['start_date'] ?? ''?></p>
                            </div>
                            
                            <div class="inputBox hidden" style="margin-left: 22px" id="end_date">
                                <span>End Date</span>
                                <input type="date" name="end_date">
                                <p><?=$errors['end_date'] ?? ''?></p>
                            </div>

                            <div class="inputBox hidden" id="months_individual"> 
                                <span>Month</span>
                                <select name="months">
                                    <option selected disabled>Select month...</option>
                                    <option value="1">January</option>
                                    <option value="2">February</option>
                                    <option value="3">March</option>
                                    <option value="4">April</option>
                                    <option value="5">May</option>
                                    <option value="6">June</option>
                                    <option value="7">July</option>
                                    <option value="8">August</option>
                                    <option value="9">September</option>
                                    <option value="10">October</option>
                                    <option value="11">November</option>
                                    <option value="12">December</option>
                                </select> 
                                <p><?=$errors['months_in'] ?? ''?></p>
                            </div>
                        </div>
                        <div class="input_row">
                            <div class="inputBox">
                                <input type="submit" name="generate" value="Genearate Report"> 
                            </div>
                            <?=$errors['error'] ?? ''?>
                            <?=$errors['success'] ?? ''?>
                        </div>
                    </form>
                </div>
                <footer>Copyright &copy; <?=date('Y')?>. All Righst Reserved.</footer>
            </div>
        </main>
    </div>
    <script src="../js/jquery.js"></script>
    <script src="../js/select2.min.js"></script>
    <script src="../js/main.js"></script>
    <script>
    $("#vehicle").select2( {
        placeholder: "Select Vehicle",
        allowClear: true
    } );
</script>
</body>
</html>