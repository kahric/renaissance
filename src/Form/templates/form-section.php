<?php

declare(strict_types=1);

/** @var $section FormSectionType */
/** @var $fields FormField[] */

use Kahric\TheTheme\Form\FormField;
use Kahric\TheTheme\Form\FormSectionType;
use Kahric\TheTheme\Helper\TemplateRenderer;

if (!isset($section, $fields) || !defined('ABSPATH')) {
    return;
}

// Bucket fields by their group, preserving first-appearance order.
$ungrouped = [];
$groups = [];
foreach ($fields as $field) {
    if ('' === $field->group) {
        $ungrouped[] = $field;
        continue;
    }
    $groups[$field->group][] = $field;
}

/** Renders one field wrapped with grid-span + conditional-display attributes. */
$renderField = static function (FormField $field): void {
    $classes = 'ren-field' . ($field->isWide() ? ' ren-field--wide' : '');
    $attrs = '';
    if (null !== $field->showIf) {
        $attrs = sprintf(
            ' data-show-if-field="%s" data-show-if-value="%s"',
            esc_attr($field->showIf['field']),
            esc_attr((string) $field->showIf['value']),
        );
    }
    echo '<div class="' . $classes . '"' . $attrs . '>';
    TemplateRenderer::echo($field->type->getTemplatePath(), ['field' => $field]);
    echo '</div>';
};
?>

<div class="ren-section hidden" data-section="<?php echo esc_attr($section->value); ?>">
    <div class="ren-section-head">
        <h2 class="ren-section-title"><?php echo esc_html($section->getLabel()); ?></h2>
        <p class="ren-section-desc"><?php echo esc_html($section->getDescription()); ?></p>
    </div>

    <?php if ([] !== $ungrouped) { ?>
        <div class="ren-fields ren-fields--lead">
            <?php foreach ($ungrouped as $field) {
                $renderField($field);
            } ?>
        </div>
    <?php } ?>

    <?php
    $first = true;
    foreach ($groups as $title => $groupFields) {
        $openClass = $first ? ' is-open' : '';
        $first = false;
        ?>
        <div class="ren-accordion<?php echo $openClass; ?>" data-accordion>
            <button type="button" class="ren-accordion-head" data-accordion-toggle>
                <span class="ren-accordion-title"><?php echo esc_html($title); ?></span>
                <span class="dashicons dashicons-arrow-down-alt2 ren-accordion-chevron"></span>
            </button>
            <div class="ren-accordion-body">
                <div class="ren-accordion-inner">
                    <div class="ren-fields">
                        <?php foreach ($groupFields as $field) {
                            $renderField($field);
                        } ?>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
