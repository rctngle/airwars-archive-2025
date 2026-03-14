<?php

define('TWITTER_CONSUMER_KEY', 'IPzAlQ0g3lxqB0Qraa7qFbL7d');
define('TWITTER_CONSUMER_SECRET', 'qO4R3KtMMx3MoiSFHBPc3Q6Kyc5ECQqhDF2X1Rk9wizpq0RDXF');
define('TWITTER_ACCESS_TOKEN', '905794103727808512-Om4qVibGPlh79xOllG23p6tYrPndStx');
define('TWITTER_ACCESS_TOKEN_SECRET', '0mHzdtiX09C2CHdkiwAadS22ZtAYYvQSPq2gusIOqaPMO');

function getTweets($lang) {
	$dir = get_template_directory() . '/data/social';
	if (!is_dir($dir)) {
		mkdir($dir, 0775);
	}
	$filepath = $dir . '/tweets_' . $lang . '.json';
	$fetch_tweets = true;

	if (file_exists($filepath)) {
		$tweets = json_decode(file_get_contents($filepath));
		$time_diff = time() - $tweets->timestamp;
		if ($time_diff < 10 * 60) {
			$fetch_tweets = false;
		}
	}

	if ($fetch_tweets && function_exists('curl_version')) {
		try {
			
			$screen_name = ($lang == 'en') ? 'airwars' : 'airwarsarabic';

			$connection = new \Abraham\TwitterOAuth\TwitterOAuth(TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET, TWITTER_ACCESS_TOKEN, TWITTER_ACCESS_TOKEN_SECRET);
			$statuses = $connection->get("statuses/user_timeline", ["screen_name" => $screen_name, "exclude_replies" => true, 'include_rts' => false]);
			if (!isset($statuses->errors)) {
				$embeds = [];
				if (is_array($statuses)) {
					for ($i=0; $i<3; $i++) {
						if ($i < count($statuses)) {


							$status = $statuses[$i];
							$id = $status->id_str;
							$url = 'https://publish.twitter.com/oembed?url=https://twitter.com/' . $screen_name . '/status/' . $id;
							
							$ch = curl_init();
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							curl_setopt($ch, CURLOPT_URL,$url);
							$result=curl_exec($ch);
							curl_close($ch);
							
							$embed = json_decode($result);	
							$embed->created_at = $status->created_at;
							$embeds[] = $embed;
						}
					}
				}

				if (count($embeds) == 3) {
					$tweets = json_decode(json_encode(['timestamp' => time(), 'tweets' => $embeds]));;
					file_put_contents($filepath, json_encode($tweets));

				}
			}
		} catch (Exception $e) {

		}
	}

	return $tweets;
}
