<?php

namespace App\Controller\Purchase;

use App\Cart\CartService;
use App\Entity\Purchase;
use App\Event\PurchaseSuccessEvent;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Annotation\Route;

class PurchasePaymentSuccessController extends AbstractController {

	/**
	 * @Route("/purchase/terminate/{id}", name="purchase_payment_success")
	 * @IsGranted("ROLE_USER")
	 */
	public function success(int $id, PurchaseRepository $repo, CartService $cart, EntityManagerInterface $em, EventDispatcherInterface $dispatcher) {
		$purchase = $repo->find($id);

		if (
			!$purchase
		    || ($purchase && $purchase->getUser() !== $this->getUser())
			|| ($purchase && $purchase->getStatus() === Purchase::STATUS_PAID)
		) {
			$this->addFlash( 'warning', "La commande n'existe pas");
			return $this->redirectToRoute( "purchase_index");
		}

		$purchase->setStatus(Purchase::STATUS_PAID);
		$em->flush();

		$cart->empty();

		// Lancer un événement qui permette aux autres dev de réagir à la prise d'une commande
		$puchaseEvent = new PurchaseSuccessEvent($purchase);
		$dispatcher->dispatch($puchaseEvent, 'purchase.success');

		$this->addFlash( 'success', "La commande a été payée et confirmée !");
		return $this->redirectToRoute( "purchase_index");
	}
}