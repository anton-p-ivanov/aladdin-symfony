<?php

namespace App\Form\Profile;

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ConfirmHandler
 *
 * @package App\Form\Profile
 */
class ConfirmHandler extends BaseHandler
{
    /**
     * @param FormInterface $form
     *
     * @return bool
     */
    public function confirm(FormInterface $form): bool
    {
        $submittedData = $form->getData();
        $response = $this->client->post('/profile/confirm', $submittedData);

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

            return false;
        }

        if ($response->getStatusCode() === Response::HTTP_OK) {
            $data = $response->getJson();
            return $this->auth($data['email'], $submittedData['password']);
        }

        $errorMessage = 'При подтверждении Вашей учётной записи возникли ошибки. Проверьте правильность заполнения ' .
            'полей формы и повторите попытку. Если ошибка повторяется, обратитесь к администратору сайта.';

        $form->addError(new FormError($errorMessage));

        return false;
    }
}