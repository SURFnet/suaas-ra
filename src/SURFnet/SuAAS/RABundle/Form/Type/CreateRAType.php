<?php

namespace SURFnet\SuAAS\RABundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CreateRAType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'contactInfo',
                null,
                array(
                    'required' => true,
                    'label' => 'Contact Information',
                )
            )
            ->add(
                'location',
                null,
                array(
                    'required' => true,
                    'label' => 'Location'
                )
            )
            ->add('Promote', 'submit');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SURFnet\SuAAS\DomainBundle\Command\PromoteRACommand',
        ));
    }

    public function getName()
    {
        return 'management_ra_create';
    }
}
