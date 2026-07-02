<?php
/**
 * Shop breadcrumb
 *
 * @param string $delimiter Separator.
 *
 * @author WooThemes
 * @package WooCommerce/Templates
 * @version 2.3.0
 * @see woocommerce_breadcrumb()
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


$delimiter = '<span class="wd-delimiter">' . $delimiter . '</span>';

if ( ! empty( $breadcrumb ) ) {
	$count = count( $breadcrumb );
	$i     = 0;

	echo wp_kses_post( $wrap_before );

	foreach ( $breadcrumb as $key => $crumb ) {
		$attr = '';

		++$i;

		if ( $i === $count - 1 ) {
			$attr = ' class="wd-last-link"';
		}

		echo wp_kses_post( $before );

		if ( ! empty( $crumb[1] ) && count( $breadcrumb ) !== $key + 1 ) :
			?>
				<a href="<?php echo esc_url( $crumb[1] ); ?>"<?php echo wp_kses( $attr, true ); ?>>
					<?php echo esc_html( $crumb[0] ); ?>
				</a>
			<?php
		else :
			$attr    = ' class="wd-last"';
			$queried = get_queried_object();

			if ( is_tax() && $queried instanceof WP_Term ) {
				$pa_taxonomy   = get_taxonomy( $queried->taxonomy );
				$is_pa_archive = taxonomy_is_product_attribute( $queried->taxonomy ) && $pa_taxonomy && $pa_taxonomy->labels->name === $crumb[0];
				if ( $is_pa_archive ) {
					$attr = '';
				}
			}
			?>
				<span<?php echo wp_kses( $attr, true ); ?>>
					<?php echo esc_html( $crumb[0] ); ?>
				</span>
			<?php
		endif;

		echo wp_kses_post( $after );

		if ( count( $breadcrumb ) !== $key + 1 ) {
			echo wp_kses_post( $delimiter );
		}
	}

	echo wp_kses_post( $wrap_after );
}
