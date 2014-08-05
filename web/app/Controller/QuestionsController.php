<?php
	class QuestionsController extends AppController { 
	
	
		public function beforeFilter(){
			parent::beforeFilter();
       		$this->Auth->allow();
		}
		
		public $components = array('AccessToken');
	
	}
?>