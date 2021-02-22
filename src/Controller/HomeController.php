<?php
/**
 * Created by PhpStorm.
 * User: krikod
 * Date: 21/02/21
 * Time: 19:02
 */

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController {

	/**
	 * @Route("/", name="homepage")
	 */
	public function homepage(  ) {
		return $this->render('home.html.twig');

	}
}