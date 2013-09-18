<?php

namespace SURFnet\SuAAS\RABundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class VerifyIdentityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'verified',
                'checkbox',
                array(
                    'required' => true,
                    'label' => 'I have verified the identity of the user',
                    'widget_checkbox_label' => 'widget'
                )
            );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SURFnet\SuAAS\DomainBundle\Command\VerifyIdentityCommand',
        ));
    }

    public function getName()
    {
        return 'ra_registration_verify_identity';
    }
}
