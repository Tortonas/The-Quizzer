<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Slapyvardis'
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'Sutinku su taisyklėmis',
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Jums reikėtu sutikti su taisyklėmis.',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'label' => 'Slaptažodis',
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Prašau įrašykite slaptažodį',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Jūsų slaptažodis turėtu būti iš {{ limit }} simbolių',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'El. Paštas',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Įrašykite savo email',
                    ]),
                ],
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
