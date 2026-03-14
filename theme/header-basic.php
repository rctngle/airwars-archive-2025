<?php 

$classes = ['no-header']
?>
<!DOCTYPE html>
<html>
<head>
	<title>Airwars</title>
	<meta name="description" content="Monitoring and assessing civilian harm from airpower-dominated international military actions. Seeking transparency and accountability from belligerents, and advocating on behalf of affected non-combatants. Archiving open-source casualty reports, and military claims by nations.">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	
	<meta property="og:url" content="<?php echo $permalink ?>" />
	<meta property="og:type" content="article" />
	<meta property="og:title" content="<?php echo $title ?>" />
	<meta property="og:description" content="<?php if (is_singular() || is_home()): echo $excerpt; endif; ?>" />

	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:site" content="@airwars">
	<meta name="twitter:creator" content="@airwars">
	<meta name="twitter:title" content="<?php echo $title ?>">
	<meta name="twitter:description" content="<?php if (is_singular() || is_home()): echo $excerpt; endif; ?>">
	<meta name="twitter:url" content="<?php echo $permalink ?>" />

	<?php 
	if ( (is_singular() || is_home()) && has_post_thumbnail(get_the_ID()) ) {
		$post_thumbs = wp_get_attachment_image_src(get_post_thumbnail_id( get_the_ID() ), 'large' );
		if ( ! empty($post_thumbs[0]) ) { 
			?>
			<meta name="twitter:image" content="<?php echo esc_url($post_thumbs[0]) ?>" />
			<meta property="og:image" content="<?php echo esc_url($post_thumbs[0]) ?>" />
			<?php 
		}
	} 
	?>
	<script src="https://kit.fontawesome.com/7beeb2b164.js" crossorigin="anonymous"></script>
	<script src="https://cdn.polyfill.io/v3/polyfill.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory');?>/build/styles/screen.css?t=<?php echo time(); ?>">
	<link href='https://api.tiles.mapbox.com/mapbox-gl-js/v0.51.0/mapbox-gl.css' rel='stylesheet' />
	<link rel="icon" type="image/png" sizes="32x32" href="<?php bloginfo('template_directory');?>/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="96x96" href="<?php bloginfo('template_directory');?>/favicon-96x96.png">
	<link rel="icon" type="image/png" sizes="16x16" href="<?php bloginfo('template_directory');?>/favicon-16x16.png">

</head>
<body class="<?php echo implode(" ", $classes); ?>">

<main>