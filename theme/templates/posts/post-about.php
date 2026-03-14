<?php
$team_bios = get_field('team_bios');
$boards = get_field('board_group');
$post_name = get_post_field( 'post_name', get_post() );

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>	
	<div class="content">
		<div class="info-left"></div>

		<div class="info-main">
			<div class="info-main-block">
				<?php if (get_field('article_subheading')): ?>
					<h2><?php the_field('article_subheading'); ?></h2>
				<?php endif; ?>

				<?php if(is_singular()): ?>
					<?php the_content(); ?>
				<?php else: ?>
					<?php the_excerpt(); ?>
				<?php endif; ?>
			</div>
		</div>
		<div class="info-right">
		</div>
	</div>
</article>


<?php if ($team_bios): ?>
	<?php 
		if($post_name == 'volunteer'): 
			$bios_title = 'Volunteer Testimonials';
		else:
			$bios_title = 'Team';
		endif; 
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class('board'); ?>>
		<div class="content">
			<div class="info-left"></div>
			<div class="info-main">
				<div class="info-main-block">
					<h1><?php echo $bios_title;?></h1>
				</div>
			</div>
		</div>
	</article>
	<?php foreach($team_bios as $member_bio): ?>

		<?php include(locate_template('templates/posts/about/bio.php')); ?>
	<?php endforeach; ?>
<?php endif; ?>

<?php if ($boards): ?>

	<?php foreach($boards as $board): ?>
		
		<article id="post-<?php the_ID(); ?>" <?php post_class('board'); ?>>	
			<div class="content">
				<div class="info-left"></div>
				<div class="info-main">
					<div class="info-main-block">
						<h1><?php echo $board['board_title']; ?></h1>
					</div>
				</div>
			</div>
		</article>

		<?php foreach($board['team_bios'] as $member_bio): ?>
			<article <?php post_class('board-member'); ?>>

			<div class="content">
				<div class="info-left">						
					<?php if ($member_bio['team_member_image']): ?>

						<div class="team-member-image">
							<div class="overlay"></div>
							<div style="background-image: url(<?php echo $member_bio['team_member_image']['sizes']['large']; ?>)" class="portrait"></div>
						</div>
					<?php endif; ?>
				</div>				
				<div class="info-main">
					<div class="info-main-block">							
						
						<h1><?php echo $member_bio['team_member_name'];?></h1>
						<?php echo $member_bio['team_member_bio'];?>
					
					</div>
				</div>					
			</div>
			</article>
		<?php endforeach; ?>				

		




	<?php endforeach; ?>
<?php endif; ?>
