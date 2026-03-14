

<article id="post-<?php the_ID(); ?>" <?php post_class('team'); ?>>	
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
			<div class="team-member info-main-block">
				<h1><?php echo $member_bio['team_member_name'];?></h1>
				
				<?php echo $member_bio['team_member_bio'];?>
				
			</div>
		</div>
		
	</div>
</article>
