<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',TextType::class, array("help"=>'Your full name',"label" => "Your Name", "required" => true))
            ->add('bongoId', NumberType::class, array(
                'help' => 'Make sure to add valid ID card number (7 digit) without 000 as prefix',
                "label" => " Your Id Card Number:", "required" => true, 'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your 7 digit',
                    ]),

                    new Length([
                        'min' => 7,
                        'minMessage' => 'Your id should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 7,
                        'maxMessage' => 'Your id should be at least {{ limit }} characters',
                    ]),
                ]))
            ->add('department', ChoiceType::class, array("label" => "Department","required" => true,
                "choices" => array(
                    "Management" => "management",
                    "Digital Marketing" => "digital marketing",
                    "A&F" => "a&f",
                    "R&D" => "r&d",
                    "Admin" => "admin",
                    "Analytics" => "analytics",
                    "Product"  => "product",
                    "Bongo Studios" => "bongo studios",
                    "Boom" => "boom",
                    "Content" =>" content",
                    "Data Entry Team" => "data entry team",
                    "Creative" => 'creative',
                    "HR" => 'hr',
                    "Legal" => 'legal',
                    "Offline Channels" => "offline channels",
                    "Video Editing" => "video editing",
                    "Media Consultant" => "media consultant"
                ) ))
            ->add('email', EmailType::class, array("help" => "Make sure you use your bongo email","label" => "Bongo Email","required" => true,)
                )
            ->add('phone', TelType::class, array("help" => "i.e: 01715119693","label" => " Mobile Number"))
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'label' => 'Password',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('save', SubmitType::class, [
                'attr' => ['class' => 'save btn btn-danger'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
