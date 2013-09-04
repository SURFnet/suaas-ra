<?php

namespace SURFnet\SuAAS\SelfServiceBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class VerifyMollieTokenType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'password',
                'text',
                array(
                    'required' => true,
                    'attr' => array(
                        'placeholder' => 'enter the password please',
                    )
                )
            )
            ->add('Verify', 'submit');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SURFnet\SuAAS\DomainBundle\Command\VerifyMollieTokenCommand',
        ));
    }

    public function getName()
    {
        return 'self_service_create_mollie';
    }
}
