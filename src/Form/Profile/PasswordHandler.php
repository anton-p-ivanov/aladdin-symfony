<?php

namespace App\Form\Profile;

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PasswordHandler
 *
 * @package App\Form\Profile
 */
class PasswordHandler extends BaseHandler
{
    /**
     * @param FormInterface $form
     *
     * @return bool
     */
    public function change(FormInterface $form): bool
    {
        $submittedData = $form->getData();
        $response = $this->client->post('/profile/password', [
            'code' => $submittedData['code'],
            'password' => $submittedData['password']
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

        if ($response->getStatusCode() === Response::HTTP_OK) {
            $responseData = $response->getJson();
            return $this->auth($responseData['email'], $submittedData['password']);
        }

        return false;
    }
}