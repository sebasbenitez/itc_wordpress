<?php

/**
 * Class Product_Attribute_Updater
 *
 * This class is responsible for updating product attributes based on changes in data.
 */
class Product_Attribute_Updater {
	/**
	 * Process attribute updates.
	 *
	 * This method processes attribute updates for a given set of data.
	 *
	 * @param array $data The data containing product attribute updates.
	 *
	 * @return array Updated data
	 */
	public static function processAttributeUpdates(array $data): array {
		foreach ($data as $product_id => $products_changes) {
			$product = wc_get_product($product_id);
			$attr_key_names = self::findProductAttributeChanges($products_changes);
			$data = self::handleAttributeChanges($product, $attr_key_names, $products_changes, $data, $product_id);
		}
		return $data;
	}
	
	/**
	 * Find product attribute changes.
	 *
	 * This method filters the keys in the given $products_changes array and returns
	 * an array containing only the keys that start with 'attribute_pa_'.
	 *
	 * @param array $products_changes The array containing product changes.
	 *
	 * @return array The array containing the product attribute changes.
	 */
	private static function findProductAttributeChanges(array $products_changes): array {
		return array_filter(array_keys($products_changes), function ($item) {
			return str_starts_with($item, 'attribute_pa_');
		});
	}
	
	/**
	 * Handle attribute changes.
	 *
	 * This method handles attribute changes for a given product.
	 *
	 * @param mixed $product The product to handle attribute changes for.
	 * @param array $attr_key_names The names of the attribute keys that have changes.
	 * @param array $products_changes The changes related to the attributes.
	 * @param array $data The overall data containing product attribute updates.
	 * @param mixed $product_id The ID of the product being updated.
	 *
	 * @return array Updated data
	 */
	private static function handleAttributeChanges($product, $attr_key_names, $products_changes, $data, $product_id): array {
		if (empty($attr_key_names)) {
			return $data;
		}
		
		$attributes = [];
		foreach ($attr_key_names as $attr_key_name) {
			list($attributes, $data) = self::handleAttributeChange($product, $attr_key_name, $products_changes, $data, $product_id, $attributes);
		}
		
		self::saveProductAttributesIfNotEmpty($product, $attributes, $product_id);
		return $data;
	}
	
	/**
	 * Handle attribute change.
	 *
	 * This method handles attribute change for a given product.
	 *
	 * @param object $product The product object.
	 * @param string $attr_key_name The attribute key name.
	 * @param array $products_changes The array containing product changes.
	 * @param array $data The data containing product attribute updates.
	 * @param int $product_id The product ID.
	 * @param array $attributes The attributes array.
	 *
	 * @return array Updated attributes and data
	 */
	private static function handleAttributeChange($product, $attr_key_name, $products_changes, $data, $product_id, $attributes): array {
		$values = $products_changes[$attr_key_name];
		$visible_fp = $products_changes[$attr_key_name.'_visiblefp'] ?? null;
		if (!isset($visible_fp)) {
			$attr_key_name_inside_product_object = preg_replace('/^attribute_/', '', $attr_key_name);
			if (
				isset($product->get_attributes()[$attr_key_name_inside_product_object]) &&
				$product->get_attributes()[$attr_key_name_inside_product_object] instanceof \WC_Product_Attribute
			) {
				$visible_fp = $product->get_attributes()[$attr_key_name_inside_product_object]->get_visible();
			}
		}
		$attr_save = new WC_Product_Attribute();
		$attr_txnms = wc_get_attribute_taxonomies();
		
		foreach ($attr_txnms as $attr) {
			if ($attr_key_name != 'attribute_pa_' . $attr->attribute_name) {
				continue;
			}
			$data = self::updateProductIfVariation($product, $values, $data, $product_id, $attr_key_name);
			$attributes = self::prepareAttributeToSave($values, $visible_fp, $attr->attribute_name, $attr->attribute_id, $attr_save, $attributes);
			unset($data[$product_id][$attr_key_name]);
		}
		return array($attributes, $data);
	}
	
	/**
	 * Update product if it's a variation.
	 *
	 * This method updates the product if it is a variation by checking if it is an instance of WC_Product_Variation.
	 * If it is, it updates the post meta with the attribute slug and removes the attribute from the data array to
	 * prevent its further update.
	 *
	 * @param WC_Product|WC_Product_Variation $product The product being updated.
	 * @param mixed $values The values of the attribute being updated.
	 * @param array $data The data containing the product attribute updates.
	 * @param int $product_id The ID of the product being updated.
	 * @param string $attr_key_name The name of the attribute being updated.
	 *
	 * @return array Updated data
	 */
	private static function updateProductIfVariation($product, $values, $data, $product_id, $attr_key_name): array {
		if ($product instanceof WC_Product_Variation) {
			$term = get_term($values);
			if (!is_wp_error($term) && !empty($term)) {
				update_post_meta($product_id, $attr_key_name, $term->slug);
				unset($data[$product_id][$attr_key_name]);
			}
		}
		return $data;
	}
	
	/**
	 * Prepares the attribute to be saved.
	 *
	 * @param string $values The attribute values separated by commas.
	 * @param bool|null $visible_fp The visibility status of the attribute.
	 * @param string $attribute_name The name of the attribute.
	 * @param int $attribute_id The ID of the attribute.
	 * @param object $attr_save The attribute object.
	 * @param array $attributes The current attributes array.
	 *
	 * @return array The updated attributes array.
	 */
	private static function prepareAttributeToSave($values, $visible_fp, $attribute_name, $attribute_id, $attr_save, $attributes): array {
		$attr_save->set_id($attribute_id);
		$attr_save->set_name('pa_' . $attribute_name);
		$values = !empty($values) ? explode(',', $values) : [];
		$int_values = [];
		foreach ($values as $val) {
			$int_values[] = intval($val);
		}
		$attr_save->set_options($int_values);
		$attr_save->set_position(count($attributes));
		if (isset($visible_fp)) {
			$attr_save->set_visible($visible_fp);
		}
		$attributes['pa_' . $attribute_name] = $attr_save;
		return $attributes;
	}
	
	/**
	 * Saves the product attributes if they are not empty and the product is not a variation.
	 *
	 * @param object $product The product object.
	 * @param array $attributes The attributes to be saved.
	 * @param int $product_id The ID of the product.
	 *
	 * @return void
	 */
	private static function saveProductAttributesIfNotEmpty($product, $attributes, $product_id): void {
		if (empty($attributes) || $product instanceof WC_Product_Variation) {
			return;
		}
		$old_attributes = $product->get_attributes();
		$attributes_update = array_merge($old_attributes, $attributes);
		$product->set_attributes($attributes_update);
		$product->save();
		wc_delete_product_transients($product_id);
		
		if ($product->get_parent_id('edit')) {
			wc_delete_product_transients($product->get_parent_id('edit'));
			WC_Cache_Helper::invalidate_cache_group('product_' . $product->get_parent_id('edit'));
		}
		
		WC_Cache_Helper::invalidate_attribute_count(array_keys($product->get_attributes()));
		WC_Cache_Helper::invalidate_cache_group('product_' . $product->get_id());
		flush_rewrite_rules();
	}
}
