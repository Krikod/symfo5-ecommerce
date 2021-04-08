<?php

namespace App\Controller\Purchase;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PurchasesListController extends AbstractController {

	/**
	 * @Route("/purchases", name="purchases_index")
	 * @IsGranted("ROLE_USER", message="Vous devez être connecté pour accéder à vos commandes")
	 */
	public function index() : Response {
		// S'assurer que user est connecté (et qui est-il), sinon Accueil -> Security
		$user = $this->getUser();

		// Appeler service Twig et envoyer Response:
		return $this->render( 'purchase/index.html.twig', [
			'purchases' => $user->getPurchases()
		]);
	}
}