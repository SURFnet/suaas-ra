<?php

namespace SURFnet\SuAAS\SelfServiceBundle\Twig;

use \Twig_Environment as Environment;
use \Twig_Extension as Extension;
use \Twig_SimpleFunction as TwigFunction;

class TokenThumbnailExtension extends Extension
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
            'tokenThumbnails' => new TwigFunction(
                'tokenThumbnails',
                array($this, 'tokenThumbnails'),
                array('is_safe' => array('html'))
            )
        );
    }

    public function tokenThumbnails($showLinks = true, array $whiteList = array())
    {
        $showAll = $showTiqr = $showSMS = $showYubikey = false;
        $count = 0;
        if (empty($whiteList)) {
            $showAll = true;
            $count = 3;
        } else {
            foreach ($whiteList as $key) {
                ${'show' . $key} = true;
                $count++;
            }
        }

        $width = 12/$count;

        return $this->environment->render(
            'SURFnetSuAASSelfServiceBundle:Token:tokenThumbnails.html.twig',
            array(
                'showLink' => $showLinks,
                'width' => $width,
                'showAll' => $showAll,
                'showTiqr' => $showTiqr,
                'showSMS' => $showSMS,
                'showYubikey' => $showYubikey
            )
        );
    }

    public function getName()
    {
        return 'token_thumbnail_extension';
    }
}
