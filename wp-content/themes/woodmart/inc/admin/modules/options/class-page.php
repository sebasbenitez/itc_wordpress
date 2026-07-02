<?php
/**
 * Object that handles theme options page.
 *
 * @package woodmart
 */

namespace XTS\Admin\Modules\Options;

use XTS\Admin\Modules\Options;
use XTS\Admin\Modules\Options\Presets;
use XTS\Admin\Modules\Options\Google_Fonts\Local_Data;
use XTS\Singleton;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Create page and display the form with all sections and fields.
 */
class Page extends Singleton {

	/**
	 * Options set prefix.
	 *
	 * @var array
	 */
	public $opt_name = 'woodmart';

	/**
	 * Options array loaded from the database.
	 *
	 * @var array
	 */
	private $options;

	/**
	 * Array of all the available sections.
	 *
	 * @var array
	 */
	private $sections;

	/**
	 * Array of all the available Field objects.
	 *
	 * @var array
	 */
	private $fields;

	/**
	 * Array of all the available Presets.
	 *
	 * @var array
	 */
	private $presets;

	/**
	 * Register hooks and load base data.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		add_action( 'init', array( $this, 'load_fields' ), 100 );
		$this->presets = Presets::get_all();
	}

	/**
	 * Load all field objects and add them to the sections set.
	 *
	 * @since 1.0.0
	 */
	public function load_fields() {
		$this->sections = Options::get_sections();
		$this->fields   = Options::get_fields();

		foreach ( $this->fields as $field ) {
			$this->sections[ $field->args['section'] ]['fields'][] = $field;
		}

		$this->options = Options::get_options();
	}

	/**
	 * Render the options page content.
	 *
	 * @since 1.0.0
	 */
	public function page_content() {
		wp_enqueue_script( 'woodmart-admin-options', WOODMART_ASSETS . '/js/options.js', array(), WOODMART_VERSION, true );
		wp_enqueue_script( 'woodmart-admin-save-options', WOODMART_ASSETS . '/js/saveThemeSettings.js', array(), WOODMART_VERSION, true );
		wp_enqueue_script( 'woodmart-theme', WOODMART_SCRIPTS . '/scripts/global/helpers.min.js', array(), WOODMART_VERSION, true );
		wp_enqueue_script( 'xts-tabs', WOODMART_SCRIPTS . '/scripts/elements/tabs.js', array(), WOODMART_VERSION, true );

		$wrapper_classes = '';

		if ( isset( $_GET['preset'] ) ) { // phpcs:ignore
			$wrapper_classes .= ' xts-preset-active';
		}

		$current_preset_id = Presets::get_current_preset();
		$all_presets       = Presets::get_all();

		if ( $current_preset_id ) {
			$title = $all_presets[ $current_preset_id ]['name'];
		} else {
			$title = esc_html__( 'Theme settings', 'woodmart' );
		}

		$wrapper_classes .= ' xts-builder-' . woodmart_get_current_page_builder();

		do_action( 'xts_before_theme_settings' );

		if ( isset( $_GET['settings-updated'] ) ) { // phpcs:ignore
			do_action( 'xts_theme_settings_save' );
		}

		$failed_loading_fonts = woodmart_get_opt( 'local_google_fonts' ) ? Local_Data::get_instance()->get_failed_fonts() : array();
		?>
		<script>
			var woodmart_settings = {
				product_gallery    : {
					thumbs_slider: {
						position: true
					}
				},
				lazy_loading_offset: 0
			};
		</script>
		<div class="xts-box xts-options xts-theme-style<?php echo esc_attr( $wrapper_classes ); ?>">
			<div class="xts-box-header">
				<div class="xts-row">
					<div class="xts-col">
						<h3>
							<?php echo esc_html( $title ); ?>
						</h3>
						<div class="xts-options-search xts-search xts-i-search">
							<input aria-label="<?php esc_attr_e( 'Search', 'woodmart' ); ?>" type="text" placeholder="<?php esc_attr_e( 'Start typing to find options...', 'woodmart' ); ?>">
						</div>
					</div>
					<div class="xts-col-auto">
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=xts_theme_settings_presets' ) ); ?>" class="xts-bordered-btn xts-color-primary xts-i-cog">
							<?php esc_html_e( 'Settings presets', 'woodmart' ); ?>
						</a>
					</div>
				</div>
			</div>

			<div class="xts-box-content">
				<form action="options.php" method="post">
					<div class="xts-row xts-sp-30">
						<div class="xts-col-12 xts-col-lg-3 xts-col-xl-2">
							<ul class="xts-nav xts-nav-vertical">
								<?php $this->display_sections_tree(); ?>
							</ul>
						</div>
						<div class="xts-col">
							<div class="xts-sections">
								<div class="xts-notices-wrapper">
									<div class="xts-notices">
										<?php $this->display_message(); ?>
										<?php if ( ! empty( $failed_loading_fonts ) ) : ?>
											<div class="xts-notice xts-warning">
												<?php
													printf(
														// translators: %s - list of failed loading fonts.
														esc_html__( 'Some fonts failed to load: %s', 'woodmart' ),
														esc_html( implode( ', ', $failed_loading_fonts ) )
													);
												?>
											</div>
										<?php endif; ?>
									</div>
								</div>
								<?php $this->display_sections(); ?>
								<div class="xts-options-actions">
									<input type="hidden" class="xts-last-tab-input" name="xts-<?php echo esc_attr( $this->opt_name ); ?>-options[last_tab]" value="<?php echo esc_attr( $this->get_last_tab() ); ?>" />
									<button class="xts-save-options-btn xts-i-save xts-btn xts-color-primary"><?php esc_html_e( 'Save options', 'woodmart' ); ?></button>

									<?php if ( isset( $_GET['preset'] ) ) : // phpcs:ignore ?>
										<a href="<?php echo esc_url( admin_url( 'admin.php?page=xts_theme_settings' ) ); ?>" class="xts-btn xts-color-warning xts-i-global">
											<?php esc_html_e( 'To global settings', 'woodmart' ); ?>
										</a>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>
					<input type="hidden" name="page_options" value="xts-<?php echo esc_attr( $this->opt_name ); ?>-options" />
					<input type="hidden" name="action" value="update" />
					<?php if ( Presets::get_current_preset() ) : ?>
						<input type="hidden" class="xts-fields-to-save" name="xts-<?php echo esc_attr( $this->opt_name ); ?>-options[fields_to_save]" value="<?php echo esc_attr( $this->get_fields_to_save() ); ?>" />
						<input type="hidden" name="xts-<?php echo esc_attr( $this->opt_name ); ?>-options[preset]" value="<?php echo esc_attr( Presets::get_current_preset() ); ?>" />
					<?php endif; ?>
					<?php settings_fields( 'xts-options-group' ); ?>
				</form>
			</div>
		</div>

		<?php do_action( 'xts_after_theme_settings' ); ?>
		<?php
	}

	/**
	 * Get last visited tab by visitor.
	 *
	 * @since 1.0.0
	 */
	private function get_last_tab() {

		reset( $this->sections );

		$first_tab = key( $this->sections );

		$current_tab = $first_tab;

		if ( isset( $this->options['last_tab'] ) && isset( $_GET['settings-updated'] ) ) { // phpcs:ignore WordPress.Security
			$current_tab = $this->options['last_tab'];
		} elseif ( isset( $_GET['tab'] ) ) { // phpcs:ignore WordPress.Security
			$current_tab = $_GET['tab']; // phpcs:ignore WordPress.Security
		}

		if ( ! isset( $this->sections[ $current_tab ]['fields'] ) && isset( $this->sections[ $current_tab ]['id'] ) ) {
			$parent_id = $this->sections[ $current_tab ]['id'];

			foreach ( $this->sections as $section ) {
				if ( ! empty( $section['parent'] ) && $section['parent'] === $parent_id ) {
					$current_tab = $section['id'];
					break;
				}
			}
		}

		return $current_tab;
	}

	/**
	 * Display saved/imported message.
	 *
	 * @since 1.0.0
	 */
	private function display_message() {
		$message = $this->get_last_message();

		$text = false;

		if ( 'save' === $message ) {
			$text = esc_html__( 'Settings have been saved successfully.', 'woodmart' );
		} elseif ( 'import' === $message ) {
			$text = esc_html__( 'New options have been successfully imported.', 'woodmart' );
		} elseif ( 'reset' === $message ) {
			$text = esc_html__( 'All options have been set to default values.', 'woodmart' );
		}

		if ( $text ) {
			echo '<div class="xts-notice xts-success">' . $text . '</div>'; // phpcs:ignore
		}
	}

	/**
	 * Get last message.
	 *
	 * @since 1.0.0
	 */
	private function get_last_message() {

		return ( isset( $this->options['last_message'] ) && isset( $_GET['settings-updated'] ) ) ? $this->options['last_message'] : ''; // phpcs:ignore
	}

	/**
	 * Display sections navigation tree.
	 *
	 * @since 1.0.0
	 */
	private function display_sections_tree() {
		$current_tab   = $this->get_last_tab();
		$active_parent = '';

		if ( isset( $this->sections[ $current_tab ]['parent'] ) ) {
			$active_parent = $this->sections[ $current_tab ]['parent'];
		}

		foreach ( $this->sections as $key => $section ) {
			if ( isset( $section['parent'] ) ) {
				continue;
			}

			$subsections = array_filter(
				$this->sections,
				function ( $el ) use ( $section ) {
					return isset( $el['parent'] ) && $el['parent'] === $section['id'];
				}
			);

			$classes = '';

			if ( $key === $current_tab || $key === $active_parent ) {
				$classes .= ' xts-active-nav';
			}
			if ( is_array( $subsections ) && count( $subsections ) > 0 ) {
				$classes .= ' xts-has-child';
			}

			if ( ! empty( $section['class'] ) ) {
				$classes .= ' ' . $section['class'];
			}
			?>
				<li class="<?php echo esc_attr( $classes ); ?>">
					<a class="<?php echo esc_html( $section['icon'] ); ?>" href="javascript:void(0);" data-id="<?php echo esc_attr( $key ); ?>" data-id="<?php echo esc_attr( $key ); ?>">
						<span>
							<?php echo $section['name']; // phpcs:ignore ?>
						</span>
					</a>

					<?php if ( is_array( $subsections ) && count( $subsections ) > 0 ) : ?>
						<ul class="xts-sub-menu">
							<?php foreach ( $subsections as $key => $subsection ) : ?>
								<?php
								$subsections_classes = 'xts-sub-menu-item';

								if ( $key === $current_tab ) {
									$subsections_classes .= ' xts-active-nav';
								}

								if ( ! empty( $subsection['class'] ) ) {
									$subsections_classes .= ' ' . $subsection['class'];
								}
								?>

								<li class="<?php echo esc_attr( $subsections_classes ); ?>">
									<a href="javascript:void(0);" data-id="<?php echo esc_attr( $key ); ?>">
										<span>
											<?php echo $subsection['name']; // phpcs:ignore ?>
										</span>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>

				</li>
			<?php
		}
	}

	/**
	 * Loop through all the sections and render all the fields.
	 *
	 * @since 1.0.0
	 */
	private function display_sections() {
		foreach ( $this->sections as $key => $section ) {
			?>
			<div class="xts-section <?php echo ( $this->get_last_tab() !== $key ) ? 'xts-hidden' : 'xts-active-section'; ?>" data-id="<?php echo esc_attr( $key ); ?>">
				<div class="xts-section-title">
					<?php if ( ! empty( $section['icon'] ) ) : ?>
						<div class="xts-title-icon <?php echo esc_html( $section['icon'] ); ?>"></div>
					<?php endif; ?>
					<h3><?php echo esc_html( $section['name'] ); ?></h3>
				</div>
				<div class="xts-fields">
					<?php

					$tabs = array();
					if ( isset( $section['fields'] ) ) {
						foreach ( $section['fields'] as $field ) {
							if ( isset( $field->args['t_tab'] ) ) {
								if ( ! isset( $tabs[ $field->args['t_tab']['id'] ][ $field->args['t_tab']['tab'] ] ) ) {
									$tabs[ $field->args['t_tab']['id'] ][ $field->args['t_tab']['tab'] ] = array();
								}

								if ( isset( $field->args['t_tab']['icon'] ) ) {
									$tabs[ $field->args['t_tab']['id'] ][ $field->args['t_tab']['tab'] ]['icon'] = $field->args['t_tab']['icon'];
								}

								if ( isset( $field->args['t_tab']['tab'] ) ) {
									$tabs[ $field->args['t_tab']['id'] ][ $field->args['t_tab']['tab'] ]['title'] = $field->args['t_tab']['tab'];
								}

								if ( ! empty( $field->args['t_tab']['extra_class'] ) ) {
									$tabs[ $field->args['t_tab']['id'] ][ $field->args['t_tab']['tab'] ]['extra_class'] = $field->args['t_tab']['extra_class'];
								}
							}
						}
					}

					$printed_tabs  = false;
					$printed_tab   = false;
					$printed_group = false;

					if ( isset( $section['fields'] ) ) {
						foreach ( $section['fields'] as $field ) {
							if ( $printed_tab && ( ! isset( $field->args['t_tab'] ) || $printed_tab !== $field->args['t_tab']['tab'] ) ) {
								echo '</div>';
								$printed_tab = false;
							}

							if ( $printed_tabs && ( ! isset( $field->args['t_tab'] ) || $printed_tabs !== $field->args['t_tab']['id'] ) ) {
								echo '</div>';
								echo '</div>';
								$printed_tabs = false;
							}

							if ( $printed_group && ( ! isset( $field->args['group'] ) || $printed_group !== $field->args['group'] ) ) {
								echo '</div>';
								$printed_group = false;
							}

							if ( isset( $field->args['group'] ) && $printed_group !== $field->args['group'] ) {
								$printed_group = $field->args['group'];
								echo '<div class="xts-group-title"><span>' . esc_html( $printed_group ) . '</span></div>';
								echo '<div class="xts-fields-group xts-group">';
							}

							if ( isset( $field->args['t_tab'] ) && $printed_tabs !== $field->args['t_tab']['id'] ) {
								$attrs        = '';
								$tabs_classes = 'xts-field wd-tabs xts-tabs wd-style-' . $field->args['t_tab']['style'];

								if ( isset( $field->args['t_tab']['class'] ) ) {
									$tabs_classes .= ' ' . $field->args['t_tab']['class'];
								}

								if ( isset( $field->args['t_tab']['requires'] ) ) {
									$tabs_classes .= ' ' . $field->dependency_class( $field->args['t_tab']['requires'] );

									$data = '';
									foreach ( $field->args['t_tab']['requires'] as $dependency ) {
										if ( is_array( $dependency['value'] ) ) {
											$dependency['value'] = implode( ',', $dependency['value'] );
										}
										$data .= $dependency['key'] . ':' . $dependency['compare'] . ':' . $dependency['value'] . ';';
									}

									$attrs .= 'data-dependency="' . esc_attr( $data ) . '"';
								}

								echo '<div class="' . $tabs_classes . '" ' . $attrs . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

								echo '<div class="xts-tabs-header wd-tabs-header">';
								if ( isset( $field->args['t_tab']['title'] ) ) {
									echo '<h3>' . $field->args['t_tab']['title'] . '</h3>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								}
								echo '<div class="wd-nav-wrapper wd-nav-tabs-wrapper xts-nav-wrapper wd-nav-wrapper xts-nav-tabs-wrapper">';
								echo '<ul class="wd-nav wd-nav-tabs xts-nav xts-nav-tabs">';
								foreach ( $tabs[ $field->args['t_tab']['id'] ] as $tab ) {
									$classes       = '';
									$extra_classes = '';

									if ( ! empty( $tab['icon'] ) ) {
										$classes .= ' ' . $tab['icon'];
									}
									if ( ! empty( $tab['extra_class'] ) ) {
										$extra_classes .= ' ' . $tab['extra_class'];
									}

									echo '<li class="' . $extra_classes . '"><a href="#" class="wd-nav-link xts-nav-link' . $classes . '" title="' . $tab['title'] . '"><span class="nav-link-text wd-tabs-title xts-tabs-title">' . $tab['title'] . '</span></a></li>'; // phpcs:ignore
								}
								echo '</ul>';
								echo '</div>';
								echo '</div>';

								echo '<div class="wd-tabs-content-wrapper xts-tabs-content-wrapper xts-group">';

								$printed_tabs = $field->args['t_tab']['id'];
							}

							if ( isset( $field->args['t_tab'] ) && $printed_tab !== $field->args['t_tab']['tab'] ) {
								echo '<div class="wd-tab-content xts-tab-content">';

								$printed_tab = $field->args['t_tab']['tab'];
							}

							if ( $this->is_inherit_field( $field->get_id() ) ) {
								$field->inherit_value( true );
							}

							$field->render( null, Presets::get_current_preset() );
						}

						if ( $printed_tab ) {
							echo '</div>';
							$printed_tab = false;
						}

						if ( $printed_tabs ) {
							echo '</div>';
							echo '</div>';
							$printed_tabs = false;
						}

						if ( $printed_group ) {
							echo '</div>';
							$printed_group = false;
						}
					}
					?>
				</div>
			</div>
			<?php
		}
	}

	/**
	 * Get fields to save value.
	 *
	 * @since 1.0.0
	 */
	private function get_fields_to_save() {
		if ( ! isset( $this->options[ Presets::get_current_preset() ] ) || ! isset( $this->options[ Presets::get_current_preset() ]['fields_to_save'] ) ) {
			return '';
		}

		return $this->options[ Presets::get_current_preset() ]['fields_to_save'];
	}

	/**
	 * Is field by id inherits value.
	 *
	 * @since 1.0.0
	 *
	 * @param int $id Field's id.
	 *
	 * @return bool
	 */
	public function is_inherit_field( $id ) {
		return false === strpos( $this->get_fields_to_save(), $id );
	}
}

Page::get_instance();
