<?php 

return array( 
	
	/*
	|--------------------------------------------------------------------------
	| oAuth Config
	|--------------------------------------------------------------------------
	*/

	/**
	 * Storage
	 */
	'storage' => 'Session', 

	/**
	 * Consumers
	 */
	'consumers' => array(

		/**
		 * Facebook
		 */
        'Facebook' => array(
            'client_id'     => '751664208282208',
            'client_secret' => '2ebd691db915251504450e3e59439326',
            'scope'         => array('email','read_friendlists','user_online_presence'),
        ),	
		'Twitter' => array(
			'client_id'     => 'jo23nFF9g3rJkacxwrVzuYAbV',
			'client_secret' => 'vyke5EcO1eTUkIr5UkyLid0zoXyeqf10isxZDbw0veybU682p0',
			// No scope - oauth1 doesn't need scope
		),
		'Google' => array(
			'client_id'     => '334688264646-im8thmtcn78a9rspgi2sejl2h91a2go3.apps.googleusercontent.com',
			'client_secret' => 'nqk-1oVULRu1NJQ8dJjVQXoJ',
			'scope'         => array('userinfo_email', 'userinfo_profile'),
		),
	)

);