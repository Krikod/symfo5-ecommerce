<?php

namespace App\Controller\Purchase;

use App\Repository\PurchaseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PurchasePaymentController extends AbstractController {

	/**
	 * @Route("/purchase/pay/{id}", name="purchase_payment_form")
	 */
	public function showCardForm( $id, PurchaseRepository $repo ) {
		$purchase = $repo->find( $id);

		if (!$purchase) {
			return $this->redirectToRoute( 'cart_show');
		}

		\Stripe\Stripe::setApiKey('sk_test_51Ih0llBZitoH9S2VaCqyUGfiThz5nenZkUJHez4eiRpb7R82cXWLGY9HfVJ0lgO1CUWT6BBkfb6qeooV5FztkQst00ITUjRAiS');

		// Méthode Create nous envoie un objet de type PaymentIntent
		$intent = \Stripe\PaymentIntent::create([
			'amount' => $purchase->getTotal(),
			'currency' => 'eur',
		]);

		dump( $intent->client_secret);


		return $this->render( 'purchase/payment.html.twig', [
			'clientSecret' => $intent->client_secret
		]);
	}
}