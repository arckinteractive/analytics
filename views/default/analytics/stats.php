<?php

$stats = elgg_extract('stats', $vars);

$output = '';
foreach ($stats as $stat) {
	$params = $vars;
	$params['stat'] = $stat;
	$output .= elgg_view('analytics/stat', $params);
}

?>
<div class="analytics-stats">
	<?= $output ?>
</div>
