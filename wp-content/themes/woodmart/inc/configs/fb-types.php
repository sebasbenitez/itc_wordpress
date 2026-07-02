<?php if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );}

/**
 * -------------------------------------------------------------------------------
 * Floating block types
 * -----------------------------------------------------------------------------
 */

return array(
	'popup'          => array(
		'post_type'   => 'wd_popup',
		'label'       => esc_html__( 'Popup', 'woodmart' ),
		'ajax_action' => 'wd_popup_create',
		'prefix'      => 'wd_popup_',
		'options'     => array(
			'version',
			'close_by_selector',
			'animation',
			'close_btn',
			'close_btn_display',
			'hide_popup',
			'hide_popup_tablet',
			'hide_popup_mobile',
			'enable_page_scrolling',
			'close_by_overlay',
			'close_by_esc',
			'persistent_close',
		),
		'create_form' => array(
			'label_text'       => esc_html__( 'Predefined popups', 'woodmart' ),
			'name_label'       => esc_html__( 'Popup name', 'woodmart' ),
			'name_placeholder' => esc_html__( 'Enter popup name', 'woodmart' ),
			'default_name'     => esc_html__( 'Popup', 'woodmart' ),
			'submit_text'      => esc_html__( 'Create popup', 'woodmart' ),
			'templates_label'  => esc_html__( 'Popup templates', 'woodmart' ),
			'preview_alt'      => esc_html__( 'Popup preview', 'woodmart' ),
		),
		'templates'   => array(
			'promotion'    => array(
				'title'   => esc_html__( 'Promotion', 'woodmart' ),
				'layouts' => array(
					'layout-1' => array(
						'title' => esc_html__( 'Simple', 'woodmart' ),
					),
					'layout-2' => array(
						'title' => esc_html__( 'Simple2', 'woodmart' ),
					),
					'layout-3' => array(
						'title' => esc_html__( 'Simple3', 'woodmart' ),
					),
					'layout-4' => array(
						'title' => esc_html__( 'Simple4', 'woodmart' ),
					),
					'layout-5' => array(
						'title' => esc_html__( 'Simple5', 'woodmart' ),
					),
					'layout-6' => array(
						'title' => esc_html__( 'Simple6', 'woodmart' ),
					),
					'layout-7' => array(
						'title' => esc_html__( 'Simple7', 'woodmart' ),
					),
					'layout-8' => array(
						'title' => esc_html__( 'Simple8', 'woodmart' ),
					),
					'layout-9' => array(
						'title' => esc_html__( 'Simple9', 'woodmart' ),
					),
					'layout-10' => array(
						'title' => esc_html__( 'Simple10', 'woodmart' ),
					),
					'layout-11' => array(
						'title' => esc_html__( 'Simple11', 'woodmart' ),
					),
					'layout-12' => array(
						'title' => esc_html__( 'Simple12', 'woodmart' ),
					),
				),
			),
			'newsletter'   => array(
				'title'   => esc_html__( 'Newsletter', 'woodmart' ),
				'layouts' => array(
					'layout-1' => array(
						'title' => esc_html__( 'Simple', 'woodmart' ),
					),
					'layout-2' => array(
						'title' => esc_html__( 'Simple2', 'woodmart' ),
					),
					'layout-3' => array(
						'title' => esc_html__( 'Simple3', 'woodmart' ),
					),
					'layout-4' => array(
						'title' => esc_html__( 'Simple4', 'woodmart' ),
					),
					'layout-5' => array(
						'title' => esc_html__( 'Simple5', 'woodmart' ),
					),
					'layout-6' => array(
						'title' => esc_html__( 'Simple6', 'woodmart' ),
					),
					'layout-7' => array(
						'title' => esc_html__( 'Simple7', 'woodmart' ),
					),
					'layout-8' => array(
						'title' => esc_html__( 'Simple8', 'woodmart' ),
					),
					'layout-9' => array(
						'title' => esc_html__( 'Simple9', 'woodmart' ),
					),
					'layout-10' => array(
						'title' => esc_html__( 'Simple10', 'woodmart' ),
					),
					'layout-11' => array(
						'title' => esc_html__( 'Simple11', 'woodmart' ),
					),
					'layout-12' => array(
						'title' => esc_html__( 'Simple12', 'woodmart' ),
					),
				),
			),
			'contact_form' => array(
				'title'   => esc_html__( 'Contact Form', 'woodmart' ),
				'layouts' => array(
					'layout-1' => array(
						'title' => esc_html__( 'Simple', 'woodmart' ),
					),
					'layout-2' => array(
						'title' => esc_html__( 'Simple2', 'woodmart' ),
					),
					'layout-3' => array(
						'title' => esc_html__( 'Simple3', 'woodmart' ),
					),
					'layout-4' => array(
						'title' => esc_html__( 'Simple4', 'woodmart' ),
					),
					'layout-5' => array(
						'title' => esc_html__( 'Simple5', 'woodmart' ),
					),
				),
			),
			'exit_intent'  => array(
				'title'   => esc_html__( 'Exit Intent', 'woodmart' ),
				'layouts' => array(
					'layout-1' => array(
						'title' => esc_html__( 'Simple', 'woodmart' ),
					),
					'layout-2' => array(
						'title' => esc_html__( 'Simple2', 'woodmart' ),
					),
					'layout-3' => array(
						'title' => esc_html__( 'Simple3', 'woodmart' ),
					),
					'layout-4' => array(
						'title' => esc_html__( 'Simple4', 'woodmart' ),
					),
					'layout-5' => array(
						'title' => esc_html__( 'Simple5', 'woodmart' ),
					),
					'layout-6' => array(
						'title' => esc_html__( 'Simple6', 'woodmart' ),
					),
					'layout-7' => array(
						'title' => esc_html__( 'Simple7', 'woodmart' ),
					),
					'layout-8' => array(
						'title' => esc_html__( 'Simple8', 'woodmart' ),
					),
					'layout-9' => array(
						'title' => esc_html__( 'Simple9', 'woodmart' ),
					),
					'layout-10' => array(
						'title' => esc_html__( 'Simple10', 'woodmart' ),
					),
					'layout-11' => array(
						'title' => esc_html__( 'Simple11', 'woodmart' ),
					),
					'layout-12' => array(
						'title' => esc_html__( 'Simple12', 'woodmart' ),
					),
				),
			),
			'information'  => array(
				'title'   => esc_html__( 'Information', 'woodmart' ),
				'layouts' => array(
					'layout-1' => array(
						'title' => esc_html__( 'Simple', 'woodmart' ),
					),
					'layout-2' => array(
						'title' => esc_html__( 'Simple2', 'woodmart' ),
					),
					'layout-3' => array(
						'title' => esc_html__( 'Simple3', 'woodmart' ),
					),
					'layout-4' => array(
						'title' => esc_html__( 'Simple4', 'woodmart' ),
					),
					'layout-5' => array(
						'title' => esc_html__( 'Simple5', 'woodmart' ),
					),
					'layout-6' => array(
						'title' => esc_html__( 'Simple6', 'woodmart' ),
					),
					'layout-7' => array(
						'title' => esc_html__( 'Simple7', 'woodmart' ),
					),
					'layout-8' => array(
						'title' => esc_html__( 'Simple8', 'woodmart' ),
					),
					'layout-9' => array(
						'title' => esc_html__( 'Simple9', 'woodmart' ),
					),
					'layout-10' => array(
						'title' => esc_html__( 'Simple10', 'woodmart' ),
					),
					'layout-11' => array(
						'title' => esc_html__( 'Simple11', 'woodmart' ),
					),
					'layout-12' => array(
						'title' => esc_html__( 'Simple12', 'woodmart' ),
					),
				),
			),
		),
	),

	'floating-block' => array(
		'post_type'   => 'wd_floating_block',
		'label'       => esc_html__( 'Floating Block', 'woodmart' ),
		'ajax_action' => 'wd_floating_block_create',
		'prefix'      => 'wd_fb_',
		'options'     => array(
			'version',
			'close_by_selector',
			'persistent_close',
		),
		'create_form' => array(
			'label_text'       => esc_html__( 'Predefined floating blocks', 'woodmart' ),
			'name_label'       => esc_html__( 'Floating block name', 'woodmart' ),
			'name_placeholder' => esc_html__( 'Enter floating block name', 'woodmart' ),
			'default_name'     => esc_html__( 'Floating block', 'woodmart' ),
			'submit_text'      => esc_html__( 'Create floating block', 'woodmart' ),
			'templates_label'  => esc_html__( 'Floating blocks templates', 'woodmart' ),
			'preview_alt'      => esc_html__( 'Floating block preview', 'woodmart' ),
		),
		'templates'   => array(
			'button' => array(
				'title'   => esc_html__( 'Button', 'woodmart' ),
				'layouts' => array(
					'layout-1' => array(
						'title' => esc_html__( 'Simple', 'woodmart' ),
					),
					'layout-2' => array(
						'title' => esc_html__( 'Simple2', 'woodmart' ),
					),
					'layout-3' => array(
						'title' => esc_html__( 'Simple3', 'woodmart' ),
					),
					'layout-4' => array(
						'title' => esc_html__( 'Simple4', 'woodmart' ),
					),
					'layout-5' => array(
						'title' => esc_html__( 'Simple5', 'woodmart' ),
					),
					'layout-6' => array(
						'title' => esc_html__( 'Simple6', 'woodmart' ),
					),
				),
			),
			'banner' => array(
				'title'   => esc_html__( 'Banner', 'woodmart' ),
				'layouts' => array(
					'layout-1' => array(
						'title' => esc_html__( 'Simple', 'woodmart' ),
					),
					'layout-2' => array(
						'title' => esc_html__( 'Simple2', 'woodmart' ),
					),
					'layout-3' => array(
						'title' => esc_html__( 'Simple3', 'woodmart' ),
					),
					'layout-4' => array(
						'title' => esc_html__( 'Simple4', 'woodmart' ),
					),
					'layout-5' => array(
						'title' => esc_html__( 'Simple5', 'woodmart' ),
					),
					'layout-6' => array(
						'title' => esc_html__( 'Simple6', 'woodmart' ),
					),
					'layout-7' => array(
						'title' => esc_html__( 'Simple7', 'woodmart' ),
					),
					'layout-8' => array(
						'title' => esc_html__( 'Simple8', 'woodmart' ),
					),
					'layout-9' => array(
						'title' => esc_html__( 'Simple9', 'woodmart' ),
					),
					'layout-10' => array(
						'title' => esc_html__( 'Simple10', 'woodmart' ),
					),
					'layout-11' => array(
						'title' => esc_html__( 'Simple11', 'woodmart' ),
					),
					'layout-12' => array(
						'title' => esc_html__( 'Simple12', 'woodmart' ),
					),
					'layout-13' => array(
						'title' => esc_html__( 'Simple13', 'woodmart' ),
					),
					'layout-14' => array(
						'title' => esc_html__( 'Simple14', 'woodmart' ),
					),
					'layout-15' => array(
						'title' => esc_html__( 'Simple15', 'woodmart' ),
					),
				),
			),
			'bar'    => array(
				'title'   => esc_html__( 'Bar', 'woodmart' ),
				'layouts' => array(
					'layout-1' => array(
						'title' => esc_html__( 'Simple', 'woodmart' ),
					),
					'layout-2' => array(
						'title' => esc_html__( 'Simple2', 'woodmart' ),
					),
					'layout-3' => array(
						'title' => esc_html__( 'Simple3', 'woodmart' ),
					),
					'layout-4' => array(
						'title' => esc_html__( 'Simple4', 'woodmart' ),
					),
					'layout-5' => array(
						'title' => esc_html__( 'Simple5', 'woodmart' ),
					),
					'layout-6' => array(
						'title' => esc_html__( 'Simple6', 'woodmart' ),
					),
				),
			),
		),
	),
);
