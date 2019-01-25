<?php

namespace App\Form\Profile;

use App\Security\WebServiceAuthenticator;
use App\Service\Guzzle\Guzzle;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RegisterHandler
 *
 * @package App\Form\Profile
 */
class RegisterHandler extends BaseHandler
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * RegisterHandler constructor.
     *
     * @param ContainerInterface $container
     * @param Guzzle $client
     * @param RequestStack $requestStack
     * @param WebServiceAuthenticator $authenticator
     */
    public function __construct(
        ContainerInterface $container,
        Guzzle $client,
        RequestStack $requestStack,
        WebServiceAuthenticator $authenticator
    ) {
        parent::__construct($client, $requestStack, $authenticator);

        $this->container = $container;
    }

    /**
     * @param FormInterface $form
     *
     * @return bool
     */
    public function register(FormInterface $form): bool
    {
        $submittedData = $form->getData();
        $response = $this->client->post('/profile/register', [
            'email' => $submittedData['email'],
            'password' => $submittedData['password'],
            'fname' => $submittedData['fname'],
            'lname' => $submittedData['lname']
        ]);

        if ($response->hasValidationErrors()) {
            $validationErrors = $response->getValidationErrors();
            foreach ($validationErrors as $field => $errors) {
                if (is_integer($field)) {
                    $form->addError(new FormError($errors));
                }
                foreach ($errors as $error) {
                    $form->get($field)->addError(new FormError($error));
                }
            }
        }

        return $response->getStatusCode() === Response::HTTP_OK;
    }
}