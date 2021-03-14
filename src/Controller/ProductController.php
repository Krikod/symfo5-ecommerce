<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/{slug}", name="product_category")
     */
    public function category($slug, CategoryRepository $category_repository): Response
    {
    	$category = $category_repository->findOneBy([
    		'slug' => $slug
	    ]);

    	if (!$category) {
    		throw $this->createNotFoundException("La catégorie demandée n'existe pas.");
	    }

        return $this->render('product/category.html.twig', [
			'category' => $category
        ]);
    }

	/**
	 * @Route("/{category_slug}/{slug}", name="product_show")
	 */
	public function show($slug, ProductRepository $product_repository): Response
	{
		$product = $product_repository->findOneBy([
			'slug' => $slug
		]);

		if (!$product) {
			throw $this->createNotFoundException("Le produit demandé n'existe pas.");
		}

		return $this->render('product/show.html.twig', [
			'product' => $product
		]);
    }

	/**
	 * @Route("/admin/product/create", name="product_create")
	 */
	public function create(FormFactoryInterface $factory) {
		$builder = $factory->createBuilder();
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

		$form = $builder->getForm();

		$formView = $form->createView();

		return $this->render('product/create.html.twig', [
			'formView' => $formView
		]);
    }
}
