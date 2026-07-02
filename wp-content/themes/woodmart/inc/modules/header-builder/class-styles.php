<?php
/**
 * Header builder styles class file.
 *
 * @package woodmart
 */

namespace XTS\Modules\Header_Builder;

/**
 * Styles class that generates CSS for the header builder.
 */
class Styles {

	/**
	 * Header elements css.
	 *
	 * @var string
	 */
	private $elements_css;

	/**
	 * Get elements css.
	 *
	 * @return string
	 */
	public function get_elements_css() {
		return $this->elements_css;
	}

	/**
	 * Get all element css.
	 *
	 * @param array $el Element data.
	 * @param array $options Element options.
	 *
	 * @return string
	 */
	public function get_all_css( $el, $options ) {
		$this->set_elements_css( $el );

		return $this->get_header_css( $options ) . $this->get_elements_css();
	}

	/**
	 * Set header elements css.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $el Header structure.
	 */
	public function set_elements_css( $el = false ) {
		if ( ! $el ) {
			$el = woodmart_get_config( 'header-builder-structure' );
		}

		$selector = 'whb-' . $el['id'];

		if ( isset( $el['content'] ) && is_array( $el['content'] ) ) {
			foreach ( $el['content'] as $element ) {
				$this->set_elements_css( $element );
			}
		}

		$css        = '';
		$rules      = '';
		$border_css = '';

		if ( isset( $el['params']['background'] ) && ( 'categories' !== $el['type'] ) ) {
			$bg_value = $el['params']['background']['value'];
			$rules   .= $this->generate_background_css( $bg_value );

			$backdrop_css = $this->generate_backdrop_filter_css(
				array(
					'blur'       => isset( $bg_value['background-blur'] ) ? $bg_value['background-blur'] : '',
					'brightness' => isset( $bg_value['background-brightness'] ) ? $bg_value['background-brightness'] : '',
					'contrast'   => isset( $bg_value['background-contrast'] ) ? $bg_value['background-contrast'] : '',
					'grayscale'  => isset( $bg_value['background-grayscale'] ) ? $bg_value['background-grayscale'] : '',
					'hue-rotate' => isset( $bg_value['background-hue-rotate'] ) ? $bg_value['background-hue-rotate'] : '',
					'invert'     => isset( $bg_value['background-invert'] ) ? $bg_value['background-invert'] : '',
					'opacity'    => isset( $bg_value['background-opacity'] ) ? $bg_value['background-opacity'] : '',
					'saturate'   => isset( $bg_value['background-saturate'] ) ? $bg_value['background-saturate'] : '',
					'sepia'      => isset( $bg_value['background-sepia'] ) ? $bg_value['background-sepia'] : '',
				)
			);

			if ( $backdrop_css ) {
				$css .= '.' . $selector . ':before {backdrop-filter:' . $backdrop_css . '; -webkit-backdrop-filter:' . $backdrop_css . ';}';
			}
		}

		if ( isset( $el['params']['border'] ) && ( 'categories' !== $el['type'] ) ) {
			$sides      = isset( $el['params']['border']['value']['sides'] ) ? $el['params']['border']['value']['sides'] : array( 'bottom' );
			$border_css = $this->generate_border_css( $el['params']['border']['value'], $sides );
		}

		if ( isset( $el['params']['border'] ) && isset( $el['params']['border']['value']['applyFor'] ) && 'boxed' === $el['params']['border']['value']['applyFor'] ) {
			$css .= '.' . $selector . '-inner { ' . $border_css . ' }';
		} elseif ( $border_css ) {
			$rules .= $border_css;
		}

		if ( 'categories' === $el['type'] ) {
			if ( isset( $el['params']['border'] ) && $el['params']['border']['value'] ) {
				$sides = isset( $el['params']['border']['value']['sides'] ) ? $el['params']['border']['value']['sides'] : array( 'bottom' );
				$css  .= '.' . $selector . ' .menu-opener { ' . $this->generate_border_css( $el['params']['border']['value'], $sides ) . ' }';
			}

			if ( isset( $el['params']['more_cat_button'] ) && $el['params']['more_cat_button']['value'] ) {
				$count = $el['params']['more_cat_button_count']['value'] + 1;
				$css  .= '.' . $selector . '.wd-more-cat:not(.wd-show-cat) .item-level-0:nth-child(n+' . $count . '):not(:last-child) {
				    display: none;
				}.
				wd-more-cat .item-level-0:nth-child(n+' . $count . ') {
				    animation: wd-fadeIn .3s ease both;
				}';
			}
		}
		if ( $rules ) {
			$css .= "\n" . '.' . $selector . ' {' . "\n";
			$css .= "\t" . $rules . "\n";
			$css .= '}' . "\n";
		}

		$css_selectors = array();

		if ( isset( $el['params'] ) && $el['params'] ) {
			foreach ( $el['params'] as $params ) {
				if ( ( empty( $params['selectors'] ) && empty( $params['selector'] ) ) || ( isset( $params['generate_zero'] ) && '' === $params['value'] ) || ( ! isset( $params['generate_zero'] ) && empty( $params['value'] ) ) || ! $this->check_dependencies( $params['id'], $el ) ) {
					continue;
				}

				if ( ! empty( $params['selector'] ) && 'bg' === $params['type'] ) {
					$bg_css = $this->generate_background_css( $params['value'] );

					if ( $bg_css ) {
						$active_selector = str_replace( '{{WRAPPER}}', $selector, $params['selector'] );

						$css_selectors[ $active_selector ][] = $bg_css;
					}

					continue;
				}

				foreach ( $params['selectors'] as $selectors => $attributes ) {
					if ( 'group' === $params['type'] && ! empty( $params['fields'] ) ) {
						foreach ( $params['fields'] as $field ) {
							$field_value = isset( $params['value'][ $field['id'] ] ) ? $params['value'][ $field['id'] ] : '';

							if ( empty( $field['selectors'] ) || ( isset( $field['generate_zero'] ) && '' === $field_value ) || ( ! isset( $params['generate_zero'] ) && empty( $field_value ) ) ) {
								continue;
							}

							foreach ( $field['selectors'] as $field_selectors => $field_attributes ) {
								$active_selector = str_replace( '{{WRAPPER}}', $selector, $field_selectors );

								foreach ( $field_attributes as $field_attribute ) {
									$value = $field_value;

									if ( isset( $field_value['r'] ) && isset( $field_value['g'] ) && isset( $field_value['b'] ) && isset( $field_value['a'] ) ) {
										$value = 'rgba(' . $field_value['r'] . ', ' . $field_value['g'] . ', ' . $field_value['b'] . ', ' . $field_value['a'] . ')';
									}

									$css_selectors[ $active_selector ][] = "\t" . str_replace( '{{VALUE}}', $value, $field_attribute ) . "\n";
								}
							}
						}
					}

					$active_selector = str_replace( '{{WRAPPER}}', $selector, $selectors );

					if ( 'group' === $params['type'] ) {
						$group_values   = $params['value'];
						$default_values = array();

						foreach ( $params['fields'] as $field ) {
							$default_values[ $field['id'] ] = isset( $field['value'] ) ? $field['value'] : '';
						}

						$group_values = wp_parse_args( $group_values, $default_values );

						foreach ( $attributes as $attribute ) {
							$css_atts = $attribute;

							preg_match_all( '/{{([A-Z_]+)}}/', $attribute, $matches );

							$placeholders = $matches[1];

							$all_exists = true;

							foreach ( $placeholders as $ph ) {
								$lower_key = strtolower( $ph );
								if ( ! isset( $group_values[ $lower_key ] ) ) {
									$all_exists = false;
									break;
								}
							}

							if ( ! $all_exists ) {
								continue;
							}

							foreach ( $group_values as $key => $value ) {
								if ( is_array( $value ) && isset( $value['r'] ) && isset( $value['g'] ) && isset( $value['b'] ) && isset( $value['a'] ) ) {
									$value = 'rgba(' . $value['r'] . ', ' . $value['g'] . ', ' . $value['b'] . ', ' . $value['a'] . ')';
								}

								if ( isset( $params['fields'][ $key ]['allowed_css_values'] ) && ! in_array( $value, $params['fields'][ $key ]['allowed_css_values'], true ) ) {
									$value = '';
								}

								$css_atts = str_replace( '{{' . strtoupper( $key ) . '}}', $value, $css_atts );
							}

							$css_selectors[ $active_selector ][] = "\t" . $css_atts . "\n";
						}

						continue;
					}

					foreach ( $attributes as $attribute ) {
						$value = $params['value'];

						if ( 'backdrop' === $params['type'] ) {
							$backdrop_css = $this->generate_backdrop_filter_css( $value );

							if ( $backdrop_css ) {
								$css_selectors[ $active_selector ][] = "\t" . str_replace( '{{VALUE}}', $backdrop_css, $attribute ) . "\n";
							}

							continue;
						}

						if ( isset( $params['value']['r'] ) && isset( $params['value']['g'] ) && isset( $params['value']['b'] ) && isset( $params['value']['a'] ) ) {
							$value = 'rgba(' . $params['value']['r'] . ', ' . $params['value']['g'] . ', ' . $params['value']['b'] . ', ' . $params['value']['a'] . ')';
						}

						$css_selectors[ $active_selector ][] = "\t" . str_replace( '{{VALUE}}', $value, $attribute ) . "\n";
					}
				}
			}
		}

		if ( $css_selectors ) {
			foreach ( $css_selectors as $selector => $css_atts ) {
				if ( ! $css_atts ) {
					continue;
				}

				$css .= "\n." . $selector . " {\n";
				$css .= implode( '', $css_atts );
				$css .= '}';
			}
		}

		$this->elements_css .= $css;
	}

	/**
	 * Generate background CSS code.
	 *
	 * @since 1.0.0
	 *
	 * @param array $bg Background data.
	 *
	 * @return string
	 */
	public function generate_background_css( $bg ) {
		$css = '';

		if ( isset( $bg['background-color'] ) ) {
			extract( $bg['background-color'] ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract
		}

		if ( isset( $r ) && isset( $g ) && isset( $b ) && isset( $a ) ) {
			$css .= "\t" . 'background-color: rgba(' . $r . ', ' . $g . ', ' . $b . ', ' . $a . ');' . "\n";
		}

		if ( isset( $bg['background-image'] ) ) {
			extract( $bg['background-image'] ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract
		}

		if ( isset( $url ) ) {
			$css .= "\t" . 'background-image: url(' . $url . ');' . "\n";

			if ( isset( $bg['background-size'] ) ) {
				$css .= "\t" . 'background-size: ' . $bg['background-size'] . ';' . "\n";
			}

			if ( isset( $bg['background-attachment'] ) ) {
				$css .= "\t" . 'background-attachment: ' . $bg['background-attachment'] . ';' . "\n";
			}

			if ( isset( $bg['background-position'] ) ) {
				$css .= "\t" . 'background-position: ' . $bg['background-position'] . ';' . "\n";
			}

			if ( isset( $bg['background-repeat'] ) ) {
				$css .= "\t" . 'background-repeat: ' . $bg['background-repeat'] . ';' . "\n";
			}
		}

		return $css;
	}

	/**
	 * Generate backdrop filter CSS code.
	 *
	 * @since 1.0.0
	 *
	 * @param array $values Data.
	 *
	 * @return string
	 */
	public function generate_backdrop_filter_css( $values ) {
		$styles = '';

		if ( ! empty( $values['blur'] ) ) {
			$styles .= ' blur(' . $values['blur'] . 'px)';
		}
		if ( isset( $values['brightness'] ) && 1 !== (float) $values['brightness'] && ( $values['brightness'] || 0 === (float) $values['brightness'] ) ) {
			$styles .= ' brightness(' . $values['brightness'] . ')';
		}
		if ( ! empty( $values['contrast'] ) && 100 !== (int) $values['contrast'] ) {
			$styles .= ' contrast(' . $values['contrast'] . '%)';
		}
		if ( ! empty( $values['grayscale'] ) ) {
			$styles .= ' grayscale(' . $values['grayscale'] . '%)';
		}
		if ( ! empty( $values['hue-rotate'] ) ) {
			$styles .= ' hue-rotate(' . $values['hue-rotate'] . 'deg)';
		}
		if ( ! empty( $values['invert'] ) ) {
			$styles .= ' invert(' . $values['invert'] . '%)';
		}
		if ( ! empty( $values['opacity'] ) && 100 !== (int) $values['opacity'] ) {
			$styles .= ' opacity(' . $values['opacity'] . '%)';
		}
		if ( ! empty( $values['saturate'] ) && 100 !== (int) $values['saturate'] ) {
			$styles .= ' saturate(' . $values['saturate'] . '%)';
		}
		if ( ! empty( $values['sepia'] ) ) {
			$styles .= ' sepia(' . $values['sepia'] . '%)';
		}

		return $styles;
	}

	/**
	 * Generate border CSS code.
	 *
	 * @since 1.0.0
	 *
	 * @param array $border Border data.
	 * @param array $sides Sides data.
	 *
	 * @return string
	 */
	public function generate_border_css( $border, $sides ) {
		$css = '';

		if ( is_array( $border ) ) {
			extract( $border ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract
		}
		if ( isset( $color ) ) {
			extract( $color ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract	
		}

		if ( isset( $r ) && isset( $g ) && isset( $b ) && isset( $a ) && isset( $width ) ) {
			$css .= 'border-color: rgba(' . $r . ', ' . $g . ', ' . $b . ', ' . $a . ');';
		}

		foreach ( $sides as $side ) {
			if ( isset( $width ) ) {
				$css .= 'border-' . $side . '-width: ' . $width . 'px;';

				$css .= ( isset( $style ) ) ? 'border-' . $side . '-style: ' . $style . ';' : 'border-' . $side . '-style: solid;';
			}
		}

		return $css;
	}

	/**
	 * Get header elements css.
	 *
	 * @param array $options Header options.
	 *
	 * @return false|string
	 */
	public function get_header_css( $options ) {
		$sticky_clone = $options['sticky_clone'] && 'slide' === $options['sticky_effect'];

		ob_start();

		?>
:root{
	--wd-top-bar-h: <?php echo esc_attr( ! $options['top-bar']['hide_desktop'] && $options['top-bar']['height'] ? $options['top-bar']['height'] : '.00001' ); ?>px;
	--wd-top-bar-sm-h: <?php echo esc_attr( ! $options['top-bar']['hide_mobile'] && $options['top-bar']['mobile_height'] ? $options['top-bar']['mobile_height'] : '.00001' ); ?>px;
	--wd-top-bar-sticky-h: <?php echo esc_attr( ! $sticky_clone && $options['top-bar']['sticky'] && $options['top-bar']['sticky_height'] ? $options['top-bar']['sticky_height'] : '.00001' ); ?>px;
	--wd-top-bar-brd-w: <?php echo esc_attr( ! $options['top-bar']['hide_desktop'] && ! empty( $options['top-bar']['border']['width'] ) ? $options['top-bar']['border']['width'] : '.00001' ); ?>px;

	--wd-header-general-h: <?php echo esc_attr( ! $options['general-header']['hide_desktop'] && $options['general-header']['height'] ? $options['general-header']['height'] : '.00001' ); ?>px;
	--wd-header-general-sm-h: <?php echo esc_attr( ! $options['general-header']['hide_mobile'] && $options['general-header']['mobile_height'] ? $options['general-header']['mobile_height'] : '.00001' ); ?>px;
	--wd-header-general-sticky-h: <?php echo esc_attr( ! $sticky_clone && $options['general-header']['sticky'] && $options['general-header']['sticky_height'] ? $options['general-header']['sticky_height'] : '.00001' ); ?>px;
	--wd-header-general-brd-w: <?php echo esc_attr( ! $options['general-header']['hide_desktop'] && ! empty( $options['general-header']['border']['width'] ) ? $options['general-header']['border']['width'] : '.00001' ); ?>px;

	--wd-header-bottom-h: <?php echo esc_attr( ! $options['header-bottom']['hide_desktop'] && $options['header-bottom']['height'] ? $options['header-bottom']['height'] : '.00001' ); ?>px;
	--wd-header-bottom-sm-h: <?php echo esc_attr( ! $options['header-bottom']['hide_mobile'] && $options['header-bottom']['mobile_height'] ? $options['header-bottom']['mobile_height'] : '.00001' ); ?>px;
	--wd-header-bottom-sticky-h: <?php echo esc_attr( ! $sticky_clone && $options['header-bottom']['sticky'] && $options['header-bottom']['sticky_height'] ? $options['header-bottom']['sticky_height'] : '.00001' ); ?>px;
	--wd-header-bottom-brd-w: <?php echo esc_attr( ! $options['header-bottom']['hide_desktop'] && ! empty( $options['header-bottom']['border']['width'] ) ? $options['header-bottom']['border']['width'] : '.00001' ); ?>px;

	--wd-header-clone-h: <?php echo esc_attr( $sticky_clone && $options['sticky_height'] ? $options['sticky_height'] : '.00001' ); ?>px;

	--wd-header-brd-w: calc(var(--wd-top-bar-brd-w) + var(--wd-header-general-brd-w) + var(--wd-header-bottom-brd-w));
	--wd-header-h: calc(var(--wd-top-bar-h) + var(--wd-header-general-h) + var(--wd-header-bottom-h) + var(--wd-header-brd-w));
	--wd-header-sticky-h: calc(var(--wd-top-bar-sticky-h) + var(--wd-header-general-sticky-h) + var(--wd-header-bottom-sticky-h) + var(--wd-header-clone-h) + var(--wd-header-brd-w));
	--wd-header-sm-h: calc(var(--wd-top-bar-sm-h) + var(--wd-header-general-sm-h) + var(--wd-header-bottom-sm-h) + var(--wd-header-brd-w));
}
<?php /* phpcs:disable Generic.WhiteSpace.ScopeIndent.Incorrect */ ?>
<?php if ( ! $options['top-bar']['hide_desktop'] ) : ?>
<?php // DROPDOWN ALIGN BOTTOM IN TOP BAR. ?>
.whb-top-bar .wd-dropdown {
	margin-top: <?php echo esc_html( $options['top-bar']['height'] / 2 - 21 ); ?>px;
}

.whb-top-bar .wd-dropdown:after {
	height: <?php echo esc_html( $options['top-bar']['height'] / 2 - 10 ); ?>px;
}

<?php if ( ! $sticky_clone && $options['top-bar']['sticky'] ) : ?>
.whb-sticked .whb-top-bar .wd-dropdown:not(.sub-sub-menu) {
	margin-top: <?php echo esc_html( $options['top-bar']['sticky_height'] / 2 - 21 ); ?>px;
}

.whb-sticked .whb-top-bar .wd-dropdown:not(.sub-sub-menu):after {
	height: <?php echo esc_html( $options['top-bar']['sticky_height'] / 2 - 10 ); ?>px;
}
<?php endif; ?>
<?php endif; ?>

<?php if ( ! $options['general-header']['hide_desktop'] && ! $sticky_clone && $options['general-header']['sticky'] ) : ?>
.whb-sticked .whb-general-header .wd-dropdown:not(.sub-sub-menu) {
	margin-top: <?php echo esc_html( $options['general-header']['sticky_height'] / 2 - 21 ); ?>px;
}

.whb-sticked .whb-general-header .wd-dropdown:not(.sub-sub-menu):after {
	height: <?php echo esc_html( $options['general-header']['sticky_height'] / 2 - 10 ); ?>px;
}
<?php endif; ?>

<?php if ( ! empty( $options['top-bar']['border']['width'] ) ) : ?>
:root:has(.whb-top-bar.whb-border-boxed) {
	--wd-top-bar-brd-w: .00001px;
}

@media (max-width: 1024px) {
:root:has(.whb-top-bar.whb-hidden-mobile) {
	--wd-top-bar-brd-w: .00001px;
}
}
<?php endif; ?>

<?php if ( ! empty( $options['general-header']['border']['width'] ) ) : ?>
:root:has(.whb-general-header.whb-border-boxed) {
	--wd-header-general-brd-w: .00001px;
}

@media (max-width: 1024px) {
:root:has(.whb-general-header.whb-hidden-mobile) {
	--wd-header-general-brd-w: .00001px;
}
}
<?php endif; ?>

<?php if ( ! empty( $options['header-bottom']['border']['width'] ) ) : ?>
:root:has(.whb-header-bottom.whb-border-boxed) {
	--wd-header-bottom-brd-w: .00001px;
}

@media (max-width: 1024px) {
:root:has(.whb-header-bottom.whb-hidden-mobile) {
	--wd-header-bottom-brd-w: .00001px;
}
}
<?php endif; ?>

<?php if ( ! $options['header-bottom']['hide_desktop'] ) : ?>
<?php // DROPDOWN ALIGN BOTTOM IN HEADER BOTTOM. ?>
.whb-header-bottom .wd-dropdown {
	margin-top: <?php echo esc_html( $options['header-bottom']['height'] / 2 - 21 ); ?>px;
}

.whb-header-bottom .wd-dropdown:after {
	height: <?php echo esc_html( $options['header-bottom']['height'] / 2 - 10 ); ?>px;
}

<?php if ( ! $sticky_clone && $options['header-bottom']['sticky'] ) : ?>
.whb-sticked .whb-header-bottom .wd-dropdown:not(.sub-sub-menu) {
	margin-top: <?php echo esc_html( $options['header-bottom']['sticky_height'] / 2 - 21 ); ?>px;
}

.whb-sticked .whb-header-bottom .wd-dropdown:not(.sub-sub-menu):after {
	height: <?php echo esc_html( $options['header-bottom']['sticky_height'] / 2 - 10 ); ?>px;
}
<?php endif; ?>
<?php endif; ?>

<?php if ( $sticky_clone ) : ?>
<?php // DROPDOWN ALIGN BOTTOM IN HEADER CLONE. ?>
.whb-clone.whb-sticked .wd-dropdown:not(.sub-sub-menu) {
	margin-top: <?php echo esc_html( $options['sticky_height'] / 2 - 21 ); ?>px;
}

.whb-clone.whb-sticked .wd-dropdown:not(.sub-sub-menu):after {
	height: <?php echo esc_html( $options['sticky_height'] / 2 - 10 ); ?>px;
}
<?php endif; ?>
<?php /* phpcs:enable Generic.WhiteSpace.ScopeIndent.Incorrect */ ?>
		<?php

		return ob_get_clean();
	}

	/**
	 * Check whether dependencies for a specific option in the specified element are fulfilled.
	 *
	 * @param  string $option_id - The id of the dependency option to check.
	 * @param  array  $el - List of settings for this item.
	 * @return bool - return true if all dependencies for this option have been met, or the no dependencies option.
	 */
	private function check_dependencies( $option_id, $el ) {
		$params = $el['params'];

		if ( isset( $params[ $option_id ]['conditions'] ) ) {
			return $this->evaluate_condition(
				$params[ $option_id ]['conditions'],
				$params
			);
		}

		if ( ! isset( $params[ $option_id ]['condition'] ) ) {
			return true;
		}

		$condition = $params[ $option_id ]['condition'];
		$terms     = array();

		foreach ( $condition as $field => $rule ) {
			$terms[] = array(
				'field'      => $field,
				'comparison' => $rule['comparison'],
				'value'      => $rule['value'],
			);
		}

		return $this->evaluate_condition(
			array(
				'relation' => 'and',
				'terms'    => $terms,
			),
			$params
		);
	}

	/**
	 * Evaluate condition.
	 *
	 * @param array $condition Condition.
	 * @param array $params Params.
	 * @return bool
	 */
	private function evaluate_condition( $condition, $params ) {
		if ( isset( $condition['field'] ) ) {
			return $this->check_simple_condition( $condition['field'], $condition, $params );
		}

		if ( isset( $condition['relation'] ) && isset( $condition['terms'] ) ) {
			$relation = strtolower( $condition['relation'] );
			$terms    = $condition['terms'];

			if ( 'and' === $relation ) {
				foreach ( $terms as $term ) {
					if ( ! $this->evaluate_condition( $term, $params ) ) {
						return false;
					}
				}
				return true;
			}

			if ( 'or' === $relation ) {
				foreach ( $terms as $term ) {
					if ( $this->evaluate_condition( $term, $params ) ) {
						return true;
					}
				}
				return false;
			}
		}

		return false;
	}

	/**
	 * Check simple condition.
	 *
	 * @param string $field Field.
	 * @param array  $rule Rule.
	 * @param array  $params Params.
	 * @return bool
	 */
	private function check_simple_condition( $field, $rule, $params ) {
		$actual = isset( $params[ $field ]['value'] ) ? $params[ $field ]['value'] : ( $params[ $field ] ?? null );

		if ( null === $actual ) {
			return false;
		}

		$comparison = $rule['comparison'];
		$value      = $rule['value'];

		if ( 'equal' === $comparison ) {
			if ( is_array( $value ) ) {
				return in_array( $actual, $value, true );
			}
			return $actual === $value;
		}

		if ( 'not_equal' === $comparison ) {
			if ( is_array( $value ) ) {
				return ! in_array( $actual, $value, true );
			}
			return $actual !== $value;
		}

		return false;
	}
}
