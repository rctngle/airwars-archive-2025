<?php 


$lang = get_language();
$embed = (get_query_var('embedded') == "1");
$iframe = (get_query_var('iframe') == "1");



$short_header_post_types = ['conflict_data_new', 'news_and_analysis', 'research', 'investigation'];
$short_header_post_names = [ 'conflict-data-new', 'british-ekia', 'coalition-declared-strikes-timeline', 'coalition-confirmed-strikes-timeline', 'the-credibles', 'the-siege-of-tripoli', 'raqqa-city-map', 'battle-of-mosul', 'civilian-casualties-gaza-may-2021-map', 'ukraine-population-density', 'civilian-casualties-in-gaza-may-10th-20th-2021', 'mapping-urban-warfare-heatmap', 'mapping-urban-warfare-density'];

$exclude_conflict_data = ['israeli-military-in-syria-the-gaza-strip', 'palestinian-militants-in-israel', 'israeli-military-in-syria-the-gaza-strip-arabic', 'palestinian-militants-in-israel-arabic', 'israeli-military-in-syria-the-gaza-strip-hebrew', 'palestinian-militants-in-israel-hebrew', 'turkish-military-in-iraq-and-syria'];

$post_type = get_post_type();


if (!$post_type) {
	$post_type = (isset($wp_query->query['post_type'])) ? $wp_query->query['post_type'] : 'post';
}

$classes = get_body_class();
$classes[] = $post_type;
$classes[] = $lang;

$post_name = get_post_field( 'post_name', get_post() );

$is_home_page = (in_array($post_name, ['airwars-homepage-redesign', 'ar']));
$is_arabic_home_page = (in_array($post_name, ['ar']));

if ($is_home_page) {
	$classes[] = 'airwars-homepage';
}

if (is_singular() || is_home()) {
	$classes[] = 'single';
	$classes[] = $post_name;;
} else {
	$classes[] = 'archive';
}

if ($embed) {
	$classes[] = 'embed';
}
if ($iframe) {
	$classes[] = 'iframe';
}


$background_image = false;
$post_thumb_caption = false;
if (get_the_post_thumbnail_url() && is_singular() && get_post_type() != 'civ') {
	$background_image = "background-image: url(" . get_the_post_thumbnail_url(null, 'full') . ");";
	$post_thumb_caption = get_the_post_thumbnail_caption(null);
}
$featured_image_caption = get_field('featured_image_caption', get_the_ID());

$countries = (isset($_GET['country'])) ? explode(',', $_GET['country']) : [];
$belligerents = (isset($_GET['belligerent'])) ? explode(',', $_GET['belligerent']) : [];
$conflict_post = false;

if (count($countries) > 0 || count($belligerents) > 0) {

	$tax_query = [
		'relation' => 'AND',
	];

	if ($belligerents && is_array($belligerents) && count($belligerents) > 0) {
		$tax_query[] = [
			'taxonomy' => 'belligerent',
			'field' => 'slug',
			'terms' => $belligerents,
			'include_children' => false
		];	
	}

	if ($countries && is_array($countries) && count($countries) > 0) {
		$tax_query[] = [
			'taxonomy' => 'country',
			'field' => 'slug',
			'terms' => $countries,
			'include_children' => false
		];
	}

	$the_query = new WP_Query([
		'post_type' => 'conflict',
		'numberposts' => 1,
		'tax_query' => $tax_query,
	]);

	if (isset($_GET['country']) && isset($_GET['belligerent']) && isset($_GET['start_date']) && $_GET['country'] == 'the-gaza-strip' && $_GET['belligerent'] == 'israeli-military' && $_GET['start_date'] == '2023-10-07') {

		$the_query = new WP_Query([
			'post_type' => 'conflict',
			'numberposts' => 1,
			'p' => CONFLICT_ID_ISRAEL_AND_GAZA_2023,
		]);

	}



	if ( $the_query->have_posts() ) {
		$the_query->the_post();
		global $post;
		$conflict_post = $post;
		$classes[] = $conflict_post->post_name;;
		$background_image = "background-image: url(" . get_the_post_thumbnail_url($conflict_post->ID, 'full') . ");";
	}

	wp_reset_postdata();	
}

if (in_array($post_type, ['conflict', 'conflict_ar', 'conflict_he'])) {
	$conflict_post = $post;
}

if ($conflict_post) {
	$featured_image_caption = get_field('featured_image_caption', $conflict_post->ID);

	$country_terms = get_the_terms($conflict_post->ID, 'country');
	$belligerent_terms = get_the_terms($conflict_post->ID, 'belligerent');

	$country_slugs = [];
	$belligerent_slugs = [];

	if ($country_terms) {
		foreach($country_terms as $term) {
			$country_slugs[] = $term->slug;
		}
	}

	if ($belligerent_terms) {
		foreach($belligerent_terms as $term) {
			$belligerent_slugs[] = $term->slug;
		}
	}

	$params = [];
	if (count($belligerent_slugs) > 0) {
		$params['belligerent'] = implode(',', $belligerent_slugs);
	}
	if (count($country_slugs) > 0) {
		$params['country'] = implode(',', $country_slugs);
	}

	$civilian_casualties_active = (stristr($_SERVER['REQUEST_URI'], 'civilian-casualties'));
	$military_claims_active = (stristr($_SERVER['REQUEST_URI'], 'military-claims'));
	$research_active = (stristr($_SERVER['REQUEST_URI'], 'research'));
	$news_active = (stristr($_SERVER['REQUEST_URI'], 'news'));

}

$header_classes = [];



// if (!$conflict_post && in_array($post_type, $short_header_post_types)) {

if (in_array($post_type, $short_header_post_types)) {
	$header_classes[] = 'short';
}

if (is_tax('citation')) {
	$header_classes[] = 'tax_citation';
	$header_classes[] = 'short';
}
if(is_page('citations-gaza-israel')){
	$header_classes[] = 'short';
}


if(is_single() && in_array($post_name, $short_header_post_names)){

	$header_classes[] = 'short';

	if($post->post_name !== 'british-ekia'){
		$classes[] = 'full-visualisation';	
	}
	
}

$is_bij_archive = false;
if (stristr($_SERVER['REQUEST_URI'], 'archives/bij-drone-war')) {
	$path_parts = array_values(array_filter(explode('/', $_SERVER['REQUEST_URI'])));
	
	if ($path_parts[count($path_parts) - 1] == 'bij-drone-war') {
		$header_classes[] = 'bij';		
		$is_bij_archive = true;		
	} else {
		$header_classes[] = 'short';	
	}
}

setup_postdata( $post );

$excerpt = strip_tags(get_the_excerpt());

if (get_field('carding_summary') && get_field('carding_summary') != '') {
	$excerpt = get_field('carding_summary', get_the_ID());
} else if (get_field('article_subheading', get_the_ID())) {
	$excerpt = get_field('article_subheading', get_the_ID());
} else if (get_field('preview_summary', get_the_ID())) {
	$excerpt = get_field('preview_summary', get_the_ID());
}



$permalink = get_permalink();
$title = get_the_title();



if (is_archive()) {

	$queried_object = get_queried_object();
	$title = $queried_object->label;
	$permalink = get_post_type_archive_link($queried_object->name); 
} else if ($is_arabic_home_page) {
	$title = dict('airwars', 'ar');
	$permalink = site_url() . '/ar';
} else if ($is_home_page) {
	$title = 'Airwars';
	$permalink = site_url();
}
if (is_post_type_archive('investigation')) {
	$excerpt = get_field('social_media_description', 'options');
	$title = 'Airwars Investigations';
}

if (get_the_ID() == 77067 && $lang == 'ar') {
	$title = 'حصيلة المدنيين في غزة';
	$excerpt = 'خريطة تفاعلية جديدة توثّق ضحايا حرب مايو / أيار ٢٠٢١‎';
	$permalink = get_permalink() . '?lang=ar';
}

if (get_field('social_media_title')) {
	$title = get_field('social_media_title');
}

if (get_field('social_media_description')) {
	$excerpt = get_field('social_media_description');
}


?>
<!DOCTYPE html>
<html>
<head>
	<title>Airwars</title>
	<meta name="description" content="Monitoring and assessing civilian harm from airpower-dominated international military actions. Seeking transparency and accountability from belligerents, and advocating on behalf of affected non-combatants. Archiving open-source casualty reports, and military claims by nations.">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">

	
	<?php wp_head(); ?>

	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:site" content="@airwars">
	<meta name="twitter:creator" content="@airwars">


	<meta property="og:url" content="<?php echo $permalink ?>" />
	<meta property="og:type" content="article" />
	<meta property="og:title" content="<?php echo $title ?>" />

	<meta property="og:description" content="<?php if ($excerpt): echo $excerpt; endif; ?>" />

	<?php 

	if ((is_singular() || is_home()) && has_post_thumbnail(get_the_ID()) ) {
		$post_thumbs = wp_get_attachment_image_src(get_post_thumbnail_id( get_the_ID() ), 'large' );
		if ( ! empty($post_thumbs[0]) ) { 
			?>
			<meta property="og:image" content="<?php echo esc_url($post_thumbs[0]) ?>" />
			<?php 
		}
	} elseif (is_post_type_archive('conflict_data_new')) {
		?>
		<meta property="og:image" content="https://airwars.org/wp-content/uploads/2022/10/conflictdata-2.jpg" />
		<?php
	} elseif (is_post_type_archive('investigation')) {
		?>
		<meta property="og:image" content="https://airwars.org/wp-content/uploads/2023/01/Investigations-Homepage.jpg" />
		<?php
	}
	?>
	<script src="https://kit.fontawesome.com/7beeb2b164.js" crossorigin="anonymous"></script>
	<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory');?>/build/styles/screen.css">
	<link href='https://cdn.jsdelivr.net/npm/mapbox-gl@2.15.0/dist/mapbox-gl.min.css' rel='stylesheet' />
	<link rel="icon" type="image/png" sizes="32x32" href="<?php bloginfo('template_directory');?>/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="96x96" href="<?php bloginfo('template_directory');?>/favicon-96x96.png">
	<link rel="icon" type="image/png" sizes="16x16" href="<?php bloginfo('template_directory');?>/favicon-16x16.png">

</head>
<body class="<?php echo implode(" ", $classes); ?>" data-lang="<?php echo get_language(); ?>">

<header <?php if ($background_image): ?>style="<?php echo $background_image; ?>"<?php endif; ?> class="<?php echo implode(" " , $header_classes); ?>">
	<div class="content logo-navigation">
		<div class="logo info-left">
			<?php if ($lang == 'en' || $lang == 'he'): ?>
				<a href="/">
					<img src="<?php bloginfo('template_directory');?>/resources/media/images/logo-white.svg">
				</a>
			<?php else: ?>
				<a href="/ar">
					<img src="<?php bloginfo('template_directory');?>/resources/media/images/logo-ar-white.svg">
				</a>
			<?php endif; ?>
			<div class="mobile-nav-toggle"><?php echo dict('menu'); ?></div>
		</div>
		<div class="page-title">
			<?php include(locate_template('templates/header/page-title.php')); ?>
		</div>
		<?php get_template_part('templates/nav/nav-menu'); ?>
	</div>
	<div class="mobile-gradient"></div>
	<div class="content mobile-page-title">
		<?php include(locate_template('templates/header/page-title.php')); ?>
	</div>
	
	<?php if ($conflict_post): ?>
		<div class="content conflict">
			<div class="info-left">
				<?php /*
				<?php if ($conflict_post): ?>
					<h1><?php echo $conflict_post->post_title; ?></h1>
				<?php else: ?>
					<h1><?php echo $post->post_title; ?></h1>
				<?php endif; ?>
				*/ ?>
			</div>
			<div class="nav-conflict">

				<ul>
					<?php if (in_array($conflict_post->ID, [CONFLICT_ID_ISRAEL_AND_GAZA_2023])):?>
						<li>
							<a href="<?php echo get_post_type_archive_link('civ'); ?>/?country=the-gaza-strip&start_date=2023-10-07&belligerent=israeli-military">Civilian Casualty Incidents</a>
						</li>
					<?php elseif (isset($belligerent_terms) && $belligerent_terms && count($belligerent_terms) > 0): ?>
						<?php foreach($belligerent_terms as $belligerent_term): ?>
							<?php if (isset($country_terms) && $country_terms && count($country_terms) > 0): ?>
								<?php foreach($country_terms as $country_term): ?>
									<?php
									$civcas_active = ($civilian_casualties_active && isset($_GET['belligerent']) && $_GET['belligerent'] == $belligerent_term->slug && isset($_GET['country']) && $_GET['country'] == $country_term->slug);

									$civcas_url = '/civilian-casualties/?country=' . $country_term->slug . '&belligerent=' . $belligerent_term->slug;
									
									?>
									<li>
										<a href="<?php echo $civcas_url; ?>" class="<?php echo ($civcas_active) ? "active" : ""; ?>">
											<?php if ($country_term->slug === 'somalia' || $country_term->slug === 'yemen'): ?>
												US Strikes &amp; Civilian Casualties
											<?php else: ?>
												Civilian Casualties
											<?php endif; ?>

											<?php if ($belligerent_term->slug === 'turkish-military'): ?>
												from Turkish actions
											<?php endif; ?>

											<?php if (count($country_terms) > 1): ?>
												in <?php echo $country_term->name; ?>
											<?php endif; ?>

											<?php if ($conflict_post->post_name == 'israeli-military-in-syria-the-gaza-strip' && $country_term->slug == 'the-gaza-strip'): ?>
												May 2021
											<?php endif; ?>
										</a>
									</li>
								<?php endforeach; ?>
							<?php endif; ?>
						<?php endforeach; ?>
					<?php else: ?>
							<?php if (isset($country_terms) && $country_terms && count($country_terms) > 0): ?>
								<?php foreach($country_terms as $country_term): ?>
									<?php

									$civcas_active = ($civilian_casualties_active && isset($_GET['belligerent']) && $_GET['belligerent'] == $belligerent_term->slug && isset($_GET['country']) && $_GET['country'] == $country_term->slug);
									$civcas_url = '/civilian-casualties/?country=' . $country_term->slug;
									if (get_the_ID() == CONFLICT_ID_ALL_BELLIGERENTS_IN_LIBYA_2011) {
										$civcas_url .= '&start_date=2011-01-01&end_date=2011-12-31';
									}
									?>
									<li>
										<a href="<?php echo $civcas_url; ?>" class="<?php echo ($civcas_active) ? "active" : ""; ?>">
											Civilian Casualties
											<?php if (count($country_terms) > 1): ?>
												in <?php echo $country_term->name; ?>
											<?php endif; ?>
										</a>
									</li>
								<?php endforeach; ?>
							<?php endif; ?>						
					<?php endif; ?>					


					<?php if (in_array($conflict_post->post_name,  ['coalition-in-iraq-and-syria', ])): ?>

						<?php
						$credibles_post = get_the_credibles_post();
						?>

						<?php if ($credibles_post): ?>
							<li><a href="<?php the_permalink($credibles_post->ID); ?>">The Credibles</a></li>
						<?php endif; ?>

					<?php endif; ?>

					<?php if (in_array($conflict_post->post_name,  ['coalition-in-iraq-and-syria', 'all-belligerents-in-libya'])): ?>
						<li><a href="/military-claims?<?php echo http_build_query($params); ?>" class="<?php echo ($military_claims_active) ? "active" : ""; ?>">Military Claims</a></li>
					<?php endif; ?>
					
					<?php if ($conflict_post->post_name == 'turkish-military-in-iraq-and-syria'): ?>
						<li>
							<?php
							$civcas_active = ($civilian_casualties_active && isset($_GET['belligerent']) && $_GET['belligerent'] == 'ypg');
							?>
							<a href="/civilian-casualties?belligerent=ypg" class="<?php echo ($civcas_active) ? "active" : ""; ?>">
								Civilian Casualties from YPG Counterfire
							</a>
						</li>
					<?php endif; ?>
					
					<?php /*
					<?php if (in_array($conflict_post->post_name,  ['israel-and-gaza-2023'])): ?>
						<li>
							<?php
							$civcas_active = ($civilian_casualties_active && isset($_GET['country']) && $_GET['country'] == 'lebanon' && isset($_GET['belligerent']) && $_GET['belligerent'] == 'israeli-military');
							?>
							<a href="/civilian-casualties?belligerent=israeli-military&country=lebanon" class="<?php echo ($civcas_active) ? "active" : ""; ?>">
								Civilian Casualties in Lebanon
							</a>
						</li>
					<?php endif; ?>
					*/ ?>
					
					<?php if ($conflict_post->post_name == 'israeli-military-in-syria-the-gaza-strip'): ?>
						<li>
							<?php
							$civcas_active = ($civilian_casualties_active && isset($_GET['belligerent']) && $_GET['belligerent'] == 'palestinian-militants');
							?>
							<a href="/civilian-casualties?belligerent=palestinian-militants" class="<?php echo ($civcas_active) ? "active" : ""; ?>">
								Civilian Casualties from Palestinian Militant Actions May 2021
							</a>
						</li>
					<?php endif; ?>

					
					<?php if (!in_array($conflict_post->post_name, $exclude_conflict_data)): ?>
						<li><a href="/research?<?php echo http_build_query($params); ?>" class="<?php echo ($research_active) ? "active" : ""; ?>">Research</a></li>
					<?php endif; ?>
					
					<li><a href="/news?<?php echo http_build_query($params); ?>" class="<?php echo ($news_active) ? "active" : ""; ?>">News</a></li>


					<?php if (isset($country_term) && ($country_term->slug === 'somalia' || $country_term->slug === 'yemen')): ?>
						<li><a href="/archives/bij-drone-war">Bureau Drone Wars Archive</a></li>
					<?php endif; ?>
				</ul>
			</div>
		</div>
	<?php endif; ?>

	<?php if ($is_home_page): ?>
		<div class="content intro">
			<div class="full">
				<?php the_content(); ?>
			</div>
		</div>
	<?php endif; ?>

	<?php if ($is_bij_archive): ?>
		<article>
			<div class="content bij">				
				<div class="full">
					<h1>Bureau of Investigative Journalism –<br/>The Covert Drone Wars Archive</h1>
				</div>
			</div>
		</article>
	<?php endif; ?>


	<?php if ($featured_image_caption): ?>
		<p class="caption"><?php echo $featured_image_caption; ?></p>	
	<?php elseif ($post_thumb_caption): ?>
		<p class="caption"><?php echo $post_thumb_caption; ?></p>		
	<?php endif; ?>
</header>

<main>

	<?php /*
	<?php if (get_the_ID() !== 41455): ?>
		<div id="donate">
			<a href="<?php echo site_url();?>/about/donate">We need your support to hold governments to account. Please <span>donate</span> here.</a>
		</div>
	<?php endif; ?>
	*/ ?>