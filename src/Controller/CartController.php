<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("/cart/add/{id}", name="cart_add", requirements={"id":"\d+"})
     */
    public function add($id, ProductRepository $repo, SessionInterface $session): Response
    {
    	// Produit existe ?
	    $product = $repo->find( $id);
	    if (!$product) {
	    	throw $this->createNotFoundException("Le produit $id n'existe pas");
	    }
    	// Retrouver le panier dans la session : []
	    // S'il n'existe pas, prendre un [] vide
//		$cart = $request->getSession()->get( 'cart', []);
		$cart = $session->get( 'cart', []);

		// Voir si le produit $id existe dans le tab: [12 => 3, 29 => 2]
	    // Si oui, augmenter la quantité / Sinon, ajouter pduit avec q 1
	    if (array_key_exists($id, $cart)) {
	    	$cart[$id]++;
	    } else {
	    	$cart[$id] = 1;
	    }

	    // Enregistrer le tab mis à jour dans la session
//	    $request->getSession()->set( 'cart', $cart);
	    $session->set( 'cart', $cart);
//		$request->getSession()->remove( 'cart');

//	    dd( $session->get('cart'));
//	    dd( $session->getBag( 'flashes'));

	    /** @var FlashBag */
	    $flashBag = $session->getBag( 'flashes');

		$flashBag->add('success', "Le produit a bien été ajouté au panier");
//		$flashBag->add('warning', "Attention !");
//		dump( $flashBag->get('success'));
//	    dd($flashBag);

	    return $this->redirectToRoute( 'product_show', [
	    	'category_slug' => $product->getCategory()->getSlug(),
	    	'slug' => $product->getSlug()
	    ]);
    }
}
