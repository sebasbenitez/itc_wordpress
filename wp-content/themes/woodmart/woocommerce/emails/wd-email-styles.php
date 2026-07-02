<?php
/**
 * Woodmart email styles.
 *
 * @package woodmart
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$base             = get_option( 'woocommerce_email_base_color' );
$is_email_preview = apply_filters( 'woocommerce_is_email_preview', false );

if ( $is_email_preview ) {
	$base_transient = get_transient( 'woocommerce_email_base_color' );
	$base           = $base_transient ? $base_transient : $base;
}

$btn_text_color = wc_light_or_dark( $base, '#333', '#ffffff' );

?>
.xts-align-start {
	text-align: start;
}

.xts-align-end {
	text-align: end;
}

.xts-prod-table {
	font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;
	width: 100%;
	margin: 0 0 16px;
}

.xts-tbody-td {
	vertical-align: middle;
	font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;
}

.xts-thumb-link {
	display: flex;
	align-items: center;
	border-bottom: none;
	text-decoration: none;
}

.xts-img-col {
	width: 32px;
}

.xts-unit-slash {
	margin-inline: 4px;
}

.xts-thumb {
	margin-inline-end: 15px;
	max-width:70px;
}

.xts-add-to-cart {
	display: inline-block;
	background-color: <?php echo esc_attr( $base ); ?>;
	color: <?php echo esc_attr( $btn_text_color ); ?>;
	white-space: nowrap;
	padding: .618em 1em; 
	border-radius: 3px;
	text-decoration: none;
}
<?php
