<?php

if (!$post_type) {
	$post_type = get_post_type();

	if (!$post_type) {
		$post_type = $wp_query->query['post_type'];
	}
}

if (is_array($post_type)) {
	$post_type = $post_type[0];
}

$filters = get_filters($post_type);

$current_filters = [];
$current_query = [];
if ($filters && count($filters) > 0) {
	foreach($filters as $filter => $group) {

		if (isset($_GET[$filter])) {
			$current_query[$filter] = $_GET[$filter];
			$values = explode(',',$_GET[$filter]);

			foreach($values as $value) {
				$value = trim($value);		
				if (isset($filters[$filter]['options'])) {
					foreach($filters[$filter]['options'] as $option) {
						if ($value == $option['value']) {
							if (!isset($current_filters[$filter])) {
								$current_filters[$filter] = [];
							}
							$current_filters[$filter][] = $option;
						}
					}
				}

				if ($filters[$filter]['type'] == 'date') {
					$current_filters[$filter] = $value;
				}
			}
		}
	}
}

$show_civilian_harm_bar = true;
$civilian_harm_bar_countries = ['somalia', 'yemen'];

$show_strike_filters = false;
$strike_filters_countries = ['somalia', 'yemen'];


if (isset($current_filters['country'])) {
	foreach($current_filters['country'] as $country) {
		if (in_array($country['value'], $civilian_harm_bar_countries)) {
			$show_civilian_harm_bar = true;
		}
		if (in_array($country['value'], $strike_filters_countries)) {
			$show_strike_filters = true;
		}
	}
}

if (!$show_strike_filters) {
	unset($filters['strike_status']);
	unset($filters['type_of_strike']);
}


$orderby = (isset($_GET['orderby'])) ? $_GET['orderby'] : 'incident_date';
$order = (isset($_GET['order'])) ? $_GET['order'] : 'desc';

$orderby_options = [
	'incident_date' => 'Incident date',
	'alleged_fatalities' => 'Civilian deaths alleged',
	'confirmed_fatalities' => 'Civilian deaths confirmed by belligerent',
];

if (isset($_GET['civilian_harm_reported']) && $_GET['civilian_harm_reported'] == 'no') {
	$orderby_options = [
		// 'incident_date' => 'Incident date',
	];
} else {
	$orderby_options = [
		'incident_date' => 'Incident date',
		'alleged_fatalities' => 'Civilian deaths alleged',
		'confirmed_fatalities' => 'Civilian deaths confirmed by belligerent',
	];
}

?>
<section id="filters">
	<div class="full">
		<div id="filter-bar" class="content">	
			<div class="info-left"></div>
			<div class="info-main">
				<?php if ($filters && is_array($filters)): ?>
					<?php foreach($filters as $filter => $group): ?>
						<?php if ($group['type'] == 'multiselect'): ?>

							<div class="filter <?php echo $filter; ?>">
								<div class="label"><?php echo $group['label']; ?></div>
								<div class="ui select">
									<select data-filter="<?php echo $filter; ?>" disabled>
										<option value="all"><?php echo (isset($group['label_plural'])) ? $group['label_plural'] : 'All'; ?></option>
										<?php foreach($group['options'] as $option): ?>
											<?php if ($option['value'] == '-'): ?>
												<option value="" disabled="disabled">────────</option>
											<?php else: ?>
												<option value="<?php echo $option['value']; ?>"><?php echo $option['label']; ?></option>
											<?php endif; ?>
										<?php endforeach; ?>
									</select>
									<i class="fal fa-plus-circle"></i>
								</div>

								<?php if (isset($current_filters[$filter])): ?>
									<?php foreach($current_filters[$filter] as $current_filter): ?>
										<div class="current-filter" data-filter="<?php echo $filter; ?>" data-value="<?php echo $current_filter['value']; ?>"><?php echo $current_filter['label']; ?> <i class="far fa-times-circle"></i> </div>
									<?php endforeach; ?>
								<?php endif; ?>

							</div>
						<?php elseif ($group['type'] == 'date'): ?>
							<?php

							$value = (isset($current_filters[$group['filter']])) ? date('M j Y', strtotime($current_filters[$group['filter']])) : false;
							?>
							<div class="filter date <?php echo $group['label'];?>">
								<div class="label"><?php echo $group['label'];?> date</div>
								<div class="ui text">
									<input type="text" name="<?php echo $group['filter']; ?>" placeholder="<?php echo $group['label'];?> date" <?php if ($value): ?>class="date-selected" value="<?php echo $value; ?>"<?php endif; ?> autocomplete="off">
									<i class="far fa-times-circle"></i>
									<i class="fal fa-calendar"></i>
								</div>
							</div>
						<?php endif; ?>
					<?php endforeach; ?>		
				<?php endif; ?>

				<div class="filter search">
					<div class="label">text search</div>
					<div class="ui text">
						<form id="search-form" action="">
							<input id="search-input" type="text" placeholder="<?php echo "Search..."; ?>" name="search" value="<?php echo (isset($_GET['search'])) ? $_GET['search'] : ''; ?>" autocomplete="off">
							<i class="far fa-search"></i>
						</form>
					</div>
				</div>	
			</div>
		</div>

		<?php if ($show_civilian_harm_bar): ?>		
			<div>
				<?php foreach($filters as $filter => $group): ?>
					<?php if ($group['type'] == 'radio'): ?>

						<div class="radio-bar">
							<?php foreach($group['options'] as $option_count => $option): ?>
								<?php
								$checked = false;
								if (isset($current_filters[$filter])) {
									foreach($current_filters[$filter] as $current_filter_val) {
										if ($current_filter_val['value'] == $option['value']) {
											$checked = true;
										}
									}
								} else {
									$checked = ($option_count == 0) ? true : false;
								}
								?>
								<input id="<?php echo $filter . '-' . $option_count; ?>" type="radio" data-filter="<?php echo $filter; ?>" name="<?php echo $filter; ?>" value="<?php echo $option['value']; ?>" <?php if ($checked): ?>checked="checked"<?php endif; ?> disabled />
								<label for="<?php echo $filter . '-' . $option_count; ?>"><?php echo $option['label']; ?></label>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<div class="results-bar content <?php echo $post_type; ?>">
			<div class="num-results">
				<div class="number"><?php echo $wp_query->found_posts; ?> <?php echo ($wp_query->found_posts == 1) ? "Result" : "Results"; ?></div>
				<div class="label">sort by:</div>		
			</div>
			<?php if ($post_type == 'civ' && count($orderby_options) > 0): ?>
				<div class="sort-by-options">
					<div class="label">Sort by:</div>
					<?php foreach($orderby_options as $orderby_option => $orderby_label): ?>
						<div class="option <?php if ($orderby == $orderby_option): echo 'selected'; endif; ?> <?php if ($order): echo $order; endif; ?>">
							<?php
							$next_order = 'desc';
							if ($orderby == $orderby_option) {
								$next_order = ($order == 'asc') ? 'desc' : 'asc';	
							}
							
							$query = [];
							foreach ($current_query as $filter => $value) {
								$query[$filter] = $value;
							}

							$query['orderby'] = $orderby_option;
							$query['order'] = $next_order;

							?>
							<a href="?<?php echo http_build_query($query); ?>"><?php echo $orderby_label; ?></a><i class="far fa-arrow-up"></i><i class="far fa-arrow-down"></i>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

		</div>	
	</div>
</section>

<?php /*
<?php if (isset($current_filters['infrastructure'])): ?>
	<article class="filterwarning">
		<div class="radio-bar-note content">
			<div class="info-left"></div>
			<div class="info-main">As part of an on-going project to identify damage to civilian infrastructure, we have begun updating our incident classifications. To date, these classifications include attacks on schools across our entire civilian harm archive, and attacks on medical facilities so far resulting from alleged Russian actions in Syria only.</div>
		</div>
	</article>
<?php endif; ?>
*/ ?>

<script src="<?php bloginfo('template_directory');?>/build/scripts/filterbar.js"></script>
