<?php

declare(strict_types=1);

namespace Kahric\TheTheme\Form;

final class FormField
{
    public const string VALUE_NOT_SET = '!![the_theme_value_not_set]';

    public function __construct(
        public FormFieldType $type,
        public string $label,
        public string $name,
        public string|null $value = self::VALUE_NOT_SET,
        public array $options = [],
        public string $description = '',
        /** Accordion group title this field belongs to (empty = ungrouped). */
        public string $group = '',
        /**
         * Conditional display. ['field' => '<other field name>', 'value' => '<value>'].
         * Use value '*' to mean "show when the other field is non-empty".
         */
        public ?array $showIf = null,
    )
    {
    }

    /**
     * Wide fields (textarea, media) span the full settings grid;
     * short fields (text, number, select, checkbox) sit two-per-row.
     */
    public function isWide(): bool
    {
        return in_array($this->type, [FormFieldType::TEXTAREA, FormFieldType::IMAGE, FormFieldType::VIDEO], true);
    }
}
