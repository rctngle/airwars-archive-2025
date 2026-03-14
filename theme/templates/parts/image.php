<?php

$image_id = false;
if(isset($args['id'])){
	$image_id = $args['id'];
}

if ($image_id) {
	$image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);   

	$size = 'large';
	if(isset($args['size'])){
		$size = $args['size'];
	}
}
?>


<?php if($image_id):?>
	<?php echo wp_get_attachment_image( $image_id, $size, false, ['alt' => $image_alt] ); ?>	
<?php endif;?>	