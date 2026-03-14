<?php 

$lang = get_language(); 

?>

<?php if (have_rows('conflict_events')): ?>
	<section id="conflict-events">
		<div class="content">
			<div class="full">
				<h1><?php echo dict('conflict_events_in_focus'); ?></h1>
			</div>
		</div>
		<div class="scroller grabbable">
			<div class="scroller__outer">
				<div class="scroller__inner conflict-events">
					<div class="scroller__left"></div>
					<?php while(have_rows('conflict_events')): the_row(); ?>
						<div class="event">
							<div class="date">
								<?php if ($lang == 'en'): ?>
									<?php the_sub_field('date');?>
								<?php else: ?>
									<?php

									$fmt = datefmt_create(
										'ar-LY',
										IntlDateFormatter::FULL,
										IntlDateFormatter::NONE,
										'Europe/London',
										IntlDateFormatter::GREGORIAN,
									);
									// $fmt->setPattern('d MMM YYYY');
									$d = date_create_from_format('j F, Y', get_sub_field('date'));
									$t = strtotime($d->format('Y-m-d'));


									echo datefmt_format($fmt, $t);

									?>
								<?php endif; ?>
							</div>
							<?php
							$image = get_sub_field('image');
							?>
							<div class="image">
								<img src="<?php echo $image['sizes']['large']; ?>" />
							</div>
							<div class="text">
								<p><?php the_sub_field('text'); ?></p>
							</div>
							<div class="quote">
								“<?php the_sub_field('quote'); ?>”
								<?php if(get_sub_field('quote_citation')):?>
									<p>&mdash; <?php the_sub_field('quote_citation'); ?></p>
								<?php endif;?>

							</div>
						</div>
					<?php endwhile; ?>
					<div class="scroller__spacer"></div>
				</div>
			</div>
		</div>
		<div class="container">
			<div class="scroller__controls">
				<div class="scroller__leftarrow"><button><i class="far fa-arrow-left"></i></button></div>
				<div class="scroller__rightarrow"><button><i class="far fa-arrow-right"></i></button></div>
			</div>
		</div>
	</section>
<?php endif;?>