<?php declare(strict_types=1);

namespace MauticPlugin\MauticSevenBundle\Form\Type;

use GuzzleHttp\Client;
use Mautic\IntegrationsBundle\Form\Type\Auth\BasicAuthKeysTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ConfigAuthType extends AbstractType {
    use BasicAuthKeysTrait;

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder->add('apiKey', PasswordType::class, [
                'always_empty' => false,
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    $this->getNotBlankConstraint(),
/*                    new Callback(
                        function ($validateMe, ExecutionContextInterface $context) {
                            $res = (new Client)->post('https://gateway.seven.io/api/balance', [
                                'headers' => [
                                    'Accept' => 'application/json',
                                    //'Content-Type' => 'application/json',
                                    'X-Api-Key' => $validateMe,
                                ],
                            ]);
                            $balance = $res->getBody()->getContents();
                            dump($balance);
                            $balance = json_decode($balance);
                            if (!is_array($balance) || !is_object($balance)) $context
                                ->buildViolation('mautic.plugin.seven.missing_api_key')
                                ->addViolation();
                        }
                    ),*/
                ],
                'help' => 'mautic.plugin.seven.apikey_help',
                'label' => 'mautic.plugin.seven.apikey',
                'label_attr' => ['class' => 'control-label'],
                'required' => true,
                'trim' => true,
            ]
        );

        $builder->add('sms_from', TextType::class, [
                'attr' => ['class' => 'form-control', 'maxlength' => 16],
                'label' => 'mautic.plugin.seven.sms_from',
                'label_attr' => ['class' => 'control-label'],
                'required' => false,
            ]
        );

        $builder->add('voice_from', TextType::class, [
                'attr' => ['class' => 'form-control', 'maxlength' => 16],
                'label' => 'mautic.plugin.seven.voice_from',
                'label_attr' => ['class' => 'control-label'],
                'required' => false,
            ]
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults(['integration' => null]);
    }
}
