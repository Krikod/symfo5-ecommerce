<?php

namespace App\Purchase;

use App\Cart\CartService;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\DateTime;

class PurchasePersister {

	protected $security;
	protected $cart;
	protected $em;

	public function __construct(Security $security, CartService $cart, EntityManagerInterface $em) {
		$this->security = $security;
		$this->cart = $cart;
		$this->em = $em;
	}

	public function storePurchase( Purchase $purchase ) {
		 // Intégrer tout ce qu'il faut et persister la purchase.
		 $purchase->setUser($this->security->getUser())
		          ->setPurchasedAt(new \DateTime())
		          ->setTotal($this->cart->getTotal());

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
//		$purchase->setTotal($this->cart->getTotal());
// 8. Enregistrer la commande -> EntityManagerInterface
		$this->em->flush();
 	}

 }