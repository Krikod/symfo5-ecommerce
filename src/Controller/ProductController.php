<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

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
	 * @Route("/admin/product/{id}/edit", name="product_edit")
	 */
	public function edit($id, ProductRepository $repo,
		Request $request, EntityManagerInterface $em, SluggerInterface $slugger) {

		$product = $repo->find($id);

		$form = $this->createForm(ProductType::class, $product);

		$form->handleRequest($request);

		if ($form->isSubmitted()) {
			$product->setSlug(strtolower($slugger->slug($product->getName())));

			$em->flush();

		}

		$formView = $form->createView();

//		dd($product);
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

		if ($form->isSubmitted()) {
			$product->setSlug(strtolower($slugger->slug($product->getName())));

		$em->persist($product);
		$em->flush();

		dump($product);
		}

		$formView = $form->createView();

		return $this->render('product/create.html.twig', [
			'formView' => $formView
		]);
    }
}
