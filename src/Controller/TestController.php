<?php
/**
 * Created by PhpStorm.
 * User: krikod
 * Date: 12/02/21
 * Time: 19:24
 */

namespace App\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController {
	public function index(  ) {
		dd("cool");
	}

	/**
	 * @Route("/test/{age<\d+>?0}", name="test", methods={"GET", "POST"},
	 *     host="localhost", schemes={"https", "http"})
	 *
	 * @param Request $request
	 * @param $age
	 *
	 * @return Response
	 */
	public function test(Request $request, $age) {
		dump($request);
		return new Response("Vous avez $age ans");
	}
}