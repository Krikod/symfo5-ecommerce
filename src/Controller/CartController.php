<?php

namespace App\Controller;

use App\Cart\CartService;
use App\Form\CartConfirmationType;
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
	 * @var ProductRepository
	 */
	protected $repo;

	/**
	 * @var CartService
	 */
	protected $cart_service;

	public function __construct(ProductRepository $repo, CartService $cart_service) {
		$this->repo = $repo;
		$this->cart_service = $cart_service;
	}

	/**
     * @Route("/cart/add/{id}", name="cart_add", requirements={"id":"\d+"})
     */
    public function add(int $id, Request $request): Response
    {
    	// Produit existe ?
	    $product = $this->repo->find( $id);
	    if (!$product) {
	    	throw $this->createNotFoundException("Le produit $id n'existe pas");
	    }

	    // A la place du code placé dans le service CartService:
	    $this->cart_service->add( $id);

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
	public function show() {

		$form = $this->createForm( CartConfirmationType::class);

		$detailedCart = $this->cart_service->getDetailedCartItems();

		$total = $this->cart_service->getTotal();


		return $this->render( 'cart/index.html.twig', [
			'items' => $detailedCart,
			'total' => $total,
			'confirmationForm' => $form->createView()
		]);
    }

	/**
	 * @Route("/cart/delete/{id}", name="cart_delete", requirements={"id":"\d+"})
	 */
	public function delete($id) {
		if (!$this->repo->find( $id)) {
			throw $this->createNotFoundException("Le produit $id n'existe pas et ne peut pas être supprimé");
		}

		$this->cart_service->remove($id);
		$this->addFlash( "success", "Le produit a bien été supprimé du panier");
		return $this->redirectToRoute( 'cart_show');
    }

	/**
	 * @Route("/cart/decrement/{id}", name="cart_decrement", requirements={"id":"\d+"})
	 */
	public function decrement(int $id) {
		if (!$this->repo->find( $id)) {
			throw $this->createNotFoundException("Le produit $id n'existe pas et ne peut pas être décrémenté");
		}

		$this->cart_service->decrement($id);
		$this->addFlash( 'success', 'Le produit a bien été retiré du panier');
		return $this->redirectToRoute( 'cart_show');
    }
}
