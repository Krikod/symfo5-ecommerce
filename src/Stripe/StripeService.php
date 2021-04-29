<?php

namespace App\Stripe;

use App\Entity\Purchase;

// Pour créer un PaymentIntent

class StripeService {

	protected $secretKey;
	protected $publicKey;

	public function __construct(string $secretKey, string $publicKey) {
		$this->secretKey = $secretKey;
		$this->publicKey = $publicKey;
	}

	public function getPublicKey(  ) : string {
		return $this->publicKey;
	}

	public function getPaymentIntent(Purchase $purchase) {
		\Stripe\Stripe::setApiKey($this->secretKey);

		//		// Méthode Create nous envoie un objet de type PaymentIntent
//		$intent = \Stripe\PaymentIntent::create([
//			'amount' => $purchase->getTotal(),
//			'currency' => 'eur',
//		]);

		// Au lieu de le stocker dans une variable, on doit le retourner.
		return \Stripe\PaymentIntent::create([
			'amount' => $purchase->getTotal(),
			'currency' => 'eur',
		]);
	}
}