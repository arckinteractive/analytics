<?php

$data = elgg_extract('data', $vars);
if (empty($data)) {
	return;
}
?>
<script>
	require(['analytics/logger'], function (logger) {
		logger.logEntityView(<?= json_encode(array_filter($data)) ?>);
	});
</script>