<?php
require_once 'core/init.php';
$user = new User();
if (!$user->isLoggedIn()) { // check user login (user should not be logged in)
    Redirect::to('login.php');
}
?>

<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>

    <?php include("Layout_PHP/headPrivate.php"); ?>

</head>
<body>
<?php include("Layout_PHP/nav.php"); ?>
<header class="container">
<section class="content">
<div class="centerTextBox">

<ul>
    <li>
        <?php
        if (Session::exists('successMessage')) {
            echo(Session::flash('successMessage'));
        }
        if (Session::exists('failMessage')) {
            echo(Session::flash('failMessage'));
        }
        if (Session::exists('restrictUserMessage')) {
            echo(Session::flash('restrictUserMessage'));
        }
        ?>
    </li>
</ul>
<?php
if ($user->getData()->role == 'admin') {
    ?>
    <form class="searchForm" method="post" action="">
        <div class="input-group col-md-2">
            <select class="selectpicker" name="searchBy">
                <option default value="first_name">Search by first name</option>
                <?php
                echo '<option value="last_name">Last name</option>
                                    <option value="username">Username</option>';

                ?>
            </select>
        </div>
        <div id="custom-search-input">

            <div class="input-group col-md-2">
                <input type="text" name="search" id="search" value="<?php echo Input::get('search'); ?>"
                       class="form-control input-lg"/>
                    <span class="input-group-btn">
                        <input class="btn btn-info btn-lg" type="submit" value="Search">
                    </span>
            </div>

        </div>
    </form>
    <div class="table-responsive">
        <table class="table  table-inverse">
            <thead>
            <tr>
                <th>#</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Username</th>
                <th>Position</th>
                <th colspan="4"><a class="btn btn-primary btn-xs" href="registerUser.php">Add new user</a></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $db = DB::getInstance();
            $usersDB = $db->get('user', array('first_name', 'LIKE', '%' . Input::get('search') . '%'
            ), 'ORDER BY first_name ASC');
            if (Input::exists()) {
                $usersDB = DB::getInstance()->get('user', array(Input::get('searchBy'), 'LIKE', '%' . Input::get('search') . '%'
                ), 'ORDER BY first_name ASC');
            }

            for ($i = 0; $i < $usersDB->count(); $i++) {
                $counter = $i + 1;
                ?>
                <tr>
                    <th scope='row'><?php echo $counter; ?></th>
                    <td><?php echo ucfirst($usersDB->result()[$i]->first_name); ?></td>
                    <td><?php echo ucfirst($usersDB->result()[$i]->last_name); ?></td>
                    <td><?php echo $usersDB->result()[$i]->username; ?></td>
                    <td><?php echo ucfirst($usersDB->result()[$i]->role); ?></td>
                    <td><a class="btn btn-primary btn-xs"
                           href="userDetails.php?id=<?php echo ucfirst($usersDB->result()[$i]->id); ?>">Details</a></td>
                    <td><a class="btn btn-primary btn-xs"
                           href="userUpdate.php?id=<?php echo ucfirst($usersDB->result()[$i]->id); ?>">Update</a></td>
                    <td><a class="btn btn-primary btn-xs"
                           href="userDelete.php?id=<?php echo ucfirst($usersDB->result()[$i]->id); ?>">Remove</a></td>
                    <td><a class="btn btn-primary btn-xs"
                           href="userPass.php?id=<?php echo ucfirst($usersDB->result()[$i]->id); ?>">Reset
                            password</a> (123)
                    </td>
                </tr>
            <?php
            }
            ?>
            <tbody>
        </table>
    </div>
<?php
} else if ($user->getData()->role == 'optician') {
    // appointment coming for the optician logged in
    $db = DB::getInstance();
    $currentOptician = $db->get('appointment', array('optician_id', '=', $user->getData()->id), 'ORDER BY date');
    $getAppoints = $currentOptician->result();
    $patients = array();
    if (count($getAppoints) < 1) {
        echo "There are not any appointment setup for Dr " . ucfirst($user->getData()->first_name) . ' ' . ucfirst($user->getData()->last_name);
        echo '<br><a class="btn btn-primary btn-xs" href="appointAdd.php">Add new appointment</a>';
    } else {
        ?>
        <h1>Appointments for
            Dr <?php echo ucfirst($user->getData()->first_name) . ' ' . ucfirst($user->getData()->last_name); ?></h1>
        <br>
        <div class="table-responsive">
            <table class="table  table-inverse">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Patient Name</th>
                    <th>Date/Time</th>
                    <th>Details</th>
                    <th colspan="3"><a class="btn btn-primary btn-xs" href="appointAdd.php">Add new appointment</a></th>
                </tr>
                </thead>
                <tbody>
                <?php

                for ($i = 0; $i < count($getAppoints); $i++) {
                    if ($getAppoints[$i]->status != 'clear'){
                        $patients[$i] = $db->get('user', array('id', '=', $getAppoints[$i]->patient_id))->result()[0];
                        ?>
                        <tr>
                            <th scope='row'><?php echo $i + 1; ?></th>
                            <td><?php echo ucfirst($patients[$i]->first_name) . ' ' . ucfirst($patients[$i]->last_name); ?></td>
                            <td><?php
                                if ($getAppoints[$i]->date < date('Y-m-d H:i:s')) {
                                    echo '<span style="color:red;">' . date("D, j F Y, g:i a", strtotime($getAppoints[$i]->date)) . '</span>';
                                } else {
                                    if ($getAppoints[$i]->status == 'cancel'){
                                        echo '<span style="color:red;">' . date("D, j F Y, g:i a", strtotime($getAppoints[$i]->date)) . '</span>';
                                    }else {
                                        echo '<span style="color:green;">' . date("D, j F Y, g:i a", strtotime($getAppoints[$i]->date)) . '</span>';
                                    }
                                }
                                ?>
                            </td>
                            <td><?php echo $getAppoints[$i]->details; ?></td>
                            <?php
                            if ($getAppoints[$i]->date < date('Y-m-d H:i:s')) {
                                echo '<td></td>';
                                echo '<td><a class="btn btn-primary btn-xs" href="appointUpdate.php?id=' . $getAppoints[$i]->id . '">Edit</a></td>';
                                echo '<td><a class="btn btn-primary btn-xs" href="appointClear.php?id=' . $getAppoints[$i]->id . '">Clear</a></td>';
                            } else if ($getAppoints[$i]->status == 'cancel'){
                                echo '<td><span style="color: red">Canceled</span></td>';
                                echo '<td><a class="btn btn-primary btn-xs" href="appointUpdate.php?id=' . $getAppoints[$i]->id . '">Edit</a></td>';
                                echo '<td><a class="btn btn-primary btn-xs" href="appointClear.php?id=' . $getAppoints[$i]->id . '">Clear</a></td>';
                            } else if ($getAppoints[$i]->status == 'done'){
                                echo '<td><span style="color: limegreen">Done</span></td>';
                                echo '<td><a class="btn btn-primary btn-xs" href="appointUpdate.php?id=' . $getAppoints[$i]->id . '">Edit</a></td>';
                                echo '<td><a class="btn btn-primary btn-xs" href="appointClear.php?id=' . $getAppoints[$i]->id . '">Clear</a></td>';
                            }else {
                                echo '<td><a class="btn btn-primary btn-xs" href="report.php?id=' . $getAppoints[$i]->id . '">Add Report</a></td>';
                                echo '<td><a class="btn btn-primary btn-xs" href="appointUpdate.php?id=' . $getAppoints[$i]->id . '">Edit</a></td>';
                                echo '<td><a class="btn btn-primary btn-xs" href="appointCancel.php?id=' . $getAppoints[$i]->id . '">Cancel</a></td>';

                            }
                            ?>
                        </tr>
                    <?php
                    }
                }
                ?>
                <tbody>
            </table>
        </div>
    <?php
    }
} else {
    // appointment coming for the patient logged in
    $db = DB::getInstance();
    $currentPatient = $db->get('appointment', array('patient_id', '=', $user->getData()->id), 'ORDER BY date');
    $getAppoints = $currentPatient->result();
    $opticians = array();
    if (count($getAppoints) < 1) {
        echo "There are not any appointment set for " . ucfirst($user->getData()->first_name) . ' ' . ucfirst($user->getData()->last_name);
    } else {
        ?>

        <h1>Appointments</h1>
        <br>
        <div class="table-responsive">
            <table class="table  table-inverse">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Optician Name</th>
                    <th>Date/Time</th>
                    <th>Details</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php


                for ($i = 0; $i < count($getAppoints); $i++) {
                    $opticians[$i] = $db->get('user', array('id', '=', $getAppoints[$i]->optician_id))->result()[0];
                    ?>
                    <tr>
                        <th scope='row'><?php echo $i + 1; ?></th>
                        <td><?php echo 'Dr ' . ucfirst($opticians[$i]->first_name) . ' ' . ucfirst($opticians[$i]->last_name); ?></td>
                        <td><?php
                            if ($getAppoints[$i]->date < date('Y-m-d H:i:s')) {
                                echo '<span style="color:red;">' . date("D, j F Y, g:i a", strtotime($getAppoints[$i]->date)) . '</span>';
                            } else {
                                echo '<span style="color:green;">' . date("D, j F Y, g:i a", strtotime($getAppoints[$i]->date)) . '</span>';
                            }
                            ?>
                        </td>
                        <td><?php echo $getAppoints[$i]->details; ?></td>
                        <td>
                            <?php
                            if ($getAppoints[$i]->date < date('Y-m-d H:i:s')) {
                                echo '<a class="btn btn-primary btn-xs" href="appointClear.php?id=' . $getAppoints[$i]->id . '">Clear</a>';
                            } else {
                                echo '<a class="btn btn-primary btn-xs" href="addReport.php?id=' . $getAppoints[$i]->id . '">Cancel request</a>';
                            }
                            ?>
                        </td>
                    </tr>
                <?php
                }
                ?>
                <tbody>
            </table>
        </div>
    <?php
    }
}
?>

</div>
</section>
</header>

<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="bootstrap/js/bootstrap.min.js"></script>
</body>
</html>