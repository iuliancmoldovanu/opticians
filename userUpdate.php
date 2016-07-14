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
$hasErrors = false;
if(Input::exists('post')){

    $validate = new Validate();
    $validation = $validate->check($_POST, array(
        'username' => array(
            'name' => 'username',
            'required' => true,
            'min' => 3,
            'max' => 20
        ),
        'f_name' => array(
            'name' => 'first name',
            'required' => true,
            'min' => 3,
            'max' => 50
        ),
        'l_name' => array(
            'name' => 'last name',
            'required' => true,
            'min' => 3,
            'max' => 50
        ),
        'phone' => array(
            'name' => 'phone no',
            'required' => true,
            'numeric' => true,
            'min' => 11,
            'max' => 20
        ),
        'email' => array(
            'name' => 'email',
            'min' => 7,
            'max' => 50
        ),
        'role' => array(
            'name' => 'role',
            'option' => true
        )
    ));

    if ($validate->passed()) {
        try{
            $db->update(
                'user',
                array(
                    'first_name'    => Input::get('f_name'),
                    'last_name'     => Input::get('l_name'),
                    'username'      => Input::get('username'),
                    'address'       => Input::get('address'),
                    'phone'         => Input::get('phone'),
                    'email'         => Input::get('email'),
                    'dob'           => Input::get('dob'),
                    'role'          => Input::get('role')
                ),
                'id',
                Input::get('id')
            );
        }catch(Exception $e){
            die($e->getMessage());
        }
        if(!$db->error()){
            Session::flash('successMessage', '<br><br><div class="alert alert-success"><strong>Success!</strong><br>You have successfully updated the user.</div>');
        }else{
            Session::flash('failMessage', '<div class="alert alert-danger"><strong>Error.</strong><br>Nothing has been change. Contact administrator if the problem persist !!!</div>');
        }
        Redirect::to('dashboard.php');
    } else {
        $hasErrors = true;
    }
}

$username = $userDetails[0]->username;
$f_name = $userDetails[0]->first_name;
$l_name = $userDetails[0]->last_name;
$address = $userDetails[0]->address;
$phone = $userDetails[0]->phone;
$email = $userDetails[0]->email;
$dob = $userDetails[0]->dob;
$role = $userDetails[0]->role;
if(Input::exists('post')) {
    if ($username != escape(Input::get('username'))) {
        $username = escape(Input::get('username'));
    }
    if ($f_name != escape(Input::get('f_name'))) {
        $f_name = escape(Input::get('f_name'));
    }
    if ($l_name != escape(Input::get('l_name'))) {
        $l_name = escape(Input::get('l_name'));
    }
    if ($address != escape(Input::get('address'))) {
        $address = escape(Input::get('address'));
    }
    if ($phone != escape(Input::get('phone'))) {
        $phone = escape(Input::get('phone'));
    }
    if ($email != escape(Input::get('email'))) {
        $email = escape(Input::get('email'));
    }
    if ($dob != escape(Input::get('dob'))) {
        $dob = escape(Input::get('dob'));
    }
    if ($role != escape(Input::get('role'))) {
        $role = escape(Input::get('role'));
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

                    <input type="text" name="username" id="username" value="<?php echo $username; ?>" placeholder="Username*" autocomplete="off">

                    <input type="text" name="f_name" id="f_name" value="<?php echo $f_name; ?>" placeholder="First name*">

                    <input type="text" name="l_name" id="l_name" value="<?php echo $l_name; ?>" placeholder="Last name*">

                    <input type="text" name="phone" id="phone" value="<?php echo $phone; ?>" placeholder="Phone No*">

                    <input type="text" name="dob" id="dob" value="<?php echo $dob; ?>" onfocus="(this.type='date')" onblur="(this.type='text')" placeholder="Date of Birth">

                    <input type="email" name="email" id="email" value="<?php echo $email; ?>" placeholder="Email address">

                    <textarea name="address" id="address" placeholder="Address"><?php echo $address; ?></textarea>

                    <select class="selectpicker" name="role">
                        <option default value="default">Select role...</option>
                        <?php
                        switch($role){
                            case 'admin':
                                echo '<option value="admin" selected="selected">Admin</option>
                                    <option value="optician">Optician</option>
                                    <option value="patient">Patient</option>';
                                break;
                            case 'optician':
                                echo '<option value="admin">Admin</option>
                                    <option value="optician" selected="selected">Optician</option>
                                    <option value="patient">Patient</option>';
                                break;
                            case 'patient':
                                echo '<option value="admin">Admin</option>
                                    <option value="optician">Optician</option>
                                    <option value="patient" selected="selected">Patient</option>';
                                break;
                            default:
                                echo '<option value="admin">Administrator</option>
                                    <option value="optician">Optician</option>
                                    <option value="patient">Patient</option>';
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