<?php
class Question extends AppModel {
	
	public $belongsTo = array(
		'User' => array(
			'type' => 'INNER',
			'fields' => array(
				'id', 'username', 'firstName', 'lastName', 'email', 'aquamarine', 'bloodstone', 'fbID', 'updated', 'dateTime'
			)
		)
	);
	
	public $hasMany = 'Response';
	
	
	public function getForUserIdWithResponseUser($user_id = -1){
		if($user_id > -1){
			$this->unbindModel(
				array(
					'belongsTo' => array('User')
				)
			);
			$this->Response->unbindModel(
				array(
					'belongsTo' => array('Question')
				)
			);
			$this->recursive = '2';
			$questions = $this->find('all', array(
				'conditions' => array(
					'user_id' => $user_id
				)
			));
			
			return $questions;
		}
	}
	
}