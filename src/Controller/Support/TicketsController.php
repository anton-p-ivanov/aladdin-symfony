<?php

namespace App\Controller\Support;

use App\Form\Support\MessageHandler;
use App\Form\Support\MessageType;
use App\Form\Support\TicketHandler;
use App\Form\Support\TicketType;
use App\Security\User\WebServiceUser;
use App\Service\Otrs\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
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
     * @param Request $request
     *
     * @return string
     */
    protected function getCacheID(Request $request): string
    {
        return 'tickets.' . sha1($this->getCustomer()) . sha1($this->getConditions($request));
    }

    /**
     * @param Request $request
     *
     * @return string|string[]|null
     */
    protected function getConditions(Request $request)
    {
        $conditions = null;
        if ($request->headers->has('Widget-Search-Conditions')) {
            $conditions = $request->headers->get('Widget-Search-Conditions');
        }

        return $conditions;
    }

    /**
     * @return string
     */
    protected function getCustomer(): string
    {
        return $this->get('security.token_storage')->getToken()->getUser()->getUsername();
    }

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
        $customer = $this->getCustomer();
        $conditions = $this->getConditions($request);
        $cacheID = $this->getCacheID($request);

        if ($request->get('refresh')) {
            $cache->deleteItem($cacheID);
            return $this->redirectToRoute('support:tickets');
        }

        $cacheItem = $cache->getItem($cacheID);
        $cacheItem->expiresAfter(\DateInterval::createFromDateString('1 hour'));

        if (!$cacheItem->isHit()) {
            if ($conditions) {
                $conditions = json_decode($conditions, true);
            }
            $cacheDate = new \DateTime();
            $tickets = $client
                ->where([
                    'QueueIDs' => [22],
                    'CustomerID' => $customer,
                    'ArchiveFlags' => 'n',
                ])
                ->order(['Changed' => SORT_DESC, 'Created' => SORT_DESC])
                ->limit($conditions['limit'] ?? 10)
                ->all();

            $data = ['tickets' => $tickets, 'cacheDate' => $cacheDate];
            $cache->save($cacheItem->set($data));
        }
        else {
            $data = $cacheItem->get();
        }

        if ($request->isXmlHttpRequest()) {
            return $this->render("support/_widgets/tickets.html.twig", $data);
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

    /**
     * @Route("/create", name="support:tickets:create")
     *
     * @param Request $request
     * @param TicketHandler $handler
     *
     * @return Response
     */
    public function create(Request $request, TicketHandler $handler): Response
    {
        $initialData = [];

        /* @var $user WebServiceUser */
        if ($user = $this->getUser()) {
            $initialData = [
                'fname' => $user->getFname(),
                'lname' => $user->getLname(),
                'email' => $user->getUsername()
            ];
        }

        $form = $this->createForm(TicketType::class, $initialData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $handler->create($form->getNormData());
            if (isset($result['TicketID'])) {
                $this->addFlash('ticket.created', 'Ticket has been created successfully.');
                if (!$user) {
                    return $this->redirectToRoute('support');
                }

                $cache = new FilesystemAdapter();
                $cache->deleteItem($this->getCacheID($request));

                return $this->redirectToRoute('support:tickets');
            }
        }

        return $this->render("support/tickets/create.html.twig", [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{tid}/reply", name="support:tickets:reply")
     *
     * @param string $tid
     * @param Request $request
     * @param MessageHandler $handler
     * @param Client $client
     *
     * @return Response
     */
    public function reply(string $tid, Request $request, MessageHandler $handler, Client $client)
    {
        $ticket = $client->one($tid, ['AllArticles' => 1, 'ArticleOrder' => 'DESC', 'Attachments' => 1]);

        if (isset($ticket->ErrorCode)) {
            throw new NotFoundHttpException('Обращение не найдено');
        }

        $form = $this->createForm(MessageType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getNormData();
            $data['email'] = $this->getUser()->getUsername();

            $result = $handler->send($ticket, $data);
            if (isset($result['TicketID'])) {
                $this->addFlash('ticket.updated', 'Message has been sent successfully.');

                return $this->redirectToRoute('support:tickets:view', ['ticketID' => $tid]);
            }
        }

        return $this->render("support/tickets/reply.html.twig", [
            'ticket' => $ticket,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{tid}/message/{aid}", name="support:tickets:message")
     *
     * @param string $tid
     * @param string $aid
     * @param Client $client
     *
     * @return Response
     */
    public function message(string $tid, string $aid, Client $client): Response
    {
        $ticket = $client->one($tid, ['AllArticles' => 1, 'ArticleOrder' => 'DESC', 'Attachments' => 1]);

        if (isset($ticket->ErrorCode)) {
            throw new NotFoundHttpException('Обращение не найдено');
        }

        if (is_array($ticket->getArticle())) {
            $article = array_filter($ticket->getArticle(), function ($article) use ($aid) {
                return $article->ArticleID === $aid;
            });
        }
        else {
            $article = [$ticket->getArticle()];
        }

        $article = array_shift($article);
        $message = nl2br(preg_replace(['/^>.*$/m', '/[\r\n]{2,}/', '/(\s){2,}/'], ['', "\n\n", "$1"], $article->Body));

        return $this->render("support/tickets/message.html.twig", [
            'ticket' => $ticket,
            'article' => $article,
            'message' => $message
        ]);
    }

    /**
     * @Route("/jacarta_sf", name="support:tickets:jacarta_sf")
     */
    public function jacarta_sf()
    {

    }
}