<?php

global $post;

$news = get_posts([
	'name' => $post->post_name,
	'post_type' => 'news_and_analysis',
	'post_status' => 'publish'
]);

$reports = get_posts([
	'name' => $post->post_name,
	'post_type' => 'report',
	'post_status' => 'publish'
]);

$post_redirects = array_merge($news, $reports);

$page_redirects_map = [
	'about-us' => [
		'path' => '/about/team', 
		'title' => 'Who We Are',
	],
];

$page_redirect = false;
if (isset($page_redirects_map[$post->post_name])) {
	$page_redirect = $page_redirects_map[$post->post_name];
}

?>
<?php if (count($post_redirects) > 0): ?>
	<article>
		<div class="content">
			<div class="info-left"></div>
			<div class="info-main">
				<div class="info-main-block">
				<p>This article has moved to:</p>
				<p><a href="<?php echo get_permalink($post_redirects[0]->ID); ?>"><?php echo $post_redirects[0]->post_title; ?></a></p>
			</div>
			</div>
		</div>
	</article>
<?php elseif ($page_redirect): ?>
	<article>
		<div class="content">
			<div class="info-left"></div>
			<div class="info-main">
				<div class="info-main-block">
				<p>This page has moved to:</p>
				<p><a href="<?php echo $page_redirect['path']; ?>"><?php echo $page_redirect['title']; ?></a></p>
			</div>
			</div>
		</div>
	</article>	
<?php else: ?>
	<?php get_template_part('404'); ?>	
<?php endif; ?>
