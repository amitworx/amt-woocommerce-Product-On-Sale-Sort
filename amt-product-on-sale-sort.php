<?php
/*
Plugin Name: Amt Product On Sale Sort
Description: This plugin adds option ' Sort by on sale ' to product sorting dropdown menu for woocommerce shop, and activates the default sorting for the products on an archive page for 'Sort by on sale'.
Author:      Amit Sharma
Author URI:  https://amitsharma.dev/
Version:     1.0
License:     GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.txt

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version
2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
with this program. If not, visit: https://www.gnu.org/licenses/
*/


// adding option ' Sort by on sale ' to sorting dropdown menu
add_filter( 'woocommerce_default_catalog_orderby_options', 'amt_catalog_orderby' );
add_filter( 'woocommerce_catalog_orderby', 'amt_catalog_orderby' );
function amt_catalog_orderby( $sortby ) {
    $sortby['on_sale'] = 'Sort by on sale';
    return $sortby;
}




/**
 * return default ordering of the products in catalog by 'on_sale'
 **/
add_filter('woocommerce_default_catalog_orderby', 'amt_default_catalog_orderby');

function amt_default_catalog_orderby() {
    return 'on_sale'; 
}



/**
 * WooCommerce Sales Sorting Filter
 */
add_filter( 'woocommerce_get_catalog_ordering_args', 'amt_get_catalog_ordering_args' );
function amt_get_catalog_ordering_args( $args ) {
	$orderby_value = isset( $_GET['orderby'] ) ? wc_clean( $_GET['orderby'] ) : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
	if ( 'on_sale' == $orderby_value ) {
      // get current categoy ID
       $current_category = get_queried_object();
       $Current_cat_id = $current_category->term_id;

       // querying product for current category ID and where meta_key _sale_price is > 0  
       $product_args = array(
            'post_type'             => 'product',
            'post_status'           => 'publish',
            'ignore_sticky_posts'   => 1,
            'posts_per_page'        => '-1',
            'tax_query'             => array(
                array(
                    'taxonomy'      => 'product_cat',
                    'field'         => 'term_id',
                    'terms'         => $Current_cat_id,
                    'operator'      => 'IN' 
                )
              
            ),
            'meta_query' => array(
                array(
                    'key'     => '_sale_price',
                    'value'   => 0,
                    'compare' => '>',
                )
            )
        );

        // initialize the counter for number of products in the current category which are on sale
        $i=0;
        $products = new WP_Query($product_args);
        while ($products->have_posts()) : 
            $products->the_post();
            $i++;
        endwhile;

        // modify the query if we have products on sale in a category
        if($i>0){
        $args['orderby'] = array(
            'meta_value_num' => 'DESC', 'title' => 'ASC'
           );
           $args['meta_key'] = '_sale_price';
        }
	}
	return $args;
}





