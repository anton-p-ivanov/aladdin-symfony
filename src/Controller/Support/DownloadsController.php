<?php

namespace App\Controller\Support;

use App\Repository\Repository;
use App\Resource\Catalog\Element;
use App\Resource\Storage\Storage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Role\Role;

/**
 * Class DownloadsController
 *
 * @Route("/support/downloads")
 *
 * @package App\Controller\Support
 */
class DownloadsController extends AbstractController
{
    /**
     * @Route("/{uuid<[\w\-]{36}>}", name="support:downloads:view")
     *
     * @param Repository $repository
     * @param string $uuid
     *
     * @return Response
     */
    public function view(Repository $repository, string $uuid): Response
    {
        /* @var $storage Storage */
        $storage = null;

        /* @var $element Element */
        $element = $repository->get(Element::class)->findOne($uuid);

        if (!$element) {
            throw new NotFoundHttpException('Requested element not found.');
        }

        if ($storageUuid = $element->getValues()['FILE']) {
            $storage = $repository->get(Storage::class)->findOne($storageUuid);
            $storage->isGranted = $this->isAccessGranted($storage);
        }

        return $this->render("support/downloads/view.html.twig", ['element' => $element, 'storage' => $storage]);
    }

    /**
     * @Route("/{uuid<[\w\-]{36}>}/get", name="download")
     *
     * @param Repository $repository
     * @param string $uuid
     *
     * @return Response
     */
    public function download(Repository $repository, string $uuid): Response
    {
        /* @var $storage Storage */
        $storage = $repository->get(Storage::class)->findOne($uuid);

        if (!$storage) {
            throw new NotFoundHttpException('Requested file not found.');
        }

        if (!$this->isAccessGranted($storage)) {
            throw new AccessDeniedHttpException('You don`t have privileges to download this file.');
        }

        $filename = $this->getParameter('files.host') . "/" . $storage->getFile()->getUuid() . "/download";

        $size = $storage->getFile()->getSize();
        $name = urlencode($storage->getFile()->getName());

        $headers = [
            "Content-Type" => "application/octet-stream",
            "Content-Length" => $size,
            "Content-Disposition" =>  "attachment; filename=\"$name\"",
            "Pragma" => "public",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0"
        ];

        $response = new StreamedResponse();
        $response->headers->add($headers);
        $response->setCallback(
            function () use ($filename) {
                $this->readRemoteFile($filename);
            }
        );

        return $response;
    }

    /**
     * @param string $filename
     * @param int $chunkSize
     */
    public function readRemoteFile(string $filename, $chunkSize = 1048576): void
    {
        $context = stream_context_create(['ssl' => ['verify_peer' => false]]);

        if ($stream = fopen($filename, 'rb', false, $context)) {
            while (!feof($stream)) {
                echo stream_get_contents($stream, $chunkSize, -1);
            }
            fclose($stream);
            return;
        }

        throw new BadRequestHttpException('Requested file could not be read.');
    }

    /**
     * @param Storage $storage
     *
     * @return bool
     */
    protected function isAccessGranted(Storage $storage): bool
    {
        $roles = $storage->getRoles();
        if (count($roles) === 0 || array_search('GUEST', $roles)) {
            return true;
        }

        $token = $this->get('security.token_storage')->getToken();
        if ($token && ($userRoles = $token->getRoles())) {
            $userRoles = array_map(
                function (Role $role) {
                    return $role->getRole();
                },
                $userRoles
            );

            return count(array_intersect($userRoles, $roles)) > 0;
        }

        return false;
    }
}