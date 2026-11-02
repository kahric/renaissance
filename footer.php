<?php

use Kahric\TheTheme\Config;

if (!defined('ABSPATH')) {
    exit;
}  ?>
</main>

<?php do_action( 'renaissance_content_end' ); ?>

</div>

<?php do_action( 'renaissance_content_after' ); ?>

<footer id="colophon" class="site-footer border-t border-primary-100 mt-24 text-sm" role="contentinfo">
    <?php do_action( 'renaissance_footer' ); ?>

    <?php
    // Newsletter — prikazuje se samo ako je Mailchimp postavljen (inače forma ne bi radila).
    if ('' !== Config::getAsString('mailchimp-key')) {
        $nlDiscount   = (int) Config::getAsString('mailchimp-newsletter-discount');
        $nlEyebrow    = Config::getAsString('newsletter-eyebrow') ?: 'Newsletter';
        $nlTitle      = Config::getAsString('newsletter-title') ?: 'Ostanite u toku';
        $nlFineprint  = Config::getAsString('newsletter-fineprint') ?: 'Bez spama. Odjava u svakom trenutku.';
        // Podnaslov: admin override > popust-tekst > neutralan default.
        $nlSubtitle = Config::getAsString('newsletter-subtitle')
            ?: ($nlDiscount > 0
                ? sprintf('Prijavite se i ostvarite %d%% popusta na prvu narudžbu.', $nlDiscount)
                : 'Mirisne novosti i mali rituali, povremeno u vaš inbox.');
    ?>
    <div class="container mx-auto px-10 lg:px-0 py-16 lg:py-20 border-b border-primary-100">
        <div class="max-w-lg mx-auto text-center">
            <?php if ('' !== $nlEyebrow) { ?><p class="eyebrow mb-3"><?php echo esc_html($nlEyebrow); ?></p><?php } ?>
            <h2 class="title-lg mb-3"><?php echo esc_html($nlTitle); ?></h2>
            <p class="text-ink/75 leading-relaxed"><?php echo esc_html($nlSubtitle); ?></p>

            <form id="newsletter-form" class="mt-8 mx-auto flex max-w-md items-stretch overflow-hidden rounded-full border border-field-border bg-surface-raised transition-colors focus-within:border-gold focus-within:ring-2 focus-within:ring-gold/20">
                <label for="email" class="sr-only">Email adresa</label>
                <input type="email" id="email" name="email" required autocomplete="email"
                       placeholder="vaša@email.com"
                       class="min-w-0 flex-1 border-0 bg-transparent py-3 pl-5 pr-3 text-base text-ink placeholder:text-muted focus:outline-none focus:ring-0" />
                <button type="submit" class="shrink-0 bg-gold px-6 text-sm font-medium tracking-wide text-white transition-colors hover:bg-primary-600">
                    Prijavi se
                </button>
            </form>
            <?php if ('' !== $nlFineprint) { ?><p class="text-xs text-ink/50 mt-4"><?php echo esc_html($nlFineprint); ?></p><?php } ?>
        </div>
    </div>
    <?php } ?>

    <div class="container mx-auto px-10 lg:px-0 py-16">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-10 lg:gap-12">

            <!-- Kolona 1 — Brand -->
            <div class="col-span-2 lg:col-span-1">
                <p class="font-serif text-2xl text-ink tracking-wide"><?php echo esc_html(get_bloginfo('name')); ?></p>
                <p class="mt-4 max-w-xs text-ink/70 leading-relaxed">Mirisi i rituali za sporiji, svjesniji dom.</p>
                <span class="mt-6 block h-px w-12 bg-gold"></span>
            </div>

            <?php
            // Renders a single footer menu column from a registered menu location.
            $render_footer_column = static function (string $location, string $heading): void {
                $menu = get_menu($location);
                if ([] === $menu) {
                    return;
                }
                echo '<div>';
                echo '<h2 class="title-sm mb-5">' . esc_html($heading) . '</h2>';
                echo '<ul class="space-y-3">';
                foreach ($menu as $menu_item) {
                    printf(
                        '<li><a class="text-ink/80 hover:text-gold transition-colors" href="%s">%s</a></li>',
                        esc_url($menu_item['url']),
                        esc_html($menu_item['title']),
                    );
                }
                echo '</ul>';
                echo '</div>';
            };

            // Kolona 2 — Informacije
            $render_footer_column('footer', 'Informacije');

            // Kolona 3 — Pravno
            $render_footer_column('footer-legal', 'Pravno');
            ?>

            <!-- Kolona 4 — Kontakt -->
            <div class="col-span-2 lg:col-span-1">
                <h2 class="title-sm mb-5">Kontakt</h2>
                <div class="space-y-1 text-ink/80">
                    <p>Zona natura d.o.o.</p>
                    <p>Zagreb</p>
                    <p>OIB: 46122534067</p>
                    <p><a class="hover:text-gold transition-colors" href="mailto:info@durance.hr">info@durance.hr</a></p>
                    <?php
                    $hours = Config::getAsString('footer-hours');
                    if ('' !== $hours) {
                        echo '<p class="mt-3">' . nl2br(esc_html($hours)) . '</p>';
                    }
                    ?>
                </div>
            </div>

        </div>

        <div class="mt-16 pt-8 border-t border-primary-100 text-center text-xs text-ink/60">
            &copy; <?php echo date_i18n( 'Y' );?> <?php echo esc_html(get_bloginfo( 'name' ));?>
            &nbsp;|&nbsp; Zona natura d.o.o. &nbsp;|&nbsp; Zagreb &nbsp;|&nbsp; OIB: 46122534067 &nbsp;|&nbsp; info@durance.hr
        </div>
    </div>
</footer>

</div>

<?php // Cookie/consent is handled by the Complianz GDPR plugin — no theme banner (avoids duplicate). ?>

<?php wp_footer(); ?>

</body>
</html>
