<?php

$conflicts = get_posts([
	'post_type' => 'conflict',
	'post_status'    => 'publish',
	'posts_per_page' => -1,
	// 'orderby' => 'title',
	// 'order' => 'DESC',
]);

$about = get_posts([
	'post_type' => 'about',
	'post_status'    => 'publish',
	'posts_per_page' => -1,
	'orderby' => 'menu_order',
	'order' => 'ASC',
]);

$about_ar = get_posts([
	'post_type' => 'about_ar',
	'post_status'    => 'publish',
	'orderby' => 'menu_order',
	'posts_per_page' => -1,
]);

$conflicts_ar = get_posts([
	'post_type' => 'conflict_ar',
	'post_status'    => 'publish',
	'posts_per_page' => -1,
	'orderby' => 'menu_order',
	'order' => 'ASC',
]);


usort($conflicts, function($a, $b) {
	$av = get_field('nav_order', $a->ID);
	$bv = get_field('nav_order', $b->ID);

	if ($av == $bv) return 0;
	return ($av < $bv) ? -1 : 1;
});


$conflicts_excluded_from_nav = [
	'israeli-military-in-the-gaza-strip', 
	'palestinian-militants-in-israel',
	'israeli-military-in-the-gaza-strip-arabic',
	'palestinian-militants-in-israel-arabic',
	'israeli-military-in-iraq-syria',
];

?>

<nav class="">
	<ul>
		<li class="with-subnav conflicts">
			<span class="<?php echo (get_post_type() == "conflict") ? "active" : ""; ?>">Conflicts <i class="fal fa-angle-down"></i></span>
			<?php if (count($conflicts) > 0): ?>
				<ul>
					<?php foreach($conflicts as $conflict): ?>
						<?php if (!in_array($conflict->post_name, $conflicts_excluded_from_nav)): ?>
							<li class="nav-<?php echo $conflict->post_name;?>">
								<a href="<?php echo get_the_permalink($conflict->ID); ?>">
									<?php if($conflict->post_name === 'all-belligerents-in-libya'):?>
										All Belligerents in Libya, 2012-present
									<?php else:?>
										<?php echo $conflict->post_title; ?>
									<?php endif;?>
								</a>
							</li>
						<?php endif; ?>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</li>
		<li>
			<a href="/civilian-casualties" class="<?php echo (get_post_type() == "civ") ? "active" : ""; ?>">Archive</span></a>
		</li>
		<li>
			<a href="/research" class="<?php echo (get_post_type() == "research") ? "active" : ""; ?>">Research</span></a>
		</li>
		<li>
			<a href="/investigations" class="<?php echo (get_post_type() == "investigation") ? "active" : ""; ?>">Investigations</span></a>
		</li>
		<li>
			<a href="/news" class="<?php echo (get_post_type() == "news_and_analysis") ? "active" : ""; ?>">News</span></a>
		</li>

		
		<li class="with-subnav en-about">
			<span class="<?php echo (get_post_type() == "about") ? "active" : ""; ?>">About <i class="fal fa-angle-down"></i></span>
			<a href="/" class="<?php echo (get_post_type() == "about") ? "active" : ""; ?>">Airwars <i class="fal fa-angle-down"></i></a>
			<?php if (!empty($about)): ?>
				<ul>
					<?php foreach($about as $page): ?>
						<li><a href="<?php echo get_the_permalink($page->ID); ?>"><?php echo $page->post_title; ?></a></li>
					<?php endforeach; ?>
					<?php /*
					<li><a href="/events">Events</a></li>
					*/ ?>
				</ul>
			<?php endif; ?>
		</li>
		<li class="arabic home">
			<a href="/ar"><?php echo dict('home', 'ar'); ?></a>
		</li>

		<?php /*
		<li class="arabic with-subnav">
			<span>النزاعات <i class="fal fa-angle-down"></i></span>
			<?php if (count($conflicts_ar) > 0): ?>
				<ul>
					<?php foreach($conflicts_ar as $conflict): ?>
						<?php if (!in_array($conflict->post_name, $conflicts_excluded_from_nav)): ?>
							<li><a href="<?php echo get_the_permalink($conflict->ID); ?>"><?php echo $conflict->post_title; ?></a></li>
						<?php endif; ?>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</li>
		*/ ?>

		<li class="arabic with-subnav about">
			<span><?php echo dict('airwars', 'ar'); ?> <i class="fal fa-angle-down"></i></span>
			<a href="/ar"><span><?php echo dict('airwars', 'ar'); ?> <i class="fal fa-angle-down"></i></span></a>
			<?php if (count($about_ar) > 0): ?>
				<ul>
					<?php foreach($about_ar as $page): ?>
						<li><a href="<?php echo get_the_permalink($page->ID); ?>"><?php echo $page->post_title; ?></a></li>
					<?php endforeach; ?>

				</ul>
			<?php endif; ?>
		</li>
	</ul>
</nav>
