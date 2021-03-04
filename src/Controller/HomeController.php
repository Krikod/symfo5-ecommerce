<?php
/**
 * Created by PhpStorm.
 * User: krikod
 * Date: 21/02/21
 * Time: 19:02
 */

namespace App\Controller;

use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController {

	/**
	 * @Route("/", name="homepage")
	 */
	public function homepage(ProductRepository $product_repository) {
		$products = $product_repository->findBy([], [], 3);

		return $this->render('home.html.twig', [
			'products' => $products
		]);

	}
}