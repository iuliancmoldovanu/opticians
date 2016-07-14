<?php
// READ THE FILE NAME FROM URL ex: "index", "login"
$fileName = explode("/", $_SERVER['PHP_SELF']);
$fileName = explode(".", $fileName[1]);
if(!$user->isLoggedIn()){ // check user login (user should not be logged in)
    if($fileName[0] == "index" || $fileName[0] == "Index"){
    ?>
    <nav class="navbar">
        <div class="inner">
            <a href="login.php">Login</a>
        </div>
    </nav>
    <?php
    }else{
    ?>
        <nav class="navbar">
            <div class="inner">
                <a href="index.php">Home</a>
                <?php
                if(Session::exists('logout')) {
                    echo '<span style="margin-left: 20px; font-size: 12px" >';
                    echo(Session::flash('logout'));
                    echo '</span>';
                }
                ?>
            </div>
        </nav>
    <?php

    }
}else{ // user must be logged in to see the code bellow
?>
    <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="navContainer">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                        aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php">The Eye Centre</a>
            </div>
            <div id="navbar" class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <?php
                    if($user->getData()->role == 'admin') {
                    ?>
                        <li><!-- add link for admins --></li>
                    <?php
                    }
                    ?>
                    <li><a href = "managePassword.php">Manage password</a></li>
                    <li><a href = "dashboard.php">Dashboard</a></li>
                    <li><a href = "logout.php">Log out</a></li>
                    <li><p class = "navbar-text">Signed in as <?php echo $user->getData()->role . ' (Username: ' . $user->getData()->username . ')'; ?></p></li>
                </ul>
            </div>

            <!--/.nav-collapse -->
        </div>
    </nav>
<?php
}
?>