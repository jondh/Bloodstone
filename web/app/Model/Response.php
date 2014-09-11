<?php
class Response extends AppModel {
	
	public $belongsTo = array(
		'User' => array(
			'type' => 'INNER',
			'fields' => array(
				'id', 'username', 'firstName', 'lastName', 'email', 'aquamarine', 'bloodstone', 'fbID', 'updated', 'dateTime'
			)
		),
		'Question' => array(
			'type' => 'INNER'
		)
	);
	
	public function add($data = -1){
		$data['created'] = null;
		if($this->save($data)){
			$result['result'] = 'success';
			return $result;
		}
		$result['result'] = 'failure';
		return $result;
	}
	
	public function getResponseQuestionIdsForUser($user_id = -1){
		if($user_id > 0){
			$responses = $this->find('all', array(
				'conditions' => array(
					'user_id' => $user_id
				),
				'fields' => array(
					'question_id'
				)
			));
			
			return Hash::extract($responses, '{n}.Response.question_id');
		}
	}
	
	public function getResponsesWithQuestionForUser($user_id = -1){
		if($user_id > 0){
			
			$this->unbindModel(
				array(
					'belongsTo' => array('User')
				)
			);
			$this->recursive = '1';
			$responses = $this->find('all', array(
				'conditions' => array(
					'Response.user_id' => $user_id
				)
			));
			
			return $responses;
		}
	}
	
}