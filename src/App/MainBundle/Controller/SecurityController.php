<?php

namespace App\MainBundle\Controller;

use App\MainBundle\Entity\User;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

class SecurityController extends BaseController {

    public function loginAction(Request $request) {
        $securityContext = $this->container->get('security.context');
        if( $securityContext->isGranted('IS_AUTHENTICATED_FULLY') ){
            return $this->redirect($this->generateUrl('default'));
        }
        $session = $request->getSession();

        $user = null;

        $error = '';
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } elseif ($session->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }


        $form = $this->getLoginForm($request);

        $form->handleRequest($request);

        if ($form->isValid()) {
            if ($error) {
                $form->addError(new FormError($error->getMessage()));
                $form->get('_password')->addError(new FormError($error->getMessage()));
            }
        }

        return $this->render('MainBundle:Security:login.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    public function registerAction(Request $request)
    {
        $securityContext = $this->container->get('security.context');
        if( $securityContext->isGranted('IS_AUTHENTICATED_FULLY') ){
            return $this->redirect($this->generateUrl('default'));
        }

        $user = new User();
        $user
            ->setEnabled(true); // FIXME

        $builder = $this->createFormBuilder($user, [
            'validation_groups' => ['Default', 'registration']
        ]);

        $builder
            ->add('email', 'email', [
                'label' => 'E-mail'
            ])
            ->add('password', 'repeated', [
                'type' => 'password',
                'first_options'  => ['label' => 'Пароль'],
                'second_options' => ['label' => 'И еще раз'],

            ])
            ->add('agree', 'checkbox', [
                'label' => 'Я согласен',
                'required' => true,
                'mapped' => false,
            ])
        ;

        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isValid()) {

            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
            $user->setPassword($password);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->render('MainBundle:Security:register_complete.html.twig');
        }

        return $this->render('MainBundle:Security:register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function recoverAction(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('_username', 'text', [
                'constraints' => [
                    new NotBlank()
                ],
            ])
            ->add('captcha', 'captcha', [
                'width' => 261,
                'height' => 59,
                'background_color' => [255, 255, 255],
                'reload' => false
            ])
            ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isValid()) {
            $username = $form->get('_username')->getData();

            try {
                $user = $this->getUserProvider()->loadUserByUsername($username);
                $mailSender = $this->getMailSender();

                if (!$user->getRecoverToken()) {
                    $user->updateRecoverToken();
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user);
                    $em->flush();
                }

                if ($mailSender->send($user->getEmail(), 'recover', ['user' => $user])) {
                    return $this->redirect($this->generateUrl('recover_sent'));
                }
                $form->addError(new FormError('При попытке восстановления пароля произошла ошибка. Пожалуйста, попробуйте еще раз'));
            } catch (UsernameNotFoundException $e) {
                $error = new FormError([
                    'error_text' => 'Пользователь не найден',
                    'error_advice' => 'Пожалуйста, проверьте правильность написания логина или e-mail'
                ]);

                $form->get('_username')->addError($error);
            }
        }


        return $this->render('MainBundle:Security:recover.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    private function getLoginForm(Request $request) {
        $session = $request->getSession();
        $username = $session->get(SecurityContext::LAST_USERNAME);

        $data = [
            '_username' => $username,
            '_password' => '',
            '_remember_me' => false,
        ];

        $builder = $this->container->get('form.factory')->createNamedBuilder('', 'form', $data, [
            'csrf_protection' => true,
            'csrf_field_name' => '_csrf_token',
            'intention'       => 'authenticate'
        ]);

        $builder
            ->add('_username', 'email', [
                'label' => 'Email'
            ])
            ->add('_password', 'password', [
                'label' => 'Пароль'
            ])
            ->add('_remember_me', 'checkbox', [
                'label' => 'Запомнить меня',
                'required' => false,
            ])
            ->setAction($this->generateUrl('login_check'))
        ;

        return $builder->getForm();
    }

    public function loginFormAction(Request $request)
    {
        return $this->render('MainBundle:Security:loginForm.html.twig', [
            'form' => $this->getLoginForm($request)->createView()
        ]);
    }
}