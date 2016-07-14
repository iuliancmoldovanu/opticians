<?php
require_once 'core/init.php';
$user = new User();
if($user->isLoggedIn()) { // if user logged in send him to the dashboard page
   Redirect::to('dashboard.php');
}
$hasErrors = false;
$isUserFound = false;
if(Input::exists()){
        $validate = new Validate();
        $validation = $validate->check($_POST, array(
            'username' => array(
                'required'  => true,
                'name'      => 'username'
            ),
            'password' => array(
                'required' => true,
                'name' => 'password'
            )
        ));
        if($validate->passed()){
            $login = $user->login(Input::get('username'), Input::get('password'));
            if($login){
                Redirect::to('dashboard.php');
            }else{
                $isUserFound = true;
            }
        }else{
            $hasErrors = true;
        }
}
?>

<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Account login</title>
    <?php include("Layout_PHP/headPublic.php"); ?>
    <link rel="stylesheet" href="stylesheets/loginFormStyle.css">

</head>
<body>
<?php include("Layout_PHP/nav.php");?>

<header class="container">
    <section class="content">
        <div class="centerTextBox">
            <div class="wrap">
                <p class="form-title">
                    Account Login</p>
                <form class="form" action="" method="post">
                    <?php
                    if($hasErrors) {
                        echo '<div class="errors">';
                        foreach ($validate->getErrors() as $error) {
                            echo '<span>' . $error . '</span><br>';
                        }
                        echo '</div>';
                    }
                    if($isUserFound){
                        echo '<div class="errors">';
                        echo '<span>Login failed !!!<br>Username and password not match.</span><br>';
                        echo '</div>';
                    }
                    ?>
                    <input type="text" name="username" id="username"
                           value="<?php echo escape(Input::get('username')); ?>"
                           placeholder="Username*" autocomplete="off">

                    <input type="password" name="password" id="password" placeholder="Password*">

                    <input type="submit" value="Sign In" class="btn btn-success btn-sm" />
                </form>
            </div>
        </div>
    </section>
</header>

</body>
</html>