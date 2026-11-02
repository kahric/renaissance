<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.6.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

/**
 * Hook: woocommerce_before_main_content.
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked woocommerce_breadcrumb - 20
 * @hooked WC_Structured_Data::generate_website_data() - 30
 */
do_action( 'woocommerce_before_main_content' );

/**
 * Hook: woocommerce_shop_loop_header.
 *
 * @since 8.6.0
 *
 * @hooked woocommerce_product_taxonomy_archive_header - 10
 */
do_action( 'woocommerce_shop_loop_header' );

if ( woocommerce_product_loop() ) {
    $term = get_queried_object();
    // Glavni shop je post-type arhiva (WP_Post_Type, nema term_id);
    // samo kategorije/oznake su WP_Term.
    $termId = ( $term instanceof WP_Term ) ? $term->term_id : 0;



    echo '<div class="mt-10 gap-10 flex flex-wrap xl:flex-nowrap">';
        echo '<div class="order-2 xl:order-1 w-full xl:w-9/12">';
        /**
         * Hook: woocommerce_before_shop_loop.
         *
         * @hooked woocommerce_output_all_notices - 10
         * @hooked woocommerce_result_count - 20
         * @hooked woocommerce_catalog_ordering - 30
         */
        do_action( 'woocommerce_before_shop_loop' );
        
        woocommerce_product_loop_start();
    
        if ( wc_get_loop_prop( 'total' ) ) {
            while ( have_posts() ) {
                the_post();
    
                /**
                 * Hook: woocommerce_shop_loop.
                 */
                do_action( 'woocommerce_shop_loop' );
    
                wc_get_template_part( 'content', 'product' );
            }
        }
    
        woocommerce_product_loop_end();
        do_action( 'woocommerce_after_shop_loop' );
        echo '</div>';
            

        echo '<div class="order-1 xl:order-2 w-full xl:w-3/12 flex flex-col">';

        // Opis kategorije: na mobitelu PRVI (prije filtera), na desktopu zadnji (ispod filtera).
        $custom_content = get_term_meta($termId, 'category_aside_text', true);
        if (!empty($custom_content)) {
            echo '<div class="custom-category-content order-1 xl:order-2 mb-10 xl:mb-0 xl:mt-8">' . wpautop($custom_content) . '</div>';
        }

        // Filteri: na mobitelu poslije opisa (tik iznad proizvoda/sorta), na desktopu gore.
        // Collapsible "Filteri" (collapsed po defaultu na mobitelu); na xl je panel uvijek otvoren.
        echo '<div class="order-2 xl:order-1">';
        echo '<button type="button" class="shop-filters-toggle" aria-controls="shop-filters" aria-expanded="false">'
            . '<span>Filteri</span>'
            . '<svg class="shop-filters-chevron" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>'
            . '</button>';
        echo '<div id="shop-filters" class="shop-filters shop-filters-panel space-y-6">'
            . the_theme_category_filter()
            . do_blocks( the_theme_shop_filters_markup() )
            . '</div>';
        echo '</div>';

        echo '</div>';

    echo '</div>';


    echo '<div class="xl:w-9/12">';
    $custom_content = get_term_meta($termId, 'category_below_text', true);
    if (!empty($custom_content)) {
        echo '<div class="custom-category-content mt-10">' . wpautop($custom_content) . '</div>';
    }
    echo '</div>';

	/**
	 * Hook: woocommerce_after_shop_loop.
	 *
	 * @hooked woocommerce_pagination - 10
	 */
} else {
	/**
	 * Hook: woocommerce_no_products_found.
	 *
	 * @hooked wc_no_products_found - 10
	 */
	do_action( 'woocommerce_no_products_found' );
}

/**
 * Hook: woocommerce_after_main_content.
 *
 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action( 'woocommerce_after_main_content' );

/**
 * Hook: woocommerce_sidebar.
 *
 * @hooked woocommerce_get_sidebar - 10
 */
do_action( 'woocommerce_sidebar' );

get_footer( 'shop' );
