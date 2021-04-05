<?php

namespace App\Controller;

use App\Cart\CartService;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("/cart/add/{id}", name="cart_add", requirements={"id":"\d+"})
     */
    public function add($id, ProductRepository $repo, CartService $cart_service, Request $request): Response
    {
    	// Produit existe ?
	    $product = $repo->find( $id);
	    if (!$product) {
	    	throw $this->createNotFoundException("Le produit $id n'existe pas");
	    }

	    // A la place du code placé dans le service CartService:
	    $cart_service->add( $id);

	    // Effacer car on se livre le FlashBagInterface
//	    /** @var FlashBag */
//	    $flashBag = $session->getBag( 'flashes');
//		$flashBag->add('success', "Le produit a bien été ajouté au panier");
		$this->addFlash('success', "Le produit a bien été ajouté au panier");

//		$flashBag->add('warning', "Attention !");
//		dump( $flashBag->get('success'));
//	    dd($flashBag);

	    if ($request->query->get( 'returnToCart')) {
	    	return $this->redirectToRoute( 'cart_show');
	    }
	    return $this->redirectToRoute( 'product_show', [
	    	'category_slug' => $product->getCategory()->getSlug(),
	    	'slug' => $product->getSlug()
	    ]);
    }

	/**
	 * @Route("/cart", name="cart_show")
	 */
	public function show(CartService $cart_service) {

		$detailedCart = $cart_service->getDetailedCartItems();
		$total = $cart_service->getTotal();


		return $this->render( 'cart/index.html.twig', [
			'items' => $detailedCart,
			'total' => $total
		]);
    }

	/**
	 * @Route("/cart/delete/{id}", name="cart_delete", requirements={"id":"\d+"})
	 */
	public function delete($id, ProductRepository $repo, CartService $cartService) {
		if (!$repo->find( $id)) {
			throw $this->createNotFoundException("Le produit $id n'existe pas et ne peut pas être supprimé");
		}

		$cartService->remove($id);
		$this->addFlash( "success", "Le produit a bien été supprimé du panier");
		return $this->redirectToRoute( 'cart_show');

    }

	/**
	 * @Route("/cart/decrement/{id}", name="cart_decrement", requirements={"id":"\d+"})
	 */
	public function decrement(int $id, CartService $cartService, ProductRepository $repo) {
		if (!$repo->find( $id)) {
			throw $this->createNotFoundException("Le produit $id n'existe pas et ne peut pas être décrémenté");
		}

		$cartService->decrement($id);
		$this->addFlash( 'success', 'Le produit a bien été retiré du panier');
		return $this->redirectToRoute( 'cart_show');
    }
}
