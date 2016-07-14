<?php
require_once 'core/init.php';
$user = new User();
if (!$user->isLoggedIn()) { // check user login (user should not be logged in)
    Redirect::to('login.php');
} else {

    if ($user->getData()->role != 'admin') {
        Session::flash('restrictUserMessage',
            '<div class="alert alert-danger">
            <strong>Attention</strong><br>You are not allowed to access that page.</div>');
        Redirect::to('dashboard.php');
    }

}

$hasErrors = false;
if (Input::exists()) {

    $validate = new Validate();
    $validation = $validate->check($_POST, array(
        'username' => array(
            'name' => 'username',
            'required' => true,
            'min' => 3,
            'max' => 20,
            'unique' => 'user'
        ),
        'password' => array(
            'name' => 'password',
            'required' => true,
            'min' => 3,
            'max' => 20
        ),
        'password_again' => array(
            'name' => 'confirm password',
            'required' => true,
            'matches' => 'password'
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
        $user = new User();
        $salt = Hash::salt(32);
        try {
            $user->create(array(
                'first_name' => Input::get('f_name'),
                'last_name' => Input::get('l_name'),
                'username' => Input::get('username'),
                'password' => Hash::make(Input::get('password'), $salt),
                'address' => Input::get('address'),
                'phone' => Input::get('phone'),
                'email' => Input::get('email'),
                'role' => Input::get('role'),
                'date_created' => date('Y-m-d H:i:s'),
                'dob' => Input::get('dob'),
                'salt' => $salt
            ));
        } catch (Exception $e) {
            die($e->getMessage());
        }
        $db = DB::getInstance();
        if (!$db->error()) {
            Session::flash('successMessage', '<br><br><div class="alert alert-success"><strong>Success!</strong><br>You have successfully registered a new ' . Input::get('role') . ' !</div>');
        } else {
            Session::flash('failMessage', '<div class="alert alert-danger"><strong>Error.</strong><br>Nothing has been change. Contact administrator if the problem persist !!!</div>');
        }
        Redirect::to('dashboard.php');
    } else {
        $hasErrors = true;
    }
    //}
}
?>
<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register</title>

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
                    Register new account</p>

                <form class="form" action="" method="post">
                    <?php
                    if ($hasErrors) {
                        echo '<div class="errors">';
                        foreach ($validate->getErrors() as $error) {
                            echo '<span>' . $error . '</span><br>';
                        }
                        echo '</div>';
                    }
                    ?>

                    <input type="text" name="username" id="username"
                           value="<?php echo escape(Input::get('username')); ?>" placeholder="Username*"
                           autocomplete="off">

                    <input type="password" name="password" id="password" placeholder="Password*">

                    <input type="password" name="password_again" id="password_again" placeholder="Confirm password*">

                    <input type="text" name="f_name" id="f_name" value="<?php echo escape(Input::get('f_name')); ?>"
                           placeholder="First name*">

                    <input type="text" name="l_name" id="l_name" value="<?php echo escape(Input::get('l_name')); ?>"
                           placeholder="Last name*">

                    <input type="text" name="phone" id="phone" value="<?php echo Input::get('phone'); ?>"
                           placeholder="Phone No*">

                    <input type="text" name="dob" id="dob" value="<?php echo escape(Input::get('dob')); ?>"
                           onfocus="(this.type='date')" onblur="(this.type='text')" placeholder="Date of Birth">

                    <input type="email" name="email" id="email" value="<?php echo escape(Input::get('email')); ?>"
                           placeholder="Email address">

                    <textarea name="address" id="address"
                              placeholder="Address"><?php echo escape(Input::get('address')); ?></textarea>

                    <select class="selectpicker" name="role">
                        <option default value="default">Select role...</option>
                        <?php
                        $selected = Input::get('role');
                        switch ($selected) {
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

                    <input type="hidden" name="token" value="<?php echo Token::generate(); ?>">

                    <input type="submit" value="Register" class="btn btn-success btn-sm">

                </form>
            </div>
        </div>
    </section>
</header>

</body>
</html>