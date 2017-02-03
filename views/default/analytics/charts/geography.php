<?php
if (!elgg_is_active_plugin('hypeMapsOpen')) {
	return;
}

echo elgg_view('analytics/charts/map', $vars);
?>
<script>
	require(['analytics/charts/geography']);
</script>