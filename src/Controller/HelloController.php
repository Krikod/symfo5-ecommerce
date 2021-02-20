<?php
/**
 * Created by PhpStorm.
 * User: krikod
 * Date: 14/02/21
 * Time: 14:21
 */

namespace App\Controller;


use App\Taxes\Calculator;
use App\Taxes\Detector;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Cocur\Slugify\Slugify;
use Twig\Environment;

class HelloController {

//	protected $calculator;
//	protected $detector;

	/**
	 * @param LoggerInterface $logger
	 * @Route("/hello/{who}", name="hello")
	 * @param string $who
	 *
	 * @return Response
	 */
	public function hello(
		$who = "world",
//		LoggerInterface $logger,
//		Calculator $calculator,
//		Slugify $slugify,
//		Environment $twig,
//		Detector $detector
	) {

//		dump($detector->detect(101));
//		dump($detector->detect(10));
//
//		dump($twig);
//		dump($slugify->slugify("Hello world"));
//
//		$logger->info('voilà un log');
//		$tva = $calculator->calcul(120);
//		dump($tva);

		return new Response( "Hello $who");
	}
}