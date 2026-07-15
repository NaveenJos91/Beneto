<?php

namespace App\Form;

use App\Entity\Annonce;
use App\Entity\Theme;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Ceci décrit le FORMULAIRE de publication d'une annonce (les champs à remplir).
 * On l'utilise à la fois pour CRÉER et pour MODIFIER une annonce.
 * Les règles de validation (titre obligatoire, etc.) sont, elles, dans l'entité Annonce.
 */
class AnnonceType extends AbstractType
{
    // On construit le formulaire champ par champ.
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Le titre : un champ texte simple.
            ->add('titre', TextType::class, ['label' => 'Titre de l\'annonce'])
            // La description : une zone de texte plus grande (textarea), 5 lignes de haut.
            ->add('description', TextareaType::class, [
                'label' => 'Description (matière, niveau, disponibilités...)',
                'attr' => ['rows' => 5], // 'attr' = attributs HTML ; ici la hauteur de la zone
            ])
            // La thématique : une liste déroulante remplie depuis la table Theme.
            // 'choice_label' => 'nom' = on affiche le nom du thème.
            ->add('theme', EntityType::class, [
                'class' => Theme::class,
                'choice_label' => 'nom',
                'label' => 'Thématique',
                'placeholder' => 'Choisissez une thématique',
            ])
            // La ville : liste déroulante remplie depuis la table Ville.
            ->add('ville', EntityType::class, [
                'class' => Ville::class,
                'choice_label' => 'nom',
                'label' => 'Ville',
                'placeholder' => 'Choisissez une ville',
            ]);
    }

    // On indique que ce formulaire remplit un objet de type Annonce.
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Annonce::class]);
    }
}