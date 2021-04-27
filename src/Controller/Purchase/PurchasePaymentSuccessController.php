<?php

namespace App\Controller\Purchase;

use App\Cart\CartService;
use App\Entity\Purchase;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PurchasePaymentSuccessController extends AbstractController {

	/**
	 * @Route("/purchase/terminate/{id}", name="purchase_payment_success")
	 * @IsGranted("ROLE_USER")
	 */
	public function success(int $id, PurchaseRepository $repo, CartService $cart, EntityManagerInterface $em) {
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

		$this->addFlash( 'success', "La commande a été payée et confirmée !");
		return $this->redirectToRoute( "purchase_index");
	}
}