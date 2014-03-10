<?php

require_once __DIR__ . '/TwitterOAuth/TwitterOAuth.php';
require_once __DIR__ . '/TwitterOAuth/Exception/TwitterException.php';

error_reporting(E_ALL);
use TwitterOAuth\TwitterOAuth;

date_default_timezone_set('UTC');


/**
 * Array with the OAuth tokens provided by Twitter when you create application
 *
 * output_format - Optional - Values: text|json|array|object - Default: object
 */
$config = array(
    'consumer_key' => 'iTtLdPZRUollR6GtOCPw',
    'consumer_secret' => 'A8wEjSoFCaXRVgo1mTyiJPKplYP5zqHwceFKUCq2c4',
    'oauth_token' => '306824681-DyzCTDEJQZh0G1fIau38kxqvWRuDz3zXxCPdoeCL',
    'oauth_token_secret' => 'udxXVceACyQIS8OKMp9kj4nclQ4A51qyfugJoY6gdq0TA',
    'output_format' => 'object'
);

/**
 * Instantiate TwitterOAuth class with set tokens
 */
$tw = new TwitterOAuth($config);
$mysql = new mysqli("localhost","root","","twitter");


function getdata($name, $max_id=null){
	global $tw, $mysql;

	/**
	 * Returns a collection of the most recent Tweets posted by the user
	 * https://dev.twitter.com/docs/api/1.1/get/statuses/user_timeline
	 */
	$params = array(
		'screen_name' => $name,
		'count' => 20,
		'exclude_replies' => false
	);
	if($max_id != null){
		$params['max_id']=$max_id;
	}
	/**
	 * Send a GET call with set parameters
	 */
	$response = $tw->get('statuses/user_timeline', $params);

	$count = 0;
	$max_id = null;
	foreach ($response as $t){
		$count++;
		$max_id = $t->{'id_str'};
		//save in database here
		//make sure to configure the database connection above this function
		//print_r($t);
		$handle = $t->user->screen_name;
		$name = $t->user->name;
		$tweet = mysql_real_escape_string($t->text);
		$time = strtotime($t->created_at);
		$mysql->query("INSERT INTO `tweets` (`handle`, `name`, `tweet`, `time`, `maxid`) VALUES ('$handle', '$name', '$tweet', '$time', '$max_id')") or die("penis");
	}

	return array("count"=>$count, "max_id"=>$max_id);
}

function getalluserdata($name){
	$max_id = null;
	$max_depth = 20;
	$total_count = 0;
	while(true){
		$data = getdata($name, $max_id);
		$max_depth--;
		
		$max_id = $data['max_id'];
		
		$total_count += $data['count'];

		if($max_depth == 0 || $data['count'] <= 1){
			break;
		}
	}
	
	return $total_count;
}

$hand_csv = fopen("handles2.csv","r");
while(($data = fgetcsv($hand_csv)) !== FALSE){
	print getalluserdata($data[0]);
}
fclose($hand_csv);

set_time_limit(0);