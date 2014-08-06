<?php
	class UsersController extends AppController { 
	
	
		public function beforeFilter(){
			parent::beforeFilter();
       		$this->Auth->allow();
		}
		
		public $components = array('AccessToken');
	
		public function index(){
			
		}
	
		// logs in, gets all user's data if successful
		public function loginAjax(){
			$this->layout = 'ajax';
			if ($this->request->is('post')) {
				$this->request->data['User']['username'] = $this->request->data['username'];
				$this->request->data['User']['password'] = $this->request->data['password'];
				if ($this->Auth->login()) {
					$resultAuth = $this->User->generateNewTokens($this->Auth->user('id'));
					$result = $this->User->getUserAndData($this->Auth->user('id'));
					$result['result'] = $resultAuth['result'];
					return new CakeResponse(array('body' => json_encode($result)));
          	  	}
				
				$result['result'] = 'not logged in';
				return new CakeResponse(array('body' => json_encode($this->request->data )));
				
       		}
       		$result['result'] = 'not post';
       		return new CakeResponse(array('body' => json_encode($result)));
       		
		}
		
		public function logoutAjax(){
			$this->layout = 'ajax';
			if($this->request->is('post')){
				$tokenSuccess = $this->AccessToken->checkAccessTokens($this->request->data['public_token'], $this->request->data['private_token'], $this->request->data['timeStamp']);
				
				$result = $this->User->clearTokens($this->request->data['user_id']);
				return new CakeResponse(array('body' => json_encode($result)));
					
			}
       		$result['result'] = 'failure';
       		return new CakeResponse(array('body' => json_encode($result)));
		}
		
		public function loginStatusAjax(){
			$this->layout = 'ajax';
			if($this->request->is('post')){
				$tokenSuccess = $this->AccessToken->checkAccessTokens($this->request->data['public_token'], $this->request->data['private_token'], $this->request->data['timeStamp']);
				
				if($tokenSuccess == "they good"){
					$userMatch = $this->User->getUserWithPT($this->request->data['user_id'], $this->request->data['public_token']);
					
					if($userMatch){
						return new CakeResponse(array('body' => 'success'));
					}
					else{
						return new CakeResponse(array('body' => 'failure'));
					}
				}
				else if($tokenSuccess == "public"){
					return new CakeResponse(array('body' => 'bad token'));
				}
				else if($tokenSuccess == "private"){
					return new CakeResponse(array('body' => 'bad token'));
				}
				else if($tokenSuccess == "bad data"){
					return new CakeResponse(array('body' => 'bad token'));
				}
				else{
					return new CakeResponse(array('body' => 'bad token'));
				}
			}
		}
		
		public function addAjax(){
			$this->layout = 'ajax';
			if($this->request->is('post')){
				$addResult = $this->User->add($this->request->data);
				if($addResult['result'] == 'success'){
					$result = $this->User->getUser($addResult['id']);
					$result['result'] = $addResult['result'];
					return new CakeResponse(array('body' => json_encode($result)));
				}
				
				return new CakeResponse(array('body' => json_encode($addResult)));
			}
			$result['result'] = 'failure';
			return new CakeResponse(array('body' => json_encode($result)));
		}
		
		public function facebookLoginAjax(){
			$this->layout = 'ajax';
			if($this->request->is('post') ){
			
				$this->Facebook->setAccessToken( $this->request->data['token'] );
				if( $this->request->data['fbID'] == $this->Facebook->getUser() ){
				
					// We have a user ID, so probably a logged in user.
					// If not, we'll get an exception, which we handle below.
					try {
					
						$user_profile = $this->Facebook->api('/me','GET');
						
						$user = $this->User->getUserFromFbId($this->request->data['fbID']);
						
						if($user){
							$user_profile['fbID'] = $user_profile['id'];
							$user_profile['id'] = $user['User']['id'];
							
							$editResult = $this->User->edit($user_profile);
							$resultAuth = $this->User->generateNewTokens($user['User']['id']);
							$result = $this->User->getUserAndData($user['User']['id']);
							$result['result'] = $resultAuth['result'];
							return new CakeResponse(array('body' => json_encode($result)));
						}
						else if($this->request->data['new'] == 'true'){
							$resultAdd = $this->User->addFromFacebook($user_profile);
							$result = $this->User->getUserAndData($resultAdd['id']);
							$result['result'] = $resultAdd['result'];
							return new CakeResponse(array('body' => json_encode($result)));
						}
						
						$user['result'] = 'none';
						
						return new CakeResponse(array('body' => json_encode( $user )));

					} catch(FacebookApiException $e) {
						// If the user is logged out, you can have a 
						// user ID even though the access token is invalid.
						// In this case, we'll get an exception, so we'll
						// just ask the user to login again here.
						
						$user['result'] = 'faliureToken';
						
						error_log($e->getType());
						error_log($e->getMessage());
						   
					} 
					
				}
				else{ 
					$user['result'] = 'failureToken';
					return new CakeResponse(array('body' => json_encode( $user ))); 
				}
	            
			}
			$user['result'] = 'failure';
			return new CakeResponse(array('body' => json_encode( $user ))); 
		}
		
		public function getUserAjax(){
			$this->layout = 'ajax';
			if($this->request->is('post')){
				$tokenSuccess = $this->AccessToken->checkAccessTokens($this->request->data['public_token'], $this->request->data['private_token'], $this->request->data['timeStamp']);
				
				if($tokenSuccess == "they good"){
					$user = $this->User->find('first', array(
						'conditions' => array(
							'User.id' => $this->request->data['user_id']
						)
					));
					if($user){
						unset($user['User']['password']);
						unset($user['User']['salt']);
						unset($user['User']['public_access_token']);
						unset($user['User']['private_access_token']);
						$result['empty'] = false;
						$result['user'] = $user['User'];
					}
					else{
						$result['empty'] = true;
					}
					$result['result'] = 'success';
					return new CakeResponse(array('body' => json_encode($result)));
				}
				else if($tokenSuccess == "public"){
					$result['result'] = 'bad token';
					return new CakeResponse(array('body' => json_encode($result)));
				}
				else if($tokenSuccess == "private"){
					$result['result'] = 'bad token';
					return new CakeResponse(array('body' => json_encode($result)));
				}
				else if($tokenSuccess == "bad data"){
					$result['result'] = 'bad token';
					return new CakeResponse(array('body' => json_encode($result)));
				}
				else{
					$result['result'] = 'bad token';
					return new CakeResponse(array('body' => json_encode($result)));
				}	
		
			}
			else{
				$result['result'] = 'not post';
				return new CakeResponse(array('body' => json_encode($result)));
			}
		}
		
		
	}
?>