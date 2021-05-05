<?php

namespace App\Event;

use App\Entity\Purchase;
use Symfony\Contracts\EventDispatcher\Event;

class PurchaseSuccessEvent extends Event {
	private $purchase;

	public function __construct(Purchase $purchase) {
		$this->purchase = $purchase;
	}

	// Comme donnée privée, on crée:
	public function getPurchase(): Purchase {
		return $this->purchase;
	}
}