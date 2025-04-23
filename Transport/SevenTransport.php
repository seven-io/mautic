<?php declare(strict_types=1);

namespace MauticPlugin\MauticSevenBundle\Transport;

use Exception;
use Mautic\IntegrationsBundle\Exception\IntegrationNotFoundException;
use Mautic\IntegrationsBundle\Exception\PluginNotConfiguredException;
use Mautic\IntegrationsBundle\Helper\IntegrationsHelper;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\LeadBundle\Model\DoNotContact;
use Mautic\SmsBundle\Sms\TransportInterface;
use MauticPlugin\MauticSevenBundle\Exception\SevenPluginException;
use MauticPlugin\MauticSevenBundle\Integration\SevenIntegration;
use Monolog\Logger;

/**
 * Class SevenTransport is the transport service for mautic.
 */
class SevenTransport implements TransportInterface {
    private ?string $apiKey = null;
    private bool $clientConfigured = false;

    public function __construct(
        private readonly IntegrationsHelper $integrationsHelper,
        private readonly Logger             $logger,
        private readonly DoNotContact $doNotContactService
    ) {
    }

    /**
     * @param Lead $lead
     * @param string $content
     * @return bool|PluginNotConfiguredException|string
     * @throws SevenPluginException
     * @throws IntegrationNotFoundException
     * @throws Exception
     * @noinspection PhpUnused
     */
    public function sendSms(Lead $lead, $content) {
        $this->logger->info('sms via seven', ['content' => $content]);

        $number = $lead->getLeadPhoneNumber();
        if (empty($number)) return false;

        if (!$this->clientConfigured && !$this->configureClient())
            return new PluginNotConfiguredException;

        try {
            $params = [
                'to' => $number,
                'text' => $content
            ];

            $ch = curl_init('https://gateway.seven.io/api/sms');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: application/json',
                'Content-type: application/json',
                'SentWith: Mautic',
                'X-Api-Key: ' . $this->apiKey
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);
        } catch (Exception $e) {
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
                $this->apiKey = $keys['apiKey'];
                $this->clientConfigured = true;
                return true;
            }
        }

        return false;
    }
}
