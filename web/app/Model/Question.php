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
	
	public function add($data = -1){
		$data['created'] = null;
		$db = ConnectionManager::getDataSource('default');
		$data['updated'] = $db->expression('NOW()');
		if($this->save($data)){
			$result['result'] = 'success';
			return $result;
		}
		$result['result'] = 'failure';
		return $result;
	}
	
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
	
	public function getQuestionsAndResponses(){
		$this->unbindModel(
			array(
				'belongsTo' => array('User')
			)
		);
		$this->recursive = '1';
		$questions = $this->find('all');
		
		return $questions;
	}
	
	public function getQuestionsAndResponseCount($user_id = -1){
		if($user_id > 0){
			
			$questions = $this->find('all', array(
				'conditions' => array(
					'NOT' => array(
						'id' => (array)$this->Response->getResponseQuestionIdsForUser($user_id),
						'user_id' => $user_id
					)
				)
			));
			
			if($questions){
				for($i = 0; $i < count($questions); $i++){
					$questions[$i]['responses_yes'] = $this->Response->find('count', array(
						'conditions' => array(
							'question_id' => $questions[$i]['Question']['id'],
							'answer' => true
						)
					));
					$questions[$i]['responses_no'] = $this->Response->find('count', array(
						'conditions' => array(
							'question_id' => $questions[$i]['Question']['id'],
							'answer' => false
						)
					));
				}
			}
		
			return $questions;
		}
	}
	
	public function getQuestionsWithResponseCountForUser($user_id = -1){
		if($user_id > 0){
			
			$questions = $this->find('all', array(
				'conditions' => array(
					'user_id' => $user_id
				)
			));
			
			if($questions){
				for($i = 0; $i < count($questions); $i++){
					$questions[$i]['responses_yes'] = $this->Response->find('count', array(
						'conditions' => array(
							'question_id' => $questions[$i]['Question']['id'],
							'answer' => true
						)
					));
					$questions[$i]['responses_no'] = $this->Response->find('count', array(
						'conditions' => array(
							'question_id' => $questions[$i]['Question']['id'],
							'answer' => false
						)
					));
				}
			}
		
			return $questions;
		}
	}
	
}