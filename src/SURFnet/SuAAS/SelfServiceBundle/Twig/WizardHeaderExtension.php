<?php

namespace SURFnet\SuAAS\SelfServiceBundle\Twig;

use \Twig_Environment as Environment;
use \Twig_Extension as Extension;
use \Twig_SimpleFunction as TwigFunction;

class WizardHeaderExtension extends Extension
{
    /**
     * @var Environment
     */
    private $environment;

    /**
     * {@inheritdoc}
     */
    public function initRuntime(Environment $environment)
    {
        $this->environment = $environment;
    }

    public function getFunctions()
    {
        return array(
            'wizardHeader' => new TwigFunction(
                'wizardHeader',
                array($this, 'wizardHeader'),
                array('is_safe' => array('html'))
            )
        );
    }

    public function wizardHeader($stepNumber)
    {
        if ($stepNumber > 4) {
            $barWidth = 100;
        } else {
            $barWidth = 8 + ($stepNumber - 1) * 25;
        }

        return $this->environment->render(
            'SURFnetSuAASSelfServiceBundle:Wizard:wizardHeader.html.twig',
            array('width' => $barWidth)
        );
    }

    public function getName()
    {
        return 'wizard_header_extesion';
    }
}
