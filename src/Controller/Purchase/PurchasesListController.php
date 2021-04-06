<?php

namespace App\Controller\Purchase;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;

class PurchasesListController extends AbstractController {

	protected $security;
	protected $router;
	protected  $twig;

	public function __construct(Security $security, RouterInterface $router, Environment $twig) {
		$this->security = $security;
		$this->router = $router;
		$this->twig = $twig;
	}

	/**
	 * @Route("/purchases", name="purchases_index")
	 */
	public function index() : Response {
		// S'assurer que user est connecté, sinon redirigé vers homepage
		// -> Security
		/** @var User */ // sur VS CODE
		$user = $this->security->getUser(); // UserInterface n'a pas de méthode getPurchase(), donc précision

		if (!$user) {
			// Générer une URL en fonction du nom d'une route
			// -> UrlGeneratorInterface, ou RouterInterface
//			$url = $this->router->generate('homepage');
//			return new RedirectResponse($url);
			// Plutôt rediriger vers login puis commandes avec:
			throw new AccessDeniedException("Vous devez être connecté pour accéder à vos commandes.");
		}

		// Savoir QUI est connecté -> Security
		// ok

		// Passer user connecté à Twig pour afficher ses commandes
		// -> Environment de Twig + Response

		$html = $this->twig->render( 'purchase/index.html.twig', [
			'purchases' => $user->getPurchases()
		]);
		return new Response($html);
	}
}