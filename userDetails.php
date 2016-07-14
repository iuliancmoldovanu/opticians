<?php
require_once 'core/init.php';
$user = new User();
$db = DB::getInstance();
if(!$user->isLoggedIn()) { // check user login (user should not be logged in)
    Redirect::to('login.php');
}else{
    if($user->getData()->role != 'admin') {
        Session::flash('restrictUserMessage', '<div class="alert alert-danger"><strong>Attention</strong><br>You are not allowed to access that page.</div>');
        Redirect::to('dashboard.php');
    }else{
        if(Input::exists('get')) {
            $userDetails = $db->get('user', array('id', '=', Input::get('id')))->result();
        }
    }
}
$f_name = $userDetails[0]->first_name;
$l_name = $userDetails[0]->last_name;
$address = $userDetails[0]->address;
$phone = $userDetails[0]->phone;
$email = $userDetails[0]->email;
$dob = $userDetails[0]->dob;
$username = $userDetails[0]->username;
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
                    Account DETAILS</p>
                <table class="table  table-inverse">
                    <thead>
                    <tr>
                        <th>Id</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Username</th>
                        <th>Position</th>
                        <th>Address</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>DOB</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo $userDetails[0]->id; ?></td>
                            <td><?php echo ucfirst($userDetails[0]->first_name); ?></td>
                            <td><?php echo ucfirst($userDetails[0]->last_name); ?></td>
                            <td><?php echo $userDetails[0]->username; ?></td>
                            <td><?php echo $userDetails[0]->role; ?></td>
                            <td><?php echo $userDetails[0]->address; ?></td>
                            <td><?php echo $userDetails[0]->phone; ?></td>
                            <td><?php echo $userDetails[0]->email; ?></td>
                            <td><?php echo $userDetails[0]->dob; ?></td>
                        </tr>
                    <tbody>
                </table>
                <br><br>
                <a href="dashboard.php" class="button">Go back</a>
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