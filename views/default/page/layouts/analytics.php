<?php
/**
 * Layout for main column with one sidebar
 *
 * @package Elgg
 * @subpackage Core
 *
 * @uses $vars['title']   Optional title for main content area
 * @uses $vars['content'] Content HTML for the main column
 * @uses $vars['sidebar'] Optional content that is added to the sidebar
 * @uses $vars['nav']     Optional override of the page nav (default: breadcrumbs)
 * @uses $vars['header']  Optional override for the header
 * @uses $vars['footer']  Optional footer
 * @uses $vars['class']   Additional class to apply to layout
 */

$class = 'elgg-layout elgg-layout-analytics clearfix';
if (isset($vars['class'])) {
	$class = "$class {$vars['class']}";
}

ob_start();
?>
<div class="<?php echo $class; ?>">
	<div class="elgg-sidebar">
		<?php
			echo elgg_extract('sidebar', $vars);
		?>
	</div>

	<div class="elgg-main elgg-body">
		<?php
		echo elgg_extract('content', $vars);
		?>
	</div>
</div>
<?php
$vars['content'] = ob_get_clean();
unset($vars['sidebar']);

echo elgg_view('page/layouts/one_column', $vars);
