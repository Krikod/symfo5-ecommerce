<?php

namespace App\EventDispatcher;

use App\Entity\User;
use App\Event\PurchaseSuccessEvent;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Core\Security;

class PurchaseSuccessEmailSubscriber implements EventSubscriberInterface {

	private $logger;
	private $mailer;
	private $security;

	public function __construct(LoggerInterface $logger, MailerInterface $mailer, Security $security) {
		$this->logger = $logger;
		$this->mailer = $mailer;
		$this->security = $security;
	}

	public static function getSubscribedEvents() {
		return [
			'purchase.success' => 'sendSuccessEmail'
		];
	}

	public function sendSuccessEmail(PurchaseSuccessEvent $purchaseSuccessEvent) {

		// Récup user en ligne (pour connaître son address)
		// Security ou $purchaseSuccessEvent..
		/** @var User */
//		$currentUser = $this->security->getUser();
		$currentUser = $purchaseSuccessEvent->getPurchase()->getUser();

		// Récup commande (ds PurchaseSuccessEvent)
		$purchase = $purchaseSuccessEvent->getPurchase();

		// Ecrire le mail (nveau TemplatedEmail)
		$email = new TemplatedEmail();
		$email->to(new Address($currentUser->getEmail(), $currentUser->getFullName()))
			->from("contact@mail.com")
			->subject("Bravo, votre commande ({$purchase->getId()}) a bien été confirmée ()")
			->htmlTemplate('emails/purchase_success.html.twig')
			->context([
				'purchase' => $purchase,
				'user' => $currentUser
			]);

		// Envoyer le mail (MailerInterface)
		$this->mailer->send($email);

		$this->logger->info( "Email envoyé pour la commande n° " .
		$purchaseSuccessEvent->getPurchase()->getId());
	}
}
