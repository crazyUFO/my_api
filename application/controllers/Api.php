<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {

	public function __construct()
	{
			parent::__construct();
			$this->load->model('Model_class','model');
			if(!strstr($_SERVER['REQUEST_URI'],'login') && !$this->check_token()){
					$output =[ 
						'status'=>0,
						'msg'=>'授权信息过期，请重新登录！'
					];
					echo json_decode($output);
					return;
			}
	}
	public function index()
	{
		echo 'api server is running!';
	}
	/*登录*/ 
	public function login()
	{
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		$query = $this->db->query("SELECT * FROM users WHERE username = '$username'");
		$data = isset($query->result_array()[0]) ? $query->result_array()[0] : [];
		$output=[];
		if(!empty($data)){
			if($data['password'] == md5($password)){/*密码正确返回token*/
					$time = time()+86400;
					$token = $this->create_token($username,$password,$time);
					$sql = "UPDATE users SET token = ? WHERE username = ?";
					$query = $this->db->query($sql,[$token,$username]);//将token插入表
					if($query){
						$output=[
							'status'=>1,
							'token'=>$token,
						];
					}else{
						$output=[
							'status'=>0,
							'msg'=>'插入表失败',
						];
					}
			}else{
				$output=[
					"status"=>0,
					"msg"=>"账号或密码错误"
				];
			}
		}else{

				$output=[
					"status"=>0,
					"msg"=>"账号或密码错误"
				];
		}
		echo json_encode($output);
	}
	/*生成token过期时间为24小时*/
	public function create_token($uname,$pwd,$time){
			$token = md5($uname).'.'.md5($pwd).'.'.md5($time);
			return $token;
	}
	/*检查token*/
	public function check_token(){
		if(isset($_SERVER['HTTP_TOKEN']) && !empty($_SERVER['HTTP_TOKEN'])){
				$arr = explode('.',$_SERVER['HTTP_TOKEN']);
				print_r($arr);die;
				$time = md5($arr[2]);
				if($item > time()){
					return true;
				}
				return false;
		}else{
			return false;
		}
	}
}
