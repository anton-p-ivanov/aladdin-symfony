<?php

namespace App\Controller\Support;

use App\Form as Form;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SupportController
 *
 * @Route("/support")
 *
 * @package App\Controller\Support
 */
class SupportController extends AbstractController
{
    /**
     * @Route("/request/{code<[\w\-]+>}", name="support:request")
     *
     * @param Request $request
     * @param Form\BaseHandler $handler
     *
     * @return Response
     */
    public function request(Request $request, Form\BaseHandler $handler): Response
    {
        $code = $request->get('code');
        $types = [
            'jacarta_sdk' => Form\Sdk\JaCartaType::class,
            'jacarta-2_sdk' => Form\Sdk\JaCarta2Type::class,
            'jc-mobile_sdk' => Form\Sdk\JCMobileType::class,
            'jc-webclient_sdk' => Form\Sdk\JCWebClientType::class,
            'jms_sdk' => Form\Sdk\JmsType::class,
            'callback' => Form\Request\CallbackType::class,
            'presentation' => Form\Request\PresentationType::class,
            'sac' => Form\Request\SacType::class
        ];

        if (!array_key_exists($code, $types)) {
            throw new NotFoundHttpException();
        }

        $form = $this->createForm($types[$code]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $handler->setFormUuid($types[$code]::${"form_uuid"});
            if ($handler->send($form->getNormData())) {
                return $this->render("support/requests/" . $code . "_sent.html.twig");
            }

            foreach ($handler->getErrors()['errors'] as $attribute => $errors) {
                foreach ($errors as $error) {
                    $form->get($attribute)->addError(new FormError($error));
                }
            }
        }

        return $this->render("support/requests/" . $code . ".html.twig", [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/sdk/request", name="support:sdk:request")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function sdkRequest(Request $request): Response
    {
        return $this->redirectToRoute('support:request', ['code' => strtolower($request->get('type'))]);
    }
}