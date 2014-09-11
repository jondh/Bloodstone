<?php
	class QuestionsController extends AppController { 
	
	
		public function beforeFilter(){
			parent::beforeFilter();
       		$this->Auth->allow();
		}
		
		public $components = array('AccessToken');
		
		public function getSomeQuestions(){
			$this->layout = 'ajax';
			if($this->request->is('post')){
				$tokenSuccess = $this->AccessToken->checkAccessTokens($this->request->data['public_token'], $this->request->data['private_token'], $this->request->data['timeStamp']);
				
				if($tokenSuccess == "they good"){
					$questions = $this->Question->getQuestionsAndResponseCount($this->request->data['user_id']);
					
					if($questions){
						$result['result'] = 'success';
						$result['questions'] = $questions;
						return new CakeResponse(array('body' => json_encode($result)));
					}
				}
			}
			$result['result'] = 'failure';
			return new CakeResponse(array('body' => json_encode($result)));
		}
		
		public function getMyQuestions(){
			$this->layout = 'ajax';
			if($this->request->is('post')){
				$tokenSuccess = $this->AccessToken->checkAccessTokens($this->request->data['public_token'], $this->request->data['private_token'], $this->request->data['timeStamp']);
				
				if($tokenSuccess == "they good"){
					$questions = $this->Question->getQuestionsWithResponseCountForUser( $this->request->data['user_id'] );
					
					if($questions){
						$result['result'] = 'success';
						$result['questions'] = $questions;
						return new CakeResponse(array('body' => json_encode($result)));
					}
				}
			}
			$result['result'] = 'failure';
			return new CakeResponse(array('body' => json_encode($result)));
		}
		
		public function addQuestion(){
			$this->layout = 'ajax';
			if($this->request->is('post')){
				$tokenSuccess = $this->AccessToken->checkAccessTokens($this->request->data['public_token'], $this->request->data['private_token'], $this->request->data['timeStamp']);
				
				if($tokenSuccess == "they good"){
					$result = $this->Question->add($this->request->data);
					return new CakeResponse(array('body' => json_encode($result)));
				}
			}
			$result['result'] = 'failure';
			return new CakeResponse(array('body' => json_encode($result)));
		}
	
	}
?>