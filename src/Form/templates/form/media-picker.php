<?php

declare(strict_types=1);

/** @var $field FormField */

use Kahric\TheTheme\Form\FormField;
use Kahric\TheTheme\Form\FormFieldType;

if (!defined('ABSPATH') || !isset($field) || (FormFieldType::IMAGE !== $field->type && FormFieldType::VIDEO !== $field->type)) {
    return;
}

$name = $field->name;
$type = $field->type->value;
$your_img_id = $field->value;

$upload_link = esc_url(get_upload_iframe_src($type, $name));
$your_img_src = null !== $your_img_id ? wp_get_attachment_image_src($your_img_id, 'medium') : false;
$you_have_img = is_array($your_img_src);
?>

<span class="ren-label"><?php echo esc_html($field->label); ?></span>
<?php if ('' !== $field->description) { ?>
    <p class="ren-help"><?php echo esc_html($field->description); ?></p>
<?php } ?>

<div class="media-picker ren-media">
    <div class="image-container ren-media-preview<?php echo $you_have_img ? '' : ' is-empty'; ?>">
        <?php if ($you_have_img) : ?>
            <img src="<?php echo esc_url($your_img_src[0]); ?>" alt="" />
        <?php endif; ?>
    </div>

    <p class="hide-if-no-js ren-media-actions">
        <a class="ren-media-btn add-media<?php echo $you_have_img ? ' hidden' : ''; ?>" href="<?php echo $upload_link; ?>">
            <span class="dashicons dashicons-format-image"></span> Odaberi sliku
        </a>
        <a class="ren-media-btn ren-media-btn--ghost delete-media<?php echo $you_have_img ? '' : ' hidden'; ?>" href="#">
            <span class="dashicons dashicons-trash"></span> Ukloni sliku
        </a>
    </p>

    <input class="image-id" id="media-input-<?php echo esc_attr($name); ?>" name="<?php echo esc_attr($name); ?>" type="hidden" value="<?php echo esc_attr((string) $your_img_id); ?>" />
</div>
