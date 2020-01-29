<?php


namespace App\Form;


use App\Entity\Ledger;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class LedgerEntryFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('credit', NumberType::class, array('label' => 'How much taka you want to add?',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter an amount',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'You must add at least 75 taka',
                        // max length allowed by Symfony for security reasons
                        'max' => 5,
                    ]),
                ]));
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ledger::class,
        ]);
    }
}