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

           'client_id'     => '443983125768088',
            'client_secret' => '4754b5024acbfb76d4ccb3afdeba5a1d',
            'scope'         => array('email','read_friendlists','user_online_presence'),
        ),	
		'Twitter' => array(
			'client_id'     => 'jo23nFF9g3rJkacxwrVzuYAbV',
			'client_secret' => 'vyke5EcO1eTUkIr5UkyLid0zoXyeqf10isxZDbw0veybU682p0',
			// No scope - oauth1 doesn't need scope
		),
		'Google' => array(
			'client_id'     => '633566585990-a2j7n2kmspv2dm61psre6p6iqngs18jf.apps.googleusercontent.com',
			'client_secret' => 'T78bu8dqTPn9HAPIZqMdA3Pi',
			'scope'         => array('userinfo_email', 'userinfo_profile'),
		),
		/*sever
		'Google' => array(
			'client_id'     => '633566585990-6snr58fvjhccal4oikkhrctc2vkq16pt.apps.googleusercontent.com',
			'client_secret' => 'baJdg5va7o9M8EL-Opt3U1F0',
			'scope'         => array('userinfo_email', 'userinfo_profile'),
		),
		*/
	//// Ding datt
	'Dropbox' => array(
    'key'     => 'z6tj74qaywh91i9',
    'secret' => 'aztki7bprhu04iy',
    ),

	)

);