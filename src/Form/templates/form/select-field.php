<?php

declare(strict_types=1);

/** @var $field FormField */

use Kahric\TheTheme\Form\FormField;
use Kahric\TheTheme\Form\FormFieldType;

if (!defined('ABSPATH') || !isset($field) || FormFieldType::SELECT !== $field->type) {
    return;
}

$name = $field->name;
$value = $field->value;
$options = $field->options;
?>

<label for="input-<?php echo esc_attr($name); ?>" class="ren-label"><?php echo esc_html($field->label); ?></label>
<?php if ('' !== $field->description) { ?>
    <p class="ren-help"><?php echo esc_html($field->description); ?></p>
<?php } ?>
<select class="ren-input ren-select" id="input-<?php echo esc_attr($name); ?>" name="<?php echo esc_attr($name); ?>">
    <option value="">—</option>
    <?php foreach ($options as $id => $optionLabel) {
        printf(
            '<option value="%1$s"%2$s>%3$s</option>',
            esc_attr((string) $id),
            (string) $value === (string) $id ? ' selected="selected"' : '',
            esc_html((string) $optionLabel),
        );
    } ?>
</select>
