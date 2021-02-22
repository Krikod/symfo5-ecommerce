<?php
/**
 * Created by PhpStorm.
 * User: krikod
 * Date: 14/02/21
 * Time: 14:21
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HelloController extends AbstractController {

	/**
	 * @Route("/hello/{who}", name="hello")
	 * @param string $who
	 *
	 */
	public function hello($who = "world") {

		return $this->render('hello.html.twig', ['prenom' => $who]);
	}
}