<?php
/**
 * Created by PhpStorm.
 * User: krikod
 * Date: 21/02/21
 * Time: 19:02
 */

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController {

	/**
	 * @Route("/", name="homepage")
	 */
	public function homepage(EntityManagerInterface $em) {

		return $this->render('home.html.twig');

	}
}