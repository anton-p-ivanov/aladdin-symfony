<?php

namespace App\Form\Profile;

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ProfileHandler
 *
 * @package App\Form\Profile
 */
class ProfileHandler extends BaseHandler
{
    /**
     * @param FormInterface $form
     *
     * @return bool
     */
    public function update(FormInterface $form): bool
    {
        $submittedData = $form->getData();
        $response = $this->client->put('/profile/update', [
            'email' => $submittedData['email'],
            'fname' => $submittedData['fname'],
            'lname' => $submittedData['lname'],
            'sname' => $submittedData['sname'],
            'phone' => $submittedData['phone'],
            'skype' => $submittedData['skype'],
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