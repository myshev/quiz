<?php

namespace App\AdminBundle\Controller;

use App\MainBundle\Entity\Question;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Validator\Constraints\NotBlank;

class QuestionController extends CoreController
{
    public function indexAction()
    {
        return $this->redirect($this->generateUrl('admin_question_list'));
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

        $importForm = $this->getImportForm();
        $importForm->handleRequest($request);

        if($importForm->isValid()) {
            if ('csv' !== $importForm->get('file')->getData()->getClientOriginalExtension()) {
                $importForm->addError(new FormError('Неправильный формат файла. Допускается использование файлов только формата .csv'));
            }
//            $file = file_get_contents()
//            var_dump($importForm['file']->getData());die;
        }


        return $this->render('AdminBundle:Question:list.html.twig', [
            'pagination' => $pagination,
            'filterForm' => $form->createView(),
            'importForm' => $importForm->createView()
        ]);
    }

    public function editAction($id = null)
    {
        $isNew = null === $id;

        if ($isNew) {
            $entity = new Question();
        } else {
            $entity = $this->findQuestion($id);
        }

        $builder = $this->createFormBuilder($entity)
            ->add('name', null, [
                'label' => 'Название'
            ])
            ->add('category', null, [
                'label' => 'Категория'
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
                $this->addFlashMessage('success', 'Вопрос создан');
            } else {
                $this->addFlashMessage('success', 'Вопрос сохранен');
            }

            return $this->redirect($this->generateUrl('admin_question_edit', [
                'id' => $entity->getId()
            ]));
        }

        return $this->render('AdminBundle:Question:edit.html.twig', [
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
        $qb = $this->getQuestionRepository()->createQueryBuilder('q');

        if ($form->get('name')->getNormData()) {
            $qb->andWhere('q.name LIKE :name');
            $qb->setParameter('name', '%' . $form->get('name')->getNormData() . '%');
        }

        if ($form->has('sort_field') && $form->get('sort_field')->getNormData()) {
            $qb->orderBy('q.' . $form->get('sort_field')->getNormData(), $form->get('sort_order')->getNormData());
        }

        return $qb->getQuery();
    }

    /**
     * @param $id
     * @return null|Question
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function findQuestion($id)
    {
        $entity = $this->getQuestionRepository()->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Вопрос не найден');
        }

        return $entity;
    }



    public function removeAction($id)
    {
        $question = $this->findQuestion($id);

        $em = $this->getDoctrine()->getManager();

        $em->remove($question);
        $em->flush();

        $this->addFlashMessage('success', 'Вопрос удален');

        return $this->redirect($this->generateUrl('admin_question_list'));
    }

    private function getImportForm() {

        $builder = $this->container->get('form.factory')->createNamedBuilder('import', 'form', null, [])
            ->add('file', 'file', [
                'mapped' => false,
                'label' => 'Выберите файл (csv)',
                'constraints' => [
                    new NotBlank(['message' => 'Необходимо указать файл для загрузки']),
                ]
            ])
            ->add('submit', 'submit', [
                'label' => 'Загрузить'
            ])
            ->getForm();
        return $builder;
    }
}