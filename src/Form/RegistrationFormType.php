<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prenom', TextType::class, ['label' => 'Prénom'])
            ->add('nom', TextType::class, ['label' => 'Nom'])
            ->add('email', EmailType::class, ['label' => 'Email'])
            ->add('ville', EntityType::class, [
                'class' => Ville::class,
                'choice_label' => 'nom',
                'label' => 'Ville',
                'placeholder' => 'Choisissez votre ville',
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Je souhaite être',
                'mapped' => false,
                'multiple' => true,
                'expanded' => true,
                'choices' => [
                    'Apprenant (je cherche de l\'aide)' => 'ROLE_APPRENANT',
                    'Bénévole (je propose mon aide)' => 'ROLE_BENEVOLE',
                ],
                'constraints' => [
                    new Count(min: 1, minMessage: 'Choisissez au moins un rôle.'),
                ],
            ])
            // Double saisie du mot de passe + règles de robustesse
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'first_options' => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmez le mot de passe'],
                'invalid_message' => 'Les deux mots de passe ne correspondent pas.',
                'constraints' => [
                    new NotBlank(message: 'Veuillez saisir un mot de passe.'),
                    new Length(min: 8, minMessage: 'Le mot de passe doit faire au moins {{ limit }} caractères.'),
                    new Regex(
                        pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).+$/',
                        message: 'Il doit contenir au moins une minuscule, une majuscule, un chiffre et un caractère spécial.'
                    ),
                ],
            ])
            // Consentement RGPD obligatoire
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => 'J\'accepte la politique de confidentialité (RGPD).',
                'constraints' => [
                    new IsTrue(message: 'Vous devez accepter la politique de confidentialité.'),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => User::class]);
    }
}
