<?php

namespace App\Controller\Company;

use App\Form\FeedbackHandler;
use App\Form\FeedbackType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CompanyController
 *
 * @Route("/company")
 *
 * @package App\Controller\Company
 */
class CompanyController extends AbstractController
{
    /**
     * @Route("/contacts", name="company:contacts")
     *
     * @param Request $request
     * @param FeedbackHandler $handler
     *
     * @return Response
     */
    public function contacts(Request $request, FeedbackHandler $handler): Response
    {
        $form = $this->createForm(FeedbackType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($handler->send()) {
                // ok

                return $this->render('company/contacts_sent.html.twig');
            }

            var_dump($handler->getErrors());
//            foreach ($handler->getErrors() as $error) {
//
//            }
        }

        return $this->render('company/contacts.html.twig', [
            'form' => $form->createView()
        ]);
    }
}