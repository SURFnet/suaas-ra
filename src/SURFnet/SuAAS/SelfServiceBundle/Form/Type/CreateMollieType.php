<?php

namespace SURFnet\SuAAS\SelfServiceBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CreateMollieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'phoneNumber',
                null,
                array(
                    'required' => true,
                    'widget_addon' => array(
                        'type' => 'prepend',
                        'text' => '+31 (0) 6 - '
                    ),
                    'attr' => array(
                        'placeholder' => '12345678',
                    )
                )
            )
            ->add('Proceed', 'submit');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SURFnet\SuAAS\DomainBundle\Command\CreateMollieCommand',
        ));
    }

    public function getName()
    {
        return 'self_service_create_mollie';
    }
}
