<?php

namespace App\Controller\Support;

use App\Service\Otrs\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TicketsController
 *
 * @Route("/support/tickets")
 *
 * @package App\Controller\Support
 */
class TicketsController extends AbstractController
{
    /**
     * @Route("/", name="support:tickets")
     *
     * @param Request $request
     * @param Client $client
     *
     * @return Response
     */
    public function tickets(Request $request, Client $client): Response
    {
        $cache = new FilesystemAdapter();
        $customer = 'webmaster@aladdin.ru';//$this->get('security.token_storage')->getToken()->getUser()->getEmail()

        if ($request->get('refresh')) {
            $cache->deleteItem('tickets.' . sha1($customer));
            return $this->redirectToRoute('support:tickets');
        }

        $cacheItem = $cache->getItem('tickets.' . sha1($customer));
        $cacheItem->expiresAfter(\DateInterval::createFromDateString('1 hour'));

        if (!$cacheItem->isHit()) {
            $cacheDate = new \DateTime();
            $tickets = $client
                ->where([
                    'QueueIDs' => [22],
                    'CustomerID' => $customer,
                    'ArchiveFlags' => 'n',
                ])
                ->order(['Changed' => SORT_DESC, 'Created' => SORT_DESC])
                ->limit(10)
                ->all();

            $data = ['tickets' => $tickets, 'cacheDate' => $cacheDate];
            $cache->save($cacheItem->set($data));
        }
        else {
            $data = $cacheItem->get();
        }

        return $this->render("support/tickets/list.html.twig", $data);
    }

    /**
     * @Route("/{ticketID<\d+>}", name="support:tickets:view")
     *
     * @param string $ticketID
     * @param Client $client
     *
     * @return Response
     */
    public function view(string $ticketID, Client $client): Response
    {
        $ticket = $client->one($ticketID, ['AllArticles' => 1, 'ArticleOrder' => 'DESC', 'Attachments' => 0]);

        if (isset($ticket->ErrorCode)) {
            throw new NotFoundHttpException('Обращение не найдено');
        }

        $articles = is_array($ticket->getArticle()) ? $ticket->getArticle() : [$ticket->getArticle()];
        $articles = array_filter($articles, function ($article) {
            return in_array(strtolower($article->SenderType), ['customer', 'agent']) && $article->ArticleType != 'note-internal';
        });

        return $this->render("support/tickets/view.html.twig", [
            'ticket' => $ticket,
            'articles' => $articles
        ]);
    }
}