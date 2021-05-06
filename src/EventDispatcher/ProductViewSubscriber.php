<?php

namespace App\EventDispatcher;


use App\Event\ProductViewEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductViewSubscriber implements EventSubscriberInterface {
	protected $logger;

	public function __construct(LoggerInterface $logger) {
		$this->logger = $logger;
	}

	public static function getSubscribedEvents() {
		return [
			'product.view' => 'sendEmail'
		];
	}

	public function sendEmail(ProductViewEvent $event) {
		$this->logger->info("Un email a été envoyé à l'admin pour le produit n° " .
		                    $event->getProduct()->getId());
	}
}