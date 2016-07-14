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
        $salt = Hash::salt(32);
        try{
            $db->update(
                'user',
                array(
                    'password'      => Hash::make(123, $salt),
                    'salt'          => $salt
                ),
                'id',
                Input::get('id')
            );
        }catch(Exception $e){
            die($e->getMessage());
        }
        if(!$db->error()){
            Session::flash('successMessage', '<br><br><div class="alert alert-success"><strong>Success!</strong><br>You have successfully reset the password.</div>');
        }else{
            Session::flash('failMessage', '<div class="alert alert-danger"><strong>Error.</strong><br>Nothing has been change. Contact administrator if the problem persist !!!</div>');
        }
        Redirect::to('dashboard.php');
    }
}

?>
