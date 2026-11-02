<?php

declare(strict_types=1);

/** @var $field FormField */

use Kahric\TheTheme\Form\FormField;
use Kahric\TheTheme\Form\FormFieldType;

if (!defined('ABSPATH') || !isset($field) || (FormFieldType::TEXT !== $field->type && FormFieldType::NUMBER !== $field->type)) {
    return;
}

$name = $field->name;
?>

<label for="input-<?php echo esc_attr($name); ?>" class="ren-label"><?php echo esc_html($field->label); ?></label>
<?php if ('' !== $field->description) { ?>
    <p class="ren-help"><?php echo esc_html($field->description); ?></p>
<?php } ?>
<input
    class="ren-input"
    id="input-<?php echo esc_attr($name); ?>"
    name="<?php echo esc_attr($name); ?>"
    type="<?php echo esc_attr($field->type->value); ?>"
    value="<?php echo esc_attr((string) $field->value); ?>" />
