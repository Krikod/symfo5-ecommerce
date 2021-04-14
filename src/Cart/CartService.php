<?php

namespace App\Cart;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService {

	/**
	 * @var SessionInterface
	 */
	protected $session;

	/**
	 * @var ProductRepository
	 */
	protected $repo;

	/**
	 * CartService constructor.
	 *
	 * @param SessionInterface $session
	 * @param ProductRepository $repo
	 */
	public function __construct(SessionInterface $session, ProductRepository $repo) {
		$this->session = $session;
		$this->repo    = $repo;
	}

	// Construct donc au lieu de $session, on aura $this->session

	/**
	 * @return array
	 */
	protected function getCart(): array {
		return $this->session->get( 'cart', []);
	}

	/**
	 * @param array $cart
	 */
	protected function saveCart(array $cart) {
		$this->session->set( 'cart', $cart);
	}

	public function empty() {
//		$this->session->remove( 'cart');
		$this->saveCart([]);
	}

	/**
	 * @param int $id
	 */
	public function add(int $id) {
		// Retrouver le panier dans la session : []
		// S'il n'existe pas, prendre un [] vide
//		$cart = $request->getSession()->get( 'cart', []);
//		$cart = $this->session->get( 'cart', []);
		$cart = $this->getCart();

		// Voir si le produit $id existe dans le tab: [12 => 3, 29 => 2]
		// Si oui, augmenter la quantité / Sinon, ajouter pduit avec q 1
//		if (array_key_exists($id, $cart)) {
//			$cart[$id]++;
//		} else {
//			$cart[$id] = 1;
//		}
		if (!array_key_exists($id, $cart)) {
			$cart[$id] = 0;
		}
		$cart[$id]++;

		// Enregistrer le tab mis à jour dans la session
//	    $request->getSession()->set( 'cart', $cart);
//		$request->getSession()->remove( 'cart');
//		$this->session->set( 'cart', $cart);
		$this->saveCart($cart);

//	    dd( $session->get('cart'));
//	    dd( $session->getBag( 'flashes'));
	}

	/**
	 * @param int $id
	 */
	public function remove(int $id) {
		$cart = $this->getCart();
		unset( $cart[$id]);
		$this->saveCart($cart);
	}

	/**
	 * @param int $id
	 */
	public function decrement(int $id) {
		$cart = $this->getCart();
		if (!array_key_exists($id, $cart)) {
			return;
		}
		// Si produit à 1, le supprimer
		if ($cart[$id] === 1) {
			$this->remove( $id);
			return;
		} else{
			// Si produit à plus de 1, décrémenter
			$cart[$id]--;
			$this->saveCart($cart);
		}
	}

	/**
	 * @return int
	 */
	public function getTotal() : int {
		$total = 0;
		foreach ($this->getCart() as $id => $qty) {
			$product = $this->repo->find($id);
			if (!$product) {
				continue; // recommencer la boucle
			}
			$total += $product->getPrice() * $qty;
		}
		return $total;
	}

	/**
	 * @return CartItem[]
	 */
	public function getDetailedCartItems() : array {

		$detailedCart = [];

		foreach ($this->getCart() as $id => $qty) {
			$product = $this->repo->find( $id);

			if (!$product) {
				continue;
			}

//			$detailedCart[] = [
//				'product' => $product,
//				'qty' => $qty
//			];
			$detailedCart[] = new CartItem($product, $qty);
		}
		return $detailedCart;
	}
}