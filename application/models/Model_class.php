<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Model_class extends MY_Model
{
  public function __construct()
    {
        parent::__construct();
    }
    public function login($params=[]){
        $result=[];
        if(!empty($params)){
            $query = $this->db->query("SELECT * FROM users WHERE username = '$username'");
            $result = $query->result_array()[0];
        }
        return $result;
    }
}