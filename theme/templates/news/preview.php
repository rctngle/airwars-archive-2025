<?php
$display_author = get_display_author();
$url = (get_field('alternative_permalink')) ? get_field('alternative_permalink') : get_the_permalink();
?>

<article class="news-preview">
	<a href="<?php echo $url; ?>"><div class="image" style="background-image: url(<?php echo get_the_post_thumbnail_url(); ?>)"></div></a>
	<div class="title"><h2><a href="<?php echo $url; ?>"><?php the_title(); ?></a></h2></div>
	<div class="author-date">
		<?php echo get_the_date(); ?>
		<?php if ($display_author): ?>
			<br/>
			<?php echo $display_author; ?>
		<?php endif; ?>
	</div>
</article>

