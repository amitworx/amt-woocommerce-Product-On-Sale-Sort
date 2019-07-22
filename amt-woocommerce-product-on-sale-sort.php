<?php
/*
Plugin Name: Amt Product On Sale Sort
Description: This plugin adds option ' Sort by on sale ' to product sorting dropdown menu for woocommerce shop, and activates the default sorting for the products on an archive page for 'Sort by on sale'.
Author:      Amit Sharma
Author URI:  https://amitsharma.dev/
Version:     1.1
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
    // die('default sorting');
    return 'on_sale'; 
   
}



/**
 * WooCommerce Sales Sorting Filter
 */
add_filter( 'woocommerce_get_catalog_ordering_args', 'amt_get_catalog_ordering_args' );
function amt_get_catalog_ordering_args( $args ) {
	$orderby_value = isset( $_GET['orderby'] ) ? wc_clean( $_GET['orderby'] ) : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
	if ( 'on_sale' == $orderby_value ) {


        $current_category = get_queried_object();
        
        // var_dump($current_category);
       
        add_action( 'woocommerce_product_query', 'amt_on_sale_query' );

        function amt_on_sale_query( $q ){
            $meta_query = $q->get( 'meta_query' );
            $meta_query = array( 
                'relation' => 'OR',
                array(
                    'key' => '_sale_price',
                    'compare' => 'NOT EXISTS'
                ),
                array(
                    'relation' => 'OR',
                    array(
                        'key' => '_sale_price',
                        'value'=>array(''),
                        'compare' => 'IN'
                    ),
                    array(
                        'key' => '_sale_price',
                        'value'=>0,
                        'compare' => '>=',
                        'type'   => 'numeric',
                    )
                ),
            );
           
            $q->set('meta_query', $meta_query );
            $q->set('orderby', array( 'meta_value' => 'DESC', 'date' => 'ASC' ));
 
        }

    }
}





