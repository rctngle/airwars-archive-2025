
<div class="pagination">
	
	<div class="pagination__inner">
	<?php 

		$pagination_links = paginate_links([
			'prev_text' => __('<i class="fa-light fa-arrow-left-long"></i> <span>prev</span>'),
			'next_text' => __('<span>next</span> <i class="fa-light fa-arrow-right-long"></i>'),
		]);
			
		echo $pagination_links;
	?>
	</div>
</div>