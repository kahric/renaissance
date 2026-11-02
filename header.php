<?php use Kahric\TheTheme\Config;

if (!defined('ABSPATH')) {
    exit;
}

$config = Config::get();

?>
    <!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width">
        <link rel="profile" href="http://gmpg.org/xfn/11">
        <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400;0,500;1,400&family=Inter:wght@400;500;600&display=swap"
              rel="stylesheet">

        <script>
            window.postUrl = '<?php echo esc_url(admin_url('admin-ajax.php')); ?>';
        </script>
        <?php wp_head(); ?>
    </head>

<body <?php body_class('bg-cream text-ink antialiased'); ?>>

<?php do_action('renaissance_site_before'); ?>

<div id="page" class="min-h-screen flex flex-col">

<?php do_action('renaissance_header'); ?>
    <header>
        <div class="mx-auto container px-10 xl:px-0">
            <div class="flex lg:justify-between flex-col lg:flex-row items-center gap-5 mt-5">
                <div class="flex justify-between items-center w-full gap-10">
                    <?php if (has_custom_logo()) { ?>
                        <?php the_custom_logo(); ?>
                    <?php } else { ?>
                        <a href="<?php echo get_bloginfo('url'); ?>" class="font-extrabold text-lg uppercase">
                            <?php echo get_bloginfo('name'); ?>
                        </a>

                        <p class="text-sm font-light text-ink/70">
                            <?php echo get_bloginfo('description'); ?>
                        </p>

                    <?php } ?>

                    <div class="lg:hidden flex gap-5">
                        <a href="<?php echo wc_get_cart_url(); ?>" aria-label="Go to the cart">
                            <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 4h1.5L9 16m0 0h8m-8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm-8.5-3h9.25L19 7h-1M8 7h-.688M13" />
                            </svg>
                        </a>
                        <a href="#" aria-label="Toggle navigation" id="primary-menu-toggle">
                            <svg viewBox="0 0 20 20" class="inline-block w-6 h-6" version="1.1"
                                 xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                <g stroke="none" stroke-width="1" fill="currentColor" fill-rule="evenodd">
                                    <g id="icon-shape">
                                        <path d="M0,3 L20,3 L20,5 L0,5 L0,3 Z M0,9 L20,9 L20,11 L0,11 L0,9 Z M0,15 L20,15 L20,17 L0,17 L0,15 Z"
                                              id="Combined-Shape"></path>
                                    </g>
                                </g>
                            </svg>
                        </a>
                    </div>
                </div>
                <div class="hidden lg:flex gap-5 items-center justify-center">
                    <?php get_product_search_form() ?>

                    <a href="<?php echo wc_get_cart_url(); ?>" aria-label="Go to the cart" class="btn-ghost">
                        <span class="flex gap-3 items-center justify-center">
                            <svg width="28px" height="28px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M4.78571 5H18.2251C19.5903 5 20.5542 6.33739 20.1225 7.63246L18.4558 12.6325C18.1836 13.4491 17.4193 14 16.5585 14H6.07142M4.78571 5L4.74531 4.71716C4.60455 3.73186 3.76071 3 2.76541 3H2M4.78571 5L6.07142 14M6.07142 14L6.25469 15.2828C6.39545 16.2681 7.23929 17 8.23459 17H17M17 17C15.8954 17 15 17.8954 15 19C15 20.1046 15.8954 21 17 21C18.1046 21 19 20.1046 19 19C19 17.8954 18.1046 17 17 17ZM11 19C11 20.1046 10.1046 21 9 21C7.89543 21 7 20.1046 7 19C7 17.8954 7.89543 17 9 17C10.1046 17 11 17.8954 11 19Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
                            Košarica 
                        </span>
                    </a>
                </div>
            </div>
        </div>

        <div class="mt-5 text-center text-sm bg-primary-100 text-ink tracking-wide py-2">
            <div class="container mx-auto px-10 lg:px-0">
                <?php echo Config::getAsString('home-notice') ?>
            </div>
        </div>
        <div class="container mx-auto px-10 lg:px-0">
            <nav class="w-full lg:block hidden" id="primary-menu">
                <div class="lg:hidden my-5">
                    <?php get_product_search_form() ?>
                </div>
                <div class="relative flex flex-col lg:flex-row justify-center !divide-primary/75 divide-y lg:divide-y-0 mb-5 lg:mb-0">

                    <?php

                    $menu = get_menu('primary');

                    echo getMenu($menu);

                    ?>
                </div>
            </nav>
        </div>

    </header>

    <div id="cart-message" role="status" aria-live="polite"
         class="hidden fixed z-90 bottom-5 inset-x-5 sm:inset-x-auto sm:right-5 sm:w-[22rem] rounded-lg border border-border bg-surface-raised p-4 shadow-[0_18px_45px_-20px_rgb(57_47_29_/0.28)]">
        <div class="flex items-start gap-3">
            <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-emerald-600">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 10.5 3.5 3.5L15 6.5"/></svg>
            </span>
            <div class="flex-1">
                <p class="font-serif text-base leading-snug text-ink">Proizvod dodan u košaricu</p>
                <div class="mt-3 flex items-center gap-4">
                    <a href="<?php echo wc_get_cart_url(); ?>"
                       class="inline-flex items-center rounded-md bg-gold px-4 py-2 text-sm font-medium tracking-wide text-white transition-colors hover:bg-primary-600">
                        Otvori košaricu
                    </a>
                    <button type="button" id="cart-message-close" class="text-sm text-ink/55 transition-colors hover:text-gold">Nastavi</button>
                </div>
            </div>
        </div>
    </div>

    <div id="content">
<?php do_action('renaissance_content_start'); ?>

    <main class="container mx-auto<?php echo !is_front_page() && !is_product() ? ' px-10' : ''; ?>">