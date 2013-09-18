<?php

namespace SURFnet\SuAAS\RABundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class VerifyRegistrationCodeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'code',
                null,
                array(
                    'required' => true,
                    'label' => 'Registration Code',
                )
            )
            ->add('Check Code', 'submit');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SURFnet\SuAAS\DomainBundle\Command\VerifyRegistrationCodeCommand',
        ));
    }

    public function getName()
    {
        return 'ra_registration_verify_code';
    }
}
