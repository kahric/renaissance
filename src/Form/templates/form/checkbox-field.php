<?php

declare(strict_types=1);

/** @var $field FormField */

use Kahric\TheTheme\Form\FormField;
use Kahric\TheTheme\Form\FormFieldType;

if (!defined('ABSPATH') || !isset($field) || FormFieldType::CHECKBOX !== $field->type) {
    return;
}

$name = $field->name;
$checked = 'on' === $field->value;
?>

<label class="ren-toggle" for="input-<?php echo esc_attr($name); ?>">
    <span class="ren-toggle-text">
        <span class="ren-label"><?php echo esc_html($field->label); ?></span>
        <?php if ('' !== $field->description) { ?>
            <span class="ren-help"><?php echo esc_html($field->description); ?></span>
        <?php } ?>
    </span>
    <input
        class="ren-toggle-input peer"
        id="input-<?php echo esc_attr($name); ?>"
        name="<?php echo esc_attr($name); ?>"
        type="checkbox"
        <?php echo $checked ? 'checked="checked"' : ''; ?> />
    <span class="ren-toggle-track"></span>
</label>
