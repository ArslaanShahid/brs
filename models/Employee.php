<?php
date_default_timezone_get("Asia/Karachi");
require_once 'DbTrait.php';

Class Employee{
    use DbTrait;
    private $id;
    private $name;
    private $address;
    private $type;
    private $date_of_birth;
    private $gender;
    private $mobile_no;

    public function __set($name, $value)
    {
        $method = "set" . $name;
        if(!method_exists($this,$method)){
            throw new Exception("Set Property $name Does'nt Exist");
        }
        $this->$method($value);
    }
    public function __get($name)
    {
        $method = "get" . $name;
        if(!method_exists($this,$method)){
            throw new Exception("Set Property $name Does'nt Exist");
        }
        return $this->$method();
    }
    private function setEmp_id($id){
        if(!is_numeric($id)|| $id <=0){
            throw new Exception ("Invalid / Missing Employee ID");
        }
        $this->id = $id;
    }
    private function getEmp_id(){
        return $this->id;
    }

    private function setName($name){
        $name = trim($name);
        if($name == "" || is_numeric($name)){
            throw new Exception ("Invalid / Missing Name");
        }
        $this->name = ucfirst(strtolower($name));
    }
    private function getName(){
        return $this->name;
    }
    private function setMobile_no($mobile_no){
        $reg = "/^\+\d{2}\-\d{3}\-\d{7}$/";
        if(!preg_match($reg, $mobile_no)){
            throw new Exception("Invalid / Missing Number");
        }
        $this->mobile_no = $mobile_no;
    }
    private function getMobile_no(){
        return $this->mobile_no;
    }
    private function setGender($gender){
        $genders = ['male', 'female'];
        if(!in_array($gender, $genders)){
            throw new Exception("Invalid / Missing Gender");
        }
        $this->gender = $gender;
    }
    private function getGender(){
        return $this->gender;
    }
    private function setDate_of_birth($date_of_birth){
        if(empty($date_of_birth)){
            throw new Exception ("Missing Date of Birth");
        }
        $this->date_of_birth = $date_of_birth;
    }
    private function getDate_of_birth(){
        return $this->date_of_birth;
    }

    private function setAddress($address){
        $address = trim($address);
        if($address == ""){
            throw new Exception("Address is Missing");
        }
        $this->address = $address;
    }
    private function getAddress(){
        return $this->address;
    }
    private function setType($type){
        $type = trim($type);
        if($type == ""){
            throw new Exception ("Job Type is Missing");
        }
        $this->type=$type;
    }
    private function getType(){
        return $this->type;
    }

    public function addEmp(){
    $obj_db = self::obj_db();
    $now = date("Y-m-d H:i:s");
    $query = "INSERT into employees "
    ."(`id`,`name`,`gender`, `type` ,`address` , `mobile_no`, `reg_date`)"
    . " values "
    . "( NULL , '$this->name' , '$this->gender' , '$this->type' , '$this->address' , '$this->mobile_no' , '$now')";
    $obj_db->query($query);
    if($obj_db->errno){
        throw new Exception("Query Insert Error ". $obj_db->errno. $obj_db->error);
    }
    }
    public static function viewEmp(){
        $obj_db=self::obj_db();
        $query= " select * from employees";
        $result = $obj_db->query($query);
        if($obj_db->errno){
            throw new Exception("Select Error - $obj_db->errno - $obj_db->error");
        }
        while ($data = $result->fetch_object()){
            $employees[] = $data;
        }
        return $employees;
    }

}