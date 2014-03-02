<?php
App::uses('AppModel', 'Model');
/**
 * Url Model
 *
 */
class Url extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'url' => array(
			'url' => array(
				'rule' => array('url'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	public function saveUrl($url){
		return $this->find('count', array(
			'conditions' => array('Url.url' => $url)
		));
	}
}
