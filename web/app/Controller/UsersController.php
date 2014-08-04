<?php
	class UsersController extends AppController { 
	
	
		public function beforeFilter(){
			parent::beforeFilter();
       		$this->Auth->allow();
		}
		
		public $components = array('AccessToken');
	
		public function index(){
			
		}
	
		public function loginAjax(){
			$this->layout = 'ajax';
			if ($this->request->is('post')) {
				$this->request->data['User']['username'] = $this->request->data['username'];
				$this->request->data['User']['password'] = $this->request->data['password'];
				if ($this->Auth->login()) {
					$resultAuth = $this->User->generateNewTokens($this->Auth->user('id'));
					$result = $this->User->getUser($this->Auth->user('id'));
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
					$resultAuth = $this->User->generateNewTokens($addResult['id']);
					$result = $this->User->getUser($addResult['id']);
					$result['result'] = $resultAuth['result'];
					return new CakeResponse(array('body' => json_encode($result)));
				}
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
						
						$user = $this->User->find('first', array(
							'conditions' => array(
								'fbID' => $this->request->data['fbID']
							)
						));
						
						if($user){
							unset($user['User']['password']);
							unset($user['User']['salt']);
							unset($user['User']['public_access_token']);
							unset($user['User']['private_access_token']);
							$this->User->set($user);
							if($user['User']['firstName'] != $user_profile['first_name']){
								$this->User->saveField('firstName', $user_profile['first_name']);
							}
							if($user['User']['lastName'] != $user_profile['last_name']){
								$this->User->saveField('lastName', $user_profile['last_name']);
							}
							if($user['User']['email'] != $user_profile['email']){
								$this->User->saveField('email', $user_profile['email']);
							}
							$user['Token']['Private'] = Security::generateAuthKey();
							$user['Token']['Public'] = Security::generateAuthKey();
							
							if ($this->User->saveField('public_access_token', $user['Token']['Public'])) {
								if ($this->User->saveField('private_access_token', $user['Token']['Private'])) {
									$user['result'] = 'success';
									return new CakeResponse(array('body' => json_encode($user)));
								}
							}
							$user['result'] = 'failure';
							return new CakeResponse(array('body' => json_encode($user)));
						}
						else if($this->request->data['new'] == 'true'){
							$this->User->create();
							if( array_key_exists('username', $user_profile) ){
								$this->request->data['User']['username'] = $user_profile['username'];
							}
							else{
								$this->request->data['User']['username'] = substr($user_profile['first_name'], 0, 1) . $user_profile['last_name'];
							}
							$this->request->data['User']['password'] = "000000";
							$this->request->data['User']['email'] = $user_profile['email'];
							$this->request->data['User']['firstName'] = $user_profile['first_name'];
							$this->request->data['User']['lastName'] = $user_profile['last_name'];
							$this->request->data['User']['fbID'] = $this->request->data['fbID'];
							$this->request->data['User']['salt'] = Security::generateAuthKey();
							if ($this->User->saveAll($this->request->data)) {
								$user['User'] = $this->request->data['User'];
								$user['Token']['Private'] = Security::generateAuthKey();
								$user['Token']['Public'] = Security::generateAuthKey();
								$userID = $this->User->find('first', array(
									'conditions' => array(
										'fbID' => $this->request->data['fbID']
									),
									'fields' => array(
										'id'
									)
								));
								$user['User']['id'] = $userID['User']['id'];
								$this->User->set($this->request->data);
								if ($this->User->saveField('public_access_token', $user['Token']['Public'])) {
									if ($this->User->saveField('private_access_token', $user['Token']['Private'])) {
										if( $this->User->saveField('password', '111111') ){
											$user['result'] = 'success';
											return new CakeResponse(array('body' => json_encode($user)));
										}
									}
								}
							}
							$user['result'] = 'failure';
							$user['errors'] = $this->User->validationErrors;
							return new CakeResponse(array('body' => json_encode($user)));
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