<?php
declare(strict_types=1);

use Kahric\TheTheme\Config;

if (!defined('ABSPATH')) {
    exit;
}

include 'header.php';

// Tagline ima default da hero nikad ne ostane prazan (samo logo).
$tagline = Config::getAsString('home-hero-tagline') ?: 'Mirisi i rituali za sporiji, svjesniji dom.';
// CTA je OPCIONALAN: gumb se prikazuje samo ako je tekst postavljen u Postavkama.
// Link se default-a na trgovinu ako je tekst zadan, a link ostavljen prazan.
$ctaText = Config::getAsString('home-hero-cta-text');
$ctaUrl  = Config::getAsString('home-hero-cta-url') ?: wc_get_page_permalink('shop');

// Opcionalna hero slika + raspored (overlay = preko širine, split = pored teksta).
$heroImg    = Config::getAsString('home-hero-img-src');
$heroLayout = Config::getAsString('home-hero-layout') ?: 'overlay';
$hasHeroImg = '' !== $heroImg;
?>

<!-- Hero -->
<?php if ($hasHeroImg && 'overlay' === $heroLayout) { ?>
    <!-- Mod: slika preko cijele širine, tekst preko slike -->
    <section class="relative flex items-center justify-center overflow-hidden min-h-[360px] lg:min-h-[480px]">
        <div class="absolute inset-0 bg-cover bg-center" style="background-image:url('<?php echo esc_url($heroImg); ?>')"></div>
        <div class="absolute inset-0 bg-ink/40"></div>
        <div class="relative max-w-2xl mx-auto px-10 text-center py-16 lg:py-20">
            <h1 class="title-xl text-white leading-relaxed mb-10">
                <?php echo nl2br(esc_html($tagline)); ?>
            </h1>
            <?php if ('' !== $ctaText && '' !== $ctaUrl) { ?>
                <a href="<?php echo esc_url($ctaUrl); ?>" class="btn-outline-light">
                    <?php echo esc_html($ctaText); ?>
                </a>
            <?php } ?>
        </div>
    </section>

<?php } elseif ($hasHeroImg) { ?>
    <!-- Mod: slika pored teksta (split) -->
    <section class="container mx-auto px-10 lg:px-0 mt-8 lg:mt-14">
        <div class="grid lg:grid-cols-2 items-center gap-8 lg:gap-16">
            <div class="order-2 lg:order-1 text-center lg:text-left">
                <p class="eyebrow mb-4"><?php echo esc_html(Config::getAsString('home-hero-eyebrow') ?: 'Renaissance'); ?></p>
                <h1 class="title-xl leading-relaxed mb-8">
                    <?php echo nl2br(esc_html($tagline)); ?>
                </h1>
                <?php if ('' !== $ctaText && '' !== $ctaUrl) { ?>
                    <a href="<?php echo esc_url($ctaUrl); ?>" class="btn-outline">
                        <?php echo esc_html($ctaText); ?>
                    </a>
                <?php } ?>
            </div>
            <div class="order-1 lg:order-2 overflow-hidden bg-primary-50 aspect-[16/10] lg:aspect-auto lg:h-[420px]">
                <div class="h-full w-full bg-cover bg-center" style="background-image:url('<?php echo esc_url($heroImg); ?>')"></div>
            </div>
        </div>
    </section>

<?php } else { ?>
    <!-- Mod: bez slike (čisti tekst) -->
    <section class="container mx-auto px-10 lg:px-0">
        <div class="max-w-2xl mx-auto text-center py-20 lg:py-28">
            <h1 class="title-lg leading-relaxed mb-12">
                <?php echo nl2br(esc_html($tagline)); ?>
            </h1>
            <?php if ('' !== $ctaText && '' !== $ctaUrl) { ?>
                <a href="<?php echo esc_url($ctaUrl); ?>" class="btn-outline">
                    <?php echo esc_html($ctaText); ?>
                </a>
            <?php } ?>
        </div>
    </section>
<?php } ?>

<!-- 4 glavne kategorije -->
<?php
$categories = [];
foreach (range(1, 4) as $n) {
    $termId = (int) Config::getAsString("home-cat-$n");
    if ($termId <= 0) {
        continue;
    }

    $term = get_term($termId, 'product_cat');
    if (!$term instanceof WP_Term) {
        continue;
    }

    $url = get_term_link($term);
    $img = Config::getAsString("home-cat-$n-img-src");
    if ('' === $img) {
        $thumbnailId = (int) get_term_meta($termId, 'thumbnail_id', true);
        $img = $thumbnailId > 0 ? (string) wp_get_attachment_image_url($thumbnailId, 'large') : '';
    }

    $categories[] = [
        'title' => $term->name,
        'url'   => is_wp_error($url) ? '#' : $url,
        'img'   => $img,
    ];
}
?>
<?php if ([] !== $categories) { ?>
<section class="container mx-auto px-10 lg:px-0 mt-20 lg:mt-32">
    <?php
    $catEyebrow = Config::getAsString('home-cat-section-eyebrow') ?: 'Kolekcije';
    $catTitle   = Config::getAsString('home-cat-section-title') ?: 'Istraži po kategoriji';
    ?>
    <div class="text-center mb-12 lg:mb-16">
        <?php if ('' !== $catEyebrow) { ?><p class="eyebrow mb-3"><?php echo esc_html($catEyebrow); ?></p><?php } ?>
        <h2 class="title-xl"><?php echo esc_html($catTitle); ?></h2>
    </div>
    <div class="grid grid-cols-1 xs:grid-cols-2 lg:grid-cols-4 gap-x-6 gap-y-14 lg:gap-x-10">
        <?php foreach ($categories as $cat) { ?>
            <a href="<?php echo esc_url($cat['url']); ?>" class="group block text-center">
                <div class="relative aspect-[3/4] overflow-hidden bg-primary-50">
                    <div class="h-full w-full bg-cover bg-center transition-transform duration-[1.2s] ease-out group-hover:scale-105"
                         <?php echo '' !== $cat['img'] ? 'style="background-image:url(\'' . esc_url($cat['img']) . '\')"' : ''; ?>></div>
                    <div class="absolute inset-0 bg-ink/0 transition-colors duration-500 group-hover:bg-ink/10"></div>
                </div>
                <h3 class="text-2xl lg:text-2xl mt-6 transition-colors group-hover:text-gold">
                    <?php echo esc_html($cat['title']); ?>
                </h3>
                <span class="mx-auto mt-3 block h-px w-8 bg-gold/40 transition-all duration-500 group-hover:w-16 group-hover:bg-gold"></span>
            </a>
        <?php } ?>
    </div>
</section>
<?php } ?>

<!-- Kratki tekst o dućanu -->
<?php $about = Config::getAsString('home-about-text'); ?>
<?php if ('' !== $about) { ?>
    <section class="container mx-auto px-10 lg:px-0 mt-20 lg:mt-32">
        <div class="mx-auto mb-10 h-px w-12 bg-gold"></div>
        <p class="max-w-2xl mx-auto text-center text-lg leading-loose text-ink">
            <?php echo nl2br(esc_html($about)); ?>
        </p>
    </section>
<?php } ?>

<!-- Odabrano (maksimalno 8 proizvoda) -->
<?php if (Config::getAsBool('home-odabrano-show') && function_exists('wc_get_products')) { ?>
    <section class="container mx-auto mt-20 lg:mt-32">
        <?php
        output_product_loop(
            ['stock_status' => 'instock', 'featured' => true, 'limit' => 8],
            Config::getAsString('home-odabrano-title') ?: 'Odabrano'
        );
        ?>
    </section>
<?php } ?>

<!-- Promo banner (opcionalno — upravlja se iz Postavki) -->
<?php if (Config::getAsBool('home-banner-show')) {
    $bannerImg  = Config::getAsString('home-banner-bg-src');
    $bannerText = Config::getAsString('home-banner-text');
    $bannerUrl  = Config::getAsString('home-banner-url');
    $bannerCta  = Config::getAsString('home-banner-cta');
    $hasImg     = '' !== $bannerImg;
?>
    <section class="mt-20 lg:mt-32">
        <div class="relative overflow-hidden <?php echo $hasImg ? 'bg-cover bg-center' : 'bg-primary-100'; ?>"
             <?php echo $hasImg ? 'style="background-image:url(\'' . esc_url($bannerImg) . '\')"' : ''; ?>>
            <?php if ($hasImg) { ?><div class="absolute inset-0 bg-ink/35"></div><?php } ?>
            <div class="relative container mx-auto px-10 py-24 lg:py-36 flex flex-col items-center text-center">
                <?php if ('' !== $bannerText) { ?>
                    <p class="title-lg leading-relaxed max-w-2xl mb-8 <?php echo $hasImg ? 'text-white' : 'text-ink'; ?>">
                        <?php echo nl2br(esc_html($bannerText)); ?>
                    </p>
                <?php } ?>
                <?php if ('' !== $bannerCta && '' !== $bannerUrl) { ?>
                    <a href="<?php echo esc_url($bannerUrl); ?>" class="<?php echo $hasImg ? 'btn-outline-light' : 'btn-outline'; ?>">
                        <?php echo esc_html($bannerCta); ?>
                    </a>
                <?php } ?>
            </div>
        </div>
    </section>
<?php } ?>

<?php

include 'footer.php';
