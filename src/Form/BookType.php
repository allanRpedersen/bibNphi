<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\Author;
use App\Form\GenericType;
use Doctrine\ORM\EntityRepository;
// use Symfony\Component\Form\AbstractType;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends GenericType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('summary')
			->add('publishedYear')
			->add('author', EntityType::class, [
				'class' => Author::class,
				'choice_label' => 'lastName',
				'query_builder' => function ( EntityRepository $er ){
					return $er->createQueryBuilder('u')
							->orderBy('u.lastName', 'ASC');
				},
			])
			->add('odtBookFile', VichFileType::class, [
				'label' => 'Document au format odt',
				'required' => true,
				'allow_delete' => false,
				'download_label' => static function (Book $book) {
					return $book->getTitle();
				},
			])

            // ->add('odtBookName')
            // ->add('odtBookSize')
            // ->add('updatedAt')
            // ->add('author')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
