<?php

declare(strict_types=1);

namespace Kahric\TheTheme\Form;

enum FormFieldType: string
{
    case IMAGE = 'image';
    case VIDEO = 'video';
    case TEXT = 'text';
    case TEXTAREA = 'textarea';
    case NUMBER = 'number';
    case CHECKBOX = 'checkbox';
    case SELECT = 'select';

    public function getTemplatePath(): string
    {
        $path = get_template_directory();

        return match ($this) {
            self::IMAGE, self::VIDEO => $path . '/src/Form/templates/form/media-picker.php',
            self::TEXT => $path . '/src/Form/templates/form/text-field.php',
            self::TEXTAREA => $path . '/src/Form/templates/form/textarea-field.php',
            self::NUMBER => $path . '/src/Form/templates/form/text-field.php',
            self::CHECKBOX => $path . '/src/Form/templates/form/checkbox-field.php',
            self::SELECT => $path . '/src/Form/templates/form/select-field.php',
        };
    }
}
