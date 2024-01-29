<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require(APPPATH.'/libraries/REST_Controller.php');

use Restserver\Libraries\REST_Controller;

class User extends REST_Controller {

    public function __construct(){
		parent::__construct();
        $this->load->library('Authorization_Token');
		$this->load->model('Api_model');
	}

    public function signup_post(){
        // set form validation rules
        $this->form_validation->set_rules('name', 'Name', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[user.email]');
        $this->form_validation->set_rules('password', 'Password', 'required|regex_match[/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/]');

        if ($this->form_validation->run() === false){
			$this->response(['Please enter valid data'], REST_Controller::HTTP_OK);
		}else{
            $data=[
                'name' => $this->input->post('name'),
                'email' => $this->input->post('email'),
                'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT)
            ];
            if($result =$this->Api_model->signup($data)){
                $token_data['user_id'] = $result; 
                $token_data['name'] = $data['name'];
                $token_data['email'] = $data['email'];
                $tokenData = $this->authorization_token->generateToken($token_data);

                $final = array();
                $final['access_token'] = $tokenData;
                $final['status'] = true;
                $final['user_id'] = $result;
                $final['message'] = 'You have successfully register!';

                $this->response($final, REST_Controller::HTTP_OK); 

			}else{
                $this->response(['Registeration failed!. Please try again.'], REST_Controller::HTTP_OK);
            }
        }
    }

    public function login_post(){
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() == false){
            $this->response(['Enter a valid email and password'], REST_Controller::HTTP_OK);
		}else{
            $email= $this->input->post('email');
            $password= $this->input->post('password');
            if($user =$this->Api_model->login($email)){
                if(password_verify($password, $user->password)){
                    $token_data['user_id'] = $user->id;
                    $token_data['email'] = $user->email; 
                    $tokenData = $this->authorization_token->generateToken($token_data);

                    $final = array();
                    $final['access_token'] = $tokenData;
                    $final['status'] = true;
                    $final['message'] = 'Login success!';

                    $this->response($final, REST_Controller::HTTP_OK); 
                }else{
                    $this->response(['Password is wrong'], REST_Controller::HTTP_OK);
                }
            }else{
                $this->response(['No account exists with this email'], REST_Controller::HTTP_OK);
            }
        }
    }
}