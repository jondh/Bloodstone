<?php
	class ResponsesController extends AppController { 
	
	
		public function beforeFilter(){
			parent::beforeFilter();
       		$this->Auth->allow();
		}
		
		public $components = array('AccessToken');
		
		public function addResponse(){
			$this->layout = 'ajax';
			if($this->request->is('post')){
				$tokenSuccess = $this->AccessToken->checkAccessTokens($this->request->data['public_token'], $this->request->data['private_token'], $this->request->data['timeStamp']);
				
				if($tokenSuccess == "they good"){
					$result = $this->Response->add($this->request->data);
					return new CakeResponse(array('body' => json_encode($result)));
				}
			}
			$result['result'] = 'failure';
			return new CakeResponse(array('body' => json_encode($result)));
		}
	
		public function getMyResponses(){
			$this->layout = 'ajax';
			if($this->request->is('post')){
				$tokenSuccess = $this->AccessToken->checkAccessTokens($this->request->data['public_token'], $this->request->data['private_token'], $this->request->data['timeStamp']);
				
				if($tokenSuccess == "they good"){
					$responses = $this->Response->getResponsesWithQuestionForUser( $this->request->data['user_id'] );
					
					if($responses){
						$result['result'] = 'success';
						$result['responses'] = $responses;
						return new CakeResponse(array('body' => json_encode($result)));
					}
				}
			}
			$result['result'] = 'failure';
			return new CakeResponse(array('body' => json_encode($result)));
		}
	
	}
?>