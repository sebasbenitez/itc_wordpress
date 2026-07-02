<?php
/**
 * Header secondary menu element.
 *
 * @package woodmart
 */

use XTS\Modules\Mega_Menu_Walker;

$menu_style = ( $params['menu_style'] ) ? $params['menu_style'] : 'default';
$location   = 'main-menu';
$classes    = 'whb-' . $id;
$classes   .= ' text-' . $params['menu_align'];
$aria_label = esc_attr__( 'Secondary navigation', 'woodmart' );

if ( 'bordered' === $params['menu_style'] ) {
	$classes .= ' wd-full-height';
}

$menu_classes = ' wd-style-' . $menu_style;
if ( isset( $params['items_gap'] ) ) {
	$menu_classes .= ' wd-gap-' . $params['items_gap'];
}

if ( ! empty( $params['icon_alignment'] ) && 'inherit' !== $params['icon_alignment'] ) {
	$menu_classes .= ' wd-icon-' . $params['icon_alignment'];
}

$items_bg_activated = ! empty( $params['items_bg_color'] ) || ! empty( $params['items_bg_color_hover'] ) || ! empty( $params['items_bg_color_active'] );

if ( $items_bg_activated ) {
	$menu_classes .= ' wd-add-pd';
}

if ( isset( $params['inline'] ) && $params['inline'] ) {
	$classes .= ' wd-inline';
}

if ( ! empty( $params['bg_overlay'] ) ) {
	woodmart_enqueue_js_script( 'menu-overlay' );

	$classes .= ' wd-with-overlay';
}

$menu_object = wp_get_nav_menu_object( $params['menu_id'] );

if ( ! empty( $menu_object ) && $menu_object->name ) {
	$aria_label = $menu_object->name;
}
?>

<nav class="wd-header-nav wd-header-secondary-nav <?php echo esc_attr( $classes ); ?>" role="navigation" aria-label="<?php echo esc_attr( $aria_label ); ?>">
	<?php
	if ( $menu_object && wp_get_nav_menu_items( $params['menu_id'] ) ) {
		if ( 'underline' === $params['menu_style'] ) {
			woodmart_enqueue_inline_style( 'mod-nav-style-underline' );
		} elseif ( in_array( $params['menu_style'], array( 'bordered', 'separated' ), true ) ) {
			woodmart_enqueue_inline_style( 'mod-nav-style-bordered-separated' );
		} elseif ( 'bg' === $params['menu_style'] ) {
			woodmart_enqueue_inline_style( 'mod-nav-style-bg' );
		}

		wp_nav_menu(
			array(
				'container'  => '',
				'menu'       => $params['menu_id'],
				'menu_class' => 'menu wd-nav wd-nav-header wd-nav-secondary' . $menu_classes,
				'walker'     => new Mega_Menu_Walker(),
			)
		);
	} elseif ( $params['menu_id'] ) {
		?>
		<span>
			<?php esc_html_e( 'Wrong menu selected', 'woodmart' ); ?>
		</span>
		<?php
	} else {
		?>
		<span>
			<?php esc_html_e( 'Choose menu', 'woodmart' ); ?>
		</span>
		<?php
	}
	?>
</nav>
