<?php

namespace SURFnet\SuAAS\DomainBundle\Service;

use SURFnet\SuAAS\DomainBundle\Command\Mail\SendConfirmationCommand;
use SURFnet\SuAAS\DomainBundle\Command\VerifyIdentityCommand;
use SURFnet\SuAAS\DomainBundle\Entity\AuthenticationMethod;
use SURFnet\SuAAS\DomainBundle\Entity\User;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;

/**
 * Class AuthenticationMethodService
 * @package SURFnet\SuAAS\DomainBundle\Service
 *
 * Service layer for the Authentication Methods
 *
 * @author Daan van Renterghem <dvrenterghem@ibuildings.nl>
 */
class AuthenticationMethodService extends ORMService
{
    /**
     * @var string Aggregate Root this service is constrained to
     */
    protected $rootEntityClass = 'SURFnet\SuAAS\DomainBundle\Entity\AuthenticationMethod';

    /**
     * Retrieve 0 or 1 token of a particular type for a particular user
     *
     * @param string $type
     * @param User   $user
     * @return null|AuthenticationMethod
     */
    protected function findTokenOfTypeForUser($type, User $user)
    {
        return $this->getRepository()->getTokenOfTypeForUser($type, $user);
    }

    /**
     * Drops all tokes belonging to a particular user
     *
     * @param User $user
     * @return null
     */
    public function removeTokensForUser(User $user)
    {
        return $this->getRepository()->removeForUser($user);
    }

    /**
     * Test if a particular user has a token
     *
     * @param User $user
     * @return bool
     */
    public function hasToken(User $user)
    {
        return (bool) $this->getRepository()->getTokenCountForUser($user);
    }

    /**
     * Confirm the registration for a user.
     *
     * @param User   $user
     * @param string $registrationCode
     * @return bool
     */
    public function confirmRegistration(User $user, $registrationCode)
    {
        $token = $this->getRepository()->findByEmailCode($registrationCode);

        if ($token === null) {
            return false;
        }

        if (!$token->isUserOwner($user)) {
            return false;
        }

        return true;
    }

    /**
     * Verify the registration code for a token
     *
     * @param AuthenticationMethod $token
     * @param Form                 $form
     * @return bool
     */
    public function verifyRegistrationCode(AuthenticationMethod $token, Form $form)
    {
        if (!$token->matchRegistrationCode($form->getData())) {
            $form->get('code')->addError(new FormError('Invalid Code Entered'));
            return false;
        }

        $this->persist($token)->flush();

        return true;
    }

    /**
     * Approve a Token
     *
     * @param AuthenticationMethod  $token
     * @param VerifyIdentityCommand $command
     * @throws \LogicException
     */
    public function approveToken(AuthenticationMethod $token, VerifyIdentityCommand $command)
    {
        if (!$command->verified || !$token->canConfirmIdentity()) {
            throw new \LogicException("Cannot approve token that has not been verified");
        }

        $token->approve($command->approvedBy);

        $this->persist($token)->flush();
    }

    /**
     * Decline a token
     *
     * @param AuthenticationMethod $token
     * @throws \LogicException
     */
    public function declineToken(AuthenticationMethod $token)
    {
        if (!$token->canConfirmIdentity()) {
            throw new \LogicException("Cannot decline token that has not been verified");
        }

        $token->decline();

        $this->persist($token)->flush();
    }

    /**
     * Find all tokens eligible for vetting for a particular RA (user)
     *
     * @param User $user
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTokensToVet(User $user)
    {
        /** @var \Doctrine\Common\Collections\ArrayCollection $tokens */
        $tokens = $this->getRepository()->findUnvettedTokens($user->getOrganisation());

        return $tokens->map(function ($token) {
            return $token->getView();
        });
    }

    /**
     * Get a list of all Vetted tokens for a particular RA (user)
     *
     * @param User $user
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getApprovedTokens(User $user)
    {
        /** @var \Doctrine\Common\Collections\ArrayCollection $tokens */
        $tokens = $this->getRepository()->findVettedTokens($user->getOrganisation());

        return $tokens->map(function ($token) {
            return $token->getView();
        });
    }

    /**
     * Find the token of a user
     *
     * @param User $user
     * @return null|AuthenticationMethod
     */
    public function findTokenForUser(User $user)
    {
        return $this->getRepository()->findTokenForUser($user);
    }

    /**
     * Create a new activation email for the user/token combo
     *
     * @param User                 $user
     * @param AuthenticationMethod $token
     * @return SendConfirmationCommand
     */
    public function createActivationEmail(User $user, AuthenticationMethod $token)
    {
        $userView = $user->getView();

        $command = new SendConfirmationCommand();
        $command->recepient = $userView->email;

        $code = $token->generateEmailToken($command);
        $this->persist($token)->flush();

        $command->parameters = array(
            'user' => $user,
            'code' => $code,
        );

        $command->template = 'SURFnetSuAASSelfServiceBundle:Email:confirmationEmail.html.twig';
        $command->subject = 'Activate Token';

        return $command;
    }

    /**
     * Create a new Registration email for the user/token combo
     *
     * @param User                 $user
     * @param AuthenticationMethod $token
     * @return SendConfirmationCommand
     */
    public function createRegistrationMail(User $user, AuthenticationMethod $token)
    {
        $userView = $user->getView();

        $command = new SendConfirmationCommand();
        $command->recepient = $userView->email;
        $code = $token->generateRegistrationCode();
        $this->persist($token)->flush();

        $command->parameters = array(
            'user' => $user,
            'code' => $code,
        );

        $command->template = 'SURFnetSuAASSelfServiceBundle:Email:registrationEmail.html.twig';
        $command->subject = 'Registration Code';

        return $command;
    }

    /**
     * Remove a particular token
     *
     * @param AuthenticationMethod $token
     */
    public function remove(AuthenticationMethod $token)
    {
        $em = $this->doctrine->getManager();
        $em->remove($token);
        $em->flush();
    }
}
