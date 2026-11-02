<?php

declare(strict_types=1);

namespace Kahric\TheTheme\Form;

use Kahric\TheTheme\Helper\TemplateRenderer;

final readonly class FormRenderer
{
    public function render(Form $form): void
    {
        echo TemplateRenderer::render($form->getTemplate(), ['form' => $form]);
    }
}
