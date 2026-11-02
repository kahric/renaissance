<?php

declare(strict_types=1);

/** @var $field FormField */

use Kahric\TheTheme\Form\FormField;
use Kahric\TheTheme\Form\FormFieldType;

if (!defined('ABSPATH') || !isset($field) || FormFieldType::TEXTAREA !== $field->type) {
    return;
}

$name = $field->name;
?>

<label for="input-<?php echo esc_attr($name); ?>" class="ren-label"><?php echo esc_html($field->label); ?></label>
<?php if ('' !== $field->description) { ?>
    <p class="ren-help"><?php echo esc_html($field->description); ?></p>
<?php } ?>
<textarea
    class="ren-input ren-textarea"
    id="input-<?php echo esc_attr($name); ?>"
    name="<?php echo esc_attr($name); ?>"
    rows="3"><?php echo esc_textarea((string) $field->value); ?></textarea>
