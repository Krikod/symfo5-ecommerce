<?php

namespace App\Controller\Purchase;

use App\Cart\CartService;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use App\Form\CartConfirmationType;
use App\Purchase\PurchasePersister;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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

//	protected $factory;
//	protected $router;
//	protected $security;
	protected $cart;
	protected $em;
	protected $persister;

	public function __construct(CartService $cart, EntityManagerInterface $em, PurchasePersister $persister) {
//		$this->factory = $factory;
//		$this->router = $router;
//		$this->security = $security;
		$this->cart = $cart;
		$this->em = $em;
		$this->persister = $persister;
	}

	/**
	 * @Route("purchase/confirm", name="purchase_confirm")
	 * @IsGranted("ROLE_USER", message="Vous devez être connecté pour confirmer une commande")
	 */
	public function confirm( Request $request): Response {
		//** 1. Lire données formulaire -> FormFactoryInterface, Request **//
//		$form = $this->factory->create( CartConfirmationType::class);
		$form = $this->createForm( CartConfirmationType::class);

		$form->handleRequest($request);


		//** 2. Si formulaire non soumis: dégager ! **//
		if (!$form->isSubmitted()) {
			// Message flash puis redirection -> SessionInterface ? Mieux, FlashBagInterface
//			$flash_bag->add( 'warning', 'Vous devez remplir le formulaire de confirmation');
//			return new RedirectResponse( $this->router->generate('cart_show'));
			$this->addFlash( 'warning', 'Vous devez remplir le formulaire de confirmation');
			return $this->redirectToRoute( 'cart_show');
		}

		//** 3. Si pas connecté: dégager ! -> Security **//
//		$user = $this->security->getUser();
//		$user = $this->getUser(); // Fait grâce au IsGranted

//		if (!$user) {
//			throw new AccessDeniedException("Vous devez être connecté pour confirmer une commande");
//		}

		// 4. Si form soumis et connecté MAIS si pas de produit dans panier: dégager !
		// -> SessionInterface ou CartService
		$cartItems = $this->cart->getDetailedCartItems();
		if (count($cartItems) === 0) {
//			$flash_bag->add( 'warning', 'Vous ne pouvez pas confirmer une commande avec un panier vide');
//			return new RedirectResponse( $this->router->generate( 'cart_show'));
			$this->addFlash( 'warning', 'Vous ne pouvez pas confirmer une commande avec un panier vide');
			return $this->redirectToRoute( 'cart_show');
		}

		// 5. Si tout ok, créer une purchase
		$purchase = $form->getData();

		// Grace à notre PurchasePersister via le __construct()
		$this->persister->storePurchase( $purchase);

//		$this->cart->empty(); // Vider après paiement

//		$flash_bag->add( 'success', 'La commande a bien été enregistrée');
//		return new RedirectResponse( $this->router->generate( 'purchases_index'));

		// Diriger vers le form de paieent à la place:
//		$this->addFlash( 'success', 'La commande a bien été enregistrée');
//		return $this->redirectToRoute( 'purchases_index');
		return $this->redirectToRoute( 'purchase_payment_form', [
			'id' => $purchase->getId()
		]);


	}
}