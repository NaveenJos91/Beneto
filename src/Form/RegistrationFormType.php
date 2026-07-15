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

/**
 * Ceci décrit le FORMULAIRE d'inscription : quels champs il contient et leurs règles.
 * Symfony génère ensuite le HTML du formulaire à partir de cette description.
 */
class RegistrationFormType extends AbstractType
{
    // buildForm = on construit le formulaire champ par champ.
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Champs texte simples.
            ->add('prenom', TextType::class, ['label' => 'Prénom'])
            ->add('nom', TextType::class, ['label' => 'Nom'])
            // Champ email (vérifie le format email côté navigateur).
            ->add('email', EmailType::class, ['label' => 'Email'])
            // Liste déroulante des villes, remplie automatiquement depuis la table Ville.
            // 'choice_label' => 'nom' = on affiche le nom de la ville dans la liste.
            ->add('ville', EntityType::class, [
                'class' => Ville::class,
                'choice_label' => 'nom',
                'label' => 'Ville',
                'placeholder' => 'Choisissez votre ville',
            ])
            // Choix des rôles, sous forme de cases à cocher.
            // 'multiple' => true = on peut en cocher plusieurs (apprenant ET bénévole).
            // 'expanded' => true = affiché en cases à cocher (au lieu d'une liste déroulante).
            // 'mapped' => false = ce champ n'est pas relié directement à l'entité ; on le traite à la main dans le contrôleur.
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
                    // On oblige à cocher au moins un rôle.
                    new Count(min: 1, minMessage: 'Choisissez au moins un rôle.'),
                ],
            ])
            // Mot de passe : RepeatedType = deux champs (saisie + confirmation) pour éviter les fautes de frappe.
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false, // on ne stocke pas ce champ tel quel : il sera haché dans le contrôleur
                'first_options' => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmez le mot de passe'],
                'invalid_message' => 'Les deux mots de passe ne correspondent pas.',
                'constraints' => [
                    new NotBlank(message: 'Veuillez saisir un mot de passe.'),
                    // Au moins 8 caractères.
                    new Length(min: 8, minMessage: 'Le mot de passe doit faire au moins {{ limit }} caractères.'),
                    // Regex = règle de format : au moins une minuscule, une majuscule, un chiffre et un caractère spécial.
                    new Regex(
                        pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).+$/',
                        message: 'Il doit contenir au moins une minuscule, une majuscule, un chiffre et un caractère spécial.'
                    ),
                ],
            ])
            // Case à cocher OBLIGATOIRE pour le consentement RGPD.
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => 'J\'accepte la politique de confidentialité (RGPD).',
                'constraints' => [
                    // IsTrue = la case doit être cochée pour valider le formulaire.
                    new IsTrue(message: 'Vous devez accepter la politique de confidentialité.'),
                ],
            ]);
    }

    // On indique que ce formulaire remplit un objet de type User.
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => User::class]);
    }
}