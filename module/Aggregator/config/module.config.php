<?php
return array(
	'aggregator' => array(
		'modules' => array(
			'facebook',
			'twitter',
			'instagram',
			'google',
			'youtube',
			'dailymotion',
			//'flickr',
			'pinterest',
			'vimeo',
		),
		'facebook' => array(
			'pageID' 	=> '134815143308923',
			'appID' 	=> '467664709950042',
			'appSecret' => '9e54c8dccc732bcf9716759fc24d9dcf',
			'limit' 	=> 10
		),
		'twitter' => array(
			'userName' 			=> 'AgenceUzik',
			'userID' 			=> 303552028821553152,
			'limit' 			=> 10,
			'trimUserName' 		=> 1,
			'consumerKey' 		=> 'wmzEBVz72oqrOjVPZ7Lhg',
			'consumerSecret' 	=> 'TKOT7GTwxWNoO4XsJIqd0bzkN0F7iCKWIzgjYnV6xBs',
			'accessToken' 		=> '1039690309-TqhSrpj3svqCCUwFjvLaK1VRJZkmzUF38RRGt9y',
			'accessTokenSecret' => '74g0RDuACwnj6uW7ORJD6xfkUEqzRSIgkem6FVCxECs'
		),
		'instagram' => array(
			'userID' 	=> 'uziktn',
			'appID' 	=> 'e3ae44f62b7846daa1668da621a6dcd5',
			'token' 	=> '372081675.e3ae44f.ccf4ca4c659f4be5b25af3974840a757',
			'limit' 	=> 10
		),
		'google' => array(
			'userID' 	=> '116390953644072251178',
			'devKEY' 	=> 'AIzaSyBNHvBS6bTqtgCND-5nWcvbWfsGWwigehQ',
			'limit' 	=> 10
		),
		'youtube' => array(
			'chaineID' 	=> 'UCb5k_zo47GKHS0I7u-I7WOQ',
			'devKEY' 	=> 'AIzaSyBNHvBS6bTqtgCND-5nWcvbWfsGWwigehQ',
			'limit' 	=> 10
		),
		'dailymotion' => array(
			'userID' 	=> 'perrier-jouet',
			'limit' 	=> 10
		),
		'flickr' => array(
			'userID' 	=> '69352287@N03',
			'apiKey' 	=> '6861ddcc6ab62aef54a28e1ba08bda11',
			'limit' 	=> 10
		),
		'pinterest' => array(
			'userName' 	=> 'chaussuresandre',
			'limit' 	=> 10,
			'activite' 	=> ''
		),
		'vimeo' => array(
			'userID' 	=> 'vplusa',
			'limit' 	=> 10
		),
		'cache' => array(
			'lifetime' 	=> 2,
			'cachename' => 'uzik'
		),
	)
);