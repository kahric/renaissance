<?php

declare(strict_types=1);

namespace Kahric\TheTheme\Form;

enum FormSectionType: string
{
    private const string TEMPLATE_PATH = '/src/Form/templates/form-section.php';

    case GENERAL_SETTINGS = 'general';
    case HOMEPAGE_SETTINGS = 'homepage';
    case MAILCHIMP_SETTINGS = 'mailchimp';

    public function getLabel(): string
    {
        return match ($this) {
            self::GENERAL_SETTINGS => 'Generičke postavke',
            self::HOMEPAGE_SETTINGS => 'Naslovnica',
            self::MAILCHIMP_SETTINGS => 'Mailchimp',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::GENERAL_SETTINGS => 'Osnovne postavke teme — obavijesti, integracije i footer.',
            self::HOMEPAGE_SETTINGS => 'Sadržaj naslovnice: hero, kategorije, izdvojeni proizvodi i banner.',
            self::MAILCHIMP_SETTINGS => 'Povezivanje s Mailchimpom i postavke newsletter kupona.',
        };
    }

    public function getIconClass(): string
    {
        return match ($this) {
            self::GENERAL_SETTINGS => 'dashicons-admin-generic',
            self::HOMEPAGE_SETTINGS => 'dashicons-admin-home',
            self::MAILCHIMP_SETTINGS => 'dashicons-email-alt',
        };
    }


    public function getTemplate(): string
    {
        return get_template_directory() . self::TEMPLATE_PATH;
    }
}
