<?php
/**
 * HTML block element
 *
 * @package woodmart
 */

$classes = ' whb-' . $id;
?>
<div class="wd-header-html wd-entry-content<?php echo esc_attr( $classes ); ?>">
	<?php echo woodmart_get_html_block( $params['block_id'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</div>
