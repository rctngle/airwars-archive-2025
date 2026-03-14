<?php global $post; ?>

<?php if ($post->post_name == 'ar'): ?>
	<?php get_template_part('home'); ?>
<?php elseif ($post->post_name == 'detroit'): ?>
	<?php get_template_part('templates/projects/detroit/index'); ?>
<?php elseif ($post->post_name == 'conflicting-truth'): ?>
	<?php get_template_part('projects/conflicting-truth/index'); ?>
<?php else: ?>	
	<?php get_template_part('templates/pages/'.$post->post_name); ?>
<?php endif; ?>