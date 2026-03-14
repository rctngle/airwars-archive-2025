<?php


define( 'DONOTCACHEPAGE', true );
header( 'Cache-Control: private, no-store, no-cache, must-revalidate, max-age=0' );
header( 'Pragma: no-cache' );   // old proxies
header( 'Expires: 0' );

acf_form_head();

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory');?>/build/styles/source.css?t=<?php echo time(); ?>">
	<title>Source</title>
	<?php wp_head(); ?>
</head>
<body>
	<div class="containers <?php echo (is_user_logged_in()) ? 'logged-in' : 'logged-out';?>">
		<div class="container">


		<?php



		if (is_user_logged_in()) {
			acf_form([
				'post_id' => get_the_ID(),
				'post_title' => false,
				'post_content' => false,
				'return' => get_the_permalink(),
				'new_post' => false,
				'field_groups' => [
					131346, // Source
					141440, // Source Casualties
					141177, // Source Incident Details
				],
				'fields' => [
					'source_content_translated',
					'source_includes_video',
					'source_video_transcript_translated',

					'killed_injured_civilian_non_combatants',
					'killed_injured_children',
					'killed_injured_women',
					'killed_injured_men',
					'killed_injured_belligerents',

					'source_observations',
					// 'source_belligerents',
					'source_posted_by',
					'source_not_primary_belligerent',
					// 'source_strike_status_prelim',
					// 'source_civilian_harm_status_prelim',
				],
			]);

		}
		?>


		<?php
			$images = get_field('source_images');
			$element_capture = get_field('source_element_capture');
			$window_capture = get_field('source_window_capture');
		?>



		</div>
		<div class="container">
			<?php if(is_user_logged_in()):?>
				<div>
					<button id="copy-btn">Copy</button>
				</div>
			<?php endif;?>

			<div id="assessment-content">
				<?php if(is_user_logged_in()):?>
					<h2>[Airwars Source ID: <?php echo get_the_ID(); ?>]</h2>
					<br/>
				<?php else:?>
					<h2>Source</h2>
					<br/>
				<?php endif;?>
				<div>
					<span>URL: </span> 
					<a href="<?php the_field('source_url'); ?>" target="_blank">
						<?php the_field('source_url'); ?>
					</a>
				</div>
				
				<div>
					<span>Captured Post Date: </span> <?php the_field('source_date'); ?>
				</div>

				<?php if (get_field('source_author') != get_field('source_author_translated')): ?>
					<div>
						<span>Translated Author: </span> <?php the_field('source_author_translated'); ?>
					</div>
				<?php endif; ?>

				<div>
					<span>Author: </span> <?php the_field('source_author'); ?>
				</div>

				<br/>
			
				<?php if (trim(get_field('source_content')) != trim(get_field('source_content_translated'))): ?>
					<div>
						<span>Translated Content: </span>
						
						<div>
							<strong>
								<?php the_field('source_content_translated', false, false); ?>
							</strong>
						</div>
					</div>

					<br/>

				<?php endif; ?>

				<?php if (get_field('source_includes_video') && get_field('source_video_transcript_translated')): ?>

					<div>
						<span>Translated Video Transcript: </span>

						<strong>
							<?php the_field('source_video_transcript_translated', false, false); ?>
						</strong>
					</div>

					<br/>

				<?php endif; ?>			

			
				<div>
					<span>Content: </span>
					<div><?php the_field('source_content', false, false); ?></div>
				</div>

				<br/>

				<?php if ($images && is_array($images)): ?>

					<div>
						<span>Images: </span>
						<div>
							<?php foreach($images as $idx => $image): ?>
								<img src="<?php echo str_replace('airwars.localhost', 'airwars.org', $image['sizes']['medium']); ?>" />
								
								<?php if ($idx > 0 && ($idx+1) % 2 == 0): ?>
									<br/>
								<?php endif; ?>
							<?php endforeach; ?>
						</div>
					</div>
					


				<?php endif; ?>

			</div>

			<div class="block">
				<h2>Additional Details</h2>
				<div>
					<span>Captured Date</span> <div><?php the_field('source_capture_date'); ?></div>
				</div>
					

				<div>
					<span>Captured Post ID</span> <div class="wrap"><?php the_field('source_post_id'); ?></div>
				</div>
				
			</div>
			<div class="element">
				<h3>Element</h3>
				<?php if ($element_capture): ?>
					<div><img src="<?php echo str_replace('airwars.localhost', 'airwars.org', $element_capture['sizes']['large']); ?>" /></div>
				<?php endif; ?>
			</div>

			<?php if(is_user_logged_in()):?>
				<div class="window">
					<h3>Window</h3>
					<?php if ($window_capture): ?>
						<div><img src="<?php echo str_replace('airwars.localhost', 'airwars.org', $window_capture['sizes']['large']); ?>" /></div>
					<?php endif; ?>
				</div>
			<?php endif;?>



		</div>
	</div>
	<script>

		document.addEventListener('DOMContentLoaded', () => {
			const copyBtn = document.getElementById('copy-btn');


			copyBtn.addEventListener('click', () => {

				let html = document.getElementById('assessment-content').innerHTML;

				// Remove tabs, newlines, and excessive spaces
				html = html
					.replace(/\t/g, '')
					.replace(/\n/g, '')
					.replace(/\r/g, '')
					.replace(/\s{2,}/g, ' ');


				const clipboardItem = new ClipboardItem({ 
					'text/html': new Blob([html], { type: 'text/html' }),
					'text/plain': new Blob([html], { type: 'text/plain' })
				});

				navigator.clipboard.write([clipboardItem])
					.then(() => console.log("clipboard.write() Ok"))
					.catch(error => alert(error))

			})
		});
	</script>

<?php wp_footer(); ?>
</body>
</html>
