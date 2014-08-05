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
	
}