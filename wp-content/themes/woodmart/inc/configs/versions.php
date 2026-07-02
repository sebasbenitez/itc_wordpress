<?php

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

/**
 * ------------------------------------------------------------------------------------------------
 * Array of versions for dummy content import section
 * ------------------------------------------------------------------------------------------------
 */
return apply_filters(
	'woodmart_get_versions_to_import',
	[
		'main'                  => [
			'title'      => 'WoodMart Main',
			'process'    => 'xml,home,options,widgets,headers,images',
			'type'       => 'version',
			'base'       => 'base',
			'link'       => 'https://woodmart.xtemos.com/home/',
			'categories' => [
				[
					'name' => 'Furniture',
					'slug' => 'furniture',
				],
			],
		],
		'jewellery-2'            => [
			'title'      => 'Jewellery 2',
			'process'    => 'xml,home,options,headers,widgets',
			'type'       => 'version',
			'base'       => 'jewellery-2_base',
			'link'       => 'https://woodmart.xtemos.com/jewellery-2/',
			'categories' => [
				[
					'name' => 'Fashion',
					'slug' => 'fashion',
				],
			],
		],
		'edc'            => [
			'title'      => 'EDC',
			'process'    => 'xml,home,options,widgets',
			'type'       => 'version',
			'base'       => 'edc_base',
			'link'       => 'https://woodmart.xtemos.com/edc/',
			'categories' => [
				[
					'name' => 'Mega Store',
					'slug' => 'mega_store',
				],
			],
		],
		'keyboards'            => [
			'title'      => 'Keyboards',
			'process'    => 'xml,home,options,widgets',
			'type'       => 'version',
			'base'       => 'keyboards_base',
			'link'       => 'https://woodmart.xtemos.com/keyboards/',
			'categories' => [
				[
					'name' => 'Electronics',
					'slug' => 'electronics',
				],
			],
		],
		'electronics-3'            => [
			'title'      => 'Electronics 3',
			'process'    => 'xml,home,options,widgets',
			'type'       => 'version',
			'base'       => 'electronics-3_base',
			'link'       => 'https://woodmart.xtemos.com/electronics-3/',
			'categories' => [
				[
					'name' => 'Electronics',
					'slug' => 'electronics',
				],
				[
					'name' => 'Mega Store',
					'slug' => 'mega_store',
				],
			],
		],
		'fashion-2'            => [
			'title'      => 'Fashion 2',
			'process'    => 'xml,home,options,widgets',
			'type'       => 'version',
			'base'       => 'fashion-2_base',
			'link'       => 'https://woodmart.xtemos.com/fashion-2/',
			'categories' => [
				[
					'name' => 'Fashion',
					'slug' => 'fashion',
				],
				[
					'name' => 'Mega Store',
					'slug' => 'mega_store',
				],
			],
		],
		'perfumes'            => [
			'title'      => 'Perfumes',
			'process'    => 'xml,home,options,widgets,headers',
			'type'       => 'version',
			'base'       => 'perfumes_base',
			'link'       => 'https://woodmart.xtemos.com/perfumes/',
			'categories' => [
				[
					'name' => 'Fashion',
					'slug' => 'fashion',
				],
			],
		],
		'merchandise'            => [
			'title'      => 'Merchandise',
			'process'    => 'xml,home,options,widgets',
			'type'       => 'version',
			'base'       => 'merchandise_base',
			'link'       => 'https://woodmart.xtemos.com/merchandise/',
			'categories' => [
				[
					'name' => 'Fashion',
					'slug' => 'fashion',
				],
			],
		],
		'christmas-2'            => [
			'title'      => 'Christmas 2',
			'process'    => 'xml,home,options,widgets,headers',
			'type'       => 'version',
			'base'       => 'christmas-2_base',
			'link'       => 'https://woodmart.xtemos.com/christmas-2/',
			'categories' => [
				[
					'name' => 'Mega Store',
					'slug' => 'mega_store',
				],
			],
		],
		'pets'            => [
			'title'      => 'Pets',
			'process'    => 'xml,home,options,widgets',
			'type'       => 'version',
			'base'       => 'pets_base',
			'link'       => 'https://woodmart.xtemos.com/pets/',
			'categories' => [
				[
					'name' => 'Food',
					'slug' => 'food',
				],
				[
					'name' => 'Mega Store',
					'slug' => 'mega_store',
				],
			],
		],
		'vinyls'            => [
			'title'      => 'Vinyls',
			'process'    => 'xml,home,options,widgets',
			'type'       => 'version',
			'base'       => 'vinyls_base',
			'link'       => 'https://woodmart.xtemos.com/vinyls/',
			'categories' => [
				[
					'name' => 'Electronics',
					'slug' => 'electronics',
				],
			],
		],
		'handmade-bags'            => [
			'title'      => 'Handmade bags',
			'process'    => 'xml,home,options,widgets,headers',
			'type'       => 'version',
			'base'       => 'handmade-bags_base',
			'link'       => 'https://woodmart.xtemos.com/handmade-bags/',
			'categories' => [
				[
					'name' => 'Fashion',
					'slug' => 'fashion',
				]
			],
		],
		'hemp-shoes'            => [
			'title'      => 'Hemp shoes',
			'process'    => 'xml,home,options,widgets,headers,images',
			'type'       => 'version',
			'base'       => 'base',
			'categories' => [
				[
					'name' => 'Landing',
					'slug' => 'landing',
				],
				[
					'name' => 'Fashion',
					'slug' => 'fashion',
				]
			],
		],
		't-shirts'            => [
			'title'      => 'T-shirts',
			'process'    => 'xml,home,options,widgets',
			'type'       => 'version',
			'base'       => 't-shirts_base',
			'link'       => 'https://woodmart.xtemos.com/t-shirts-prints/',
			'categories' => [
				[
					'name' => 'Service',
					'slug' => 'service',
				],
				[
					'name' => 'Fashion',
					'slug' => 'fashion',
				]
			],
		],
		'barbershop'            => [
			'title'      => 'Barbershop',
			'process'    => 'xml,home,options,widgets,headers,images',
			'type'       => 'version',
			'base'       => 'base',
			'link'       => 'https://woodmart.xtemos.com/barbershop/',
			'categories' => [
				[
					'name' => 'Service',
					'slug' => 'service',
				],
				[
					'name' => 'Fashion',
					'slug' => 'fashion',
				],
				[
					'name' => 'Landing',
					'slug' => 'landing',
				],
			],
		],
		'marketplace2'            => [
			'title'      => 'Marketplace 2',
			'process'    => 'xml,home,options,widgets',
			'type'       => 'version',
			'base'       => 'marketplace2_base',
			'link'       => 'https://woodmart.xtemos.com/marketplace2/',
			'categories' => [
				[
					'name' => 'Electronics',
					'slug' => 'electronics',
				],
				[
					'name' => 'Mega Store',
					'slug' => 'mega_store',
				],
			],
		],
		'makeup'            => [
			'title'      => 'Makeup',
			'process'    => 'xml,home,options,widgets',
			'type'       => 'version',
			'base'       => 'makeup_base',
			'link'       => 'https://woodmart.xtemos.com/makeup/',
			'categories' => [
				[
					'name' => 'Fashion',
					'slug' => 'fashion',
				],
			],
		],
		'vegetables'            => [
			'title'      => 'Vegetables',
			'process'    => 'xml,home,options,widgets',
			'type'       => 'version',
			'base'       => 'vegetables_base',
			'link'       => 'https://woodmart.xtemos.com/vegetables/',
			'categories' => [
				[
					'name' => 'Food',
					'slug' => 'food',
				],
				[
					'name' => 'Mega Store',
					'slug' => 'mega_store',
				],
			],
		],
		'pottery'            => [
			'title'      => 'Pottery',
			'process'    => 'xml,home,options,widgets,headers',
			'type'       => 'version',
			'base'       => 'pottery_base',
			'link'       => 'https://woodmart.xtemos.com/pottery/',
			'categories' => [
			],
		],
		'pills'            => [
			'title'      => 'Pills',
			'process'    => 'xml,home,options,widgets',
			'type'       => 'version',
			'base'       => 'pills_base',
			'link'       => 'https://woodmart.xtemos.com/pills/',
			'categories' => [
			],
		],
		'organic-farm'            => [
			'title'      => 'Organic Farm',
			'process'    => 'xml,home,options,widgets',
			'type'       => 'version',
			'base'       => 'organic-farm_base',
			'link'       => 'https://woodmart.xtemos.com/organic-farm/',
			'categories' => [
				[
					'name' => 'Food',
					'slug' => 'food',
				],
			],
		],
		'kids'            => [
			'title'      => 'Kids',
			'process'    => 'xml,home,options,widgets,headers',
			'type'       => 'version',
			'base'       => 'kids_base',
			'link'       => 'https://woodmart.xtemos.com/kids/',
			'categories' => [
				[
					'name' => 'Fashion',
					'slug' => 'fashion',
				],
			],
		],
		'plants'            => [
			'title'      => 'Plants',
			'process'    => 'xml,home,options,widgets,headers',
			'type'       => 'version',
			'base'       => 'plants_base',
			'link'       => 'https://woodmart.xtemos.com/plants/',
			'categories' => [
			],
		],
		'games-light'            => [
			'title'      => 'Games',
			'process'    => 'xml,home,options,widgets',
			'type'       => 'version',
			'base'       => 'games_base-light',
			'link'       => 'https://woodmart.xtemos.com/games/',
			'categories' => [
				[
					'name' => 'Mega Store',
					'slug' => 'mega_store',
				],
			],
		],
		'games-dark'            => [
			'title'      => 'Games Dark',
			'process'    => 'xml,home,options,widgets,images',
			'type'       => 'version',
			'base'       => 'games_base-dark',
			'link'       => 'https://woodmart.xtemos.com/games/home-dark/',
			'categories' => [
				[
					'name' => 'Mega Store',
					'slug' => 'mega_store',
				],
			],
		],
		'furniture2'            => [
			'title'      => 'Furniture 2',
			'process'    => 'xml,home,options,widgets,headers',
			'type'       => 'version',
			'base'       => 'furniture2_base',
			'link'       => 'https://woodmart.xtemos.com/furniture2/',
			'categories' => [
				[
					'name' => 'Furniture',
					'slug' => 'furniture',
				],
				[
					'name' => 'Mega Store',
					'slug' => 'mega_store',
				],
			],
		],
		'food-delivery'            => [
			'title'      => 'Food Delivery',
			'process'    => 'xml,home,options,widgets,headers,images',
			'type'       => 'version',
			'base'       => 'base',
			'categories' => [
				[
					'name' => 'Food',
					'slug' => 'food',
				],
				[
					'name' => 'Service',
					'slug' => 'service',
				],
				[
					'name' => 'Landing',
					'slug' => 'landing',
				],
			],
		],
		'event-agency'            => [
			'title'      => 'Event Agency',
			'process'    => 'xml,home,options,widgets,headers,images',
			'type'       => 'version',
			'base'       => 'base',
			'categories' => [
				[
					'name' => 'Service',
					'slug' => 'service',
				],
				[
					'name' => 'Corporate',
					'slug' => 'corporate',
				],
				[
					'name' => 'Landing',
					'slug' => 'landing',
				],
			],
		],
		'developer'            => [
			'title'      => 'Developer',
			'process'    => 'xml,home,options,widgets,headers,images',
			'type'       => 'version',
			'base'       => 'base',
			'categories' => [
				[
					'name' => 'Corporate',
					'slug' => 'corporate',
				],
				[
					'name' => 'Landing',
					'slug' => 'landing',
				],
			],
		],
		'architecture-studio'            => [
			'title'      => 'Architecture Studio',
			'process'    => 'xml,home,options,widgets,headers,images',
			'type'       => 'version',
			'base'       => 'base',
			'categories' => [
				[
					'name' => 'Corporate',
					'slug' => 'corporate',
				],
				[
					'name' => 'Landing',
					'slug' => 'landing',
				],
			],
		],
		'mega-electronics'            => [
			'title'      => 'Mega Electronics',
			'process'    => 'xml,home,options,widgets',
			'type'       => 'version',
			'base'       => 'mega-electronics_base',
			'link'       => 'https://woodmart.xtemos.com/mega-electronics/',
			'categories' => [
				[
					'name' => 'Electronics',
					'slug' => 'electronics',
				],
				[
					'name' => 'Mega Store',
					'slug' => 'mega_store',
				],
			],
		],
		'megamarket'            => [
			'title'      => 'Megamarket',
			'process'    => 'xml,home,options,widgets',
			'type'       => 'version',
			'base'       => 'megamarket_base',
			'link'       => 'https://woodmart.xtemos.com/megamarket/',
			'categories' => [
				[
					'name' => 'Mega Store',
					'slug' => 'mega_store',
				],
			],
		],
		'accessories'            => [
			'title'      => 'Accessories',
			'process'    => 'xml,home,options,widgets,headers',
			'type'       => 'version',
			'base'       => 'accessories_base',
			'link'       => 'https://woodmart.xtemos.com/accessories/',
			'categories' => [
				[
					'name' => 'Electronics',
					'slug' => 'electronics',
				],
				[
					'name' => 'Mega Store',
					'slug' => 'mega_store',
				],
			],
		],
		'smart-home'            => [
			'title'      => 'Smart Home',
			'process'    => 'xml,home,options,widgets,headers,images',
			'type'       => 'version',
			'base'       => 'base',
			'categories' => [
				[
					'name' => 'Electronics',
					'slug' => 'electronics',
				],
			],
		],
		'school'                => [
			'title'   => 'School',
			'process' => 'xml,home,options,widgets,headers,images',
			'type'    => 'version',
			'base'    => 'base',
			'categories' => [
				[
					'name' => 'Service',
					'slug' => 'service',
				],
				[
					'name' => 'Landing',
					'slug' => 'landing',
				],
			],
		],
		'real-estate'           => [
			'title'   => 'Real Estate',
			'process' => 'xml,home,options,widgets,headers,images',
			'type'    => 'version',
			'base'    => 'base',
			'categories' => [
				[
					'name' => 'Corporate',
					'slug' => 'corporate',
				],
				[
					'name' => 'Landing',
					'slug' => 'landing',
				],
			],
		],
		'beauty'                => [
			'title'      => 'Beauty',
			'process'    => 'xml,home,options,widgets,headers,images',
			'type'       => 'version',
			'base'       => 'base',
			'categories' => [
				[
					'name' => 'Fashion',
					'slug' => 'fashion',
				],
				[
					'name' => 'Corporate',
					'slug' => 'corporate',
				],
				[
					'name' => 'Service',
					'slug' => 'service',
				],
				[
					'name' => 'Landing',
					'slug' => 'landing',
				],
			],
		],
		'sweets-bakery'         => [
			'title'   => 'Sweets Bakery',
			'process' => 'xml,home,options,widgets,headers,images',
			'type'    => 'version',
			'base'    => 'base',
			'categories' => [
				[
					'name' => 'Food',
					'slug' => 'food',
				],
				[
					'name' => 'Service',
					'slug' => 'service',
				],
			],
		],
		'decor'                 => [
			'title'      => 'Decor',
			'process'    => 'xml,home,options,widgets,wood_slider,headers,images',
			'type'       => 'version',
			'base'       => 'base',
			'categories' => [
				[
					'name' => 'Furniture',
					'slug' => 'furniture',
				],
			],
		],
		'retail'                => [
			'title'      => 'Retail',
			'process'    => 'xml,home,options,widgets,headers,images',
			'type'       => 'version',
			'base'       => 'base',
			'categories' => [
				[
					'name' => 'Electronics',
					'slug' => 'electronics',
				],
				[
					'name' => 'Mega Store',
					'slug' => 'mega_store',
				],
			],
		],
		'books'                 => [
			'title'   => 'Books',
			'process' => 'xml,home,options,widgets,headers,images',
			'type'    => 'version',
			'base'    => 'base',
			'categories' => [
			],
		],
		'shoes'                 => [
			'title'      => 'Shoes',
			'process'    => 'xml,home,options,widgets,headers,images',
			'type'       => 'version',
			'base'       => 'base',
			'categories' => [
				[
					'name' => 'Fashion',
					'slug' => 'fashion',
				],
			],
		],
		'marketplace'           => [
			'title'      => 'Marketplace',
			'process'    => 'xml,home,options,widgets,headers,images',
			'type'       => 'version',
			'base'       => 'base',
			'categories' => [
				[
					'name' => 'Electronics',
					'slug' => 'electronics',
				],
				[
					'name' => 'Furniture',
					'slug' => 'furniture',
				],
				[
					'name' => 'Fashion',
					'slug' => 'fashion',
				],
				[
					'name' => 'Mega Store',
					'slug' => 'mega_store',
				],
			],
		],
		'electronics'           => [
			'title'      => 'Electronics',
			'process'    => 'xml,home,options,widgets,headers,images',
			'type'       => 'version',
			'base'       => 'base',
			'categories' => [
				[
					'name' => 'Electronics',
					'slug' => 'electronics',
				],
			],
		],
		'fashion-color'         => [
			'title'      => 'Fashion Color',
			'process'    => 'xml,home,options,widgets,headers,images',
			'type'       => 'version',
			'base'       => 'base',
			'link'       => 'https://woodmart.xtemos.com/demo-fashion-colored/demo/fashion-colored/',
			'categories' => [
				[
					'name' => 'Fashion',
					'slug' => 'fashion',
				],
			],
		],
		'fashion-minimalism'    => [
			'title'      => 'Fashion Minimalism',
			'process'    => 'xml,home,options,widgets,headers,images',
			'type'       => 'version',
			'base'       => 'base',
			'categories' => [
				[
					'name' => 'Fashion',
					'slug' => 'fashion',
				],
			],
		],
		'tools'                 => [
			'title'      => 'Tools',
			'process'    => 'xml,home,options,widgets,headers,images',
			'type'       => 'version',
			'base'       => 'base',
			'categories' => [
				[
					'name' => 'Electronics',
					'slug' => 'electronics',
				],
			],
		],
		'grocery'               => [
			'title'   => 'Grocery',
			'process' => 'xml,home,options,widgets,headers,images',
			'type'    => 'version',
			'base'    => 'base',
			'categories' => [
				[
					'name' => 'Food',
					'slug' => 'food',
				],
				[
					'name' => 'Mega Store',
					'slug' => 'mega_store',
				],
			],
		],
		'lingerie'              => [
			'title'      => 'Lingerie',
			'process'    => 'xml,home,options,widgets,headers,images',
			'type'       => 'version',
			'base'       => 'base',
			'categories' => [
				[
					'name' => 'Fashion',
					'slug' => 'fashion',
				],
			],
		],
		'glasses'               => [
			'title'      => 'Glasses',
			'process'    => 'xml,home,options,widgets,headers,images',
			'type'       => 'version',
			'base'       => 'base',
			'categories' => [
				[
					'name' => 'Fashion',
					'slug' => 'fashion',
				],
			],
		],
		'black-friday'          => [
			'title'      => 'Black Friday',
			'process'    => 'xml,home,options,widgets,headers,images',
			'type'       => 'version',
			'base'       => 'base',
			'categories' => [
				[
					'name' => 'Electronics',
					'slug' => 'electronics',
				],
				[
					'name' => 'Mega Store',
					'slug' => 'mega_store',
				],
			],
		],
		'retail-2'              => [
			'title'      => 'Retail 2',
			'process'    => 'xml,home,options,widgets,headers,images',
			'type'       => 'version',
			'base'       => 'base',
			'categories' => [
				[
					'name' => 'Electronics',
					'slug' => 'electronics',
				],
				[
					'name' => 'Mega Store',
					'slug' => 'mega_store',
				],
			],
		],
		'handmade'              => [
			'title'      => 'Handmade',
			'process'    => 'xml,home,options,widgets,headers,images',
			'type'       => 'version',
			'base'       => 'base',
			'link'       => 'https://woodmart.xtemos.com/handmade/',
			'categories' => [
				[
					'name' => 'Furniture',
					'slug' => 'furniture',
				],
			],
		],
		'repair'                => [
			'title'      => 'Repair',
			'process'    => 'xml,home,options,widgets,headers,images',
			'type'       => 'version',
			'base'       => 'base',
			'categories' => [
				[
					'name' => 'Service',
					'slug' => 'service',
				],
				[
					'name' => 'Corporate',
					'slug' => 'corporate',
				],
				[
					'name' => 'Landing',
					'slug' => 'landing',
				],
			],
		],
		'lawyer'                => [
			'title'   => 'Lawyer',
			'process' => 'xml,home,options,widgets,headers,images',
			'type'    => 'version',
			'base'    => 'base',
			'categories' => [
				[
					'name' => 'Corporate',
					'slug' => 'corporate',
				],
				[
					'name' => 'Service',
					'slug' => 'service',
				],
				[
					'name' => 'Landing',
					'slug' => 'landing',
				],
			],
		],
		'corporate-2'           => [
			'title'   => 'Corporate 2',
			'process' => 'xml,home,options,widgets,headers,images',
			'type'    => 'version',
			'base'    => 'base',
			'categories' => [
				[
					'name' => 'Corporate',
					'slug' => 'corporate',
				],
				[
					'name' => 'Landing',
					'slug' => 'landing',
				],
			],
		],
		'drinks'                => [
			'title'   => 'Drinks',
			'process' => 'xml,home,options,widgets,headers,images',
			'type'    => 'version',
			'base'    => 'base',
			'categories' => [
				[
					'name' => 'Food',
					'slug' => 'food',
				],
			],
		],
		'medical-marijuana'     => [
			'title'   => 'Medical Marijuana',
			'process' => 'xml,home,options,widgets,headers,images',
			'type'    => 'version',
			'base'    => 'base',
			'categories' => [
			],
		],
		'electronics-2'         => [
			'title'      => 'Electronics 2',
			'process'    => 'xml,home,options,widgets,headers,images',
			'type'       => 'version',
			'base'       => 'base',
			'categories' => [
				[
					'name' => 'Electronics',
					'slug' => 'electronics',
				],
			],
		],
		'fashion'               => [
			'title'      => 'Fashion',
			'process'    => 'xml,home,options,widgets,headers,images',
			'type'       => 'version',
			'base'       => 'base',
			'categories' => [
				[
					'name' => 'Fashion',
					'slug' => 'fashion',
				],
			],
		],
		'medical'               => [
			'title'   => 'Medical',
			'process' => 'xml,home,options,widgets,headers,images',
			'type'    => 'version',
			'base'    => 'base',
			'categories' => [
				[
					'name' => 'Service',
					'slug' => 'service',
				],
			],
		],
		'coffee'                => [
			'title'   => 'Coffee',
			'process' => 'xml,home,options,widgets,headers,images',
			'type'    => 'version',
			'base'    => 'base',
			'categories' => [
				[
					'name' => 'Food',
					'slug' => 'food',
				],
			],
		],
		'camping'               => [
			'title'   => 'Camping',
			'process' => 'xml,home,options,widgets,headers,images',
			'type'    => 'version',
			'base'    => 'base',
			'categories' => [
			],
		],
		'alternative-energy'    => [
			'title'      => 'Alternative Energy',
			'process'    => 'xml,home,options,widgets,headers,images',
			'type'       => 'version',
			'base'       => 'base',
			'categories' => [
				[
					'name' => 'Electronics',
					'slug' => 'electronics',
				],
				[
					'name' => 'Corporate',
					'slug' => 'corporate',
				],
			],
		],
		'flowers'               => [
			'title'   => 'Flowers',
			'process' => 'xml,home,options,widgets,headers,images',
			'type'    => 'version',
			'base'    => 'base',
			'categories' => [
				[
					'name' => 'Service',
					'slug' => 'service',
				],
			],
		],
		'fashion-flat'          => [
			'title'      => 'Fashion Flat',
			'process'    => 'xml,home,options,widgets,headers,images',
			'type'       => 'version',
			'base'       => 'base',
			'link'       => 'https://woodmart.xtemos.com/demo-fashion-flat/demo/flat/',
			'categories' => [
				[
					'name' => 'Fashion',
					'slug' => 'fashion',
				],
			],
		],
		'bikes'                 => [
			'title'   => 'Bikes',
			'process' => 'xml,home,options,widgets,headers,images',
			'type'    => 'version',
			'base'    => 'base',
			'categories' => [
			],
		],
		'wine'                  => [
			'title'   => 'Wine',
			'process' => 'xml,home,options,widgets,headers,images',
			'type'    => 'version',
			'base'    => 'base',
			'categories' => [
				[
					'name' => 'Food',
					'slug' => 'food',
				],
			],
		],
		'landing-gadget'        => [
			'title'      => 'Landing Gadget',
			'process'    => 'xml,home,options,widgets,headers,images',
			'type'       => 'version',
			'base'       => 'base',
			'categories' => [
				[
					'name' => 'Electronics',
					'slug' => 'electronics',
				],
				[
					'name' => 'Landing',
					'slug' => 'landing',
				],
			],
		],
		'travel'                => [
			'title'   => 'Travel',
			'process' => 'xml,home,options,widgets,headers,images',
			'type'    => 'version',
			'base'    => 'base',
			'categories' => [
				[
					'name' => 'Corporate',
					'slug' => 'corporate',
				],
				[
					'name' => 'Service',
					'slug' => 'service',
				],
			],
		],
		'corporate'             => [
			'title'   => 'Corporate',
			'process' => 'xml,home,options,widgets,headers,images',
			'type'    => 'version',
			'base'    => 'base',
			'categories' => [
				[
					'name' => 'Corporate',
					'slug' => 'corporate',
				],
				[
					'name' => 'Landing',
					'slug' => 'landing',
				],
			],
		],
		'magazine'              => [
			'title'   => 'Magazine',
			'process' => 'xml,home,options,widgets,headers,images',
			'type'    => 'version',
			'base'    => 'base',
			'link'    => 'https://woodmart.xtemos.com/magazine/',
			'categories' => [
				[
					'name' => 'Corporate',
					'slug' => 'corporate',
				],
			],
		],
		'hardware'              => [
			'title'      => 'Hardware',
			'process'    => 'xml,home,options,widgets,headers,images',
			'type'       => 'version',
			'base'       => 'base',
			'link'       => 'https://woodmart.xtemos.com/demo-hardware/?opt=hardware',
			'categories' => [
				[
					'name' => 'Electronics',
					'slug' => 'electronics',
				],
			],
		],
		'food'                  => [
			'title'   => 'Food',
			'process' => 'xml,home,options,widgets,headers,images',
			'type'    => 'version',
			'base'    => 'base',
			'categories' => [
				[
					'name' => 'Food',
					'slug' => 'food',
				],
				[
					'name' => 'Service',
					'slug' => 'service',
				],
			],
		],
		'cosmetics'             => [
			'title'      => 'Cosmetics',
			'process'    => 'xml,home,options,widgets,headers,images',
			'type'       => 'version',
			'base'       => 'base',
			'categories' => [
				[
					'name' => 'Fashion',
					'slug' => 'fashion',
				],
			],
		],
		'motorcycle'            => [
			'title'   => 'Motorcycle',
			'process' => 'xml,home,options,widgets,headers,images',
			'type'    => 'version',
			'base'    => 'base',
			'categories' => [
			],
		],
		'sport'                 => [
			'title'   => 'Sport',
			'process' => 'xml,home,options,widgets,headers,images',
			'type'    => 'version',
			'base'    => 'base',
			'categories' => [
			],
		],
		'minimalism'            => [
			'title'      => 'Minimalism',
			'process'    => 'xml,home,options,widgets,headers,images',
			'type'       => 'version',
			'base'       => 'base',
			'categories' => [
				[
					'name' => 'Furniture',
					'slug' => 'furniture',
				],
				[
					'name' => 'Fashion',
					'slug' => 'fashion',
				],
			],
		],
		'organic'               => [
			'title'   => 'Organic',
			'process' => 'xml,home,options,widgets,headers,images',
			'type'    => 'version',
			'base'    => 'base',
			'categories' => [
				[
					'name' => 'Food',
					'slug' => 'food',
				],
			],
		],
		'watches'               => [
			'title'      => 'Watches',
			'process'    => 'xml,home,options,widgets,headers,images',
			'type'       => 'version',
			'base'       => 'base',
			'link'       => 'https://woodmart.xtemos.com/demo-watches/demo/watch/',
			'categories' => [
				[
					'name' => 'Fashion',
					'slug' => 'fashion',
				],
			],
		],
		'digitals'              => [
			'title'      => 'Digital',
			'process'    => 'xml,home,options,widgets,headers,images',
			'type'       => 'version',
			'base'       => 'base',
			'categories' => [
				[
					'name' => 'Electronics',
					'slug' => 'electronics',
				],
			],
		],
		'jewellery'             => [
			'title'      => 'Jewellery',
			'process'    => 'xml,home,options,widgets,headers,images',
			'type'       => 'version',
			'base'       => 'base',
			'categories' => [
				[
					'name' => 'Fashion',
					'slug' => 'fashion',
				],
			],
		],
		'toys'                  => [
			'title'   => 'Toys',
			'process' => 'xml,home,options,widgets,headers,images',
			'type'    => 'version',
			'base'    => 'base',
			'categories' => [
			],
		],
		'mobile-app'            => [
			'title'      => 'Mobile App',
			'process'    => 'xml,home,options,widgets,headers,images',
			'type'       => 'version',
			'base'       => 'base',
			'link'       => 'https://woodmart.xtemos.com/demo-mobile-app/?opt=mobile_app',
			'categories' => [
				[
					'name' => 'Corporate',
					'slug' => 'corporate',
				],
				[
					'name' => 'Landing',
					'slug' => 'landing',
				],
			],
		],
		'christmas'             => [
			'title'   => 'Christmas',
			'process' => 'xml,home,options,widgets,headers,images',
			'type'    => 'version',
			'base'    => 'base',
			'categories' => [
			],
		],
		'dark'                  => [
			'title'   => 'Dark',
			'process' => 'xml,home,options,widgets,headers,images',
			'type'    => 'version',
			'base'    => 'base',
			'link'    => 'https://woodmart.xtemos.com/demo-dark/?opt=dark',
			'categories' => [
				[
					'name' => 'Furniture',
					'slug' => 'furniture',
				],
			],
		],
		'cars'                  => [
			'title'   => 'Cars',
			'process' => 'xml,home,options,widgets,headers,images',
			'type'    => 'version',
			'base'    => 'base',
			'link'    => 'https://woodmart.xtemos.com/home-cars/demo/cars/',
			'categories' => [
				[
					'name' => 'Service',
					'slug' => 'service',
				],
			],
		],
		'furniture'             => [
			'title'      => 'Furniture',
			'process'    => 'xml,home,options,widgets,headers,images',
			'type'       => 'version',
			'base'       => 'base',
			'categories' => [
				[
					'name' => 'Furniture',
					'slug' => 'furniture',
				],
			],
		],
		'base-rtl'              => [
			'title'      => 'Base rtl',
			'process'    => 'xml,home,options,widgets,headers,images',
			'type'       => 'version',
			'base'       => 'base',
			'link'       => 'https://woodmart.xtemos.com/home-rtl/?rtl',
			'categories' => [
				[
					'name' => 'Furniture',
					'slug' => 'furniture',
				],
			],
		],
		'basic'                 => [
			'title'      => 'Basic',
			'process'    => 'xml,home,options,widgets,headers,images',
			'type'       => 'version',
			'base'       => 'base',
			'link'       => 'https://woodmart.xtemos.com/layout-basic/?opt=layout_basic',
			'categories' => [
				[
					'name' => 'Furniture',
					'slug' => 'furniture',
				],
			],
		],
		'boxed'                 => [
			'title'      => 'Boxed',
			'process'    => 'xml,home,options,widgets,headers,images',
			'type'       => 'version',
			'base'       => 'base',
			'link'       => 'https://woodmart.xtemos.com/layout-boxed/?opt=layout_boxed',
			'categories' => [
				[
					'name' => 'Furniture',
					'slug' => 'furniture',
				],
			],
		],
		'categories'            => [
			'title'      => 'Categories',
			'process'    => 'xml,home,options,widgets,headers,images',
			'type'       => 'version',
			'base'       => 'base',
			'link'       => 'https://woodmart.xtemos.com/layout-categories/?opt=layout_categories',
			'categories' => [
				[
					'name' => 'Furniture',
					'slug' => 'furniture',
				],
			],
		],
		'landing'               => [
			'title'      => 'Landing',
			'process'    => 'xml,home,options,widgets,headers,images',
			'type'       => 'version',
			'base'       => 'base',
			'link'       => 'https://woodmart.xtemos.com/landing/?opt=layout_landing',
			'categories' => [
				[
					'name' => 'Furniture',
					'slug' => 'furniture',
				],
				[
					'name' => 'Landing',
					'slug' => 'landing',
				],
			],
		],
		'lookbook'              => [
			'title'      => 'Lookbook',
			'process'    => 'xml,home,options,widgets,headers,images',
			'type'       => 'version',
			'base'       => 'base',
			'link'       => 'https://woodmart.xtemos.com/layout-lookbook/?opt=layout_lookbook',
			'categories' => [
				[
					'name' => 'Furniture',
					'slug' => 'furniture',
				],
			],
		],
		'video'                 => [
			'title'      => 'Shaders slider',
			'process'    => 'xml,home,options,widgets,headers,images',
			'type'       => 'version',
			'base'       => 'base',
			'link'       => 'https://woodmart.xtemos.com/layout-video/?opt=layout_video',
			'categories' => [
				[
					'name' => 'Furniture',
					'slug' => 'furniture',
				],
			],
		],
		'parallax'              => [
			'title'      => 'Parallax',
			'process'    => 'xml,home,options,widgets,headers,images',
			'type'       => 'version',
			'base'       => 'base',
			'link'       => 'https://woodmart.xtemos.com/layout-parallax/?opt=layout_parallax',
			'categories' => [
				[
					'name' => 'Furniture',
					'slug' => 'furniture',
				],
				[
					'name' => 'Landing',
					'slug' => 'landing',
				],
			],
		],
		'infinite-scrolling'    => [
			'title'      => 'Infinite Scrolling',
			'process'    => 'xml,home,options,widgets,wood_slider,headers,images',
			'type'       => 'version',
			'base'       => 'base',
			'link'       => 'https://woodmart.xtemos.com/infinite-scrolling/?opt=layout_infinite',
			'categories' => [
				[
					'name' => 'Furniture',
					'slug' => 'furniture',
				],
			],
		],
		'grid'                  => [
			'title'      => 'Grid',
			'process'    => 'xml,home,options,widgets,headers,images',
			'type'       => 'version',
			'base'       => 'base',
			'link'       => 'https://woodmart.xtemos.com/layout-grid-2/?opt=layout_grid2',
			'categories' => [
				[
					'name' => 'Furniture',
					'slug' => 'furniture',
				],
			],
		],
		'digital-portfolio'     => [
			'title'      => 'Digital Portfolio',
			'process'    => 'xml,home,options,widgets,headers,images',
			'type'       => 'version',
			'base'       => 'base',
			'link'       => 'https://woodmart.xtemos.com/layout-digital-portfolio/?opt=layout_digital_portfolio',
			'categories' => [
				[
					'name' => 'Electronics',
					'slug' => 'electronics',
				],
			],
		],
		'base'                  => [
			'title'   => 'Base content (required)',
			'process' => 'xml,xml_images,widgets,options,headers',
			'type'    => 'base',
			'links'   => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/elementor/',
				'gutenberg' => 'https://dummy.xtemos.com/woodmart2/gutenberg/',
			),
			'uploads_link'  => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/wp-content/uploads/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/elementor/wp-content/uploads/sites/2/',
				'gutenberg' => 'https://dummy.xtemos.com/woodmart2/gutenberg/wp-content/uploads/sites/24/',
			),
		],
		'megamarket_base'       => [
			'title'   => 'Base content megamarket (required)',
			'process' => 'xml,xml_images,widgets,options,headers',
			'type'    => 'base',
			'links'   => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/megamarket/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/megamarket-elementor/',
				'gutenberg' => 'https://dummy.xtemos.com/woodmart2/megamarket-gutenberg/',
			),
			'uploads_link'  => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/megamarket/wp-content/uploads/sites/3/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/megamarket-elementor/wp-content/uploads/sites/4/',
				'gutenberg' => 'https://dummy.xtemos.com/woodmart2/megamarket-gutenberg/wp-content/uploads/sites/25/',
			),
		],
		'accessories_base'      => [
			'title'   => 'Base content accessories (required)',
			'process' => 'xml,xml_images,widgets,options,headers',
			'type'    => 'base',
			'links'   => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/accessories/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/accessories-elementor/',
				'gutenberg' => 'https://dummy.xtemos.com/woodmart2/accessories-gutenberg/',
			),
			'uploads_link'  => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/accessories/wp-content/uploads/sites/5/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/accessories-elementor/wp-content/uploads/sites/6/',
				'gutenberg' => 'https://dummy.xtemos.com/woodmart2/accessories-gutenberg/wp-content/uploads/sites/26/',
			),
		],
		'mega-electronics_base' => [
			'title'   => 'Base content mega electronics (required)',
			'process' => 'xml,xml_images,widgets,options,headers',
			'type'    => 'base',
			'links'   => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/mega-electronics/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/mega-electronics-elementor/',
				'gutenberg' => 'https://dummy.xtemos.com/woodmart2/mega-electronics-gutenberg/',
			),
			'uploads_link'  => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/mega-electronics/wp-content/uploads/sites/7/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/mega-electronics-elementor/wp-content/uploads/sites/8/',
				'gutenberg' => 'https://dummy.xtemos.com/woodmart2/mega-electronics-gutenberg/wp-content/uploads/sites/27/',
			),
		],
		'furniture2_base' => [
			'title'   => 'Base content furniture 2 (required)',
			'process' => 'xml,xml_images,widgets,options,headers',
			'type'    => 'base',
			'links'   => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/furniture2/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/furniture2-elementor/',
				'gutenberg' => 'https://dummy.xtemos.com/woodmart2/furniture2-gutenberg/',
			),
			'uploads_link'  => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/furniture2/wp-content/uploads/sites/9/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/furniture2-elementor/wp-content/uploads/sites/10/',
				'gutenberg' => 'https://dummy.xtemos.com/woodmart2/furniture2-gutenberg/wp-content/uploads/sites/28/',
			),
		],
		'plants_base' => [
			'title'   => 'Base content plants (required)',
			'process' => 'xml,xml_images,widgets,options,headers',
			'type'    => 'base',
			'links'   => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/plants/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/plants-elementor/',
				'gutenberg' => 'https://dummy.xtemos.com/woodmart2/plants-gutenberg/',
			),
			'uploads_link'  => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/plants/wp-content/uploads/sites/11/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/plants-elementor/wp-content/uploads/sites/12/',
				'gutenberg' => 'https://dummy.xtemos.com/woodmart2/plants-gutenberg/wp-content/uploads/sites/29/',
			),
		],
		'kids_base' => [
			'title'   => 'Base content kids (required)',
			'process' => 'xml,xml_images,widgets,options,headers',
			'type'    => 'base',
			'links'   => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/kids/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/kids-elementor/',
				'gutenberg' => 'https://dummy.xtemos.com/woodmart2/kids-gutenberg/',
			),
			'uploads_link'  => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/kids/wp-content/uploads/sites/13/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/kids-elementor/wp-content/uploads/sites/14/',
				'gutenberg' => 'https://dummy.xtemos.com/woodmart2/kids-gutenberg/wp-content/uploads/sites/30/',
			),
		],
		'games_base-light' => [
			'title'   => 'Base content games-light (required)',
			'process' => 'xml,xml_images,widgets,options,headers',
			'type'    => 'base',
			'links'   => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/games/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/games-elementor/',
			),
			'uploads_link'  => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/games/wp-content/uploads/sites/15/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/games-elementor/wp-content/uploads/sites/16/',
				'gutenberg' => 'https://dummy.xtemos.com/woodmart2/games-gutenberg/wp-content/uploads/sites/31/',
			),
		],
		'games_base-dark' => [
			'title'   => 'Base content games-dark (required)',
			'process' => 'xml,xml_images,widgets,options,headers',
			'type'    => 'base',
			'links'   => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/games/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/games-elementor/',
			),
			'uploads_link'  => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/games/wp-content/uploads/sites/15/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/games-elementor/wp-content/uploads/sites/16/',
				'gutenberg' => 'https://dummy.xtemos.com/woodmart2/games-gutenberg/wp-content/uploads/sites/31/',
			),
		],
		'organic-farm_base' => [
			'title'   => 'Base content organic-farm (required)',
			'process' => 'xml,xml_images,widgets,options,headers',
			'type'    => 'base',
			'links'   => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/farm/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/farm-elementor/',
				'gutenberg' => 'https://dummy.xtemos.com/woodmart2/farm-gutenberg/',
			),
			'uploads_link'  => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/farm/wp-content/uploads/sites/20/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/farm-elementor/wp-content/uploads/sites/22/',
				'gutenberg' => 'https://dummy.xtemos.com/woodmart2/farm-gutenberg/wp-content/uploads/sites/32/',
			),
		],
		'pills_base' => [
			'title'   => 'Base content pills (required)',
			'process' => 'xml,xml_images,widgets,options,headers',
			'type'    => 'base',
			'links'   => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/pills/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/pills-elementor/',
				'gutenberg' => 'https://dummy.xtemos.com/woodmart2/pills-gutenberg/',
			),
			'uploads_link'  => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/pills/wp-content/uploads/sites/21/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/pills-elementor/wp-content/uploads/sites/23/',
				'gutenberg' => 'https://dummy.xtemos.com/woodmart2/pills-gutenberg/wp-content/uploads/sites/33/',
			),
		],
		'pottery_base' => [
			'title'   => 'Base content pottery (required)',
			'process' => 'xml,xml_images,widgets,options,headers',
			'type'    => 'base',
			'links'   => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/pottery/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/pottery-elementor/',
				'gutenberg' => 'https://dummy.xtemos.com/woodmart2/pottery-gutenberg/',
			),
			'uploads_link'  => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/pottery/wp-content/uploads/sites/35/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/pottery-elementor/wp-content/uploads/sites/34/',
				'gutenberg' => 'https://dummy.xtemos.com/woodmart2/pottery-gutenberg/wp-content/uploads/sites/36/',
			),
		],
		'vegetables_base' => [
			'title'   => 'Base content vegetables (required)',
			'process' => 'xml,xml_images,widgets,options,headers',
			'type'    => 'base',
			'links'   => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/vegetables/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/vegetables-elementor/',
				'gutenberg' => 'https://dummy.xtemos.com/woodmart2/vegetables-gutenberg/',
			),
			'uploads_link'  => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/vegetables/wp-content/uploads/sites/18/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/vegetables-elementor/wp-content/uploads/sites/19/',
				'gutenberg' => 'https://dummy.xtemos.com/woodmart2/vegetables-gutenberg/wp-content/uploads/sites/37/',
			),
		],
		'makeup_base' => [
			'title'   => 'Base content makeup (required)',
			'process' => 'xml,xml_images,widgets,options,headers',
			'type'    => 'base',
			'links'   => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/makeup/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/makeup-elementor/',
				'gutenberg' => 'https://dummy.xtemos.com/woodmart2/makeup-gutenberg/',
			),
			'uploads_link'  => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/makeup/wp-content/uploads/sites/38/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/makeup-elementor/wp-content/uploads/sites/39/',
				'gutenberg' => 'https://dummy.xtemos.com/woodmart2/makeup-gutenberg/wp-content/uploads/sites/40/',
			),
		],
		'marketplace2_base' => [
			'title'   => 'Base content marketplace2 (required)',
			'process' => 'xml,xml_images,widgets,options,headers',
			'type'    => 'base',
			'links'   => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/marketplace2/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/marketplace2-elementor/',
				'gutenberg' => 'https://dummy.xtemos.com/woodmart2/marketplace2-gutenberg/',
			),
			'uploads_link'  => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/marketplace2/wp-content/uploads/sites/41/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/marketplace2-elementor/wp-content/uploads/sites/42/',
				'gutenberg' => 'https://dummy.xtemos.com/woodmart2/marketplace2-gutenberg/wp-content/uploads/sites/43/',
			),
		],
		't-shirts_base' => [
			'title'   => 'Base content t-shirts (required)',
			'process' => 'xml,xml_images,widgets,options,headers',
			'type'    => 'base',
			'links'   => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/t-shirts/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/t-shirts-elementor/',
				'gutenberg' => 'https://dummy.xtemos.com/woodmart2/t-shirts-gutenberg/',
			),
			'uploads_link'  => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/t-shirts/wp-content/uploads/sites/44/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/t-shirts-elementor/wp-content/uploads/sites/45/',
				'gutenberg' => 'https://dummy.xtemos.com/woodmart2/t-shirts-gutenberg/wp-content/uploads/sites/46/',
			),
		],
		'handmade-bags_base' => [
			'title'   => 'Base content handmade-bags (required)',
			'process' => 'xml,xml_images,widgets,options,headers',
			'type'    => 'base',
			'links'   => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/handmade-bags/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/handmade-bags-elementor/',
				'gutenberg' => 'https://dummy.xtemos.com/woodmart2/handmade-bags-gutenberg/',
			),
			'uploads_link' => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/handmade-bags/wp-content/uploads/sites/47/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/handmade-bags-elementor/wp-content/uploads/sites/48/',
				'gutenberg' => 'https://dummy.xtemos.com/woodmart2/handmade-bags-gutenberg/wp-content/uploads/sites/49/',
			),
		],
		'vinyls_base' => [
			'title'   => 'Base content vinyls (required)',
			'process' => 'xml,xml_images,widgets,options,headers',
			'type'    => 'base',
			'links'   => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/vinyls/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/vinyls-elementor/',
				'gutenberg' => 'https://dummy.xtemos.com/woodmart2/vinyls-gutenberg/',
			),
			'uploads_link'  => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/vinyls/wp-content/uploads/sites/50/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/vinyls-elementor/wp-content/uploads/sites/51/',
				'gutenberg' => 'https://dummy.xtemos.com/woodmart2/vinyls-gutenberg/wp-content/uploads/sites/52/',
			),
		],
		'pets_base' => [
			'title'   => 'Base content pets (required)',
			'process' => 'xml,xml_images,widgets,options,headers',
			'type'    => 'base',
			'links'   => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/pets/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/pets-elementor/',
				'gutenberg' => 'https://dummy.xtemos.com/woodmart2/pets-gutenberg/',
			),
			'uploads_link'  => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/pets/wp-content/uploads/sites/53/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/pets-elementor/wp-content/uploads/sites/54/',
				'gutenberg' => 'https://dummy.xtemos.com/woodmart2/pets-gutenberg/wp-content/uploads/sites/55/',
			),
		],
		'christmas-2_base' => [
			'title'   => 'Base content christmas-2 (required)',
			'process' => 'xml,xml_images,widgets,options,headers',
			'type'    => 'base',
			'links'   => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/christmas-2/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/christmas-2-elementor/',
				'gutenberg' => 'https://dummy.xtemos.com/woodmart2/christmas-2-gutenberg/',
			),
			'uploads_link'  => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/christmas-2/wp-content/uploads/sites/59/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/christmas-2-elementor/wp-content/uploads/sites/60/',
				'gutenberg' => 'https://dummy.xtemos.com/woodmart2/christmas-2-gutenberg/wp-content/uploads/sites/61/',
			),
		],
		'merchandise_base' => [
			'title'   => 'Base content merchandise (required)',
			'process' => 'xml,xml_images,widgets,options,headers',
			'type'    => 'base',
			'links'   => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/merchandise/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/merchandise-elementor/',
				'gutenberg' => 'https://dummy.xtemos.com/woodmart2/merchandise-gutenberg/',
			),
			'uploads_link'  => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/merchandise/wp-content/uploads/sites/62/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/merchandise-elementor/wp-content/uploads/sites/64/',
				'gutenberg' => 'https://dummy.xtemos.com/woodmart2/merchandise-gutenberg/wp-content/uploads/sites/63/',
			),
		],
		'perfumes_base' => [
			'title'   => 'Base content perfumes (required)',
			'process' => 'xml,xml_images,widgets,options,headers',
			'type'    => 'base',
			'links'   => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/perfumes/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/perfumes-elementor/',
				'gutenberg' => 'https://dummy.xtemos.com/woodmart2/perfumes-gutenberg/',
			),
			'uploads_link'  => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/perfumes/wp-content/uploads/sites/65/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/perfumes-elementor/wp-content/uploads/sites/66/',
				'gutenberg' => 'https://dummy.xtemos.com/woodmart2/perfumes-gutenberg/wp-content/uploads/sites/67/',
			),
		],
		'fashion-2_base' => [
			'title'   => 'Base content fashion-2 (required)',
			'process' => 'xml,xml_images,widgets,options,headers',
			'type'    => 'base',
			'links'   => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/fashion-2/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/fashion-2-elementor/',
				'gutenberg' => 'https://dummy.xtemos.com/woodmart2/fashion-2-gutenberg/',
			),
			'uploads_link'  => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/fashion-2/wp-content/uploads/sites/68/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/fashion-2-elementor/wp-content/uploads/sites/69/',
				'gutenberg' => 'https://dummy.xtemos.com/woodmart2/fashion-2-gutenberg/wp-content/uploads/sites/70/',
			),
		],
		'electronics-3_base' => [
			'title'   => 'Base content electronics-3 (required)',
			'process' => 'xml,xml_images,widgets,options,headers',
			'type'    => 'base',
			'links'   => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/electronics-3/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/electronics-3-elementor/',
				'gutenberg' => 'https://dummy.xtemos.com/woodmart2/electronics-3-gutenberg/',
			),
			'uploads_link'  => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/electronics-3/wp-content/uploads/sites/56/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/electronics-3-elementor/wp-content/uploads/sites/57/',
				'gutenberg' => 'https://dummy.xtemos.com/woodmart2/electronics-3-gutenberg/wp-content/uploads/sites/58/',
			),
		],
		'keyboards_base' => [
			'title'   => 'Base content keyboards (required)',
			'process' => 'xml,xml_images,widgets,options,headers',
			'type'    => 'base',
			'links'   => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/keyboards/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/keyboards-elementor/',
				'gutenberg' => 'https://dummy.xtemos.com/woodmart2/keyboards-gutenberg/',
			),
			'uploads_link'  => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/keyboards/wp-content/uploads/sites/72/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/keyboards-elementor/wp-content/uploads/sites/71/',
				'gutenberg' => 'https://dummy.xtemos.com/woodmart2/keyboards-gutenberg/wp-content/uploads/sites/73/',
			),
		],
		'edc_base' => [
			'title'   => 'Base content edc (required)',
			'process' => 'xml,xml_images,widgets,options,headers',
			'type'    => 'base',
			'links'   => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/edc/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/edc-elementor/',
				'gutenberg' => 'https://dummy.xtemos.com/woodmart2/edc-gutenberg/',
			),
			'uploads_link'  => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/edc/wp-content/uploads/sites/75/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/edc-elementor/wp-content/uploads/sites/74/',
				'gutenberg' => 'https://dummy.xtemos.com/woodmart2/edc-gutenberg/wp-content/uploads/sites/76/',
			),
		],
		'jewellery-2_base' => [
			'title'   => 'Base content jewellery-2 (required)',
			'process' => 'xml,xml_images,widgets,options,headers',
			'type'    => 'base',
			'links'   => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/jewellery-2/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/jewellery-2-elementor/',
				'gutenberg' => 'https://dummy.xtemos.com/woodmart2/jewellery-2-gutenberg/',
			),
			'uploads_link'  => array(
				'wpb'       => 'https://dummy.xtemos.com/woodmart2/jewellery-2/wp-content/uploads/sites/78/',
				'elementor' => 'https://dummy.xtemos.com/woodmart2/jewellery-2-elementor/wp-content/uploads/sites/77/',
				'gutenberg' => 'https://dummy.xtemos.com/woodmart2/jewellery-2-gutenberg/wp-content/uploads/sites/79/',
			),
		],
		
		/**
		 * Pages.
		 */
		'contact-us'            => [
			'title'   => 'Contact Us',
			'process' => 'xml',
			'type'    => 'page',
			'categories' => [
				[
					'name' => 'Contact',
					'slug' => 'contact',
				],
			],
		],
		'contact-us-2'          => [
			'title'   => 'Contact Us 2',
			'process' => 'xml',
			'type'    => 'page',
			'categories' => [
				[
					'name' => 'Contact',
					'slug' => 'contact',
				],
			],
		],
		'contact-us-3'          => [
			'title'   => 'Contact Us 3',
			'process' => 'xml',
			'type'    => 'page',
			'categories' => [
				[
					'name' => 'Contact',
					'slug' => 'contact',
				],
			],
		],
		'contact-us-4'          => [
			'title'   => 'Contact Us 4',
			'process' => 'xml',
			'type'    => 'page',
			'categories' => [
				[
					'name' => 'Contact',
					'slug' => 'contact',
				],
			],
		],
		'about-us'              => [
			'title'   => 'Old About Us',
			'process' => 'xml',
			'type'    => 'page',
			'categories' => [
				[
					'name' => 'About',
					'slug' => 'about',
				],
			],
		],
		'about-us-2'            => [
			'title'      => 'Old About Us 2',
			'process'    => 'xml',
			'type'       => 'page',
			'gutenberg'  => false,
			'categories' => [
				[
					'name' => 'About',
					'slug' => 'about',
				],
			],
		],
		'about-us-3'            => [
			'title'   => 'About Us',
			'process' => 'xml',
			'type'    => 'page',
			'categories' => [
				[
					'name' => 'About',
					'slug' => 'about',
				],
			],
		],
		'about-us-4'            => [
			'title'   => 'About Us 2',
			'process' => 'xml,headers',
			'type'    => 'page',
			'categories' => [
				[
					'name' => 'About',
					'slug' => 'about',
				],
			],
		],
		'about-me'              => [
			'title'   => 'Old About Me',
			'process' => 'xml',
			'type'    => 'page',
			'categories' => [
				[
					'name' => 'About',
					'slug' => 'about',
				],
			],
		],
		'about-me-2'            => [
			'title'   => 'About Me',
			'process' => 'xml,headers',
			'type'    => 'page',
			'categories' => [
				[
					'name' => 'About',
					'slug' => 'about',
				],
			],
		],
		'about-factory'         => [
			'title'   => 'About Factory',
			'process' => 'xml',
			'type'    => 'page',
			'link'    => 'https://woodmart.xtemos.com/handmade/about-factory/',
			'categories' => [
				[
					'name' => 'About',
					'slug' => 'about',
				],
			],
		],
		'our-team'              => [
			'title'   => 'Old Our Team',
			'process' => 'xml',
			'type'    => 'page',
			'categories' => [
				[
					'name' => 'Team',
					'slug' => 'team',
				],
			],
		],
		'our-team-2'            => [
			'title'   => 'Our Team',
			'process' => 'xml',
			'type'    => 'page',
			'categories' => [
				[
					'name' => 'Team',
					'slug' => 'team',
				],
			],
		],
		'faqs'                  => [
			'title'   => 'FAQs',
			'process' => 'xml',
			'type'    => 'page',
			'categories' => [
				[
					'name' => 'FAQs',
					'slug' => 'faq',
				],
			],
		],
		'faqs-2'                => [
			'title'   => 'FAQs 2',
			'process' => 'xml',
			'type'    => 'page',
			'link'    => 'https://woodmart.xtemos.com/faqs-two/',
			'categories' => [
				[
					'name' => 'FAQs',
					'slug' => 'faq',
				],
			],
		],
		'custom-404'            => [
			'title'   => 'Custom-404',
			'process' => 'xml',
			'type'    => 'page',
			'link'    => 'https://woodmart.xtemos.com/custom-404-page/',
			'categories' => [
				[
					'name' => '404',
					'slug' => '404page',
				],
			],
		],
		'custom-404-2'          => [
			'title'   => 'Custom-404-2',
			'process' => 'xml',
			'type'    => 'page',
			'link'    => 'https://woodmart.xtemos.com/custom-404-page-2/',
			'categories' => [
				[
					'name' => '404',
					'slug' => '404page',
				],
			],
		],
		'christmas-maintenance' => [
			'title'   => 'Christmas maintenance',
			'process' => 'xml,options',
			'type'    => 'page',
			'categories' => [
				[
					'name' => 'Maintenance',
					'slug' => 'maintenance',
				],
			],
		],
		'maintenance'           => [
			'title'   => 'Maintenance',
			'process' => 'xml,options',
			'type'    => 'page',
			'categories' => [
				[
					'name' => 'Maintenance',
					'slug' => 'maintenance',
				],
			],
		],
		'maintenance-2'         => [
			'title'   => 'Maintenance 2',
			'process' => 'xml,options',
			'type'    => 'page',
			'categories' => [
				[
					'name' => 'Maintenance',
					'slug' => 'maintenance',
				],
			],
		],
		'maintenance-3'         => [
			'title'   => 'Maintenance 3',
			'process' => 'xml,options',
			'type'    => 'page',
			'categories' => [
				[
					'name' => 'Maintenance',
					'slug' => 'maintenance',
				],
			],
		],
		'custom-privacy-policy' => [
			'title'   => 'Custom Privacy Policy',
			'process' => 'xml',
			'type'    => 'page',
			'link'    => 'https://woodmart.xtemos.com/privacy-policy/',
		],
		'track-order'           => [
			'title'   => 'Track Order',
			'process' => 'xml',
			'type'    => 'page',
			'gutenberg'  => false,
		],

		/**
		 * Element.
		 */

		'product-filters'       => [
			'title'   => 'Product filters',
			'process' => 'xml',
			'type'    => 'element',
			'categories' => [
				[
					'name' => 'Element',
					'slug' => 'element',
				],
			],
		],
		'parallax-scrolling'    => [
			'title'   => 'Parallax scrolling',
			'process' => 'xml',
			'type'    => 'element',
			'categories' => [
				[
					'name' => 'Element',
					'slug' => 'element',
				],
			],
		],
		'animations'            => [
			'title'   => 'Animations',
			'process' => 'xml',
			'type'    => 'element',
			'categories' => [
				[
					'name' => 'Element',
					'slug' => 'element',
				],
			],
		],
		'sliders'               => [
			'title'   => 'Sliders',
			'process' => 'xml,wood_slider',
			'type'    => 'element',
			'categories' => [
				[
					'name' => 'Element',
					'slug' => 'element',
				],
			],
		],
		'image-hotspot'         => [
			'title'   => 'Image Hotspot',
			'process' => 'xml',
			'type'    => 'element',
			'categories' => [
				[
					'name' => 'Element',
					'slug' => 'element',
				],
			],
		],
		'list-element'          => [
			'title'   => 'List-element',
			'process' => 'xml',
			'type'    => 'element',
			'categories' => [
				[
					'name' => 'Element',
					'slug' => 'element',
				],
			],
		],
		'buttons'               => [
			'title'   => 'Buttons',
			'process' => 'xml',
			'type'    => 'element',
			'categories' => [
				[
					'name' => 'Element',
					'slug' => 'element',
				],
			],
		],
		'video-element'         => [
			'title'   => 'Video-element',
			'process' => 'xml',
			'type'    => 'element',
			'categories' => [
				[
					'name' => 'Element',
					'slug' => 'element',
				],
			],
		],
		'timeline'              => [
			'title'   => 'Timeline',
			'process' => 'xml',
			'type'    => 'element',
			'categories' => [
				[
					'name' => 'Element',
					'slug' => 'element',
				],
			],
		],
		'top-rated-products'    => [
			'title'   => 'Top Rated Products',
			'process' => 'xml',
			'type'    => 'element',
			'gutenberg'  => false,
			'categories' => [
				[
					'name' => 'Element',
					'slug' => 'element',
				],
			],
		],
		'sale-products'         => [
			'title'   => 'Sale Products',
			'process' => 'xml',
			'type'    => 'element',
			'gutenberg'  => false,
			'categories' => [
				[
					'name' => 'Element',
					'slug' => 'element',
				],
			],
		],
		'products-categories'   => [
			'title'   => 'Products Categories',
			'process' => 'xml',
			'type'    => 'element',
			'categories' => [
				[
					'name' => 'Element',
					'slug' => 'element',
				],
			],
		],
		'products-category'     => [
			'title'   => 'Products Category',
			'process' => 'xml',
			'type'    => 'element',
			'gutenberg'  => false,
			'categories' => [
				[
					'name' => 'Element',
					'slug' => 'element',
				],
			],
		],
		'products-by-id'        => [
			'title'   => 'Products by ID',
			'process' => 'xml',
			'type'    => 'element',
			'gutenberg'  => false,
			'categories' => [
				[
					'name' => 'Element',
					'slug' => 'element',
				],
			],
		],
		'featured-products'     => [
			'title'   => 'Featured Products',
			'process' => 'xml',
			'type'    => 'element',
			'gutenberg'  => false,
			'categories' => [
				[
					'name' => 'Element',
					'slug' => 'element',
				],
			],
		],
		'recent-products'       => [
			'title'   => 'Recent Products',
			'process' => 'xml',
			'type'    => 'element',
			'gutenberg'  => false,
			'categories' => [
				[
					'name' => 'Element',
					'slug' => 'element',
				],
			],
		],
		'gradients'             => [
			'title'   => 'Gradients',
			'process' => 'xml',
			'type'    => 'element',
			'categories' => [
				[
					'name' => 'Element',
					'slug' => 'element',
				],
			],
		],
		'section-dividers'      => [
			'title'   => 'Section Dividers',
			'process' => 'xml',
			'type'    => 'element',
			'categories' => [
				[
					'name' => 'Element',
					'slug' => 'element',
				],
			],
		],
		'brands-element'        => [
			'title'   => 'Brands Element',
			'process' => 'xml',
			'type'    => 'element',
			'categories' => [
				[
					'name' => 'Element',
					'slug' => 'element',
				],
			],
		],
		'button-with-popup'     => [
			'title'   => 'Button with popup',
			'process' => 'xml',
			'type'    => 'element',
			'categories' => [
				[
					'name' => 'Element',
					'slug' => 'element',
				],
			],
		],
		'ajax-products-tabs'    => [
			'title'   => 'AJAX products tabs',
			'process' => 'xml',
			'type'    => 'element',
			'categories' => [
				[
					'name' => 'Element',
					'slug' => 'element',
				],
			],
		],
		'animated-counter'      => [
			'title'   => 'Animated counter',
			'process' => 'xml',
			'type'    => 'element',
			'categories' => [
				[
					'name' => 'Element',
					'slug' => 'element',
				],
			],
		],
		'products-widgets'      => [
			'title'   => 'Products widgets',
			'process' => 'xml',
			'type'    => 'element',
			'categories' => [
				[
					'name' => 'Element',
					'slug' => 'element',
				],
			],
		],
		'products-grid'         => [
			'title'   => 'Products grid',
			'process' => 'xml',
			'type'    => 'element',
			'gutenberg'  => false,
			'categories' => [
				[
					'name' => 'Element',
					'slug' => 'element',
				],
			],
		],
		'blog-element'          => [
			'title'   => 'Blog element',
			'process' => 'xml',
			'type'    => 'element',
			'categories' => [
				[
					'name' => 'Element',
					'slug' => 'element',
				],
			],
		],
		'portfolio-element'     => [
			'title'   => 'Portfolio element',
			'process' => 'xml',
			'type'    => 'element',
			'categories' => [
				[
					'name' => 'Element',
					'slug' => 'element',
				],
			],
		],
		'menu-price'            => [
			'title'   => 'Menu price',
			'process' => 'xml',
			'type'    => 'element',
			'categories' => [
				[
					'name' => 'Element',
					'slug' => 'element',
				],
			],
		],
		'360-degree-view'       => [
			'title'   => '360 degree view',
			'process' => 'xml',
			'type'    => 'element',
			'categories' => [
				[
					'name' => 'Element',
					'slug' => 'element',
				],
			],
		],
		'countdown-timer'       => [
			'title'   => 'Countdown timer',
			'process' => 'xml',
			'type'    => 'element',
			'categories' => [
				[
					'name' => 'Element',
					'slug' => 'element',
				],
			],
		],
		'testimonials'          => [
			'title'   => 'Testimonials',
			'process' => 'xml',
			'type'    => 'element',
			'categories' => [
				[
					'name' => 'Element',
					'slug' => 'element',
				],
			],
		],
		'team-member'           => [
			'title'   => 'Team member',
			'process' => 'xml',
			'type'    => 'element',
			'categories' => [
				[
					'name' => 'Element',
					'slug' => 'element',
				],
			],
		],
		'social-buttons'        => [
			'title'   => 'Social Buttons',
			'process' => 'xml',
			'type'    => 'element',
			'categories' => [
				[
					'name' => 'Element',
					'slug' => 'element',
				],
			],
		],
		'instagram'             => [
			'title'   => 'Instagram',
			'process' => 'xml',
			'type'    => 'element',
			'categories' => [
				[
					'name' => 'Element',
					'slug' => 'element',
				],
			],
		],
		'google-maps'           => [
			'title'   => 'Google maps',
			'process' => 'xml',
			'type'    => 'element',
			'categories' => [
				[
					'name' => 'Element',
					'slug' => 'element',
				],
			],
		],
		'banners'               => [
			'title'   => 'Banners',
			'process' => 'xml',
			'type'    => 'element',
			'categories' => [
				[
					'name' => 'Element',
					'slug' => 'element',
				],
			],
		],
		'carousels'             => [
			'title'   => 'Carousels',
			'process' => 'xml',
			'type'    => 'element',
			'categories' => [
				[
					'name' => 'Element',
					'slug' => 'element',
				],
			],
		],
		'titles'                => [
			'title'   => 'Titles',
			'process' => 'xml',
			'type'    => 'element',
			'categories' => [
				[
					'name' => 'Element',
					'slug' => 'element',
				],
			],
		],
		'images-gallery'        => [
			'title'   => 'Images gallery',
			'process' => 'xml',
			'type'    => 'element',
			'categories' => [
				[
					'name' => 'Element',
					'slug' => 'element',
				],
			],
		],
		'pricing-tables'        => [
			'title'   => 'Pricing Tables',
			'process' => 'xml',
			'type'    => 'element',
			'categories' => [
				[
					'name' => 'Element',
					'slug' => 'element',
				],
			],
		],
		'infobox'               => [
			'title'   => 'Infobox',
			'process' => 'xml',
			'type'    => 'element',
			'categories' => [
				[
					'name' => 'Element',
					'slug' => 'element',
				],
			],
		],
	]
);
