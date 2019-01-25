<?php

namespace App\Controller;

use App\Form\Profile as Form;
use App\Security\User\WebServiceUser;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation as Http;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * Class AccountController
 *
 * @Route("/account")
 *
 * @package App\Controller
 */
class AccountController extends AbstractController
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * AccountController constructor.
     *
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->eventDispatcher = $dispatcher;
    }

    /**
     * @Route("/login", name="login")
     * @Template("account/login.html.twig")
     *
     * @param Http\Request $request
     * @param AuthenticationUtils $authenticationUtils
     *
     * @return array
     */
    public function login(Http\Request $request, AuthenticationUtils $authenticationUtils): array
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return [
            'last_username' => $request->get('username', $lastUsername),
            'error' => $error,
        ];
    }

    /**
     * @Route("/reset", name="account_reset")
     *
     * @param Http\Request $request
     * @param Form\ResetHandler $handler
     *
     * @return Http\Response
     */
    public function reset(Http\Request $request, Form\ResetHandler $handler): Http\Response
    {
        $form = $this->createForm(Form\ResetType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($handler->reset($form)) {
                return $this->redirectToRoute('account_password');
            }
        }

        return $this->render('account/reset.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/password", name="account_password")
     *
     * @param Http\Request $request
     * @param Form\PasswordHandler $handler
     *
     * @return Http\Response
     */
    public function password(Http\Request $request, Form\PasswordHandler $handler): Http\Response
    {
        $form = $this->createForm(Form\PasswordType::class, $request->query->all());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($handler->change($form)) {
                if ($user = $handler->getUser()) {
                    $this->authorize($user, $request);
                    return $this->redirectToRoute('home');
                }

                return $this->redirectToRoute('login');
            }
        }

        return $this->render('account/password.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/register", name="account_register")
     *
     * @param Http\Request $request
     * @param Form\RegisterHandler $handler
     *
     * @return Http\Response
     */
    public function register(Http\Request $request, Form\RegisterHandler $handler): Http\Response
    {
        $form = $this->createForm(Form\RegisterType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($handler->register($form)) {
                $this->addFlash('register.success', true);
                return $this->redirectToRoute('account_confirm');
            }
        }

        return $this->render('account/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/confirm", name="account_confirm")
     *
     * @param Http\Request $request
     * @param Form\ConfirmHandler $handler
     *
     * @return Http\Response
     */
    public function confirm(Http\Request $request, Form\ConfirmHandler $handler): Http\Response
    {
        $form = $this->createForm(Form\ConfirmType::class, $request->query->all());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($handler->confirm($form)) {
                if ($user = $handler->getUser()) {
                    $this->authorize($user, $request);
                    return $this->redirectToRoute('home');
                }

                return $this->redirectToRoute('login');
            }
        }

        return $this->render('account/confirm.html.twig', [
            'form' => $form->createView()
        ]);
    }
//
//    /**
//     * @Route("/profile", name="account_profile")
//     *
//     * @param Request $request
//     *
//     * @return Response
//     */
//    public function profile(Request $request): Response
//    {
//        $form = $this->createForm(Form\ProfileType::class);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//
//        }
//
//        return $this->render("account/profile.html.twig", [
//            'form' => $form->createView()
//        ]);
//    }

    /**
     * @param WebServiceUser $user
     * @param Http\Request $request
     */
    protected function authorize(WebServiceUser $user, Http\Request $request)
    {
        $token = new UsernamePasswordToken($user, null, 'main', ['USER']);
        $this->get('security.token_storage')->setToken($token);
        $this->get('session')->set('_security_main', serialize($token));

        // Fire the login event manually
        $event = new InteractiveLoginEvent($request, $token);
        $this->eventDispatcher->dispatch("security.interactive_login", $event);
    }
}