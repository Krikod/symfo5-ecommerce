<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
	        ->add('name', TextType::class, [
		        'label' => 'Nom du produit',
		        'attr' => [
			        'placeholder' => 'Tapez le nom du produit'
		        ]
	        ])
	        ->add('shortDescription', TextareaType::class, [
		        'label' => 'Description courte',
		        'attr' => [
			        'placeholder' => 'Tapez une description assez courte mais parlante pour le visiteur'
		        ]
	        ])
	        ->add('price', MoneyType::class, [
		        'label' => 'Prix du produit',
		        'attr' => [
			        'placeholder' => 'Tapez le prix du produit en Euros'
		        ]
	        ])
	        ->add('mainPicture', UrlType::class, [
		        'label' => 'Image du produit',
		        'attr' => ['placeholder' => 'Tapez l\'URL de l\'image']
	        ])
	        ->add('category', EntityType::class, [
		        'label' => 'Catégorie',
//			Pas le placeholder des attributs html de la liste, mais OPTION du champ ChoiceType !
		        'placeholder' => '-- Choisir une catégorie --',
		        'class' => Category::class,
//			Choice_label ==> Fonction ou Nom de la category: 'name'
		        'choice_label' => function(Category $category) {
			        return strtoupper($category->getName());
		        }
	        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
        	'data_class' => Product::class,
        ]);
    }
}
