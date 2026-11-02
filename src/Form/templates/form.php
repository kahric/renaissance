<?php

declare(strict_types=1);

/** @var $form \Kahric\TheTheme\Form\Form */

use Kahric\TheTheme\Form\FormSectionType;
use Kahric\TheTheme\Helper\TemplateRenderer;

if (!isset($form) || !defined('ABSPATH')) {
    return;
}
?>

<form data-ajax-form="<?php echo esc_attr($form->name); ?>">

    <input type="hidden" id="settings-nonce" name="nonce"
           value="<?php echo esc_attr(wp_create_nonce('theme_settings_update')); ?>" />

    <div class="ren-toolbar">
        <span class="ren-toolbar-hint">
            <span class="dashicons dashicons-info-outline"></span>
            Promjene se spremaju tek kad kliknete „Spremi izmjene”.
        </span>
        <button type="submit" class="ren-save-btn">
            <span class="dashicons dashicons-saved"></span>
            <span data-btn-label>Spremi izmjene</span>
        </button>
    </div>

    <?php
    foreach ($form->getFields() as $section => $fields) {
        $sectionEnum = FormSectionType::from($section);

        echo TemplateRenderer::render(
            $sectionEnum->getTemplate(),
            ['section' => $sectionEnum, 'fields' => $fields],
        );
    }
    ?>
</form>
