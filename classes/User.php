<?php
class User{
    private $_db,
            $_data,
            $_session_name,
            $_isLoggedIn;

    public function __construct($user = null){
        $this->_db = DB::getInstance();
        $this->_session_name = Config::get('session/session_name');
        if(!$user){
            if(Session::exists($this->_session_name)) {
                $user = Session::get($this->_session_name);
                if ($this->find($user)) {
                    $this->_isLoggedIn = true;
                }
            }
        }else{
            $this->find($user);
        }
    }
    public function update($fields = array(), $id = null){
        if(!$id && $this->isLoggedIn()){
            $id = $this->getData()->id;
        }
        if(!$this->_db->update('user', $fields, 'id', $id)){
            throw new Exception('There was a problem updating account.');
        }
    }
    public function create($fields = array()){
       if(!$this->_db->insert('user', $fields)){
            throw new Exception('There was a problem creating an account.');
       }
    }


    public function login($username = null, $password = null){
        if(!$username && !$password && $this->exists()){
            Session::put($this->_session_name, $this->getData()->id);
        }else {
            $user = $this->find($username);
            if ($user) {
                if ($this->getData()->password == Hash::make($password, $this->getData()->salt)) {
                    Session::put($this->_session_name, $this->getData()->id);
                    return true;
                }
            }
        }
        return false;
    }


    public function find($user = null){
        if($user){
            $field = (is_numeric($user)) ? 'id' : 'username';
            $data = $this->_db->get('user', array($field, '=', $user));
            if($data->count()){
                $this->_data = $data->first();
                return true;
            }
        }
        return false;
    }


    public function hasPermission($key){
        $group = $this->_db->get('groups', array('id', '=', $this->getData()->group));
        if($group->count()){
            $permissions = json_decode($group->first()->permission, true);
            if($permissions[$key] == true){
                return true;
            }
        }
        return false;
    }
    public function exists(){
        return (!empty($this->_data)) ? true : false;
    }
    public function getData(){
        return $this->_data;
    }
    public function isLoggedIn(){
        return $this->_isLoggedIn;
    }
    public function logout(){
        Session::delete($this->_session_name);
    }

}