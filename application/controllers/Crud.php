<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require(APPPATH.'/libraries/REST_Controller.php');

use Restserver\Libraries\REST_Controller;

class Crud extends REST_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->library('Authorization_Token');	
        $this->load->model('Api_model');
    }

    public function create_post(){
        $headers = $this->input->request_headers(); 
		if(isset($headers['Authorization'])){
			$decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
            if($decodedToken['status']){
                //$email = $this->input->post('email');
                $user_id = $decodedToken['user_id'];
                $task = $this->input->post('task');
                //if($email==$decodedToken['email']){
                    $data = $this->Api_model->create($user_id, $task);
                    if($data){
                        $this->response(['Task created successfully.'], REST_Controller::HTTP_OK);
                    }else{
                        $this->response(['Failed to create task.'], REST_Controller::HTTP_BAD_REQUEST);
                    }
                /*}else{
                    $this->response(['Invalid email.'], REST_Controller::HTTP_OK);
                }*/
            }else{
                $this->response($decodedToken);
            }
		}else{
			$this->response(['Authentication failed'], REST_Controller::HTTP_BAD_REQUEST);
		}
    }

    public function read_get($user_id){
        $headers = $this->input->request_headers(); 
		if(isset($headers['Authorization'])){
			$decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
            if($decodedToken['status']){
                if(!empty($user_id) && $user_id==$decodedToken['user_id']){
                    $data = $this->Api_model->read($user_id);
                    $this->response($data, REST_Controller::HTTP_OK);
                }else{
                    $this->response(['Invalid user.'], REST_Controller::HTTP_BAD_REQUEST);
                }
            }else{
                $this->response($decodedToken);
            }
        }else{
            $this->response(['Authentication failed'], REST_Controller::HTTP_BAD_REQUEST);   
        }
    }

    public function readtask_get($task_id){
        $headers = $this->input->request_headers(); 
		if(isset($headers['Authorization'])){
			$decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
            if($decodedToken['status']){
                if(!empty($task_id)){
                    $user_id = $decodedToken['user_id'];
                    $data = $this->Api_model->readtask($task_id, $user_id);
                    if($data){
                        $this->response($data, REST_Controller::HTTP_OK);
                    }else{
                        $this->response(['No records found'], REST_Controller::HTTP_BAD_REQUEST);   
                    }
                }
            }else{
                $this->response($decodedToken);
            }
        }else{
            $this->response(['Authentication failed'], REST_Controller::HTTP_BAD_REQUEST);   
        }
    }

    public function update_put($task_id){
        $headers = $this->input->request_headers(); 
		if(isset($headers['Authorization'])){
			$decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
            if($decodedToken['status']){
                $data = json_decode(file_get_contents('php://input'), true);
                $task = $data['task'];
                $status = $data['status'];
                $user_id = $decodedToken['user_id'];
                $result = $this->Api_model->update($task_id, $task, $status, $user_id);
                if($result){
                    $this->response(['Task updated successfully.'], REST_Controller::HTTP_OK);
                }else{
                    $this->response(['Failed to update task! No records found.'], REST_Controller::HTTP_BAD_REQUEST);
                }
            }else{
                $this->response($decodedToken);
            }
        }else{
            $this->response(['Authentication failed'], REST_Controller::HTTP_BAD_REQUEST);   
        }
    }


    public function delete_delete($task_id){
        $headers = $this->input->request_headers(); 
		if(isset($headers['Authorization'])){
			$decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
            if($decodedToken['status']){
                if(!empty($task_id)){
                    $user_id = $decodedToken['user_id'];
                    $data = $this->Api_model->delete($task_id, $user_id);
                    if($data){
                        $this->response(['Task deleted successfully'], REST_Controller::HTTP_OK);
                    }else{
                        $this->response(['Failed to delete task! No records found.'], REST_Controller::HTTP_BAD_REQUEST);
                    }
                }
            }else{
                $this->response($decodedToken);
            }
        }else{
            $this->response(['Authentication failed'], REST_Controller::HTTP_BAD_REQUEST);   
        }
    }
}
?>