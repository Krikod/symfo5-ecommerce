<?php

namespace App\Cart;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService {

	protected $session;
	protected $repo;

	public function __construct( SessionInterface $session, ProductRepository $repo ) {
		$this->session = $session;
		$this->repo    = $repo;
	}

	// Construct donc au lieu de $session, on a $this->session
	public function add(int $id ) {
		// Retrouver le panier dans la session : []
		// S'il n'existe pas, prendre un [] vide
//		$cart = $request->getSession()->get( 'cart', []);
		$cart = $this->session->get( 'cart', []);

		// Voir si le produit $id existe dans le tab: [12 => 3, 29 => 2]
		// Si oui, augmenter la quantité / Sinon, ajouter pduit avec q 1
		if (array_key_exists($id, $cart)) {
			$cart[$id]++;
		} else {
			$cart[$id] = 1;
		}

		// Enregistrer le tab mis à jour dans la session
//	    $request->getSession()->set( 'cart', $cart);
		$this->session->set( 'cart', $cart);
//		$request->getSession()->remove( 'cart');

//	    dd( $session->get('cart'));
//	    dd( $session->getBag( 'flashes'));

	}

	public function getTotal(  ) : int {
		$total = 0;
		foreach ($this->session->get( 'cart', []) as $id => $qty) {
			$product = $this->repo->find($id);
			$total += $product->getPrice() * $qty;
		}
		return $total;
	}

	public function getDetailedCartItems() : array {

		$detailedCart = [];

		foreach ($this->session->get( 'cart', []) as $id => $qty) {
			$product = $this->repo->find( $id);
			$detailedCart[] = [
				'product' => $product,
				'qty' => $qty
			];
		}
		return $detailedCart;
	}
}