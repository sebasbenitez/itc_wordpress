<?php
/**
 * Sanitize fields values before save
 *
 * @package woodmart
 */

namespace XTS\Admin\Modules\Options;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Sanitization class for fields
 */
class Sanitize {
	/**
	 * Field class
	 *
	 * @var Field
	 */
	private $field;

	/**
	 * Initial field value
	 *
	 * @var Field
	 */
	private $value;

	/**
	 * Class contructor
	 *
	 * @since 1.0.0
	 *
	 * @param object $field Field object.
	 * @param string $value field value.
	 */
	public function __construct( $field, $value ) {
		$this->field = $field;
		$this->value = $value;
	}

	/**
	 * Run field value sanitization.
	 *
	 * @since 1.0.0
	 *
	 * @return sanitized value
	 */
	public function sanitize() {

		$val = $this->value;

		switch ( $this->field->args['type'] ) {
			case 'typography':
				if ( is_array( $val ) ) {
					$first = reset( $val );

					if ( ! is_array( $first ) ) {
						$val = array( $val );
					}
				}
				break;

			case 'switcher':
				if ( 'yes' === $val ) {
					$val = '1';
				}
				break;

			case 'background':
				if ( is_array( $val ) ) {
					if ( isset( $val['background-color'] ) && ! isset( $val['color'] ) ) {
						$val['color'] = $val['background-color'];
						unset( $val['background-color'] );
					}

					if ( isset( $val['background-repeat'] ) && ! isset( $val['repeat'] ) ) {
						$val['repeat'] = $val['background-repeat'];
						unset( $val['background-repeat'] );
					}

					if ( isset( $val['background-size'] ) && ! isset( $val['size'] ) ) {
						$val['size'] = $val['background-size'];
						unset( $val['background-size'] );
					}

					if ( isset( $val['background-attachment'] ) && ! isset( $val['attachment'] ) ) {
						$val['attachment'] = $val['background-attachment'];
						unset( $val['background-attachment'] );
					}

					if ( isset( $val['background-position'] ) && ! isset( $val['position'] ) ) {
						$val['position'] = $val['background-position'];
						unset( $val['background-position'] );
					}

					if ( isset( $val['background-image'] ) && ! isset( $val['url'] ) ) {
						$val['url'] = $val['background-image'];
						unset( $val['background-image'] );
					}
				}
				break;

			case 'custom_fonts':
			case 'upload_icons':
				// TODO: sanitize complex array.
				break;

			case 'textarea':
				$val = wp_kses_post( $val );

				break;

			case 'editor':
				break;

			case 'color':
				if ( ! is_array( $val ) && 7 === strlen( $val ) && ( ! isset( $this->field->args['data_type'] ) || 'hex' !== $this->field->args['data_type'] ) ) {
					$val = array( 'idle' => $val );
				}
				break;

			case 'select_with_table':
				if ( isset( $val['{{index}}'] ) ) {
					unset( $val['{{index}}'] );
				}

				if ( $val ) {
					foreach ( $val as $id => $data ) {
						if ( empty( $data['id'] ) ) {
							unset( $val[ $id ] );
						}
					}
				}

				break;

			case 'text_input':
				if ( ! empty( $this->field->args['sanitize'] ) && 'slug' === $this->field->args['sanitize'] ) {
					$val = strtolower( trim( preg_replace( '/[^A-Za-z0-9-]+/', '_', $val ) ) );
				} elseif ( ! empty( $this->field->args['sanitize'] ) && 'social_links' === $this->field->args['sanitize'] ) {
					$val = '#' === $val ? $val : $this->sanitize_social_link( $val );
				} elseif ( ! empty( $this->field->args['attributes']['type'] ) && 'url' === $this->field->args['attributes']['type'] ) {
					$val = esc_url( $val );
				} else {
					$val = sanitize_text_field( $val );
				}

				break;
			case 'discount_rules':
			case 'conditions':
				array_walk_recursive( $val, 'sanitize_text_field' );

				break;
			case 'timetable':
				foreach ( $val as $key => $dates ) {
					foreach ( $dates as $meta_key => $meta_value ) {
						switch ( $meta_key ) {
							case 'date_type':
							case 'iteration':
								$val[ $key ][ $meta_key ] = sanitize_text_field( $meta_value );
								break;
							case 'single_day':
							case 'first_day':
							case 'last_day':
								$pattern = '/^\d{4}-\d{2}-\d{2}$/';

								$val[ $key ][ $meta_key ] = preg_match( $pattern, $meta_value ) ? sanitize_text_field( $meta_value ) : '';
								break;
							default:
								$val[ $key ][ $meta_key ] = '';
								break;
						}
					}
				}

				break;
			default:
				$val = is_array( $val ) ? array_map( 'sanitize_text_field', $val ) : sanitize_text_field( $val );
				break;
		}

		return $val;
	}

	/**
	 * Sanitize social media links allowing custom protocols.
	 *
	 * @since 1.0.0
	 *
	 * @param string $url URL to sanitize.
	 * @return string Sanitized URL.
	 */
	private function sanitize_social_link( $url ) {
		// List of allowed protocols for social links.
		$allowed_protocols = array(
			'http',
			'https',
			'mailto',
			'tel',
			'fb',
			'twitter',
			'bluesky',
			'instagram',
			'threads',
			'pinterest',
			'youtube',
			'tumblr',
			'linkedin',
			'vimeo',
			'flickr',
			'github',
			'dribbble',
			'behance',
			'soundcloud',
			'spotify',
			'whatsapp',
			'snapchat',
			'tg',
			'viber',
			'tiktok',
			'discord',
			'yelp',
			'vk',
			'ok',
		);

		$url_parts = wp_parse_url( $url );

		// If URL has a scheme, check if it's allowed.
		if ( isset( $url_parts['scheme'] ) && in_array( strtolower( $url_parts['scheme'] ), $allowed_protocols, true ) ) {
			return esc_attr( $url );
		}

		// Default to esc_url for standard HTTP(S) links.
		return esc_url( $url );
	}
}
