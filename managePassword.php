<?php
require_once 'core/init.php';
$user = new User();
if (!$user->isLoggedIn()) { // check user login (user should not be logged in)
    Redirect::to('login.php');
}

$hasErrors = false;
$isPassCorrect = false;
if (Input::exists()) {
    $validate = new Validate();
        $validation = $validate->check($_POST, array(
        'current_password' => array(
            'name' => 'current password',
            'required' => true,
            'min' => 3,
            'max' => 20
        ),
        'new_password' => array(
            'name' => 'new password',
            'required' => true,
            'min' => 3,
            'max' => 20
        ),
        'new_password_again' => array(
            'name' => 'confirm new password',
            'required' => true,
            'matches' => 'new_password'
        )
    ));
    $user = new User();
    $login = $user->login($user->getData()->username, Input::get('current_password'));
    if(!$login && Input::get('current_password') != '') {
        $isPassCorrect = true;
    }else {
        if ($validate->passed()) {
            if ($login) {
                try {
                    $salt = Hash::salt(32);
                    $user->update(array(
                        'password' => Hash::make(Input::get('new_password'), $salt),
                        'salt' => $salt
                    ));
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            }
            $db = DB::getInstance();
            if (!$db->error()) {
                Session::flash('successMessage', '<br><br><div class="alert alert-success"><strong>Success!</strong><br>You have successfully updated the password!</div>');
            } else {
                Session::flash('failMessage', '<div class="alert alert-danger"><strong>Error.</strong><br>Nothing has been change. Contact administrator if the problem persist !!!</div>');
            }
            Redirect::to('dashboard.php');
        } else {
            $hasErrors = true;
        }
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
    <title>Update Password</title>

    <?php include("Layout_PHP/headPrivate.php"); ?>
    <link rel="stylesheet" href="stylesheets/loginFormStyle.css">
</head>
<body>
<?php include("Layout_PHP/nav.php"); ?>

<header class="container">
    <section class="content">
        <div class="centerTextBox">
            <div class="wrap">
                <p class="form-title">Update Password</p>

                <form class="form" action="" method="post">
                    <?php
                    echo '<div class="errors">';
                    if($isPassCorrect){
                        echo '<span>Current password not match.<br>';
                    }
                    if ($hasErrors) {
                        foreach ($validate->getErrors() as $error) {
                            echo '<span>' . $error . '</span><br>';
                        }
                    }
                    echo '</div>';
                    ?>
                    <input type="password" name="current_password" id="current_password" value="<?php echo escape(Input::get('current_password')); ?>" placeholder="Current Password*">

                    <input type="password" name="new_password" id="new_password" value="<?php echo escape(Input::get('new_password')); ?>" placeholder="New Password*">

                    <input type="password" name="new_password_again" id="new_password_again" value="<?php echo escape(Input::get('new_password_again')); ?>" placeholder="Confirm New Password*">

                    <input type="submit" value="Update Password" class="btn btn-success btn-sm">

                </form>
            </div>
        </div>
    </section>
</header>

</body>
</html>