<?php

require dirname(dirname(__DIR__)) . "/vendor/autoload.php";

function created_at_cmp($a, $b) {
	$atime = strtotime($a->created_at);
	$btime = strtotime($b->created_at);

	if ($atime === $btime) {
		return 0;
	}

	return ($atime < $btime) ? 1 : -1;
}

$tweets_en = getTweets('en');
$tweets_ar = getTweets('ar');
$tweets = array_merge($tweets_en->tweets, $tweets_ar->tweets);
usort($tweets, "created_at_cmp");

?>
<?php /*<h1>Recent tweets from <a target="blank" href="http://twitter.com/airwars">@Airwars</a> and <a target="blank" href="http://twitter.com/AirwarsArabic">@AirwarsArabic</a></h1>*/ ?>

<?php foreach($tweets as $count => $tweet): ?>
	<?php if ($count < 4): ?>
		<div class="tweet">
			<?php echo $tweet->html; ?>
			
		</div>
	<?php endif; ?>
<?php endforeach; ?>
