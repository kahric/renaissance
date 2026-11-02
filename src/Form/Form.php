<?php

declare(strict_types=1);

namespace Kahric\TheTheme\Form;

final class Form
{
    /** @var array<string, FormField[]> $fields */
    private array $fields;

    public function __construct(public readonly string $name, private readonly array $defaults)
    {
    }

    public function addField(FormSectionType $section, FormField $field, bool $override = false): self
    {
        if (!$override && isset($this->fields[$section->value][$field->name])) {
            throw new \LogicException('Overriding a non overridable field: '. $field->name);
        }

        // set the default value
        if (FormField::VALUE_NOT_SET === $field->value) {
            $field->value = $this->defaults[$field->name] ?? null;
        }

        $this->fields[$section->value][$field->name] = $field;

        return $this;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getTemplate(): string
    {
        return get_template_directory() . '/src/Form/templates/form.php';
    }
}
