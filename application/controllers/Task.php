<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require __DIR__ . '/vendor/autoload.php';

require(APPPATH.'/libraries/REST_Controller.php');

use Restserver\Libraries\REST_Controller;
use Pheanstalk\Pheanstalk;
use Pheanstalk\Values\TubeName;

class Crud extends REST_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->library('Authorization_Token');	
    }

    public function ctask_post(){
        $headers = $this->input->request_headers(); 
        if(isset($headers['Authorization'])){
			$decodedToken = $this->authorization_token->validateToken($headers['Authorization']);
            if($decodedToken['status']){
                $user_id = $decodedToken['user_id'];
                $task = $this->input->post('task');
                //beanstalkd
                $pheanstalk = new Pheanstalk('127.0.0.1'); 
                $tube       = new TubeName('createTask');

                $pheanstalk->useTube('$tube')->put(json_encode(['user_id' => $user_id, 'task' => $task]));

            }else{
                $this->response($decodedToken);
            }
        }else{
			$this->response(['Authentication failed'], REST_Controller::HTTP_BAD_REQUEST);
		}
    }
}