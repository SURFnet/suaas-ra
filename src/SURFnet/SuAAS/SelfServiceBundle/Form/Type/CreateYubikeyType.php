<?php

namespace SURFnet\SuAAS\SelfServiceBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CreateYubikeyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'otp',
                'text',
                array(
                    'required' => true,
                    'label' => 'Yubikey Password:',
                    'widget_addon' => array(
                        'type' => 'prepend',
                        'icon' => 'key'
                    ),
                    'attr' => array(
                        'autofocus' => true,
                        'placeholder' => 'enter one-time-password',
                    )
                )
            )
            ->add('Verify', 'submit');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SURFnet\SuAAS\DomainBundle\Command\CreateYubikeyCommand',
        ));
    }

    public function getName()
    {
        return 'self_service_create_yubikey';
    }
}
