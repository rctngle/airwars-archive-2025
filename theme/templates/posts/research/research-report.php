<?php
$thumbnail = get_the_post_thumbnail_url(null, 'large');
$media = get_field('media', get_the_ID());

$document = false;
if ($media && is_array($media) && count($media) > 0) {
	$document = $media[0];
}

$languages = get_field('language');
$multilang = ($languages && is_array($languages) && count($languages) > 0);


$classes = get_post_class();
if (!is_singular()) {
	$classes[] = 'in-archive';
}

if ($multilang) {
	$classes[] = 'multilang';
}

?>

<article id="post-<?php the_ID(); ?>" class="<?php echo implode(' ', $classes); ?>">
	<div class="content">
		<div class="info-left">

			<div class="meta-block">
				<h4>Published</h4>
				<?php echo date('F Y', strtotime(get_the_date())); ?>
			</div>

			<?php get_template_part('templates/posts/authors/authors'); ?>

			<?php get_template_part('templates/parts/permalink'); ?>
		</div>
		<div class="info-main">

			<?php if ($multilang): ?>
				<div class="langswitcher">
					<span>View in:</span>
					<?php foreach($languages as $language): ?>
						<a <?php if (strtolower($language['language']) == 'english'): ?>class="active"<?php endif; ?> href="#<?php echo strtolower($language['language']); ?>"><?php echo $language['language_local']; ?></a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<div class="info-main-block">

				<?php if ($multilang): ?>

					<?php foreach($languages as $language): ?>

						<div class="lang lang-<?php echo strtolower($language['language']); ?> <?php if (strtolower($language['language']) != 'english'): ?>hidden<?php endif; ?>">

							<?php if ($language['report']): ?>
								<h1><a href="<?php echo $language['report']['url']; ?>" target="_blank"><?php echo $language['title']; ?></a></h1>
							<?php else: ?>
								<h1><?php echo $language['title']; ?></h1>
							<?php endif; ?>

							<div><?php echo $language['content']; ?></div>
							
							<div class="document-thumbnail">
								<?php if ($language['preview']): ?>
									<?php if ($language['report']): ?>
										<a href="<?php echo $language['report']['url']; ?>" target="_blank">
											<img src="<?php echo $language['preview']['sizes']['large']; ?>" />
										</a>
									<?php else: ?>
										<h1><?php echo $language['title']; ?></h1>
									<?php endif; ?>
								<?php endif; ?>
							</div>
							
						</div>
					<?php endforeach; ?>

				<?php else: ?>
					<?php if ($document): ?>
						<h1><a href="<?php echo $document['media_document']['url']; ?>" target="_blank"><?php the_title(); ?></a></h1>
					<?php else: ?>
						<h1><?php the_title(); ?></h1>
					<?php endif; ?>
					<?php the_content(); ?>

					<div class="document-thumbnail">
						<?php if ($thumbnail): ?>
							<?php if ($document): ?>
								<a href="<?php echo $document['media_document']['url']; ?>" target="_blank"><img src="<?php echo $thumbnail; ?>" /></a>
							<?php else: ?>
								<h1><?php the_title(); ?></h1>
							<?php endif; ?>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<div class="info-right">	
			<div class="document-thumbnail">
				<?php if ($multilang): ?>

					<?php foreach($languages as $language): ?>

						<div class="lang lang-<?php echo strtolower($language['language']); ?> <?php if (strtolower($language['language']) != 'english'): ?>hidden<?php endif; ?>">
							<?php if ($language['preview']): ?>
								<?php if ($language['report']): ?>
									<a href="<?php echo $language['report']['url']; ?>" target="_blank">
										<img src="<?php echo $language['preview']['sizes']['large']; ?>" />
									</a>
								<?php else: ?>
									<h1><?php echo $language['title']; ?></h1>
								<?php endif; ?>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>


				<?php else: ?>
					<?php if ($thumbnail): ?>
						<?php if ($document): ?>
							<a href="<?php echo $document['media_document']['url']; ?>" target="_blank"><img src="<?php echo $thumbnail; ?>" /></a>
						<?php else: ?>
							<a href="<?php echo (get_field('alternative_permalink')) ? get_field('alternative_permalink') : get_the_permalink(); ?>"><img src="<?php echo $thumbnail; ?>" /></a>
						<?php endif; ?>
					<?php endif; ?>
				<?php endif; ?>	
			</div>
		</div>
	</div>
</article>