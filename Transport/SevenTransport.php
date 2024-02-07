<?php declare(strict_types=1);

namespace MauticPlugin\MauticSevenBundle\Transport;

use Exception;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Mautic\IntegrationsBundle\Exception\IntegrationNotFoundException;
use Mautic\IntegrationsBundle\Exception\PluginNotConfiguredException;
use Mautic\IntegrationsBundle\Helper\IntegrationsHelper;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\LeadBundle\Model\DoNotContact;
use Mautic\SmsBundle\Sms\TransportInterface;
use MauticPlugin\MauticSevenBundle\Exception\SevenPluginException;
use MauticPlugin\MauticSevenBundle\Integration\SevenIntegration;
use Monolog\Logger;
use Seven\Api\Client;
use Seven\Api\Exception\InvalidOptionalArgumentException;
use Seven\Api\Exception\InvalidRequiredArgumentException;
use Seven\Api\Params\SmsParams;
use Seven\Api\Resource\SmsResource;

/**
 * Class SevenTransport is the transport service for mautic.
 */
class SevenTransport implements TransportInterface {
    private ?Client $client = null;
    private bool $clientConfigured = false;

    public function __construct(
        private IntegrationsHelper $integrationsHelper,
        private Logger             $logger,
        private DoNotContact       $doNotContactService
    ) {
    }

    /**
     * @param Lead $lead
     * @param string $content
     * @return bool|PluginNotConfiguredException|string
     * @throws InvalidOptionalArgumentException
     * @throws InvalidRequiredArgumentException
     * @throws SevenPluginException
     * @throws IntegrationNotFoundException
     */
    public function sendSms(Lead $lead, $content) {
        $this->logger->info('sms via seven', ['content' => $content]);

        $number = $lead->getLeadPhoneNumber();
        if (empty($number)) return false;

        //if (!$this->client) throw new SevenPluginException('There is no client available');

        if (!$this->clientConfigured && !$this->configureClient())
            return new PluginNotConfiguredException;

        $util = PhoneNumberUtil::getInstance();
        $params = new SmsParams($content);

        try {
            $parsed = $util->parse($number, 'US');
            $number = $util->format($parsed, PhoneNumberFormat::E164);
            $number = substr($number, 1);
            $params->addTo($number);

            $resource = new SmsResource($this->client);
            $resource->dispatch($params);
        } catch (NumberParseException $exception) {
            $this->logger->info('Invalid number format', ['error' => $exception->getMessage()]);

            return 'mautic.seven.failed.invalid_phone_number';
        } catch (
        InvalidOptionalArgumentException|InvalidRequiredArgumentException $e
        ) {
            $this->logger->error('seven plugin unhandled exception',
                ['error' => $e->getMessage(), 'number' => $number]
            );
            throw $e;
        }

        return true;
    }

    /**
     * Add user to DNC.
     */
    private function unsubscribeInvalidUser(Lead $lead, Exception $exception): void {
        $this->logger->warning(
            'Invalid user added to DNC list. ' . $exception->getMessage(),
            ['exception' => $exception]
        );

        $this->doNotContactService->addDncForContact($lead->getId(), 'sms',
            \Mautic\LeadBundle\Entity\DoNotContact::UNSUBSCRIBED,
            $exception->getMessage());
    }

    /**
     * @throws IntegrationNotFoundException
     * @throws Exception
     */
    private function configureClient(): bool {
        $integration = $this->integrationsHelper->getIntegration(SevenIntegration::NAME);
        $integrationConfiguration = $integration->getIntegrationConfiguration();

        if ($integrationConfiguration->getIsPublished()) {
            $keys = $integrationConfiguration->getApiKeys();

            if (isset($keys['apiKey'])) {
                $this->client = new Client($keys['apiKey'], 'Mautic');
                $this->clientConfigured = true;
                return true;
            }
        }

        return false;
    }
}
