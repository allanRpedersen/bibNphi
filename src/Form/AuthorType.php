<?php

namespace App\Form;

use App\Entity\Author;
use App\Form\GenericType;
// use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AuthorType extends GenericType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
			->add('lastName', TextType::class, $this->mkBasics(false, "le nom de l'auteur(e)"))
            ->add('firstName', TextType::class, $this->mkBasics(false, "son prénom", false))
            ->add('birthYear', TextType::class, $this->mkBasics(false,"l'année de sa naissance", false))
            ->add('deathYear', TextType::class, $this->mkBasics(false,"celle de sa mort", false))
            ->add('summary', TextType::class, $this->mkBasics(false, "Une phrase de présentation", false))
            ->add('content',TextareaType::class, $this->mkBasics(false, "Une description détaillée de l'auteur", false))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Author::class,
        ]);
    }
}
