<?php
class User extends AppModel {

	public $useDbConfig = 'users';
	
	public $hasMany = array(
		'Question' => array(
			'conditions' => array('Question.active' => '1')
		),
		'Response'
	);

    public $validate = array(
        'username' => array(
			'required' => array(
				'on'         => 'create',
				'rule'       => 'notEmpty',
				'message'    => 'Enter your username',
				'required'   => true,
				'last'       => true
			),
            'nonEmpty' => array(
                'rule' => array('notEmpty'),
                'message' => 'A username is required',
                'allowEmpty' => false
            ),
            'between' => array( 
                'rule' => array('between', 5, 15), 
                'message' => 'Usernames must be between 5 to 15 characters'
            ),
             'unique' => array(
                'rule'    => array('isUniqueUsername'),
                'message' => 'This username is already in use'
            ),
            'alphaNumericDashUnderscore' => array(
                'rule'    => array('alphaNumericDashUnderscore'),
                'message' => 'Username can only be letters, numbers and underscores'
            ),
        ),
        'firstName' => array(
        	'required' => array(
				'on'         => 'create',
				'rule'       => 'notEmpty',
				'message'    => 'Enter your first name',
				'required'   => true,
				'last'       => true
			),
            'nonEmpty' => array(
                'rule' => array('notEmpty'),
                'message' => 'A first name is required',
                'allowEmpty' => false
            ),
            'between' => array( 
                'rule' => array('between', 1, 15), 
                'message' => 'Names must be between 1 to 15 characters'
            ),
            'alphaNumericDashUnderscore' => array(
                'rule'    => array('alphaNumericDashUnderscore'),
                'message' => 'Name can only be letters, numbers and underscores'
            ),
        ),
        'lastName' => array(
        	'required' => array(
				'on'         => 'create',
				'rule'       => 'notEmpty',
				'message'    => 'Enter your last name',
				'required'   => true,
				'last'       => true
			),
            'nonEmpty' => array(
                'rule' => array('notEmpty'),
                'message' => 'A last name is required',
                'allowEmpty' => false
            ),
            'between' => array( 
                'rule' => array('between', 1, 15), 
                'message' => 'Names must be between 1 to 15 characters'
            ),
            'alphaNumericDashUnderscore' => array(
                'rule'    => array('alphaNumericDashUnderscore'),
                'message' => 'Name can only be letters, numbers and underscores'
            ),
        ),
        'password' => array(
        	'required' => array(
				'on'   => 'create',
				'rule' => array('notEmpty'),
                'message' => 'A password is required'
			),
            'min_length' => array(
                'rule' => array('minLength', '6'),  
                'message' => 'Password must have a mimimum of 6 characters'
            )
        ),
         
        'passwordConfirm' => array(
            'required' => array(
            	'on'   => 'create',
                'rule' => array('notEmpty'),
                'message' => 'Please confirm your password'
            ),
             'equaltofield' => array(
                'rule' => array('equaltofield','password'),
                'message' => 'Both passwords must match.'
            )
        ),
         
        'email' => array(
            'required' => array(
            	'on'   => 'create',
                'rule' => array('email', true),    
                'message' => 'Please provide a valid email address.'   
            ),
             'unique' => array(
                'rule'    => array('isUniqueEmail'),
                'message' => 'This email is already in use',
            ),
            'between' => array( 
                'rule' => 'email', 
                'message' => 'Please enter proper email'
            )
        ),
        
         
    );
     
        /**
     * Before isUniqueUsername
     * @param array $options
     * @return boolean
     */
    function isUniqueUsername($check) {
		if(!isset($this->data[$this->name]['id'])){
			$this->data[$this->name]['id'] = -1;
		}
        $username = $this->find('first', array(
                'conditions' => array(
                    'User.username' => $check['username'],
					'User.id !=' => $this->data[$this->name]['id']
                ),
                'fields' => array(
                    'User.id',
                    'User.username'
                )
            )
        );
		
        if(!empty($username)){
            return false;
        }else{
            return true;
        }
    }
 
    /**
     * Before isUniqueEmail
     * @param array $options
     * @return boolean
     */
    function isUniqueEmail($check) {
		if(!isset($this->data[$this->name]['id'])){
			$this->data[$this->name]['id'] = -1;
		}
        $email = $this->find('first', array(
                'conditions' => array(
                    'User.email' => $check['email'],
					'User.id !=' => $this->data[$this->name]['id']
                ),
                'fields' => array(
                    'User.id'
                )
            )
        );
 
        if(!empty($email)){
            return false;
        }else{
            return true; 
        }
    }
    
    public function checkPassword($check) {
    	$pass = $this->find('first', array(
    		'fields' => array(
    			'User.password'
    		),
    		'conditions' => array(
    			'User.id' => CakeSession::read("Auth.User.id")
    		)
    	));
    	if($pass['User']['password'] == AuthComponent::password($check['currentPassword'])){
    		return true;
    	}
    	return false;
    }
     
    public function alphaNumericDashUnderscore($check) {
        // $data array is passed using the form field name as the key
        // have to extract the value to make the function generic
        $value = array_values($check);
        $value = $value[0];
 
        return preg_match('/^[a-zA-Z0-9_ \-]*$/', $value);
    }
     
    public function equaltofield($check,$otherfield) 
    { 
        //get name of field 
        $fname = ''; 
        foreach ($check as $key => $value){ 
            $fname = $key; 
            break; 
        } 
        return $this->data[$this->name][$otherfield] === $this->data[$this->name][$fname]; 
    } 
	
    public function beforeSave($options = array()) {

   		if (isset($this->data[$this->alias]['password']) && isset($this->data[$this->alias]['salt'])) {
       		$this->data[$this->alias]['password'] = Security::hash(Security::hash(Security::hash($this->data[$this->alias]['password'].$this->data[$this->alias]['salt'])));
  		}
  		return true;
   }
	
    public function getUsers($userIds){
    	if($userIds){
    		$user = $this->find('all', array(
    			'conditions' => array(
    				'id' => $userIds
    			)
    		));
    		
    		return $user;
    	}
    }
	
    public function getUsersSafe($userIds = -1){
    	if($userIds > -1){
    		$users = $this->find('all', array(
    			'conditions' => array(
    				'id' => $userIds
    			),
				'fields' => array(
					'id', 'username', 'firstName', 'lastName', 'email', 'aquamarine', 'bloodstone', 'fbID', 'updated', 'dateTime'
				)
    		));
    		
    		return $users;
    	}
    }
	
	/* REQUIRED keys in the input data
	 *	usersExclude => a list of user ids not to be included in the result
	 *	match => the text to look for when searching the usernames, names, and emails
	 *	start => the start indicy of the result
	 *	length => the maximum number of results to return
	 */
	public function findUsersPage($data){
		$user = $this->find('all', array(
			'conditions' => array(
				'NOT' => array(
					'id' => (array) $data['usersExclude']
				),
				'OR' => array(
				'User.username LIKE'=>'%'.$data['match'].'%',
				'User.firstName LIKE'=>'%'.$data['match'].'%',
				'User.lastName LIKE'=>'%'.$data['match'].'%',
				'User.email LIKE'=>'%'.$data['match'].'%'
				)
			),
			'order' => 'username ASC',
			'offset' => $data['start'],
			'limit' => $data['length']
		));
		if($user){
			$result['empty'] = false;
			for($i = 0; $i < count($user); $i++){
				unset($user[$i]['User']['password']);
				unset($user[$i]['User']['salt']);
				unset($user[$i]['User']['public_access_token']);
				unset($user[$i]['User']['private_access_token']);
			}
			$result['users'] = $user;
		}
		else{
			$result['empty'] = true;
		}
		$result['result'] = 'success';
		return $result;
	}
	
	/* REQUIRED keys in the input data -> same as above except
	 *	coach_pool => a list of coach (user) ids that the result set has to be a part of
	 */
	public function findUsersPageFromIds($data){
		$user = $this->find('all', array(
			'conditions' => array(
				'id' => (array) $data['coach_pool'],
				'NOT' => array(
					'id' => (array) $data['usersExclude']
				),
				'OR' => array(
				'User.username LIKE'=>'%'.$data['match'].'%',
				'User.firstName LIKE'=>'%'.$data['match'].'%',
				'User.lastName LIKE'=>'%'.$data['match'].'%',
				'User.email LIKE'=>'%'.$data['match'].'%'
				)
			),
			'order' => 'username ASC',
			'offset' => $data['start'],
			'limit' => $data['length']
		));
		if($user){
			$result['empty'] = false;
			for($i = 0; $i < count($user); $i++){
				unset($user[$i]['User']['password']);
				unset($user[$i]['User']['salt']);
				unset($user[$i]['User']['public_access_token']);
				unset($user[$i]['User']['private_access_token']);
			}
			$result['users'] = $user;
		}
		else{
			$result['empty'] = true;
		}
		$result['result'] = 'success';
		return $result;
	}
	
	public function getUserFromFbId($fbId = -1){
		if($fbId != -1){
			$user = $this->find('first', array(
				'conditions' => array(
					'fbID' => $fbId
				)
			));
			return $user;
		}
	}
	
	public function getUser($id = -1){
		if($id > -1){
			$user = $this->find('first', array(
				'conditions' => array(
					'id' => $id
				)
			));
			
			return $user;
		}
	}
	
	public function getUserAndData($id = -1){
		if($id > -1){
			$this->Question->unbindModel(
				array(
					'belongsTo' => array('User')
				)
			);
			$this->Response->unbindModel(
				array(
					'belongsTo' => array('User')
				)
			);
			$this->recursive = 2;
			$user = $this->find('first', array(
				'conditions' => array(
					'id' => $id
				)
			));
			$this->recursive = -1;
			$user_ids = array();
			if($user){ // get unique user_ids
				foreach( $user['Question'] as $question ){
					foreach( $question['Response'] as $response ){
						if(!in_array($response['user_id'], $user_ids)){
							array_push($user_ids, $response['user_id']);
						}
					}
				}
				foreach( $user['Response'] as $response ){
					if(!in_array($response['Question']['user_id'], $user_ids)){
						array_push($user_ids, $response['Question']['user_id']);
					}
				}
			}
			
			$user['Users'] = $this->getUsersSafe($user_ids);
			
			return $user;
		}
	}
	
	public function getPrivateTokenFromPublicToken($public_token = -1){
		if($public_token != -1){
			$token = $this->find('first', array(
				'conditions' => array(
					'public_access_token' => $public_token
				),
				'fields' => array(
					'private_access_token'
				)
			));
			return $token;
		}
	}
	
	public function getUserWithPT($id = -1, $public_token = -1){
		if($id > -1 && $public_token > -1){
			$user = $this->find('first', array(
				'conditions' => array(
					'id' => $id,
					'public_access_token' => $public_token
				)
			));
			return $user;
		}
	}
	
	public function add($data){
		$data['dateTime'] = null;
		$data['updated'] = 'NOW()';
		$data['bloodstone'] = '1';
		$data['salt'] = Security::generateAuthKey();
		if ($this->save($data)) {
			$result['result'] = "success";
			$result['id'] = $this->id;
			return $result;
		}
		else{
			$result['result'] = "faliure";
			$result['errors'] = $this->validationErrors;
			return $result;
		}
    }
	
	public function addFromFacebook($user_profile = 0){
		if($user_profile != 0){
			$this->User->create();
			if( !array_key_exists('username', $user_profile) ){
				$user_profile['username'] = substr($user_profile['first_name'], 0, 1) . $user_profile['last_name'];
			}
			
			$user_profile['password'] = "000000";
			$user_profile['firstName'] = $user_profile['first_name'];
			$user_profile['lastName'] = $user_profile['last_name'];
			$user_profile['bloodstone'] = '1';
			$user_profile['fbID'] = $user_profile['id'];
			unset($user_profile['id']);
			$user_profile['salt'] = Security::generateAuthKey();
			$user_profile['private_access_token'] = Security::generateAuthKey();
			$user_profile['public_access_token'] = Security::generateAuthKey();
			if ($this->save($user_profile)) {
				$result['result'] = "success";
				$result['id'] = $this->id;
				return $result;
			}
			else{
				$result['result'] = "faliure";
				$result['errors'] = $this->validationErrors;
				return $result;
			}
		}
		$result['result'] = "faliure";
		return $result;
	}
	
	public function generateNewTokens($id = -1){
		if($id > -1){
			$user['id'] = $id;
			$user['private_access_token'] = Security::generateAuthKey();
			$user['public_access_token'] = Security::generateAuthKey();
			
			if ($this->save($user)) {
				$result['result'] = 'success';
				return $result;
			}
			else{
				$result['result'] = $this->validationErrors;
				return $result;
			}
		}
		$result['result'] = 'failure';
		return $result;
	}
	
	public function clearTokens($id = -1){
		if($id > -1){
			if($this->exists($id)){
				$user['id'] = $id;
				$user['private_access_token'] = "";
				$user['public_access_token'] = "";
				if($this->save($user)){
					$result['result'] = 'success';
					return $result;
				}
			}
		}
		$result['result'] = 'failure';
		return $result;
	}
	
	public function edit($data){
		$data['updated'] = DboSource::expression('NOW()');
		if( $this->save($data) ){
			$result['result'] = "success";
			CakeSession::write('Auth', $this->getUser($data['id']));
			return $result;
		}
		else{
			$result['result'] = "faliure";
			$result['errors'] = $this->validationErrors;
			return $result;
		}
	}
	
	public function changePass($data){
		$data['updated'] = DboSource::expression('NOW()');
		$data['salt'] = Security::generateAuthKey();
		if( $this->save($data) ){
			$result['result'] = "success";
			return $result;
		}
		else{
			$result['result'] = "faliure";
			$result['errors'] = $this->validationErrors;
			return $result;
		}
	}
	
}