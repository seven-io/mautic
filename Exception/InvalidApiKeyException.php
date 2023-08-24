<?php declare(strict_types=1);

namespace MauticPlugin\MauticSevenBundle\Exception;

class InvalidApiKeyException extends SevenException {
    protected $message = 'mautic.plugin.seven.missing_api_key';
}
