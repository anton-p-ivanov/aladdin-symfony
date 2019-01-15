<?php

namespace App\Controller\Company;

use App\Form\VacancyHandler;
use App\Form\VacancyType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class EmploymentController
 *
 * @Route("/company/employment")
 *
 * @package App\Controller\Company
 */
class EmploymentController extends AbstractController
{
    /**
     * @Route("/{vacancy<[\w\-]+>}/respond", name="company:employment:respond")
     *
     * @param Request $request
     * @param VacancyHandler $handler
     *
     * @return Response
     */
    public function respond(Request $request, VacancyHandler $handler): Response
    {
        $form = $this->createForm(VacancyType::class);
        $form->handleRequest($request);

        $path = $this->getParameter('twig.default_path') . '/company/employment/vacancies.json.twig';
        $json = json_decode(file_get_contents($path), true);

        $title = null;
        foreach ($json as $group) {
            foreach ($group['vacancies'] as $vacancy) {
                if ($vacancy['name'] === $request->get('vacancy')) {
                    $title = $vacancy['title'];
                    break;
                }
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $params = [
                'form_field_contact_name' => null,
                'form_field_contact_email' => null,
                'form_field_contact_phone' => null,
                'form_field_link' => null,
                'form_field_message' => null
            ];

            array_walk($params, function (&$value, $key) use ($form) {
                $value = $form->get($key)->getNormData();
            });

            $params['form_field_position'] = $title;

            if ($handler->send($params)) {
                return $this->render('company/employment/respond_sent.html.twig', ['title' => $title]);
            }

            foreach ($handler->getErrors()['errors'] as $attribute => $errors) {
                foreach ($errors as $error) {
                    $form->get($attribute)->addError(new FormError($error));
                }
            }
        }

        return $this->render('company/employment/respond.html.twig', [
            'form' => $form->createView(),
            'title' => $title
        ]);
    }
}