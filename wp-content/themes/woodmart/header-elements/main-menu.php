<?php
/**
 * Header main menu element.
 *
 * @package woodmart
 */

use XTS\Modules\Mega_Menu_Walker;

$extra_class = '';
$menu_style  = ( $params['menu_style'] ) ? $params['menu_style'] : 'default';
$location    = 'main-menu';
$classes     = 'text-' . $params['menu_align'];
$icon_type   = $params['icon_type'];

if ( 'custom' === $icon_type ) {
	$extra_class .= ' wd-tools-custom-icon';
}

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
	$classes     .= ' wd-inline';
	$extra_class .= ' wd-inline';
}

if ( 'text-only' !== $params['style'] && ! empty( $params['icon_design'] ) ) {
	$extra_class .= ' wd-design-' . $params['icon_design'];
}

if ( 'text-only' === $params['style'] && ! empty( $params['text_design'] ) ) {
	$extra_class .= ' wd-design-' . $params['text_design'] . '-text';
}

if ( ! empty( $params['bg_overlay'] ) ) {
	woodmart_enqueue_js_script( 'menu-overlay' );

	$classes .= ' wd-with-overlay';
}

if ( ! empty( $params['style'] ) ) {
	$extra_class .= ' wd-style-' . $params['style'];
}

if ( isset( $params['wrap_type'], $params['style'], $params['icon_design'], $params['full_screen'] ) && 'icon_and_text' === $params['wrap_type'] && 'text' === $params['style'] && in_array( $params['icon_design'], array( '6', '7' ), true ) && $params['full_screen'] ) {
	$extra_class .= ' wd-with-wrap';
}

if ( isset( $id ) ) {
	$extra_class .= ' whb-' . $id;
	$classes     .= ' whb-' . $id;
}

$show_tools_inner = false;

if (
	isset( $params['style'] ) &&
	(
		(
			'text-only' === $params['style'] &&
			isset( $params['text_design'] ) &&
			in_array( $params['text_design'], array( '6', '7' ), true )
		) ||
		(
			'text' === $params['style'] &&
			isset( $params['icon_design'], $params['wrap_type'] ) &&
			in_array( $params['icon_design'], array( '6', '7' ), true ) &&
			'icon_and_text' === $params['wrap_type']
		) ||
		(
			'text-only' !== $params['style'] &&
			isset( $params['icon_design'] ) &&
			'8' === $params['icon_design']
		)
	)
) {
	$show_tools_inner = true;
}

if ( $params['full_screen'] ) {
	?>
		<div class="wd-tools-element wd-header-fs-nav<?php echo esc_attr( $extra_class ); ?>">
			<?php woodmart_enqueue_inline_style( 'header-fullscreen-menu' ); ?>
			<a href="#" rel="nofollow noopener">
				<?php if ( $show_tools_inner ) : ?>
					<?php
					if ( '8' === $params['icon_design'] ) {
						woodmart_enqueue_inline_style( 'mod-tools-design-8' );
					}
					?>
					<span class="wd-tools-inner">
				<?php endif; ?>

					<?php if ( 'text-only' !== $params['style'] ) : ?>
						<span class="wd-tools-icon">
							<?php if ( 'custom' === $icon_type ) : ?>
								<?php echo whb_get_custom_icon( $params['custom_icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<?php endif; ?>
						</span>
					<?php endif; ?>

					<?php if ( 'icon' !== $params['style'] ) : ?>
						<span class="wd-tools-text"><?php esc_html_e( 'Menu', 'woodmart' ); ?></span>
					<?php endif; ?>

				<?php if ( $show_tools_inner ) : ?>
					</span>
				<?php endif; ?>
			</a>
		</div>
	<?php
	return;
}
?>
<nav class="wd-header-nav wd-header-main-nav <?php echo esc_attr( $classes ); ?>" role="navigation" aria-label="<?php esc_attr_e( 'Main navigation', 'woodmart' ); ?>">
	<?php
	$args = array(
		'container'  => '',
		'menu_class' => 'menu wd-nav wd-nav-header wd-nav-main' . $menu_classes,
		'walker'     => new Mega_Menu_Walker(),
	);

	if ( empty( $params['menu_id'] ) ) {
		$args['theme_location'] = $location;
	}

	if ( ! empty( $params['menu_id'] ) ) {
		$args['menu'] = $params['menu_id'];
	}

	if ( has_nav_menu( $location ) ) {
		if ( 'underline' === $params['menu_style'] ) {
			woodmart_enqueue_inline_style( 'mod-nav-style-underline' );
		} elseif ( in_array( $params['menu_style'], array( 'bordered', 'separated' ), true ) ) {
			woodmart_enqueue_inline_style( 'mod-nav-style-bordered-separated' );
		} elseif ( 'bg' === $params['menu_style'] ) {
			woodmart_enqueue_inline_style( 'mod-nav-style-bg' );
		}

		wp_nav_menu( $args );
	} else {
		$menu_link = get_admin_url( null, 'nav-menus.php' );
		?>
			<div class="create-nav-msg">
				<?php
					printf(
						wp_kses(
							/* translators: %s: URL to the admin navigation menus screen. */
							__( 'Create your first <a href="%s"><strong>navigation menu here</strong></a> and add it to the "Main menu" location.', 'woodmart' ),
							array(
								'a' => array(
									'href' => array(),
								),
							)
						),
						esc_url( $menu_link )
					);
				?>
			</div>
		<?php
	}
	?>
</nav>
