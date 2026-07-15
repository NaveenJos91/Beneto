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

class AnnonceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, ['label' => 'Titre de l\'annonce'])
            ->add('description', TextareaType::class, [
                'label' => 'Description (matière, niveau, disponibilités...)',
                'attr' => ['rows' => 5],
            ])
            ->add('theme', EntityType::class, [
                'class' => Theme::class,
                'choice_label' => 'nom',
                'label' => 'Thématique',
                'placeholder' => 'Choisissez une thématique',
            ])
            ->add('ville', EntityType::class, [
                'class' => Ville::class,
                'choice_label' => 'nom',
                'label' => 'Ville',
                'placeholder' => 'Choisissez une ville',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Annonce::class]);
    }
}
