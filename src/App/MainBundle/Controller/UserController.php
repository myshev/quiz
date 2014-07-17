<?php

namespace App\MainBundle\Controller;


use App\MainBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class UserController extends BaseController
{
    public function settingsAction(Request $request)
    {
        $this->checkUser();

        $user = $this->getUser();

        $builder = $this->createFormBuilder($user, [
            'validation_groups' => ['Default', 'registration']
        ]);
        $builder = $this->createFormBuilder($user)
                        ->add('email', 'email')
                        ->add('password', 'repeated', [
                            'type' => 'password',
                            'first_options'  => ['label' => 'Пароль'],
                            'second_options' => ['label' => 'И еще раз'],
                            'required' => false
                        ])
                        ->add('submit', 'submit', [
                            'label' => 'Сохранить'
                        ]);

        $form = $builder->getForm();

        $form->handleRequest($request);
        if($form->isValid()) {
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);
            $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
            $user->setPassword($password);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirect($this->generateUrl('user'));
        }

        return $this->render('MainBundle:User:settings.html.twig', [
            'form' => $form->createView()
        ]);
    }



    private function checkUser()
    {
        if (!$this->getUser() instanceof User) {
            throw new AccessDeniedHttpException();
        }

        return true;
    }
}