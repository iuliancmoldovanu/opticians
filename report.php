<?php
require_once 'core/init.php';
$user = new User();
$db = DB::getInstance();
if (!$user->isLoggedIn()) { // check user login (user should not be logged in)
    Redirect::to('login.php');
} else {
    if ($user->getData()->role != 'optician') {
        Session::flash('restrictUserMessage', '<div class="alert alert-danger"><strong>Attention</strong><br>You are not allowed to access that page.</div>');
        Redirect::to('dashboard.php');
    } else {
        if (Input::exists('get')) {
            $appointDetails = $db->get('appointment', array('id', '=', Input::get('id')))->result();
        }
    }
}
$hasErrors = false;
if (Input::exists('post')) {

    $validate = new Validate();
    $validation = $validate->check($_POST, array(
        'report' => array(
            'name' => 'report',
            'required' => true,
            'min' => 7,
            'max' => 500
        )
    ));

    if ($validate->passed()) {
        try {
            $db->update(
                'appointment',
                array(
                    'report' => Input::get('report') . ' - ' . date('Y-m-d H:i:s'),
                    'status'    => 'done'
                ),
                'id',
                Input::get('id')
            );
        } catch (Exception $e) {
            die($e->getMessage());
        }
        if (!$db->error()) {
            Session::flash('successMessage', '<br><br><div class="alert alert-success"><strong>Success!</strong><br>You have successfully added the report.</div>');
        } else {
            Session::flash('failMessage', '<div class="alert alert-danger"><strong>Error.</strong><br>Nothing has been change. Contact administrator if the problem persist !!!</div>');
        }
        Redirect::to('dashboard.php');
    } else {
        $hasErrors = true;
    }
}
$report = $appointDetails[0]->report;
$patient = $appointDetails[0]->patient_id;
$optician = $appointDetails[0]->optician_id;

if (Input::exists('post')) {
    if ($report != escape(Input::get('report'))) {
        $report = escape(Input::get('report'));
    }
}
?>

<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Update user</title>

    <?php include("Layout_PHP/headPrivate.php"); ?>
    <link rel="stylesheet" href="stylesheets/loginFormStyle.css">
    <style>
        .form {
            width: 64%;

        }

        .panel-primary {

        }

        .panel-heading {
            padding: 5px !important;
        }

        .panel-title {
            font-size: 12px;
        }

        .panel-body {
            padding: 2px;
            min-height: 80px !important;
        }

        #report {
            min-height: 80px;
            max-width: 100%;
            color: #000000;
        }
    </style>
</head>
<body>
<?php include("Layout_PHP/nav.php"); ?>
<header class="container">
    <section class="content">
        <div class="centerTextBox">
            <?php
            $db = DB::getInstance()->get('user', array('id', '=', $optician));
            echo '<span>Optician name: Dr ' . $db->result()[0]->first_name . ' ' . $db->result()[0]->last_name . '</span>';
            ?>
            <br><br>

            <iframe style="width:520px; height:450px;" frameborder="2" id="iframeToricCalculator"
                    src="http://www.bausch.com/portals/77/-/m/BL/United%20States/Files/SWFs/toric%20calculator/main.swf">
            </iframe>

            <br><br>

            <form class="form" action="" method="post">
                <?php
                if ($hasErrors) {
                    echo '<div class="errors">';
                    foreach ($validate->getErrors() as $error) {
                        echo '<span>' . $error . '</span><br>';
                    }
                    echo '</div>';
                }
                $db = DB::getInstance()->get('user', array('id', '=', $patient));
                echo '<span>Patient name: ' . $db->result()[0]->first_name . ' ' . $db->result()[0]->last_name . '</span>';

                ?>
                <div class="panel panel-primary first">
                    <div class="panel-heading">
                        <h3 class="panel-title">Calculated results</h3>
                    </div>
                    <div class="panel-body">
                        <textarea name="report" id="report" placeholder="Report result:"><?php echo $report; ?></textarea>
                    </div>
                </div>

                <input type="submit" value="Save" class="btn btn-success btn-sm">
                <br><br>
                <a href="dashboard.php" class="button">Go back</a>

            </form>

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