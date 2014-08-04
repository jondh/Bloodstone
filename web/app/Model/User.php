<?php
class User extends AppModel {

	public $useDbConfig = 'users';

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
        $username = $this->find(
            'first',
            array(
                'fields' => array(
                    'User.id',
                    'User.username'
                ),
                'conditions' => array(
                    'User.username' => $check['username']
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
        $email = $this->find(
            'first',
            array(
                'fields' => array(
                    'User.id'
                ),
                'conditions' => array(
                    'User.email' => $check['email']
                )
            )
        );
 
        if(!empty($email)){
            return false;
        }else{
            return true; 
        }
    }
    
    function isUniqueEmailEdit($check) {
 		if(!$check['emailEdit']){
 			return true;
 		}
        $email = $this->find(
            'first',
            array(
                'fields' => array(
                    'User.id'
                ),
                'conditions' => array(
                    'User.email' => $check['emailEdit']
                )
            )
        );
 
        if(!empty($email)){
            if($this->data[$this->alias]['id'] == $email['User']['id']){
                return true; 
            }else{
                return false; 
            }
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
	
	public function generateNewTokens($id = -1){
		if($id > -1){
			$user['id'] = $id;
			$user['private_access_token'] = Security::generateAuthKey();
			$user['public_access_token'] = Security::generateAuthKey();
			
			if ($this->save($user)) {
				$result['result'] = 'success';
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