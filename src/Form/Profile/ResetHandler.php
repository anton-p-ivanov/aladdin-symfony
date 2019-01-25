<?php

namespace App\Form\Profile;

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ResetHandler
 *
 * @package App\Form\Profile
 */
class ResetHandler extends BaseHandler
{
    /**
     * @param FormInterface $form
     *
     * @return bool
     */
    public function reset(FormInterface $form): bool
    {
        $submittedData = $form->getData();
        $response = $this->client->post('/profile/reset', $submittedData);

        if ($response->hasValidationErrors()) {
            $validationErrors = $response->getValidationErrors();
            foreach ($validationErrors as $field => $errors) {
                foreach ($errors as $error) {
                    $form->get($field)->addError(new FormError($error));
                }
            }
        }

        return $response->getStatusCode() === Response::HTTP_OK;
    }
}