<?php

namespace App\Controller\Purchase;

use App\Cart\CartService;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use App\Form\CartConfirmationType;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Provider\DateTime;
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
	protected $em;

	public function __construct(FormFactoryInterface $factory, RouterInterface $router,
		Security $security, CartService $cart, EntityManagerInterface $em) {
		$this->factory = $factory;
		$this->router = $router;
		$this->security = $security;
		$this->cart = $cart;
		$this->em = $em;
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
		$purchase = $form->getData();

		// 6. La lier avec l'utilisateur connecté -> Security


		$purchase->setUser($user)
		->setPurchasedAt(new \DateTime());
		$this->em->persist( $purchase);


		// 7. La lier avec les produits du panier -> CartService
//		$total = 0;

		foreach ( $this->cart->getDetailedCartItems() as $cart_item ) {
			$purchaseItem = new PurchaseItem();
			$purchaseItem->setPurchase( $purchase)
				->setProduct( $cart_item->product)
				->setProductName( $cart_item->product->getName())
				->setQuantity( $cart_item->qty)
				->setTotal( $cart_item->getTotal())
				->setProductPrice( $cart_item->product->getPrice());

//			$total += $cart_item->getTotal();

			$this->em->persist( $purchaseItem);
		}
		$purchase->setTotal($this->cart->getTotal());


		// 8. Enregistrer la commande -> EntityManagerInterface
		$this->em->flush();

		$flash_bag->add( 'success', 'La commande a bien été enregistrée');
		return new RedirectResponse( $this->router->generate( 'purchases_index'));
	}

}