<?php

namespace App\Controller\Partners;

use App\Form\Partner\PartnerHandler;
use App\Repository\Repository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SearchController
 *
 * @Route("/partners")
 *
 * @package App\Controller\Partners
 */
class SearchController extends AbstractController
{
    /**
     * @Route("/", name="partners")
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->redirectToRoute("partners:search", ["type" => "business"], 301);
    }

    /**
     * @Route("/{type<(business|technical)+>}", name="partners:search")
     *
     * @param Request $request
     *
     * @param Repository $repository
     * @param string $type
     *
     * @return Response
     */
    public function search(Request $request, Repository $repository, string $type): Response
    {
        $className = 'App\\Form\\Partner\\' . ucfirst($type) . 'Type';
        if (!class_exists($className)) {
            throw new NotFoundHttpException('Invalid type parameter.');
        }

        /* @var $handler PartnerHandler */
        $handlerName = 'App\\Form\\Partner\\' . ucfirst($type) . 'Handler';
        $handler = new $handlerName($repository, $request);

        $defaults = [
            'country' => $request->get('country', 1),
            'city' => $request->get('country', 1) === 1 ? 'Москва' : null
        ];

        $form = $this->createForm($className, array_merge($defaults, $request->query->all()));
        $form->handleRequest($request);

        if ($form->isSubmitted() || $request->isXmlHttpRequest()) {
            return $this->render('partners/results.html.twig', [
                'type' => $type,
                'paginator' => $handler->search($form->getData()),
            ]);
        }

        return $this->render('partners/search.html.twig', [
            'type' => $type,
            'form' => $form->createView(),
            'paginator' => $handler->search($form->getData())
        ]);
    }

    /**
     * @Route("/{type<(business|technical)+>}/{code<\w+>}", name="partners:view")
     *
     * @param string $type
     * @param string $code
     * @param Repository $repository
     *
     * @return Response
     */
    public function view(string $type, string $code, Repository $repository): Response
    {
        $className = '\\App\\Resource\\Partner\\' . ucfirst($type);
        $response = $repository->get($className)->find([
            'catalog' => 'technical',
            'code' => $code
        ]);

        if (!$response) {
            throw new NotFoundHttpException('Partner not found.');
        }

        return $this->render("partners/view.html.twig", ['partner' => array_shift($response)]);
    }
}