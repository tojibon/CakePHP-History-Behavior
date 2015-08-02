<?php
/*
A database table with same schema as Guest but with 4 extra fields as mentioned below.
`model_id` int(11) DEFAULT NULL,
`model_action` varchar(120) COLLATE latin1_german2_ci DEFAULT NULL,
`model_json_object` text COLLATE latin1_german2_ci,
`model_action_user_id` int(11) DEFAULT '1',
*/
App::uses('AppModel', 'Model');

class GuestHistory extends AppModel {

	public $useTable = 'guests_history';	
}
