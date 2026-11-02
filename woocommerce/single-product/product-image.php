<?php
/**
 * Single Product Image
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-image.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.0.0
 */

defined( 'ABSPATH' ) || exit;

// Note: `wc_get_gallery_image_html` was added in WC 3.3.2 and did not exist prior. This check protects against theme overrides being used on older versions of WC.
if ( ! function_exists( 'wc_get_gallery_image_html' ) ) {
	return;
}

global $product;

$columns           = apply_filters( 'woocommerce_product_thumbnails_columns', 4 );
$post_thumbnail_id = $product->get_image_id();
$wrapper_classes   = apply_filters(
	'woocommerce_single_product_image_gallery_classes',
	array(
		'woocommerce-product-gallery',
		'woocommerce-product-gallery--' . ( $post_thumbnail_id ? 'with-images' : 'without-images' ),
		'woocommerce-product-gallery--columns-' . absint( $columns ),
		'images',
	)
);
?>
<div style="--swiper-navigation-color: #fff; --swiper-pagination-color: #fff;">
    <?php
    $galleryImageIds = [(int) $post_thumbnail_id, ...$product->get_gallery_image_ids()];
    
     $galleryImages = array_map(
        static fn (int $id): array|false => wp_get_attachment_image_src($id, 'large'),
        $galleryImageIds,
    );

    $galleryImages = array_map(
        static fn (array $image): string => $image[0],
        array_filter($galleryImages),
    );

    $galleryThumbnails = array_map(
        static fn (int $id): array|false => wp_get_attachment_image_src( $id ),
        $galleryImageIds,
    );

    $galleryThumbnails = array_map(
        static fn (array $image): string => $image[0],
        array_filter($galleryThumbnails),
    );


    $countOfImages = count($galleryImages);

    

    $gallerySlideTemplate = <<<HTML
  <div class="swiper-slide">
    <img src="%s" class="aspect-square object-contain w-full" alt="TODO">
  </div>
HTML;

    $thumbSlideTemplate = <<<HTML
  <div class="swiper-slide">
    <img src="%s" class="w-full" alt="TODO">
  </div>
HTML;
    $toGallerySlideTemplate = static fn (string $url): string => sprintf($gallerySlideTemplate, $url);
    $toThumbSlideTemplate = static fn (string $url): string => sprintf($thumbSlideTemplate, $url);

    $galleryImagesHtml = array_map($toGallerySlideTemplate, $galleryImages);
    $galleryThumbnailsHtml = array_map($toThumbSlideTemplate, $galleryThumbnails);

    if ($countOfImages > 1) {
    ?>
    <div class="swiper mySwiper2">
        <div class="swiper-wrapper">
            <?php echo implode('', $galleryImagesHtml); ?>
        </div>
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
    </div>
    <div class="swiper mySwiper mt-10">
        <div class="swiper-wrapper">
            <?php echo implode('', $galleryThumbnailsHtml); ?>
        </div>
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
    </div>
    <?php
    } elseif ($countOfImages === 1) {
        echo sprintf('<img src="%s" class="aspect-square object-contain w-full" alt="' . $product->get_title() . '">', $galleryImages[0]);
    } else {
        echo sprintf('<img src="%s" class="aspect-square object-contain w-full" alt="">', wc_placeholder_img_src());
    }
    ?>
</div>