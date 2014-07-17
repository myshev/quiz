<?php

namespace App\MainBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class HintExtension extends AbstractTypeExtension
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setOptional(['hint', 'hint_url', 'extra'])
            ->setDefaults([
                'hint' => '',
                'hint_url' => '',
                'extra' => null
            ])
        ;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars = array_merge([
            'hint' => $options['hint'],
            'hint_url' => $options['hint_url'],
            'extra' => $options['extra'],
        ], $view->vars);
    }

    public function getExtendedType()
    {
        return 'form';
    }
}
