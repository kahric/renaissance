<?php

use JetBrains\PhpStorm\NoReturn;
use Kahric\TheTheme\Config;
use Kahric\TheTheme\Form\Form;
use Kahric\TheTheme\Form\FormField;
use Kahric\TheTheme\Form\FormFieldType;
use Kahric\TheTheme\Form\FormSectionType;
use Kahric\TheTheme\Helper\TemplateRenderer;
use Kahric\TheTheme\Mailchimp\Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

if (!defined('ABSPATH')) {
    exit;
}

require_once(__DIR__ . '/vendor/autoload.php');
require_once(__DIR__ . '/version.php');

/**
 * Theme setup.
 */
function renaissance_setup() {
	add_theme_support( 'title-tag' );
    add_theme_support( 'woocommerce' );

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'renaissance' ),
			'footer' => __( 'Footer Menu (Kupnja)', 'renaissance' ),
			'footer-legal' => __( 'Footer Menu (Pravne informacije)', 'renaissance' ),
		)
	);

	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		)
	);

    add_theme_support( 'custom-logo' );
	add_theme_support( 'post-thumbnails' );

	add_theme_support( 'align-wide' );
	add_theme_support( 'wp-block-styles' );

	add_theme_support( 'responsive-embeds' );
}

add_action( 'after_setup_theme', 'renaissance_setup' );

function modify_homepage_query_for_products( WP_Query $query ) {
	// Ensure this only runs on the main query for the homepage
	if ( $query->is_home() && $query->is_main_query() ) {
		// Set parameters for WooCommerce products
		$query->set( 'post_type', 'product' ); // Query WooCommerce products
		$query->set( 'meta_query', array(
			array(
				'key'     => '_stock_status', // Check the stock status
				'value'   => 'instock', // Only include products that are in stock
				'compare' => '=', // Compare the stock status value
			),
		));
		$query->set( 'orderby', 'date' ); // Order by the latest products
		$query->set( 'order', 'DESC' ); // Show the newest products first
	}
}
add_action( 'pre_get_posts', 'modify_homepage_query_for_products', 100);

// Remove unnecessary scripts and styles.
// WooCommerce / search pages rely on WC + WP block runtime (e.g. the
// "Filter by Price" block, block cart/checkout), so leave their scripts intact;
// only slim down classic pages (home, static, blog) that never render blocks.
function my_theme_cleanup_scripts(): void {
	if (
		! function_exists('is_woocommerce')
		|| is_woocommerce()        // shop, product, product taxonomies
		|| is_cart()
		|| is_checkout()
		|| is_checkout_pay_page()
		|| is_account_page()
		|| is_search()
	) {
		return;
	}

	// WooCommerce block scripts
	wp_dequeue_script('wc-blocks');
	wp_dequeue_script('wc-blocks-registry');
	wp_dequeue_script('wc-blocks-middleware');
	wp_dequeue_script('wc-blocks-data');
	wp_dequeue_script('wc-cart-checkout');
	wp_dequeue_script('wc-cart-checkout-base');
	wp_dequeue_script('wc-settings');
	wp_dequeue_script('wc-types');
	wp_dequeue_script('price-format');
	wp_dequeue_script('wc-blocks-shared-context');
	wp_dequeue_script('wc-blocks-shared-hocs');

	// Disable WordPress core block scripts (if not needed)
	wp_deregister_script('wp-polyfill');
	wp_deregister_script('wp-hooks');
	wp_deregister_script('wp-i18n');
	wp_deregister_script('wp-compose');
	wp_deregister_script('wp-element');
	wp_deregister_script('wp-api-fetch');
	wp_deregister_script('wp-data');
	wp_deregister_script('wp-url');
	wp_deregister_script('wp-plugins');
	wp_deregister_script('wp-notices');
	wp_deregister_script('wp-dom');

	// Deregister React and ReactDOM if not needed
	wp_deregister_script('react');
	wp_deregister_script('react-dom');
}

// Optional: Disable WooCommerce block styles completely
function disable_woocommerce_blocks_styles(): void {
    if (is_account_page()) {
        return;
    }

	wp_dequeue_style('global-styles');
	// Classic WooCommerce styles (theme provides its own).
	wp_dequeue_style('woocommerce-general');
	wp_dequeue_style('woocommerce-layout');
	wp_dequeue_style('woocommerce-smallscreen');

	// Keep WC block styles where interactive blocks render (filters, all-products);
	// the price-filter slider needs them. Strip on classic pages.
	if (! (function_exists('is_woocommerce') && is_woocommerce())) {
		wp_dequeue_style('wc-blocks-style');
	}
}

add_action('wp_enqueue_scripts', 'my_theme_cleanup_scripts', 100);
add_action('wp_enqueue_scripts', 'disable_woocommerce_blocks_styles', 20);

/**
 * Enqueue theme assets.
 */
function renaissance_enqueue_scripts() {
	$theme = wp_get_theme();

	wp_enqueue_style( 'the_theme', renaissance_asset( 'build/app.css' ), array(), $theme->get( 'Version' ) );
	wp_enqueue_script( 'the_theme', renaissance_asset( 'build/app.min.js' ), array(), $theme->get( 'Version' ) );
}

add_action( 'wp_enqueue_scripts', 'renaissance_enqueue_scripts' );

/**
 * Get asset path.
 *
 * @param string  $path Path to asset.
 *
 * @return string
 */
function renaissance_asset( $path ) {
	if ( wp_get_environment_type() === 'production' ) {
		return get_stylesheet_directory_uri() . '/' . $path;
	}

	return add_query_arg( 'time', time(),  get_stylesheet_directory_uri() . '/' . $path );
}

/**
 * Adds option 'li_class' to 'wp_nav_menu'.
 *
 * @param string  $classes String of classes.
 * @param mixed   $item The current item.
 * @param WP_Term $args Holds the nav menu arguments.
 *
 * @return array
 */
function renaissance_nav_menu_add_li_class( $classes, $item, $args, $depth ) {
	if ( isset( $args->li_class ) ) {
		$classes[] = $args->li_class;
	}

	if ( isset( $args->{"li_class_$depth"} ) ) {
		$classes[] = $args->{"li_class_$depth"};
	}

	return $classes;
}

add_filter( 'nav_menu_css_class', 'renaissance_nav_menu_add_li_class', 10, 4 );

/**
 * Adds option 'submenu_class' to 'wp_nav_menu'.
 *
 * @param string  $classes String of classes.
 * @param mixed   $item The current item.
 * @param WP_Term $args Holds the nav menu arguments.
 *
 * @return array
 */
function renaissance_nav_menu_add_submenu_class( $classes, $args, $depth ) {
	if ( isset( $args->submenu_class ) ) {
		$classes[] = $args->submenu_class;
	}

	if ( isset( $args->{"submenu_class_$depth"} ) ) {
		$classes[] = $args->{"submenu_class_$depth"};
	}

	return $classes;
}

add_filter( 'nav_menu_submenu_css_class', 'renaissance_nav_menu_add_submenu_class', 10, 3 );

if (is_admin()) {
    add_action('admin_menu', function(): void {
        $name = get_bloginfo( 'name' );
        add_menu_page(
            'Postavke teme',    // Page title
            'Postavke teme',          // Menu title
            'manage_options',       // Capability (who can access)
            'postavke',    // Menu slug
            static fn() => TemplateRenderer::echo(
                get_template_directory() . '/templates/admin/layout.php',
                ['form' => getSettingsForm()],
            ),
            'dashicons-admin-generic', // Icon (use WP dashicons)
            10                      // Position in menu order
        );
    });
    $page = $_GET['page'] ?? null;

    if ('postavke' === $page) {
        add_action('admin_enqueue_scripts', function(): void {
            $theme = wp_get_theme();
            wp_enqueue_media();
			wp_enqueue_style( 'the_theme_admin', renaissance_asset( 'build/app.css' ), array(), $theme->get( 'Version' ) );
			wp_enqueue_script( 'the_theme_admin', renaissance_asset( 'build/admin.min.js' ), array(), $theme->get( 'Version' ) );
        });
    }
}


add_action('wp_enqueue_scripts', function() {
    wp_enqueue_script('jquery');

    // Pass AJAX URL to the script
    wp_localize_script('jquery', 'site_settings', [
        'ajax_url' => admin_url('admin-ajax.php')
    ]);
});

do_action( 'wp_ajax_nopriv_cart_items_count', function() {
    response([
        'total' => WC()->cart->get_cart_contents_count(),
    ]);
});
do_action( 'wp_ajax_cart_items_count', function() {
    response([
        'total' => WC()->cart->get_cart_contents_count(),
    ]);
});

add_action( 'wp_ajax_theme_settings_update', function() {
    $request = Request::createFromGlobals();

    if (false === $request->isMethod(Request::METHOD_POST)) {
        response([
            'message' => 'Došlo je do greške',
            Response::HTTP_BAD_REQUEST,
        ]);
    }

    $settings = [];

    foreach ($request->request->all('data') as $item) {
        if ('nonce' === $item['name']) {
            if(1 !== wp_verify_nonce($item['value'], 'theme_settings_update')) {
                response([
                    'message' => 'Token nije ispravan',
                    Response::HTTP_BAD_REQUEST,
                ]);
            }

            continue;
        }

        // Image fields store an attachment ID; resolve and cache the URL under "<name>-src".
        $isImageField = 'home-banner-bg' === $item['name'] || str_ends_with($item['name'], '-img');
        if ($isImageField) {
            $src = wp_get_attachment_image_src($item['value'], 1200);

            if (!is_array($src)) {
                $settings[$item['name']] = '';
                $settings[$item['name'] . '-src'] = '';
                continue;
            }

            $settings[$item['name'] . '-src'] = $src[0];
        }

        $settings[$item['name']] = $item['value'];
    }

    Config::set($settings);

    response([
        'message' => 'Uspješno spremljeno',
    ]);
});

add_action( 'wp_ajax_theme_get_nonce', function() {
    response([
        'nonce' => wp_create_nonce('theme_settings_update'),
    ]);
});

#[NoReturn]
function response(array $data, int $statusCode = 200): void
{
    wp_send_json($data);

    // Don't forget to stop execution afterward.
    wp_die(title: $statusCode);
}

/**
 * WooCommerce product categories as an `id => name` map for <select> fields.
 * Nested categories are indented so the hierarchy is readable.
 *
 * @return array<int, string>
 */
function getProductCategoryOptions(int $parent = 0, string $prefix = ''): array
{
    $terms = get_terms([
        'taxonomy'   => 'product_cat',
        'hide_empty' => false,
        'parent'     => $parent,
        'orderby'    => 'name',
    ]);

    if (!is_array($terms)) {
        return [];
    }

    $options = [];
    foreach ($terms as $term) {
        $options[$term->term_id] = $prefix . $term->name;
        $options += getProductCategoryOptions($term->term_id, $prefix . '— ');
    }

    return $options;
}

function getSettingsForm(): Form
{
    $options = Config::get();
    $mailchimpKey = Config::getAsString('mailchimp-key');
    $mailchimp = new Client();

    // Shows banner fields only when the banner is enabled.
    $bannerShowIf = ['field' => 'home-banner-show', 'value' => 'on'];

    $form = (new Form('theme_settings_update', $options))
        // ===================== GENERIČNE POSTAVKE =====================
        ->addField(
            FormSectionType::GENERAL_SETTINGS,
            new FormField(
                FormFieldType::TEXTAREA,
                'Tekst u trakici ispod headera',
                'home-notice',
                description: 'Kratka obavijest u traci na vrhu (npr. besplatna dostava iznad 70 €). Ostavite prazno za skrivanje.',
                group: 'Općenito',
            ),
        )
        ->addField(
            FormSectionType::GENERAL_SETTINGS,
            new FormField(
                FormFieldType::TEXT,
                'Google Tag Manager ID',
                'tag-manager-id',
                description: 'Npr. GTM-XXXXXX. Ostavite prazno ako ne koristite GTM.',
                group: 'Integracije',
            ),
        )
        ->addField(
            FormSectionType::GENERAL_SETTINGS,
            new FormField(
                FormFieldType::TEXTAREA,
                'Radno vrijeme (footer, opcionalno)',
                'footer-hours',
                description: 'Prikazuje se u "Kontakt" koloni footera. Ostavite prazno za skrivanje.',
                group: 'Footer',
            ),
        )
        // ===================== NASLOVNICA =====================
        // --- Hero ---
        ->addField(
            FormSectionType::HOMEPAGE_SETTINGS,
            new FormField(
                FormFieldType::TEXTAREA,
                'Hero rečenica',
                'home-hero-tagline',
                description: 'Glavna rečenica na vrhu naslovnice. Zadano: „Mirisi i rituali za sporiji, svjesniji dom.”',
                group: 'Hero sekcija',
            ),
        )
        ->addField(
            FormSectionType::HOMEPAGE_SETTINGS,
            new FormField(FormFieldType::TEXT, 'Hero gumb - tekst', 'home-hero-cta-text', group: 'Hero sekcija'),
        )
        ->addField(
            FormSectionType::HOMEPAGE_SETTINGS,
            new FormField(FormFieldType::TEXT, 'Hero gumb - link', 'home-hero-cta-url', group: 'Hero sekcija'),
        )
        ->addField(
            FormSectionType::HOMEPAGE_SETTINGS,
            new FormField(
                FormFieldType::TEXT,
                'Hero - mali nadnaslov',
                'home-hero-eyebrow',
                description: 'Sitni tekst iznad rečenice (vidljiv u rasporedu „Slika pored teksta”). Zadano: „Renaissance”.',
                group: 'Hero sekcija',
            ),
        )
        ->addField(
            FormSectionType::HOMEPAGE_SETTINGS,
            new FormField(
                FormFieldType::IMAGE,
                'Hero slika (opcionalno)',
                'home-hero-img',
                description: 'Velika slika na vrhu naslovnice. Ostavite prazno za čistu pozadinu (samo tekst).',
                group: 'Hero sekcija',
            ),
        )
        ->addField(
            FormSectionType::HOMEPAGE_SETTINGS,
            new FormField(
                FormFieldType::SELECT,
                'Hero - raspored slike',
                'home-hero-layout',
                options: [
                    'overlay' => 'Preko cijele širine (tekst na slici)',
                    'split'   => 'Slika pored teksta',
                ],
                description: 'Kako se slika prikazuje. Vrijedi samo ako je hero slika postavljena.',
                group: 'Hero sekcija',
                showIf: ['field' => 'home-hero-img', 'value' => '*'],
            ),
        )
        // --- Naslov sekcije s kategorijama ---
        ->addField(
            FormSectionType::HOMEPAGE_SETTINGS,
            new FormField(
                FormFieldType::TEXT,
                'Mali nadnaslov',
                'home-cat-section-eyebrow',
                description: 'Sitni zlatni tekst iznad naslova. Zadano: „Kolekcije”.',
                group: 'Naslov sekcije kategorija',
            ),
        )
        ->addField(
            FormSectionType::HOMEPAGE_SETTINGS,
            new FormField(
                FormFieldType::TEXT,
                'Naslov',
                'home-cat-section-title',
                description: 'Naslov iznad mreže kategorija. Zadano: „Istraži po kategoriji”.',
                group: 'Naslov sekcije kategorija',
            ),
        )
    ;

    // --- 4 glavne kategorije (generirano iz petlje — bez dupliciranja) ---
    $categoryOptions = getProductCategoryOptions();

    foreach (range(1, 4) as $n) {
        $form
            ->addField(
                FormSectionType::HOMEPAGE_SETTINGS,
                new FormField(
                    FormFieldType::SELECT,
                    'Kategorija',
                    "home-cat-$n",
                    options: $categoryOptions,
                    description: 'Odaberite kategoriju proizvoda — naziv i poveznica preuzimaju se automatski.',
                    group: "Kategorija $n",
                ),
            )
            ->addField(
                FormSectionType::HOMEPAGE_SETTINGS,
                new FormField(
                    FormFieldType::IMAGE,
                    'Slika (opcionalno)',
                    "home-cat-$n-img",
                    description: 'Ostavite prazno za automatsku sliku odabrane kategorije.',
                    group: "Kategorija $n",
                    showIf: ['field' => "home-cat-$n", 'value' => '*'],
                ),
            );
    }

    $form
        // --- Proizvodi ---
        ->addField(
            FormSectionType::HOMEPAGE_SETTINGS,
            new FormField(
                FormFieldType::CHECKBOX,
                'Prikaži sekciju "Odabrano" (maks. 8 proizvoda)?',
                'home-odabrano-show',
                description: 'Prikazuje do 8 izdvojenih (featured) proizvoda pri dnu naslovnice.',
                group: 'Proizvodi',
            ),
        )
        ->addField(
            FormSectionType::HOMEPAGE_SETTINGS,
            new FormField(
                FormFieldType::TEXT,
                'Naslov sekcije "Odabrano"',
                'home-odabrano-title',
                description: 'Zadano: „Odabrano”.',
                group: 'Proizvodi',
                showIf: ['field' => 'home-odabrano-show', 'value' => 'on'],
            ),
        )
        // --- O dućanu ---
        ->addField(
            FormSectionType::HOMEPAGE_SETTINGS,
            new FormField(
                FormFieldType::TEXTAREA,
                'Kratki tekst o dućanu (2-3 reda)',
                'home-about-text',
                description: 'Kratak, smiren opis dućana ispod kategorija.',
                group: 'O dućanu',
            ),
        )
        // --- Banner (na dnu naslovnice) ---
        ->addField(
            FormSectionType::HOMEPAGE_SETTINGS,
            new FormField(
                FormFieldType::CHECKBOX,
                'Prikaži banner na naslovnici?',
                'home-banner-show',
                description: 'Promo banner s porukom i gumbom pri dnu naslovnice.',
                group: 'Banner',
            ),
        )
        ->addField(
            FormSectionType::HOMEPAGE_SETTINGS,
            new FormField(FormFieldType::IMAGE, 'Slika za banner', 'home-banner-bg', group: 'Banner', showIf: $bannerShowIf),
        )
        ->addField(
            FormSectionType::HOMEPAGE_SETTINGS,
            new FormField(FormFieldType::TEXTAREA, 'Tekst preko bannera', 'home-banner-text', group: 'Banner', showIf: $bannerShowIf),
        )
        ->addField(
            FormSectionType::HOMEPAGE_SETTINGS,
            new FormField(FormFieldType::TEXT, 'Link', 'home-banner-url', group: 'Banner', showIf: $bannerShowIf),
        )
        ->addField(
            FormSectionType::HOMEPAGE_SETTINGS,
            new FormField(FormFieldType::TEXT, 'Call to action (tekst na gumbu)', 'home-banner-cta', group: 'Banner', showIf: $bannerShowIf),
        )
        // ===================== MAILCHIMP =====================
        ->addField(
            FormSectionType::MAILCHIMP_SETTINGS,
            new FormField(
                FormFieldType::TEXT,
                'Mailchimp API key',
                'mailchimp-key',
                description: $mailchimpKey === '' ? 'Nakon spremanja ključa osvježite stranicu da se pojave ostale postavke.' : 'Ključ je postavljen.',
                group: 'Mailchimp',
            ),
        )
        // --- Newsletter tekstovi (forma u footeru) ---
        ->addField(
            FormSectionType::MAILCHIMP_SETTINGS,
            new FormField(FormFieldType::TEXT, 'Mali nadnaslov', 'newsletter-eyebrow', description: 'Zadano: „Newsletter”.', group: 'Newsletter tekstovi'),
        )
        ->addField(
            FormSectionType::MAILCHIMP_SETTINGS,
            new FormField(FormFieldType::TEXT, 'Naslov', 'newsletter-title', description: 'Zadano: „Ostanite u toku”.', group: 'Newsletter tekstovi'),
        )
        ->addField(
            FormSectionType::MAILCHIMP_SETTINGS,
            new FormField(FormFieldType::TEXTAREA, 'Podnaslov', 'newsletter-subtitle', description: 'Kratak opis ispod naslova. Ostavite prazno za zadani tekst.', group: 'Newsletter tekstovi'),
        )
        ->addField(
            FormSectionType::MAILCHIMP_SETTINGS,
            new FormField(FormFieldType::TEXT, 'Sitni tekst ispod forme', 'newsletter-fineprint', description: 'Zadano: „Bez spama. Odjava u svakom trenutku.”.', group: 'Newsletter tekstovi'),
        )
    ;

    if ($mailchimpKey !== '') {
        $form
            ->addField(
                FormSectionType::MAILCHIMP_SETTINGS,
                new FormField(
                    FormFieldType::SELECT,
                    'Mailchimp newsletter lista',
                    'mailchimp-list',
                    options: $mailchimp->getLists(),
                    group: 'Mailchimp',
                ),
            )
            ->addField(
                FormSectionType::MAILCHIMP_SETTINGS,
                new FormField(
                    FormFieldType::NUMBER,
                    'Procenat popusta za kupon na newsletter (0 da se ne generira kupon)',
                    'mailchimp-newsletter-discount',
                    group: 'Newsletter kupon',
                ),
            )
            ->addField(
                FormSectionType::MAILCHIMP_SETTINGS,
                new FormField(
                    FormFieldType::NUMBER,
                    'Koliko dana traje kupon?',
                    'mailchimp-newsletter-discount-duration',
                    group: 'Newsletter kupon',
                ),
            );
    }

    return $form;
}

remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs');
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_template_single_meta', 40);
add_action( 'woocommerce_single_product_summary', function () {
    global $product;
    

    $description = sanitizeHtmlToText($product->get_description());

    echo <<<HTML
<div class="text-xl">
    $description
</div>
HTML;

}, 10 );

function sanitizeHtmlToText($html): string {
    $html = str_replace(["\n", '&nbsp;'], "\n\n", $html);
    
    $text = trim(strip_tags($html));

    $text = nl2br($text);
    $text = str_replace(array("\n", "\r"), "", $text);
    $text = preg_replace("/(<br \/>){3,}/", "<br><br>", $text);

    return trim($text);
}

add_theme_support('post-thumbnails');
add_image_size('product-thumb', 400, 400, true);

remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
add_action( 'woocommerce_before_shop_loop_item_title', function () {
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    $image = woocommerce_get_product_thumbnail('product-thumb');

    echo <<<HTML
<p class="product-card__media">
    $image
</p>
HTML;

}, 10 );

add_filter('woocommerce_product_loop_title_classes', fn ($value) => $value . ' product-card__title');

//
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
add_action( 'woocommerce_single_product_summary', 'the_theme_template_single_add_to_cart',5 );
add_action( 'woocommerce_single_product_summary_actions', 'woocommerce_template_single_price', 10 ); 
add_action( 'woocommerce_single_product_summary_actions', 'woocommerce_template_single_add_to_cart', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
add_action( 'woocommerce_before_shop_loop_custom', 'woocommerce_result_count', 20 );
add_action( 'woocommerce_before_shop_loop_custom', 'woocommerce_catalog_ordering', 30 );
add_action( 'woocommerce_before_shop_loop', function() {
    wc_get_template('loop/shop-loop-before.php');
}, 20 );
add_action( 'woocommerce_after_shop_loop', function() {
    wc_get_template('loop/shop-loop-after.php');
}, 20 );

function widgets_init() {

    register_sidebar( array(
        'name'          => 'Sa strane na kategoriji',
        'id'            => 'product_category_aside',
        'before_widget' => '<div>',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="rounded">',
        'after_title'   => '</h2>',
    ) );

}
add_action( 'widgets_init', 'widgets_init' );


function the_theme_template_single_add_to_cart(): void
{
    wc_get_template( 'single-product/price-and-add-to-cart.php' );
}



function getSalePercentage(WC_Product $product): null|float
{
    if( $product->is_type('variable')){
        $percentages = array();

        // Get all variation prices
        $prices = $product->get_variation_prices();

        // Loop through variation prices
        foreach( $prices['price'] as $key => $price ){
            // Only on sale variations
            if( $prices['regular_price'][$key] !== $price ){
                // Calculate and set in the array the percentage for each variation on sale
                $percentages[] = round( 100 - ( floatval($prices['sale_price'][$key]) / floatval($prices['regular_price'][$key]) * 100 ) );
            }
        }
        // We keep the highest value
        $percentage = max($percentages);

    } elseif( $product->is_type('grouped') ){
        $percentages = array();

        // Get all variation prices
        $children_ids = $product->get_children();

        // Loop through variation prices
        foreach( $children_ids as $child_id ){
            $child_product = wc_get_product($child_id);

            $regular_price = (float) $child_product->get_regular_price();
            $sale_price    = (float) $child_product->get_sale_price();

            if ( $sale_price != 0 || ! empty($sale_price) ) {
                // Calculate and set in the array the percentage for each child on sale
                $percentages[] = round(100 - ($sale_price / $regular_price * 100));
            }
        }
        // We keep the highest value
        $percentage = max($percentages);

    } else {
        $regular_price = (float) $product->get_regular_price();
        $sale_price    = (float) $product->get_sale_price();

        if ( $sale_price != 0 || ! empty($sale_price) ) {
            $percentage    = round(100 - ($sale_price / $regular_price * 100));
        } else {
            return null;
        }

    }

    return $percentage;
}

function get_menu(string $location): array
{
    // Get the menu locations registered in the theme
    $locations = get_nav_menu_locations();
    if (!isset($locations[$location])) {
		return [];
	}

	$menu_id = $locations[$location];

	// Fetch the menu items using the menu ID
	$menu_items = wp_get_nav_menu_items($menu_id);

	$id_parent_list = [];
	foreach ($menu_items as $menu_item) {
		$id_parent_list[(int) $menu_item->menu_item_parent][] = $menu_item;
	}

	return get_simplified_menu_array($id_parent_list[0] ?? [], $id_parent_list);
}

function get_simplified_menu_array(array $menu_items, array $id_parent_list): array {
    $map = [];

    foreach ($menu_items as $menu_item) {
        $children = $id_parent_list[(int) $menu_item->ID] ?? [];

        $map[] = [
            'title' => $menu_item->title,
            'url' => $menu_item->url,
            'children' => (!empty($children)) ? get_simplified_menu_array($children, $id_parent_list) : [],
        ];
    }

    return $map;
}

/**
 * Render the nested menu array from get_menu() to HTML.
 *
 * Level 0 = top-bar items (`.menu-link` + `.submenu` dropdown panel);
 * deeper levels = `.submenu-link` + `.nested-submenu` fly-out. Output is
 * escaped; visibility/interaction is handled in CSS (desktop hover) and
 * nav.js (mobile accordion).
 */
function getMenu(array $menu, int $level = 0): string
{
    $html = '';

    foreach ($menu as $item) {
        $url         = esc_url($item['url']);
        $title       = esc_html($item['title']);
        $hasChildren = !empty($item['children']);

        if (0 === $level) {
            $submenu  = $hasChildren ? '<div class="submenu">' . getMenu($item['children'], $level + 1) . '</div>' : '';
            $dataAttr = $hasChildren ? ' data-has-children="true"' : '';

            $html .= <<<HTML
                <div class="menu-group relative group">
                    <a class="menu-link"$dataAttr href="$url">$title</a>
                    $submenu
                </div>
            HTML;
        } else {
            $submenu = $hasChildren ? '<div class="nested-submenu">' . getMenu($item['children'], $level + 1) . '</div>' : '';

            $html .= <<<HTML
                <div class="submenu-item relative">
                    <a class="submenu-link" href="$url">$title</a>
                    $submenu
                </div>
            HTML;
        }
    }

    return $html;
}

/**
 * Filter sidebar markup for the shop / product archives.
 * Rendered in the template (archive-product.php) via do_blocks() so the
 * configuration is version-controlled and survives DB re-imports.
 */
function the_theme_shop_filters_markup(): string
{
    // Legacy WC filter blocks are STATIC (no render_callback): they output a
    // `is-loading` placeholder that the frontend script hydrates, reading the
    // data-* attributes. So we must emit the full canonical save markup
    // (placeholder div + data attributes), exactly as the block editor saves it
    // — a self-closing block comment renders nothing. (product-categories IS
    // dynamic, so a self-closing tag is correct there.)
    return <<<'HTML'
<!-- wp:woocommerce/filter-wrapper {"filterType":"active-filters","heading":"Aktivni filteri"} -->
<div class="wp-block-woocommerce-filter-wrapper">
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Aktivni filteri</h3>
<!-- /wp:heading -->
<!-- wp:woocommerce/active-filters {"heading":"","displayStyle":"chips"} -->
<div class="wp-block-woocommerce-active-filters is-loading" data-display-style="chips" data-heading="" data-heading-level="3"><span aria-hidden="true" class="wc-block-active-filters__placeholder"></span></div>
<!-- /wp:woocommerce/active-filters -->
</div>
<!-- /wp:woocommerce/filter-wrapper -->

<!-- wp:woocommerce/filter-wrapper {"filterType":"price-filter","heading":"Cijena"} -->
<div class="wp-block-woocommerce-filter-wrapper">
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Cijena</h3>
<!-- /wp:heading -->
<!-- wp:woocommerce/price-filter {"heading":""} -->
<div class="wp-block-woocommerce-price-filter is-loading"><span aria-hidden="true" class="wc-block-product-price-filter__placeholder"></span></div>
<!-- /wp:woocommerce/price-filter -->
</div>
<!-- /wp:woocommerce/filter-wrapper -->

<!-- wp:woocommerce/filter-wrapper {"filterType":"stock-filter","heading":"Dostupnost"} -->
<div class="wp-block-woocommerce-filter-wrapper">
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Dostupnost</h3>
<!-- /wp:heading -->
<!-- wp:woocommerce/stock-filter {"heading":"","showCounts":true} -->
<div class="wp-block-woocommerce-stock-filter is-loading" data-show-counts="true" data-show-filter-button="false" data-heading="" data-heading-level="3"><span aria-hidden="true" class="wc-block-product-stock-filter__placeholder"></span></div>
<!-- /wp:woocommerce/stock-filter -->
</div>
<!-- /wp:woocommerce/filter-wrapper -->
HTML;
}

/**
 * Custom multi-level product-category filter for the shop sidebar.
 *
 * Unlike the WC product-categories block (which is plain navigation that drops
 * the other filters and needs a submit), this renders a hierarchical tree of
 * links that PRESERVE the active filters (price/stock/sorting via the current
 * query string), highlight the active category and expand its branch.
 */
function the_theme_category_filter(): string
{
    $currentId = 0;
    $ancestors = [];
    if (is_product_category()) {
        $obj = get_queried_object();
        if ($obj instanceof WP_Term) {
            $currentId = (int) $obj->term_id;
            $ancestors = array_map('intval', get_ancestors($currentId, 'product_cat'));
        }
    }

    // Carry the active filters over when switching category; reset pagination.
    $params = $_GET; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    unset($params['paged'], $params['product-page']);
    $qs = !empty($params) ? '?' . http_build_query($params) : '';

    $tree = the_theme_category_filter_branch(0, 0, $currentId, $ancestors, $qs);
    if ('' === $tree) {
        return '';
    }

    $reset = $currentId > 0
        ? '<a class="ren-cat-reset" href="' . esc_url(wc_get_page_permalink('shop') . $qs) . '">← Sve kategorije</a>'
        : '';

    return '<div class="ren-cat-filter-wrap"><h3 class="wp-block-heading">Kategorije</h3>' . $reset . $tree . '</div>';
}

function the_theme_category_filter_branch(int $parent, int $depth, int $currentId, array $ancestors, string $qs): string
{
    $terms = get_terms([
        'taxonomy'   => 'product_cat',
        'hide_empty' => true,
        'parent'     => $parent,
        'orderby'    => 'name',
    ]);
    if (is_wp_error($terms) || empty($terms)) {
        return '';
    }

    $out = '<ul class="ren-cat-filter' . (0 === $depth ? '' : ' ren-cat-filter--sub') . '">';
    foreach ($terms as $term) {
        $isActive = ((int) $term->term_id === $currentId);
        $onBranch = $isActive || in_array((int) $term->term_id, $ancestors, true);
        $link     = get_term_link($term);
        $url      = is_wp_error($link) ? '#' : esc_url($link . $qs);

        $out .= '<li><a class="ren-cat-link' . ($isActive ? ' is-active' : '') . '" href="' . $url . '"'
              . ($isActive ? ' aria-current="page"' : '') . '>'
              . '<span>' . esc_html($term->name) . '</span>'
              . '<span class="ren-cat-count">' . (int) $term->count . '</span></a>';
        if ($onBranch) {
            $out .= the_theme_category_filter_branch((int) $term->term_id, $depth + 1, $currentId, $ancestors, $qs);
        }
        $out .= '</li>';
    }

    return $out . '</ul>';
}

// 301: preimenovana trgovina -> /ducan (stari /shop i /trgovina URL-ovi i bookmarci).
add_action('template_redirect', function (): void {
    if (!is_404()) {
        return;
    }
    $path = trim((string) parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');
    if (preg_match('#^(shop|trgovina)(/.*)?$#', $path, $m)) {
        $target = 'ducan' . ($m[2] ?? '');
        wp_safe_redirect(home_url('/' . trailingslashit($target)), 301);
        exit;
    }
});

function output_product_loop(array $args, string $title): void
{
    if (isset($args['ids'])) {
        $products = $args['ids'];
    } else {
        $paged             = (get_query_var('paged')) ? absint(get_query_var('paged')) : 1;
        $products_per_page = $args['limit'] ?? apply_filters('loop_shop_per_page', wc_get_default_products_per_row() * wc_get_default_product_rows_per_page());

        $products = wc_get_products([
                'status'  => 'publish',
                'limit'   => $products_per_page,
                'page'    => $paged,
                'return'  => 'ids',
                'orderby' => 'ID',
                'order'   => 'DESC',
            ] + $args);

        wc_set_loop_prop('current_page', $paged);
        wc_set_loop_prop('per_page', $products_per_page);
    }
    wc_set_loop_prop('is_paginated', wc_string_to_bool(true));
    wc_set_loop_prop('page_template', get_page_template_slug());
    

    if($products) {
        ?>
        <div class="px-10">
            <h2 class="title-lg text-center mb-3">
                <?php echo esc_html($title); ?>
            </h2>
            <div class="mx-auto mb-12 h-px w-12 bg-gold"></div>
            <?php
                do_action('woocommerce_before_shop_loop');
                woocommerce_product_loop_start();
                foreach($products as $featured_product) {
                    $post_object = get_post($featured_product);
                    setup_postdata($GLOBALS['post'] =& $post_object);
                    wc_get_template_part('content', 'product');
                }
                wp_reset_postdata();
                woocommerce_product_loop_end();
                do_action('woocommerce_after_shop_loop');
            ?>
        </div>
        <?php
    }
}


/**
 * Required "I accept the Privacy Policy" checkbox on checkout.
 * The label links to the Privacy Policy page (mandatory per requirements).
 */
add_action('woocommerce_review_order_before_submit', function (): void {
    $privacy_url = function_exists('get_privacy_policy_url') ? get_privacy_policy_url() : '';

    $label = $privacy_url !== ''
        ? sprintf(
            'Pročitao/la sam i prihvaćam <a href="%s" target="_blank" class="text-gold underline">Politiku privatnosti</a>.',
            esc_url($privacy_url),
        )
        : 'Pročitao/la sam i prihvaćam Politiku privatnosti.';

    woocommerce_form_field('privacy_policy_accept', [
        'type'     => 'checkbox',
        'class'    => ['form-row privacy-policy-accept'],
        'label'    => $label,
        'required' => true,
    ], WC()->checkout->get_value('privacy_policy_accept'));
});

add_action('woocommerce_checkout_process', function (): void {
    if (empty($_POST['privacy_policy_accept'])) {
        wc_add_notice(
            'Morate prihvatiti Politiku privatnosti kako biste dovršili narudžbu.',
            'error',
        );
    }
});

// Allow SVG
add_filter( 'wp_check_filetype_and_ext', function($data, $file, $filename, $mimes) {

    global $wp_version;
    if ( $wp_version !== '4.7.1' ) {
        return $data;
    }

    $filetype = wp_check_filetype( $filename, $mimes );

    return [
        'ext'             => $filetype['ext'],
        'type'            => $filetype['type'],
        'proper_filename' => $data['proper_filename']
    ];

}, 10, 4 );

function cc_mime_types( $mimes ){
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter( 'upload_mimes', 'cc_mime_types' );

function fix_svg() {
    echo '<style type="text/css">
        .attachment-266x266, .thumbnail img {
             width: 100% !important;
             height: auto !important;
        }
        </style>';
}
add_action( 'admin_head', 'fix_svg' );


function custom_loop_shop_columns( $columns ) {
    if ( is_shop() || is_product_category() || is_product_tag() ) {
        return 3; // Set to 3 columns for product archives
    }
    return $columns; // Default return value
}
add_filter( 'loop_shop_columns', 'custom_loop_shop_columns' );


// custom fieldovi na kategoriji
$customFields = [
    'category_aside_text' => [
        'label' => 'Tekst sa strane',
        'description' => 'Tekst koji će se prikazati sa strane kada se otvori kategorija',
    ],
    'category_below_text' => [
        'label' => 'Tekst ispod proizvoda',
        'description' => 'Tekst koji će se prikazati ispod proizvoda kada se otvori kategorija',
    ],
];

foreach ($customFields as $key => $value) {
    add_category_field($key, $value['label'], $value['description']);
}

add_action('edited_product_cat', function ($term_id) use ($customFields) {
    array_map(
        static fn ($key) => update_term_meta($term_id, $key, $_POST[$key] ?? ''),
        array_keys($customFields)
    );
});

function add_category_field(string $name, string $label, string $description): void
{
    add_action('product_cat_edit_form_fields', function ($term) use ($name, $label, $description) {
        $custom_field = get_term_meta($term->term_id, $name, true);
        ?>
        <tr class="form-field">
            <th scope="row" valign="top"><label for="<?php echo $name; ?>"><?php echo $label; ?></label></th>
            <td>
                <?php
                wp_editor($custom_field, $name, array(
                    'textarea_name' => $name
                ));
                ?>
                <p class="description"><?php echo $description; ?></p>
            </td>
        </tr>
        <?php
    }, 10, 2);
}

function custom_admin_bar_menu(WP_Admin_Bar $wp_admin_bar) {
    $wp_admin_bar->add_node([
        'parent' => 'site-name',
        'id'     => 'the-theme-settings',
        'title'  => 'Postavke teme',
        'href'   => admin_url('admin.php?page=postavke'),
    ]);
}
add_action('admin_bar_menu', 'custom_admin_bar_menu', 100);

function enqueue_google_tag_manager() {
    $gtm_id = Config::getAsString('tag-manager-id');

    if (!empty($gtm_id)) {
        echo <<<GTAG
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=$gtm_id"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', '$gtm_id');
        </script>
GTAG;

    }
}
add_action('wp_head', 'enqueue_google_tag_manager');

// mailchimp magic
function subscribe_user_to_mailchimp(): void {
    $mailchimp = (new Client())->mailchimp;
    $email = $_POST['email'] ?? null;

    if (!$email) {
        echo 'invalid';

        return;
    }

    $list_id = Config::getAsString('mailchimp-list');

    try {
        $response = $mailchimp->lists->getListMember($list_id, md5(strtolower($email)));

        if ($response->status === 'subscribed') {
            echo 'already_subscribed';

            return;
        }
    } catch (Exception) {}

    // Popust 0 = newsletter bez kupona: ne pravimo 0% kupon, mail je samo zahvala.
    $discount_amount = (int) Config::getAsString('mailchimp-newsletter-discount');
    $coupon_code = $discount_amount > 0 ? generate_unique_coupon($discount_amount, $email) : '';

    $response = $mailchimp->lists->addListMember($list_id, [
        'email_address' => $email,
        'status' => 'subscribed',
    ]);

    if ($response->status === 'subscribed') {
        send_welcome_email($email, $coupon_code, $discount_amount);
    }

    echo 'subscribed';
}

function generate_unique_coupon(int $discount_amount, string $email): string {
    $coupon_code = 'RENAISSANCE' . strtoupper(wp_generate_password(8, false));
    $duration_in_days = (int) Config::getAsString('mailchimp-newsletter-discount-duration');
    $duration_in_days = 0 === $duration_in_days ? 30 : $duration_in_days;

    $coupon = new WC_Coupon();
    $coupon->set_discount_type('percent');
    $coupon->set_amount($discount_amount);
    $coupon->set_description('Newsletter kupon: ' . $email);
    $coupon->set_code($coupon_code);
    $coupon->set_usage_limit(1);
    $coupon->set_individual_use(true);
    $coupon->set_date_expires( date('Y-m-d', strtotime(sprintf('+%d days', $duration_in_days))) );

    $coupon->save();
    return $coupon_code;
}

function send_welcome_email($email, $coupon_code, $discount_amount): void {
    $content = TemplateRenderer::render(
            get_template_directory() . '/email/newsletter.php',
        ['coupon_code' => $coupon_code, 'discount_amount' => $discount_amount]
    );
    $headers = ['Content-Type: text/html; charset=UTF-8'];

    wp_mail($email, 'Uspješna prijava na newsletter!', $content, $headers);
}

add_action('wp_ajax_nopriv_subscribe_newsletter', 'subscribe_user_to_mailchimp');
add_action('wp_ajax_subscribe_newsletter', 'subscribe_user_to_mailchimp');


add_action( 'woocommerce_email_order_meta', function( $order, $sent_to_admin, $plain_text ) {
    $lockerId = $order->get_meta('_boxnow_locker_id');

    if( empty( $lockerId ) ) {
        return;
    }


    // ok, we will add the separate version for plaintext emails
    if ( false === $plain_text ) {

        // you shouldn't have to worry about inline styles, WooCommerce adds them itself depending on the theme you use
        ?>
        <h2>BOXNOW podaci</h2>
        <ul>
            <li><strong>BOXNOW Locker ID:</strong> <?php echo $lockerId ?></li>
        </ul>
        <?php

    } else {

        echo "\nBOXNOW podaci\n"
            . "BOXNOW Locker ID: $lockerId\n"
        ;

    }

}, 10, 3 );