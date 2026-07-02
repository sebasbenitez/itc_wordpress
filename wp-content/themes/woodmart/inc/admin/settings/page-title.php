<?php
/**
 * Page title settings.
 *
 * @package woodmart
 */

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

use XTS\Admin\Modules\Options;

Options::add_field(
	array(
		'id'          => 'page-title-design',
		'name'        => esc_html__( 'Page title design', 'woodmart' ),
		'description' => esc_html__( 'Select page title section design or disable it completely on all pages.', 'woodmart' ),
		'group'       => esc_html__( 'Style', 'woodmart' ),
		'type'        => 'buttons',
		'section'     => 'page_title_section',
		'options'     => array(
			'default'  => array(
				'name'  => esc_html__( 'Default', 'woodmart' ),
				'value' => 'default',
				'image' => WOODMART_ASSETS_IMAGES . '/settings/page-heading/default.jpg',
			),
			'centered' => array(
				'name'  => esc_html__( 'Centered', 'woodmart' ),
				'value' => 'centered',
				'image' => WOODMART_ASSETS_IMAGES . '/settings/page-heading/centered.jpg',
			),
			'disable'  => array(
				'name'  => esc_html__( 'Disable', 'woodmart' ),
				'value' => 'disable',
				'image' => WOODMART_ASSETS_IMAGES . '/settings/page-heading/disable.jpg',
			),
		),
		'default'     => 'centered',
		'tags'        => array(
			esc_html__( 'heading style', 'woodmart' ),
			esc_html__( 'page header design', 'woodmart' ),
			esc_html__( 'hero section', 'woodmart' ),
		),
		'priority'    => 10,
	)
);

Options::add_field(
	array(
		'id'          => 'page-title-size',
		'name'        => esc_html__( 'Page title size', 'woodmart' ),
		'description' => esc_html__( 'You can set different sizes for your page titles.', 'woodmart' ),
		'group'       => esc_html__( 'Style', 'woodmart' ),
		'type'        => 'buttons',
		'section'     => 'page_title_section',
		'options'     => array(
			'default' => array(
				'name'  => esc_html__( 'Default', 'woodmart' ),
				'hint'  => wp_kses( '<img data-src="' . WOODMART_TOOLTIP_URL . 'page-title-size-default.jpg" alt="">', true ),
				'value' => 'default',
			),
			'small'   => array(
				'name'  => esc_html__( 'Small', 'woodmart' ),
				'hint'  => wp_kses( '<img data-src="' . WOODMART_TOOLTIP_URL . 'page-title-size-small.jpg" alt="">', true ),
				'value' => 'small',
			),
			'large'   => array(
				'name'  => esc_html__( 'Large', 'woodmart' ),
				'hint'  => wp_kses( '<img data-src="' . WOODMART_TOOLTIP_URL . 'page-title-size-large.jpg" alt="">', true ),
				'value' => 'large',
			),
		),
		'default'     => 'default',
		'priority'    => 20,
	)
);

Options::add_field(
	array(
		'id'          => 'title-background',
		'name'        => esc_html__( 'Pages title background', 'woodmart' ),
		'description' => esc_html__( 'Set background image or color, that will be used as a default for all page titles, shop page and blog.', 'woodmart' ),
		'group'       => esc_html__( 'Style', 'woodmart' ),
		'type'        => 'background',
		'default'     => array(
			'color'    => '#0a0a0a',
			'position' => 'center center',
			'size'     => 'cover',
		),
		'options'     => ! apply_filters( 'woodmart_generate_legacy_page_title_bg', false ) ? array(
			'repeat'     => false,
			'attachment' => false,
			'size'       => array(
				''        => '',
				'cover'   => esc_html__( 'Cover', 'woodmart' ),
				'contain' => esc_html__( 'Contain', 'woodmart' ),
				'fill'    => esc_html__( 'Fill', 'woodmart' ),
				'none'    => esc_html__( 'None', 'woodmart' ),
			),
		) : array(),
		'allowed'     => ! apply_filters( 'woodmart_generate_legacy_page_title_bg', false ) ? array(
			'repeat'     => false,
			'attachment' => false,
		) : array(),
		'css_rules'   => ! apply_filters( 'woodmart_generate_legacy_page_title_bg', false ) ? array(
			'color'    => false,
			'image'    => false,
			'size'     => 'object-fit',
			'position' => 'object-position',
		) : array(),
		'section'     => 'page_title_section',
		'selector'    => apply_filters( 'woodmart_generate_legacy_page_title_bg', false ) ? '.wd-page-title' : '.wd-page-title .wd-page-title-bg img',
		'tags'        => array(
			esc_html__( 'page title bg', 'woodmart' ),
			esc_html__( 'heading background', 'woodmart' ),
		),
		'priority'    => 30,
	)
);

Options::add_field(
	array(
		'id'          => 'page-title-color',
		'name'        => esc_html__( 'Text color for page title', 'woodmart' ),
		'description' => esc_html__( 'You can set text different color depending on its background. It can be light or dark.', 'woodmart' ),
		'group'       => esc_html__( 'Style', 'woodmart' ),
		'type'        => 'buttons',
		'section'     => 'page_title_section',
		'options'     => array(
			'default' => array(
				'name'  => esc_html__( 'Default', 'woodmart' ),
				'value' => 'default',
			),
			'light'   => array(
				'name'  => esc_html__( 'Light', 'woodmart' ),
				'value' => 'light',
			),
			'dark'    => array(
				'name'  => esc_html__( 'Dark', 'woodmart' ),
				'value' => 'dark',
			),
		),
		'default'     => 'light',
		'priority'    => 50,
	)
);

Options::add_field(
	array(
		'id'          => 'page_title_tag',
		'name'        => esc_html__( 'Title tag', 'woodmart' ),
		'hint'        => wp_kses( '<img data-src="' . WOODMART_TOOLTIP_URL . 'page-title-title-tag.jpg" alt="">', true ),
		'description' => esc_html__( 'Choose which HTML tag to use for the page title.', 'woodmart' ),
		'group'       => esc_html__( 'SEO', 'woodmart' ),
		'type'        => 'select',
		'section'     => 'page_title_section',
		'default'     => 'default',
		'options'     => array(
			'default' => array(
				'name'  => esc_html__( 'Default', 'woodmart' ),
				'value' => 'default',
			),
			'h1'      => array(
				'name'  => 'h1',
				'value' => 'h1',
			),
			'h2'      => array(
				'name'  => 'h2',
				'value' => 'h2',
			),
			'h3'      => array(
				'name'  => 'h3',
				'value' => 'h3',
			),
			'h4'      => array(
				'name'  => 'h4',
				'value' => 'h4',
			),
			'h5'      => array(
				'name'  => 'h5',
				'value' => 'h5',
			),
			'h6'      => array(
				'name'  => 'h6',
				'value' => 'h6',
			),
			'p'       => array(
				'name'  => 'p',
				'value' => 'p',
			),
			'div'     => array(
				'name'  => 'div',
				'value' => 'div',
			),
			'span'    => array(
				'name'  => 'span',
				'value' => 'span',
			),
		),
		'priority'    => 55,
	)
);

Options::add_field(
	array(
		'id'          => 'breadcrumbs',
		'section'     => 'page_title_section',
		'name'        => esc_html__( 'Show breadcrumbs', 'woodmart' ),
		'hint'        => wp_kses( '<img data-src="' . WOODMART_TOOLTIP_URL . 'page-title-show-breadcrumbs.jpg" alt="">', true ),
		'description' => esc_html__( 'Displays a full chain of links to the current page.', 'woodmart' ),
		'group'       => esc_html__( 'SEO', 'woodmart' ),
		'on-text'     => esc_html__( 'Yes', 'woodmart' ),
		'off-text'    => esc_html__( 'No', 'woodmart' ),
		'type'        => 'switcher',
		'default'     => '1',
		'priority'    => 60,
	)
);

Options::add_field(
	array(
		'id'       => 'breadcrumbs_separator',
		'section'  => 'page_title_section',
		'type'     => 'text_input',
		'default'  => '/',
		'name'     => esc_html__( 'Breadcrumbs separator', 'woodmart' ),
		'group'    => esc_html__( 'SEO', 'woodmart' ),
		'tags'     => array(
			esc_html__( 'breadcrumb divider', 'woodmart' ),
			esc_html__( 'trail separator', 'woodmart' ),
		),
		'priority' => 70,
	)
);

Options::add_field(
	array(
		'id'       => 'yoast_breadcrumbs_notice',
		'type'     => 'notice',
		'style'    => 'info',
		'name'     => '',
		'content'  => __( 'Requires Yoast SEO plugin to be installed. Replaces the theme’s breadcrumbs with the custom one that comes with the plugin. You need to enable and configure it in Dashboard -> SEO -> Search Appearance -> Breadcrumbs.', 'woodmart' ),
		'section'  => 'page_title_section',
		'group'    => esc_html__( 'SEO', 'woodmart' ),
		't_tab'    => array(
			'id'    => 'page_title_tabs',
			'tab'   => esc_html__( 'Yoast', 'woodmart' ),
			'style' => 'default',
		),
		'priority' => 80,
	)
);

Options::add_field(
	array(
		'id'       => 'yoast_shop_breadcrumbs',
		'section'  => 'page_title_section',
		'name'     => esc_html__( 'Yoast breadcrumbs for shop', 'woodmart' ),
		'group'    => esc_html__( 'SEO', 'woodmart' ),
		'type'     => 'switcher',
		'default'  => false,
		't_tab'    => array(
			'id'    => 'page_title_tabs',
			'tab'   => esc_html__( 'Yoast', 'woodmart' ),
			'style' => 'default',
		),
		'priority' => 90,
	)
);

Options::add_field(
	array(
		'id'       => 'yoast_pages_breadcrumbs',
		'section'  => 'page_title_section',
		'name'     => esc_html__( 'Yoast breadcrumbs for pages', 'woodmart' ),
		'group'    => esc_html__( 'SEO', 'woodmart' ),
		'type'     => 'switcher',
		'default'  => false,
		't_tab'    => array(
			'id'    => 'page_title_tabs',
			'tab'   => esc_html__( 'Yoast', 'woodmart' ),
			'style' => 'default',
		),
		'priority' => 100,
	)
);

Options::add_field(
	array(
		'id'       => 'rankmath_breadcrumbs_notice',
		'type'     => 'notice',
		'style'    => 'info',
		'name'     => '',
		'content'  => __( 'Requires Rank Math SEO plugin to be installed. Replaces the theme’s breadcrumbs with the custom one that comes with the plugin. You need to enable and configure it in Rank Math SEO -> General Settings -> Breadcrumbs.', 'woodmart' ),
		'section'  => 'page_title_section',
		'group'    => esc_html__( 'SEO', 'woodmart' ),
		't_tab'    => array(
			'id'    => 'page_title_tabs',
			'tab'   => esc_html__( 'Rank Math', 'woodmart' ),
			'style' => 'default',
		),
		'priority' => 110,
	)
);

Options::add_field(
	array(
		'id'       => 'rankmath_shop_breadcrumbs',
		'section'  => 'page_title_section',
		'name'     => esc_html__( 'Rank Math breadcrumbs for shop', 'woodmart' ),
		'group'    => esc_html__( 'SEO', 'woodmart' ),
		'type'     => 'switcher',
		'default'  => false,
		't_tab'    => array(
			'id'    => 'page_title_tabs',
			'tab'   => esc_html__( 'Rank Math', 'woodmart' ),
			'style' => 'default',
		),
		'priority' => 120,
	)
);

Options::add_field(
	array(
		'id'       => 'rankmath_pages_breadcrumbs',
		'section'  => 'page_title_section',
		'name'     => esc_html__( 'Rank Math breadcrumbs for pages', 'woodmart' ),
		'group'    => esc_html__( 'SEO', 'woodmart' ),
		'type'     => 'switcher',
		'default'  => false,
		't_tab'    => array(
			'id'    => 'page_title_tabs',
			'tab'   => esc_html__( 'Rank Math', 'woodmart' ),
			'style' => 'default',
		),
		'priority' => 130,
	)
);

Options::add_field(
	array(
		'id'       => 'aioseo_breadcrumbs_notice',
		'type'     => 'notice',
		'style'    => 'info',
		'name'     => '',
		'content'  => __( 'Requires All in One SEO plugin to be installed. Replaces the theme’s breadcrumbs with custom breadcrumbs that come with the plugin. You can configure breadcrumbs in All in One SEO -> General Settings -> Breadcrumbs.', 'woodmart' ),
		'section'  => 'page_title_section',
		'group'    => esc_html__( 'SEO', 'woodmart' ),
		't_tab'    => array(
			'id'    => 'page_title_tabs',
			'tab'   => esc_html__( 'All in One SEO', 'woodmart' ),
			'style' => 'default',
		),
		'priority' => 140,
	)
);

Options::add_field(
	array(
		'id'       => 'aioseo_shop_breadcrumbs',
		'section'  => 'page_title_section',
		'name'     => esc_html__( 'All in One SEO breadcrumbs for shop', 'woodmart' ),
		'group'    => esc_html__( 'SEO', 'woodmart' ),
		'type'     => 'switcher',
		'default'  => false,
		't_tab'    => array(
			'id'    => 'page_title_tabs',
			'tab'   => esc_html__( 'All in One SEO', 'woodmart' ),
			'style' => 'default',
		),
		'priority' => 150,
	)
);

Options::add_field(
	array(
		'id'       => 'aioseo_pages_breadcrumbs',
		'section'  => 'page_title_section',
		'name'     => esc_html__( 'All in One SEO breadcrumbs for pages', 'woodmart' ),
		'group'    => esc_html__( 'SEO', 'woodmart' ),
		'type'     => 'switcher',
		'default'  => false,
		't_tab'    => array(
			'id'    => 'page_title_tabs',
			'tab'   => esc_html__( 'All in One SEO', 'woodmart' ),
			'style' => 'default',
		),
		'priority' => 160,
	)
);

Options::add_field(
	array(
		'id'       => 'seopress_breadcrumbs_notice',
		'type'     => 'notice',
		'style'    => 'info',
		'name'     => '',
		'content'  => __( 'Requires SEOPress plugin to be installed. Replaces the theme’s breadcrumbs with custom breadcrumbs that come with the plugin. You can configure breadcrumbs in SEOPress -> PRO -> Breadcrumbs -> Enable Breadcrumbs.', 'woodmart' ),
		'section'  => 'page_title_section',
		'group'    => esc_html__( 'SEO', 'woodmart' ),
		't_tab'    => array(
			'id'    => 'page_title_tabs',
			'tab'   => esc_html__( 'SEOPress', 'woodmart' ),
			'style' => 'default',
		),
		'priority' => 170,
	)
);

Options::add_field(
	array(
		'id'       => 'seopress_shop_breadcrumbs',
		'section'  => 'page_title_section',
		'name'     => esc_html__( 'SEOPress breadcrumbs for shop', 'woodmart' ),
		'group'    => esc_html__( 'SEO', 'woodmart' ),
		'type'     => 'switcher',
		'default'  => false,
		't_tab'    => array(
			'id'    => 'page_title_tabs',
			'tab'   => esc_html__( 'SEOPress', 'woodmart' ),
			'style' => 'default',
		),
		'priority' => 180,
	)
);

Options::add_field(
	array(
		'id'       => 'seopress_pages_breadcrumbs',
		'section'  => 'page_title_section',
		'name'     => esc_html__( 'SEOPress breadcrumbs for pages', 'woodmart' ),
		'group'    => esc_html__( 'SEO', 'woodmart' ),
		'type'     => 'switcher',
		'default'  => false,
		't_tab'    => array(
			'id'    => 'page_title_tabs',
			'tab'   => esc_html__( 'SEOPress', 'woodmart' ),
			'style' => 'default',
		),
		'priority' => 190,
	)
);
