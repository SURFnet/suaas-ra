<?php

namespace SURFnet\SuAAS\SelfServiceBundle\Service;

use SURFnet\SuAAS\DomainBundle\Command\Mail\MailCommand;
use \Swift_Message as Message;
use \Swift_Image as Image;
use \Swift_Mailer as Mailer;
use Symfony\Component\Templating\EngineInterface;

class MailService
{
    private $mailer;
    private $templateEngine;

    /**
     * @param Mailer          $mailer
     * @param EngineInterface $templateEngine
     */
    public function __construct(Mailer $mailer, EngineInterface $templateEngine)
    {
        $this->mailer = $mailer;
        $this->templateEngine = $templateEngine;
    }

    public function sendMail(MailCommand $command)
    {
        $message = new Message();
        $path = __DIR__ . '/../Resources/public/images/SURFnet_logo.jpg';

        $image = $message->embed(Image::fromPath($path));
        $message->setSubject($command->subject);

        $message->setBody(
            $this->templateEngine->render(
                $command->template,
                array_merge($command->parameters, array('logo' => $image))
            ),
            'text/html'
        );
        $message->addFrom('suaas.pilot@gmail.com', 'SURFnet SuAAS');
        $message->addTo($command->recepient);

        $this->mailer->send($message);
    }
}
