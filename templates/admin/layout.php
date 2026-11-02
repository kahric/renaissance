<?php

use Kahric\TheTheme\Form;

/** @var Form\Form $form */

$renderer = new Form\FormRenderer();
$sections = Form\FormSectionType::cases();
?>

<div class="ren-admin">

    <!-- Page header -->
    <div class="ren-admin-header">
        <span class="ren-admin-mark">R</span>
        <div>
            <h1 class="ren-admin-title">Postavke teme</h1>
            <p class="ren-admin-subtitle">Uredite sadržaj naslovnice, banner i integracije.</p>
        </div>
    </div>

    <div class="ren-admin-body">

        <!-- Sidebar navigation -->
        <aside class="theme-admin-nav">
            <?php foreach ($sections as $section) {
                printf(
                    '<div class="ren-nav-item" data-section="%1$s">
                        <span class="dashicons %2$s"></span>
                        <span class="ren-nav-label">%3$s</span>
                    </div>',
                    esc_attr($section->value),
                    esc_attr($section->getIconClass()),
                    esc_html($section->getLabel()),
                );
            } ?>
        </aside>

        <!-- Content -->
        <main class="ren-admin-main">
            <div class="theme-admin-sections">
                <?php $renderer->render($form); ?>
            </div>
        </main>

    </div>
</div>
