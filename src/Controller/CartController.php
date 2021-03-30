<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("/cart/add/{id}", name="cart_add", requirements={"id":"\d+"})
     */
    public function add($id, Request $request, ProductRepository $repo): Response
    {
    	// Produit existe ?
	    $product = $repo->find( $id);
	    if (!$product) {
	    	throw $this->createNotFoundException("Le produit $id n'existe pas");
	    }
    	// Retrouver le panier dans la session : []
	    // S'il n'existe pas, prendre un [] vide
		$cart = $request->getSession()->get( 'cart', []);

		// Voir si le produit $id existe dans le tab: [12 => 3, 29 => 2]
	    // Si oui, augmenter la quantité / Sinon, ajouter pduit avec q 1
	    if (array_key_exists($id, $cart)) {
	    	$cart[$id]++;
	    } else {
	    	$cart[$id] = 1;
	    }

	    // Enregistrer le tab mis à jour dans la session
	    $request->getSession()->set( 'cart', $cart);
//		$request->getSession()->remove( 'cart');

	    return $this->redirectToRoute( 'product_show', [
	    	'category_slug' => $product->getCategory()->getSlug(),
	    	'slug' => $product->getSlug()
	    ]);
    }
}
