<?php

require_once __DIR__ . '/TwitterOAuth/TwitterOAuth.php';
require_once __DIR__ . '/TwitterOAuth/Exception/TwitterException.php';


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


/**
 * Returns a collection of the most recent Tweets posted by the user
 * https://dev.twitter.com/docs/api/1.1/get/statuses/user_timeline
 */
$params = array(
    'screen_name' => 's_elizabeth_lee',
    'count' => 1,
    'exclude_replies' => false,
	);

/**
 * Send a GET call with set parameters
 */
$response = $tw->get('users/show', $params);

$fp = fopen('file.csv', 'a+');
foreach ($response as $tweet) {
	$fields = array(
		"user"=>$tweet->{'user'}->{'screen_name'},
		"tweet"=>$tweet->{'text'},
		"time"=>$tweet->{'created_at'}
	);
    fputcsv($fp, $fields);
}

fclose($fp);

var_dump($response);


/**
 * Creates a new list for the authenticated user
 * https://dev.twitter.com/docs/api/1.1/post/lists/create
 */
$params = array(
    'name' => 'TwOAuth',
    'mode' => 'private',
    'description' => 'Test List',
);

/**
 * Send a POST call with set parameters
 */
$response = $tw->post('lists/create', $params);

var_dump($response);
