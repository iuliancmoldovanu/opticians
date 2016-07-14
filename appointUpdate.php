<?php
require_once 'core/init.php';
$user = new User();
$db = DB::getInstance();
if(!$user->isLoggedIn()) { // check user login (user should not be logged in)
    Redirect::to('login.php');
}else{
    if($user->getData()->role != 'optician') {
        Session::flash('restrictUserMessage', '<div class="alert alert-danger"><strong>Attention</strong><br>You are not allowed to access that page.</div>');
        Redirect::to('dashboard.php');
    }else{
        if(Input::exists('get')) {
            $appointDetails = $db->get('appointment', array('id', '=', Input::get('id')))->result();
        }
    }
}
$hasErrors = false;
if(Input::exists('post')){

    $validate = new Validate();
    $validation = $validate->check($_POST, array(
        'date' => array(
            'name' => 'date',
            'required' => true,
            'min_date' => date('Y-m-d'),
            'week_days' => date("D", strtotime(Input::get('date')))
        ),
        'time' => array(
            'name' => 'time',
            'required' => true,
            'min_hour' => date('H:i'),
            'work_time' => date('H:i')
        ),
        'details' => array(
            'name' => 'details',
            'required' => true,
            'min' => 7,
            'max' => 500
        ),
        'patient' => array(
            'name' => 'patient',
            'option' => true
        )
    ));

    if ($validate->passed()) {
        try{
            $db->update(
                'appointment',
                array(
                    'patient_id'   => Input::get('patient'),
                    'date'      => Input::get('date') . ' ' . Input::get('time'),
                    'details'   => Input::get('details'),
                    'status'    => ''
                ),
                'id',
                Input::get('id')
            );
        }catch(Exception $e){
            die($e->getMessage());
        }
        if(!$db->error()){
            Session::flash('successMessage', '<br><br><div class="alert alert-success"><strong>Success!</strong><br>You have successfully updated the appointment.</div>');
        }else{
            Session::flash('failMessage', '<div class="alert alert-danger"><strong>Error.</strong><br>Nothing has been change. Contact administrator if the problem persist !!!</div>');
        }
        Redirect::to('dashboard.php');
    } else {
        $hasErrors = true;
    }
}
$patient = $appointDetails[0]->patient_id;
$datetime = explode(" ", $appointDetails[0]->date);
$date = $datetime[0];
$time = substr($datetime[1], 0, 5);
$details = $appointDetails[0]->details;
$status = $appointDetails[0]->status;
if(Input::exists('post')) {
    if ($patient != escape(Input::get('patient'))) {
        $patient = escape(Input::get('patient'));
    }
    if ($date != escape(Input::get('date'))) {
        $date = escape(Input::get('date'));
    }
    if ($time != escape(Input::get('time'))) {
        $time = escape(Input::get('time'));
    }
    if ($details != escape(Input::get('details'))) {
        $details = escape(Input::get('details'));
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
</head>
<body>
<?php include("Layout_PHP/nav.php"); ?>
<header class="container">
    <section class="content">
        <div class="centerTextBox">
            <div class="wrap">
                <p class="form-title">
                    Update account</p>
                <form class="form" action="" method="post">
                    <?php
                    if($hasErrors) {
                        echo '<div class="errors">';
                        foreach ($validate->getErrors() as $error) {
                            echo '<span>' . $error . '</span><br>';
                        }
                        echo '</div>';
                    }
                    ?>

                    <input type="text" name="date" value="<?php echo $date; ?>"
                           onfocus="(this.type='date')" onblur="(this.type='text')" placeholder="Date*">

                    <input type="text" name="time" value="<?php echo $time; ?>"
                           onfocus="(this.type='time')" onblur="(this.type='text')" placeholder="Time*">


                    <textarea name="details"
                              placeholder="Details"><?php echo $details; ?></textarea>

                    <select class="selectpicker" name="patient">
                        <option default value="default">Select patient...</option>
                        <?php
                        $db = DB::getInstance()->get('user', array('role', '=', 'patient'));

                        for ($i = 0; $i < count($db->result()); $i++) {
                            switch ($patient) {
                                case $db->result()[$i]->id:
                                    echo '<option value="' . $db->result()[$i]->id . '" selected="selected">' . $db->result()[$i]->first_name . ' ' . $db->result()[$i]->last_name . '</option>';
                                    break;
                                default:
                                    echo '<option value="' . $db->result()[$i]->id . '">' . ucfirst($db->result()[$i]->first_name) . ' ' . ucfirst($db->result()[$i]->last_name) . '</option>';
                            }
                        }

                        ?>
                    </select>

                    <input type="submit" value="Save" class="btn btn-success btn-sm" >
                    <br><br>
                    <a href="dashboard.php" class="button">Go back</a>

                </form>
            </div>
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