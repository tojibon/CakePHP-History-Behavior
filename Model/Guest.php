<?php
App::uses('AppModel', 'Model');

class Guest extends AppModel {

	public $useTable = 'guests';

	public $actsAs = array('History');
}
