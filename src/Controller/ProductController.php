<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductController extends AbstractController
{
    /**
     * @Route("/{slug}", name="product_category", priority=-1)
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
	 * @Route("/{category_slug}/{slug}", name="product_show", priority=-1)
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
	 * @Route("/admin/product/{id}/edit", name="product_edit")
	 */
	public function edit($id, ProductRepository $repo,
		Request $request, EntityManagerInterface $em,
		SluggerInterface $slugger,
		ValidatorInterface $validator) {


//		$product = new Product;
//
//		$result = $validator->validate( $product, null,
//			 ['Default','with-price']);
//		dd( $result);


		// VALIDATION D'OBJET
//		$product = new Product;
//		$product->setName('Bo')
//		->setPrice( 200);
//		$resultat = $validator->validate( $product);
//
//
//		if ($resultat->count() > 0) {
//			dd( "Il y a des erreurs ", $resultat);
//		}
//		dd( "Tout va bien");




		// Validation complexe
//		$client = [
//			'nom' => 'Pat',
//			'prenom' => 'Kr',
//			'voiture' => [
//				'marque' => 'Renault',
//				'couleur' => ''
//			]
//		];
//
//		$collection = new  Collection([
//			'nom' => new NotBlank(['message' => "Le nom ne doit pas être vide !"]),
//			'prenom' => [
//				new  NotBlank(['message' => "Le prénom ne doit pas être vide !"]),
//				new Length(['min' => 3, 'minMessage' => "Le prénom ne doit pas faire moins de 3 caractères." ])
//			],
//			'voiture' => new Collection([
//					'marque' => new NotBlank(['message' => "La marque de la voiture est obligatoire!"]),
//					'couleur' => new NotBlank(['message' => "La couleur de la voiture est obligatoire"])
//			])
//		]);
//
//		$resultat = $validator->validate($client, $collection);


// Validation de scalaires
//		$age = 10;
//		$resultat = $validator->validate($age, [
//			new LessThanOrEqual([
//				'value' => 120,
//				'message' => "L'âge doit etre inférieur à {{ compared_value }}, mais vous avez donné {{ value }}."
//			]),
//			new GreaterThan([
//				'value' => 0,
//				'message' => "L'age doit être sup à 0."
//			])
//		]);
//		dd( $resultat );

//		if ($resultat->count() > 0) {
//			dd( "Il y a des erreurs ", $resultat);
//		}
//		dd( "Tout va bien");
		// Ou voir le Profiler de Symfony qui affiche les violations !
		
		$product = $repo->find($id);

		$form = $this->createForm(ProductType::class, $product);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
//			$product->setSlug(strtolower($slugger->slug($product->getName())));
//			dd( $form->getData());
			$em->flush();

			return $this->redirectToRoute('product_show', [
				'category_slug' => $product->getCategory()->getSlug(),
				'slug' => $product->getSlug()
			]);
		}

		$formView = $form->createView();

		return $this->render('product/edit.html.twig', [
			'product' => $product,
			'formView' => $formView
		]);
	}


	/**
	 * @Route("/admin/product/create", name="product_create")
	 */
	public function create(Request $request, SluggerInterface $slugger,	EntityManagerInterface $em) {

		$product = new Product();
		$form = $this->createForm(ProductType::class, $product);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$product->setSlug(strtolower($slugger->slug($product->getName())));

		$em->persist($product);
		$em->flush();

		return $this->redirectToRoute('product_show', [
			'category_slug' => $product->getCategory()->getSlug(),
			'slug' => $product->getSlug()
		]);

		}

		$formView = $form->createView();

		return $this->render('product/create.html.twig', [
			'formView' => $formView
		]);
    }
}
