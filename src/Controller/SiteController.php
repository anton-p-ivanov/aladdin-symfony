<?php

namespace App\Controller;

use App\Form\FeedbackHandler;
use App\Form\FeedbackType;
use App\Service\Guzzle\Guzzle;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SiteController
 *
 * @Route("/")
 *
 * @package App\Controller
 */
class SiteController extends AbstractController
{
    /**
     * @Route("/", name="site_index")
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('site/index.html.twig');
    }

    /**
     * @Route("/feedback", name="feedback")
     *
     * @param Request $request
     * @param FeedbackHandler $handler
     *
     * @return Response
     */
    public function feedback(Request $request, FeedbackHandler $handler): Response
    {
        $form = $this->createForm(FeedbackType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $params = [
                'form_field_contact_name' => null,
                'form_field_contact_email' => null,
                'form_field_contact_phone' => null,
                'form_field_subject' => null,
                'form_field_message' => null
            ];

            array_walk($params, function (&$value, $key) use ($form) {
                $value = $form->get($key)->getNormData();
            });

            if ($handler->send($params)) {
                return $this->render('site/feedback_sent.html.twig');
            }

            foreach ($handler->getErrors()['errors'] as $attribute => $errors) {
                foreach ($errors as $error) {
                    $form->get($attribute)->addError(new FormError($error));
                }
            }
        }

        return $this->render('site/feedback.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/widget", name="widget", methods={"GET"})
     *
     * @param Request $request
     * @param Guzzle $guzzle
     *
     * @return Response
     */
    public function widget(Request $request, Guzzle $guzzle): Response
    {
        $data = null;
        $url = $request->headers->get('Widget-Request-Url');
        $conditions = $request->headers->get('Widget-Search-Conditions');

        $response = $guzzle->request('GET', $url, ['headers' => [
            'Access-Token' => '05faa7751d1b34f1e350a7bffa93e6f134233930',
            'Search-Conditions' => $conditions
        ]]);

        if ($response->getStatusCode() === Response::HTTP_OK) {
            $data = $response->getJson(true);
        }

        return $this->render($request->headers->get('Widget-Template'), [
            'response' => $data
        ]);
    }
}