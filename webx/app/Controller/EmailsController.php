<?php
App::uses('AppController', 'Controller');
/**
 * Emails Controller
 *
 * @property Email $Email
 * @property PaginatorComponent $Paginator
 */
class EmailsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator');


	public function index(){
		
	}

	public function ajax(){
		$this->autoRender = false;
	    $this->response->type('json');

	    $email = $this->Email->find('all', array(
	    	'order' => 'Email.id DESC',
			'limit' => 10,			
		));
	    $this->response->body(json_encode($email));
	}

}
