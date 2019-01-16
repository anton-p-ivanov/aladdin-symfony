<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BuyController
 *
 * @Route("/buy")
 *
 * @package App\Controller
 */
class BuyController extends AbstractController
{
    /**
     * @Route("/price", name="buy:price")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function price(Request $request): Response
    {
        $file = $request->get('file');
        $filename = $this->getParameter('kernel.root_dir') . "/../prices/xls/$file";

        if (stripos($file, 'enduser') === false || !file_exists($filename)) {
            throw new NotFoundHttpException();
        }

        return $this->file($filename);
    }
}