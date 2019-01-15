<?php

namespace App\Validator\Constraints;

use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class ReCaptchaValidator
 * @package App\Validator\Constraints
 */
class ReCaptchaValidator extends ConstraintValidator
{
    /**
     * @param mixed $value
     * @param Constraint|ReCaptcha $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) take care of that
        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        if (!$this->isCaptchaValid($value)) {
            // Add error message to root form element
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    protected function isCaptchaValid(string $value): bool
    {
        $client = new Client([
            'base_uri' => "https://www.google.com",
        ]);

        $response = $client->post('/recaptcha/api/siteverify', ['form_params' => [
            'secret' => '6Le0-HcUAAAAAKHbhpUBvZ6xpEKg4GBEo6-RDh_f',
            'response' => $value
        ]]);

        if ($response->getStatusCode() === Response::HTTP_OK) {
            $data = json_decode($response->getBody()->getContents());
            return $data->success;
        }

        return false;
    }
}