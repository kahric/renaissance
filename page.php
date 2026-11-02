<?php
/**
 * Default template for static pages (legal / informational content).
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Checkout/cart blokovi trebaju širinu da prijeđu u 2 stupca (WC "is-large" ≥ 782px);
// pravne/tekstualne stranice ostaju uske radi čitljivosti.
$isWidePage = function_exists('is_checkout') && (is_checkout() || is_cart());
$contentWidth = $isWidePage ? 'max-w-6xl' : 'max-w-2xl';
// Cart/checkout blokovi nose svoj naslov; na ostalim stranicama prikaži naslov stranice.
$showTitle = !$isWidePage;

?>

<article class="container mx-auto px-10 lg:px-0 py-14 lg:py-20">
    <div class="<?php echo esc_attr($contentWidth); ?> mx-auto">
        <?php
        while (have_posts()) :
            the_post();
            if ($showTitle) :
                ?>
                <header class="mb-10 lg:mb-12">
                    <h1 class="title-xl"><?php the_title(); ?></h1>
                    <span class="mt-5 block h-px w-12 bg-gold"></span>
                </header>
            <?php endif; ?>
            <div class="entry-content"><?php the_content(); ?></div>
            <?php
        endwhile;
        ?>
    </div>
</article>

<?php
get_footer();
