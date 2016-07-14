<?php

class Validate
{
    private $_passed = false;
    private $_errors = array();
    private $_db = null;

    public function __construct()
    {
        $this->_db = DB::getInstance();
    }

    public function check($source, $items = array()){
        foreach($items as $item => $rules){
            foreach($rules as $rule => $rule_value){
                $item = escape($item);
                $value = trim($source[$item]);
                if($rule == 'required' && empty($value)){
                    var_dump($rule == 'required');
                    $this->setErrors(ucfirst($rules['name']) . " is required.");
                }else if(!empty($value)){
                    switch($rule){
                        case 'min':
                            if(strlen($value) < $rule_value){
                                $this->setErrors(ucfirst($rules['name']) . " must have a minimum of {$rule_value} characters.");
                            }
                            break;
                        case 'max':
                            if(strlen($value) > $rule_value){
                                $this->setErrors(ucfirst($rules['name']) . " must have a maximum of {$rule_value} characters.");
                            }
                            break;
                        case 'matches':
                            if($value != $source[$rule_value]){
                                $this->setErrors(ucfirst($rule_value) . " must match the ".$rules['name'].".");
                            }
                            break;
                        case 'unique':
                            $check = $this->_db->get($rule_value, array($item, '=', $value));
                            if($check->count()){
                                $this->setErrors(ucfirst($rules['name']) . " already exists.");
                            }
                            break;
                        case 'numeric':
                            if (!is_numeric($value)) {
                                $this->setErrors(ucfirst($rules['name']) . " must contain only numbers.");
                            }
                            break;
                        case 'option':
                            if ($value == 'default') {
                                $this->setErrors('Select a ' . $rules['name'] . " from the list.");
                            }
                            break;
                        case 'min_date':
                            if ($value < date('Y-m-d')) {
                                $this->setErrors(ucfirst($rules['name']) . " must be ".date('Y-m-d')." or later.");
                            }
                            break;
                        case 'week_days':
                            if (date("D", strtotime($value)) == 'Sat' || date("D", strtotime($value)) == 'Sun') {
                                $this->setErrors(ucfirst($rules['name']) . " must be from Monday to Friday.");
                            }
                            break;
                        case 'min_hour':
                            if ($value < date('H') && Input::get('date') == date('Y-m-d')) {
                                $this->setErrors(ucfirst($rules['name']) . " must be " . date('H:i') . " or later.");
                            }
                            break;
                        case 'work_time':
                            if ($value < '09:00' || $value > '17:30') {
                                $this->setErrors(ucfirst($rules['name']) . " must be between 09:00 to 17:30.");
                            }
                            break;
                    }
                }
            }
        }
        if(empty($this->_errors)){
            $this->_passed = true;
        }
    }

    private function setErrors($errors)
    {
        $this->_errors[] = $errors;
    }

    public function getErrors()
    {
        return $this->_errors;
    }

    public function passed()
    {
        return $this->_passed;
    }
}