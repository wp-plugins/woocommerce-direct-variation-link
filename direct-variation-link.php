<?php
/*
Plugin Name: WooCommerce Direct Variation Link 
Plugin URI: http://www.wpbackoffice.com/plugins/woocommerce-direct-variation-link/
Description: Link directly to a specific WooCommerce product variation using get variables (yoursite.com/your-single-product?size=small&color=blue).
Version: 1.0.0
Author: J. Tyler Wiest
Author URI: http://www.wpbackoffice.com
*/ 

/**
* 	Output the variable product add to cart area.
*
*	@access public
* 	@subpackage  Product
* 	@return void
*/
if ( ! function_exists( 'woocommerce_variable_add_to_cart' ) ) {

	function woocommerce_variable_add_to_cart() {
		global $product; 
		
		// Enqueue variation scripts
		wp_enqueue_script( 'wc-add-to-cart-variation' );
		
		$varation_names = wpbo_get_variation_values();
		$start_vals = wpbo_get_variation_start_values( $varation_names );
				
		// If there are start values use them, otherwise use the default attribute function
		if ( $start_vals != null ) {
			woocommerce_get_template( 'single-product/add-to-cart/variable.php', array(
				'available_variations'  => $product->get_available_variations(),
				'attributes'            => $product->get_variation_attributes(),
				'selected_attributes'   => $start_vals
			) );
		} else {
			woocommerce_get_template( 'single-product/add-to-cart/variable.php', array(
				'available_variations'  => $product->get_available_variations(),
				'attributes'            => $product->get_variation_attributes(),
				'selected_attributes'   => $product->get_variation_default_attributes()
			) );
		}
	}
}

/*
*	Returns an array of variations related to a product
*
*	@access 		public 
*	@subpackage  	Product
*	@return array	variation_names
*
*/		
function wpbo_get_variation_values() {
	global $product;
	
	// Create an array of possible variations
	$available_variations = $product->get_variation_attributes();
	$varation_names = array();
	
	foreach ( $available_variations as $key => $variations ) {
		array_push( $varation_names, $key );
	}
	
	return $varation_names;
}

/*
*	Returns an array of variations related to a product
*
*	@access 		public 
*	@subpackage  	Product
*	@param	array	variation_names
*	@return array	start_vals
*
*/	
function wpbo_get_variation_start_values( $varation_names ) {
	global $product;

	$all_variations = $product->get_variation_attributes();
	$_GET_lower = array_change_key_case($_GET, CASE_LOWER);

	// Check to see if any of the attributes are in $_GET vars
	$start_vals = array();

	foreach ( $varation_names as $name ) {
		
		$lower_name = strtolower( $name );
		$flag = false;
		
		if ( isset( $_GET_lower[ $lower_name ] ) ) {
		
			foreach( $all_variations[ $name ] as $val ) {		
				if ( strtolower( $val ) == strtolower( $_GET_lower[ $lower_name ] ) ) {
					$flag = true;
				}			
			}

			if ( $flag == true ) {
				$start_vals[ $lower_name ] = $_GET_lower[ $lower_name ];
			}
		} 
	}
	
	return $start_vals;
}