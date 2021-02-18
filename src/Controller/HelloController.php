<?php
/**
 * Created by PhpStorm.
 * User: krikod
 * Date: 14/02/21
 * Time: 14:21
 */

namespace App\Controller;


use App\Taxes\Calculator;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Cocur\Slugify\Slugify;
use Twig\Environment;

class HelloController {

	protected $calculator;

	/**
	 * @param LoggerInterface $logger
	 * @Route("/hello/{who}", name="hello")
	 * @param string $who
	 *
	 * @return Response
	 */
	public function hello(LoggerInterface $logger,
		Calculator $calculator, $who = "world", Slugify $slugify, Environment $twig) {

//		dump($slugify->slugify("Hello world"));
dump($twig);
		$logger->info('voilà un log');
		$tva = $calculator->calcul(120);
		dd($tva);

		return new Response( "Hello $who");
	}
}