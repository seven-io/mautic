<?php declare(strict_types=1);

namespace MauticPlugin\MauticSevenBundle\Integration;

use Mautic\IntegrationsBundle\Integration\BasicIntegration;
use Mautic\IntegrationsBundle\Integration\DefaultConfigFormTrait;
use Mautic\IntegrationsBundle\Integration\Interfaces\BasicInterface;
use Mautic\IntegrationsBundle\Integration\Interfaces\ConfigFormAuthInterface;
use Mautic\IntegrationsBundle\Integration\Interfaces\ConfigFormInterface;
use MauticPlugin\MauticSevenBundle\Form\Type\ConfigAuthType;

class SevenIntegration extends BasicIntegration implements BasicInterface, ConfigFormInterface, ConfigFormAuthInterface {
    use DefaultConfigFormTrait;

    const NAME = 'Seven';

    /**
     * {@inheritdoc}
     * @return string
     */
    public function getAuthConfigFormName(): string {
        return ConfigAuthType::class;
    }

    /** {@inheritdoc} */
    public function getName(): string {
        return self::NAME;
    }

    public function getIcon(): string {
        return 'plugins/MauticSevenBundle/Assets/img/seven.png';
    }
}
