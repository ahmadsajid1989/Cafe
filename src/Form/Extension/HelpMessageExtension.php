<?php


namespace App\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class HelpMessageExtension
 * @package App\Form\Extension
 */
class HelpMessageExtension extends AbstractTypeExtension
{

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['help'] = $options['help'] ?? '';
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['help' => null]);
    }


    /**
     * @inheritDoc
     */
    public static function getExtendedTypes(): iterable
    {
        return [FormType::class];
    }
}