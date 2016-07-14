<?php
require_once 'core/init.php';
$user = new User();
if($user->isLoggedIn()) { // if user logged in send him to the dashboard page
    Redirect::to('dashboard.php');
}

?>
<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Home</title>
    <?php include("Layout_PHP/headPublic.php"); ?>

</head>
<body>
<?php include("Layout_PHP/nav.php"); ?>

<header class="container">
    <section class="content">
        <div class="centerTextBox">
            <h1>Welcome to The Eye Centre</h1>
            <h2>Your Vision.<span style="color: #cafaea;"> Our Mission.</span></h2>
            <h3>At the eye centre, we are dedicated to looking after your eyes, old or young, in our family friendly practice.</h3>
        </div>
    </section>
</header>


</body>
</html>