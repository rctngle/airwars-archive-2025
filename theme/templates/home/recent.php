<?php


?>

<section class="news reports">
	<div class="content">
		<div class="full">

			<?php

			// $credibles_post = get_the_credibles_post();

			// if ($credibles_post) {
			// 	$conflict_data_query = new WP_Query([
			// 		'post_type' => 'conflict_data',
			// 		'name' => 'the-credibles',
			// 		'orderby' => 'date',
			// 		'order' => 'DESC',
			// 		'posts_per_page' => 1,
			// 	]);
			// } else {
			// 	$conflict_data_query = new WP_Query([
			// 		'post_type' => 'conflict_data',
			// 		'name' => 'civilian-casualties-gaza-may-2021-map',
			// 		'orderby' => 'date',
			// 		'order' => 'DESC',
			// 		'posts_per_page' => 1,
			// 	]);


			// }

			$conflict_data_query = new WP_Query([
				'post_type' => 'conflict_data',
				'name' => 'civilian-casualties-gaza-may-2021-map',
				'orderby' => 'date',
				'order' => 'DESC',
				'posts_per_page' => 1,
			]);
			
			while ($conflict_data_query->have_posts()) : $conflict_data_query->the_post();
				$date = get_the_date();

				$image = get_field('preview_image');
				if ($image) {
					$preview = $image['url'];
				} else {
					$preview = get_the_post_thumbnail_url();
				}
				?>

				<article class="conflict-data-preview">
					<div class="title"><h1><a href="<?php the_permalink(); ?>">Featured Conflict Data</a></h1></div>
					<a href="<?php the_permalink(); ?>"><div class="image" style="background-image: url(<?php echo $preview; ?>)"></div></a>
					<div><h2><a href="<?php the_permalink(); ?>"><?php the_title();?></a></h2></div>

						<div class="author-date">
							<?php the_date(); ?>
						</div>

				</article>

				<?php
			endwhile;
			?>


			<?php
			$terms = ['monthly-annual-assessment', 'in-depth-report'];
			$titles = ['Latest Assessment', 'Latest In-Depth Report'];

			foreach($terms as $idx => $term) {
				$reports_query = new WP_Query([
					'post_type' => 'report',
					'orderby' => 'date',
					'order' => 'DESC',
					'posts_per_page' => 1,
					'tax_query' => array(
						array(
							'taxonomy' => 'report_category',
							'field'    => 'slug',
							'terms'    => $term,
						),
					),
				]);

				$title = $titles[$idx];
				while ($reports_query->have_posts()) : $reports_query->the_post();
					$date = get_the_date();
					$display_author = get_display_author();
					?>

					<article class="reports-preview">
						<div class="title"><h1><a href="<?php the_permalink(); ?>"><?php echo $title; ?></a></h1></div>
						<a href="<?php the_permalink(); ?>"><div class="image" style="background-image: url(<?php echo get_the_post_thumbnail_url(); ?>)"></div></a>
						<div><h2><a href="<?php the_permalink(); ?>"><?php the_title();?></a></h2></div>
						<div class="author-date">
							<?php the_date(); ?>
							<?php if ($display_author): ?>
								<br/>
								<?php echo $display_author; ?>
							<?php endif; ?>
						</div>
					</article>

					<?php
				endwhile;
			}
			?>
		</div>
	</div>
</section>

<?php
wp_reset_postdata();	
?>
