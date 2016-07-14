<?php
require_once 'core/init.php';
$user = new User();

if(!$user->isLoggedIn()) { // check user login (user should not be logged in)
    Redirect::to('login.php');
}else{
    if($user->getData()->role != 'admin') {
        Session::flash('restrictUserMessage', '<div class="alert alert-danger"><strong>Attention</strong><br>You are not allowed to access that page.</div>');
        Redirect::to('dashboard.php');
    }else{
        if(Input::exists('get')) {
            $db = DB::getInstance();
            $db->delete('user', array('id', '=', Input::get('id')));
            if(!$db->error()){
                Session::flash('successMessage', '<div class="alert alert-success"><strong>Success!</strong><br>The user has been deleted successfully.</div>');
            }else{
                Session::flash('failMessage', '<div class="alert alert-danger"><strong>Error.</strong><br>Nothing has been change. Contact administrator if the problem persist !!!</div>');
            }
            Redirect::to('dashboard.php');
        }
    }
}