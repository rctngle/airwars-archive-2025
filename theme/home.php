<?php

$lang = get_language();

$post_slug = get_post_field( 'post_name', get_post() );
if ($post_slug !== 'ar') {
	$query = new WP_Query(['post_type' => 'page', 'name' => 'airwars-homepage-redesign']);
	if ( $query->have_posts() ) {
		$query->the_post();
	}
} else {
	$query = new WP_Query(['post_type' => 'page', 'name' => 'ar']);
	if ( $query->have_posts() ) {
		$query->the_post();
	}
}

$what_we_do = get_field('home_what_we_do', get_the_ID());

$stats = [
	'num_conflicts_monitored' => [
		'label' => dict('conflicts_monitored'),
		'value' => format_number(airwars_get_num_conflicts_monitored()),
	],
	'num_deaths_assessed' => [
		'label' => dict('alleged_civilian_deaths_assessed'),
		'value' => format_number(airwars_get_num_civilian_deaths_assessed()),
	],
	'num_military_reports_archived' => [
		'label' => dict('military_reports_archived'),
		'value' => format_number(airwars_get_num_military_reports_archived()),
	],
	'num_civcas_incidents' => [
		'label' => dict('victim_names_recorded'),
		'value' => format_number(airwars_get_num_named_victims()),
	]
];


?>

<?php get_header(); ?>

<section id="our-monitoring" class="title">
	<div class="content">
		<div class="full">
			<div class="title"><h1><?php echo dict('our_monitoring_of_civilian_harm'); ?></h1></div>			
		</div>
	</div>
</section>


<section id="map-timeline-container">
	<div class="conflict-map-timeline" data-lang="<?php echo $lang; ?>"></div>
</section>

<section class="methodology">
	<div class="content">
		<div class="full">
			<div class="actions">
				<?php foreach($what_we_do as $action): ?>
					<div class="action">
						<h1><?php echo $action['what_we_do_title']; ?></h1>
						<p><?php echo $action['what_we_do_description']; ?></p>
					</div>
				<?php endforeach; ?>
			</div>
		</div>		
	</div>
</section>

<section class="stats">
	<div class="content">
		<div class="full">
			<div class="stats">
				<?php foreach($stats as $stat): ?>
					<?php if(isset($stat['link'])) :?>
						<a class="stat" href="<?php echo $stat['link'];?>">
							<div class="value"><?php echo $stat['value']; ?></div>
							<div class="label"><?php echo $stat['label']; ?></div>
						</a>
					<?php else: ?>
						<a class="stat" href="<?php echo get_post_type_archive_link('civ'); ?>">
							<div class="value"><?php echo $stat['value']; ?></div>
							<div class="label"><?php echo $stat['label']; ?></div>
						</a>
					<?php endif; ?>

					
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>

<section class="featureinvestigation">
	<div class="content">
		<div class="full">

			<a href="https://civilianprotectionmonitor.org/" target="_blank" class="featureinvestigation__banner">

				<div class="featureinvestigation__text">
					<div class="featureinvestigation__logo">
						<img src="<?php bloginfo('template_directory');?>/media/CPM-logo.svg">
					</div>
					
					<div>
						<h2>New tool assessing efforts<br/>to protect civilians in war</h2>
					
						<div class="featureinvestigation__button">
							<button>Learn more</button>
						</div>
					</div>

				</div>
				<img src="<?php bloginfo('template_directory');?>/media/background-with-strip.jpg">

			</a>
		</div>
	</div>
</section>
<?php /*
<section class="featureinvestigation">
	<div class="content">
		<div class="full">

			<a href="https://gaza-patterns-harm.airwars.org/" target="_blank" class="featureinvestigation__banner">
				<div class="featureinvestigation__gradient"></div>
				<div class="featureinvestigation__text">
					<div>
						<h1>Patterns of<br/>harm analysis</h1>
						<h2>Gaza, October 2023</h2>
					</div>
					<div class="featureinvestigation__button">
						<button>New <span>report</span>&nbsp;&nbsp;<span class="system">&rarr;</span></button>
					</div>

				</div>
				<img src="<?php bloginfo('template_directory');?>/media/patterns-harm-homepage-banner.jpg">
			</a>
		</div>
	</div>
</section>
*/?>
<?php /* if(get_post_status(118101) == 'publish'):?>
	<section class="featureinvestigation">
		<div class="content">
			<div class="full">
				<a href="https://idf-tweets-gaza.airwars.org" target="_blank" class="featureinvestigation__banner">
					<div class="featureinvestigation__text">
						<div>
							<h1>The Killings<br/>They Tweeted</h1>
							<h2>Investigating Israeli<br/>strike footage in Gaza</h2>
						</div>
						<div class="featureinvestigation__button">
							<button>New <span>investigation</span> <svg width="22" height="15" viewBox="0 0 24 15" fill="none" xmlns="http://www.w3.org/2000/svg" class="svelte-lnmf63"><path fill-rule="evenodd" clip-rule="evenodd" d="M11.0001 9V15L13.7565 15L23.2728 7.5L13.7565 0H11.0001V6L0.727539 6V9L11.0001 9ZM11.0001 9H15.7275V6H11.0001L11.0001 9Z" fill="black" class="svelte-lnmf63"></path></svg></button>
						</div>

					</div>
					<img src="<?php bloginfo('template_directory');?>/media/idf-tweets-gaza.jpg">
				</a>
			</div>
		</div>
	</section>
<?php endif; */?>


<section class="title conflicts">
	<div class="content">
		<div class="full">
			<div class="title"><h1><a href="<?php echo get_post_type_archive_link('civ'); ?>"><?php echo dict('civilian_casualties_archive'); ?> &rarr;</a></h1></div>
		</div>
	</div>
</section>

<section class="preview">
	<?php get_template_part('templates/home/conflicts'); ?>
</section>

<section class="home-conflict-data">
	<?php get_template_part('templates/home/conflict-data'); ?>
</section>

<?php /* 
<section class="tweets">
	<div class="content">
		<div class="full">
			<?php get_template_part('templates/social/tweets');
		</div>
	</div>
</section>
 */ ?>
<section class="home-osmp">
	<?php get_template_part('templates/home/osmp'); ?>
</section>

<?php get_template_part('templates/news/home'); ?>

<?php get_footer(); ?>
