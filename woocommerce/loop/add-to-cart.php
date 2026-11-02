<?php
/**
 * Loop Add to Cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/add-to-cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     9.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

$aria_describedby = isset( $args['aria-describedby_text'] ) ? sprintf( 'aria-describedby="woocommerce_loop_add_to_cart_link_describedby_%s"', esc_attr( $product->get_id() ) ) : '';

$image = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ), 'single-post-thumbnail' );
$image = $image[0] ?? false;

$attributes = array(
    'aria-label'       => $product->add_to_cart_description(),
    'data-quantity'    => '1',
    'data-product_id'  => $product->get_id(),
    'data-product_sku' => $product->get_sku(),
    'data-price'       => wc_get_price_to_display( $product ),
);

?>

<div class="product-card__cta-wrap">
    <button
            <?php echo $product->is_in_stock() ? '' : 'disabled="disabled"'; ?>
            <?php // add_to_cart_button + product_type_*: klase po kojima GTM4WP prepoznaje add_to_cart u listama (ne dodajemo ajax_add_to_cart — košaricu radi naš cart.js). ?>
            class="add-to-cart-btn product-card__cta add_to_cart_button product_type_<?php echo esc_attr( $product->get_type() ); ?> focus:outline-none focus:ring-0"
        <?php
        foreach ( $attributes as $attribute => $value ) {
            if ('' === $value) {
                continue;
            }

            echo $attribute . '="' . esc_attr( $value ) . '" ';
        }
        ?>
    >
        <?php if ($product->is_in_stock()): ?>
            <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 4h1.5L9 16m0 0h8m-8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm-8.5-3h9.25L19 7h-1M8 7h-.688M13 5v4m-2-2h4" />
            </svg>
            <span>Dodaj u košaricu</span>
        <?php else: ?>
            <span>Nema na stanju</span>
        <?php endif; ?>
    </button>
</div>