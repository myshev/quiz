<?php

namespace App\AdminBundle\Controller;

use App\MainBundle\Entity\Category;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;

class CategoryController extends CoreController
{
    public function indexAction()
    {
        return $this->redirect($this->generateUrl('admin_category_list'));
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


        return $this->render('AdminBundle:Category:list.html.twig', [
            'pagination' => $pagination,
            'filterForm' => $form->createView()
        ]);
    }

    public function editAction($id = null)
    {
        $isNew = null === $id;

        if ($isNew) {
            $entity = new Category();
        } else {
            $entity = $this->findCategory($id);
        }

        $builder = $this->createFormBuilder($entity)
            ->add('name', null, [
                'label' => 'Название'
            ])
            ->add('submit', 'submit', [
                'label' => $isNew ? 'Добавить' : 'Сохранить'
            ])
        ;

        $editForm = $builder->getForm();
        $editForm->handleRequest($this->getRequest());

        if ($editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            if ($isNew) {
                $this->addFlashMessage('success', 'Категория создана');
            } else {
                $this->addFlashMessage('success', 'Категория сохранена');
            }

            return $this->redirect($this->generateUrl('admin_category_edit', [
                'id' => $entity->getId()
            ]));
        }

        return $this->render('AdminBundle:Category:edit.html.twig', [
            'isNew' => $isNew,
            'entity' => $entity,
            'form'   => $editForm->createView(),
        ]);
    }

    protected function buildFilterForm(FormBuilderInterface $builder)
    {
        $builder
            ->add('name', 'text', [
                'label' => 'Название',
                'required' => false
            ])
            ->add('submit', 'submit', [
                'label' => 'Показать'
            ])
        ;
    }

    protected function createFilterQuery(Form $form)
    {
        $qb = $this->getCategoryRepository()->createQueryBuilder('c');

        if ($form->get('name')->getNormData()) {
            $qb->andWhere('c.name LIKE :name');
            $qb->setParameter('name', '%' . $form->get('name')->getNormData() . '%');
        }

        if ($form->has('sort_field') && $form->get('sort_field')->getNormData()) {
            $qb->orderBy('c.' . $form->get('sort_field')->getNormData(), $form->get('sort_order')->getNormData());
        }

        return $qb->getQuery();
    }

    /**
     * @param $id
     * @return null|Category
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function findCategory($id)
    {
        $entity = $this->getCategoryRepository()->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Категория не найден');
        }

        return $entity;
    }



    public function removeAction($id)
    {
        $category = $this->findCategory($id);

        $em = $this->getDoctrine()->getManager();

        $em->remove($category);
        $em->flush();

        $this->addFlashMessage('success', 'Категория удалена');

        return $this->redirect($this->generateUrl('admin_category_list'));
    }
}