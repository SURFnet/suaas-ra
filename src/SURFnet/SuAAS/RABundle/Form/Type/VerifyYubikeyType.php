<?php

namespace SURFnet\SuAAS\RABundle\Form\Type;

use SURFnet\SuAAS\SelfServiceBundle\Form\Type\CreateYubikeyType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class VerifyYubikeyType extends CreateYubikeyType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SURFnet\SuAAS\DomainBundle\Command\VerifyYubikeyCommand',
        ));
    }

    public function getName()
    {
        return 'ra_registration_verify_yubikey';
    }
}
