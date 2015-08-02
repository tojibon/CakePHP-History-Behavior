# CakePHP-History-Behavior
CakePHP 2.x.x Model History Behavior by which It is possible to record a history of each CRUD action based on a Model.

##Installation
Download the archive and past the Model folder into your CakePHP project installation. So that, it will copy a HistoryBehavior under Behavior directory inside Model directory.

You can enable any Model history feature by following bellow steps of installation
1. Place the HistoryBehavior.php file under Behavior directory inside Model directory.
2. Add `public $actsAs = array('History');` in any Model definition, For example see below Sample Guest Model definition.
3. [Option] - Create a History Model as well.
4. [Important] - You must create a database table with same fields of your target model table with some extra fields as mentioned below sample History Model definition.
5. That's all, Now it's your time to do what ever you want with your each model history data from database tables.

##Sample of a Model which has supports for history data:
`
App::uses('AppModel', 'Model');
class Guest extends AppModel {
	public $useTable = 'guests';
	public $actsAs = array('History');
}
`

##Sample of a History Model of above mentioned Guest Model
`
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
`
Thanks and have a wonderful night!!
