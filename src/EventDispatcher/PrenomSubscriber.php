<?php

namespace App\EventDispatcher;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class PrenomSubscriber implements EventSubscriberInterface {

	public static function getSubscribedEvents() {
		return [
			'kernel.request' => 'addPrenomToAttributes',
			// etc
		];
	}

	public function addPrenomToAttributes(RequestEvent $requestEvent) {
		$requestEvent->getRequest()->attributes->set( 'prenom', 'Kristen');
	}

}