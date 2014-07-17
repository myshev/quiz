<?php

namespace App\AdminBundle\Controller;

use App\MainBundle\DBAL\Types\Roles;
use App\MainBundle\Entity\User;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;

class UserController extends CoreController
{
    public function indexAction()
    {
        return $this->redirect($this->generateUrl('admin_user_list'));
    }

    public function listAction()
    {
        $builder = $this->createFormBuilder(null, [
            'csrf_protection' => false,
            'method' => 'get'
        ]);
        $this->buildFilterForm($builder);
        $form = $builder->getForm();
        $request = $this->getRequest();
        $form->submit($request);

        $query = $this->createFilterQuery($form);
        $pagination = $this->paginate($query);


        return $this->render('AdminBundle:User:list.html.twig', [
            'pagination' => $pagination,
            'filterForm' => $form->createView()
        ]);
    }

    public function editAction($id = null)
    {
        $isNew = null === $id;

        if ($isNew) {
            $entity = new User();
        } else {
            $entity = $this->findUser($id);
        }

        $builder = $this->createFormBuilder($entity)
            ->add('email', null, [
                'label' => 'Email'
            ])
            ->add('plainPassword', 'repeated', [
                'type' => 'password',
                'first_options'  => ['label' => 'Пароль'],
                'second_options' => ['label' => 'И еще раз'],
                'required' => false,
            ])
            ->add('roles', 'choice', [
                'label' => 'Роли',
                'choices' => Roles::getChoices(),
                'multiple' => true,
            ])
            ->add('enabled', 'checkbox', [
                'label' => 'Активирован?',
                'required' => false,
            ])
            ->add('submit', 'submit', [
                'label' => $isNew ? 'Добавить' : 'Сохранить'
            ])
        ;

        $editForm = $builder->getForm();
        $editForm->handleRequest($this->getRequest());

        if ($editForm->isValid()) {
            if($editForm->getNormData()->getPlainPassword() != '') {
                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($entity);
                $password = $encoder->encodePassword($entity->getPassword(), $entity->getSalt());
                $entity->setPassword($password);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            if ($isNew) {
                $this->addFlashMessage('success', 'Пользователь создан');
            } else {
                $this->addFlashMessage('success', 'Пользователь сохранен');
            }

            return $this->redirect($this->generateUrl('admin_user_edit', [
                'id' => $entity->getId()
            ]));
        }

        return $this->render('AdminBundle:User:edit.html.twig', [
            'isNew' => $isNew,
            'entity' => $entity,
            'form'   => $editForm->createView(),
        ]);
    }

    protected function buildFilterForm(FormBuilderInterface $builder)
    {
        $builder
            ->add('email', 'text', [
                'label' => 'Email',
                'required' => false
            ])
            ->add('submit', 'submit', [
                'label' => 'Показать'
            ])
        ;
    }

    protected function createFilterQuery(Form $form)
    {
        $qb = $this->getUserRepository()->createQueryBuilder('u');

        if ($form->get('email')->getNormData()) {
            $qb->andWhere('u.email LIKE :email');
            $qb->setParameter('email', '%' . $form->get('email')->getNormData() . '%');
        }

        if ($form->has('sort_field') && $form->get('sort_field')->getNormData()) {
            $qb->orderBy('u.' . $form->get('sort_field')->getNormData(), $form->get('sort_order')->getNormData());
        }

        return $qb->getQuery();
    }

    /**
     * @param $id
     * @return null|User
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function findUser($id)
    {
        $entity = $this->getUserRepository()->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Пользователь не найден');
        }

        return $entity;
    }

    public function removeAction(Request $request, $id)
    {
        $user = $this->findUser($id);

        $em = $this->getDoctrine()->getManager();

        $em->remove($user);
        $em->flush();

        $this->addFlashMessage('success', 'Пользователь удален');

        return $this->redirect($this->generateUrl('admin_user_list'));
    }
}