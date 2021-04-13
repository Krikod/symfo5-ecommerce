<?php

namespace App\Controller\Purchase;

use App\Cart\CartService;
use App\Form\CartConfirmationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;

class PurchaseConfirmationController extends AbstractController {

	protected $factory;
	protected $router;
	protected $security;
	protected $cart;

	public function __construct(FormFactoryInterface $factory, RouterInterface $router, Security $security, CartService $cart) {
		$this->factory = $factory;
		$this->router = $router;
		$this->security = $security;
		$this->cart = $cart;
	}

	/**
	 * @Route("purchase/confirm", name="purchase_confirm")
	 */
	public function confirm( Request $request, FlashBagInterface $flash_bag): Response {
		//** 1. Lire données formulaire -> FormFactoryInterface, Request **//
		$form = $this->factory->create( CartConfirmationType::class);
		$form->handleRequest($request);

		//** 2. Si formulaire non soumis: dégager ! **//
		if (!$form->isSubmitted()) {
			// Message flash puis redirection -> SessionInterface ? Mieux, FlashBagInterface
			$flash_bag->add( 'warning', 'Vous devez remplir le formulaire de confirmation');
			return new RedirectResponse( $this->router->generate('cart_show'));
		}

		//** 3. Si pas connecté: dégager ! -> Security **//
		$user = $this->security->getUser();
		if (!$user) {
			throw new AccessDeniedException("Vous devez être connecté pour confirmer une commande");
		}

		// 4. Si form soumis et connecté MAIS si pas de produit dans panier: dégager !
		// -> SessionInterface ou CartService
		$cartItems = $this->cart->getDetailedCartItems();
		if (count($cartItems) === 0) {
			$flash_bag->add( 'warning', 'Vous ne pouvez pas confirmer une commande avec un panier vide');
			return new RedirectResponse( $this->router->generate( 'cart_show'));
		}

		// 5. Si tout ok, créer une purchase


		// 6. La lier avec l'utilisateur connecté -> Security


		// 7. La lier avec les produits du panier -> CartService


		// 8. Enregistrer la commande -> EntityManagerInterface




	}

}