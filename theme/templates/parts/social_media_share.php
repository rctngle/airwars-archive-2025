<?php 
	$title = get_the_title();	
	$permalink = get_permalink();
?>

<h4>Share on</h4>
<div class="share">


	<a class="share__item" href="https://twitter.com/intent/tweet?text=<?php echo $title;?> via @airwars&url=<?php echo $permalink;?>" data-size="large"><i class="fab fa-twitter"></i> Twitter</a>
	<a class="share__item" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $permalink;?>"><i class="fab fa-facebook"></i> Facebook</a>
	
	<a class="share__item" href="mailto:?subject=Airwars:<?php echo $title;?>&amp;body=<?php echo $post_type;?>: %0D%0A<?php echo $title;?>%0D%0Aon Airwars%0D%0A%0D%0A<?php echo urlencode($permalink);?>">
		<i class="fas fa-solid fa-envelope"></i> Email
	</a>


</div>

