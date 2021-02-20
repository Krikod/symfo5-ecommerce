<?php
/**
 * Created by PhpStorm.
 * User: krikod
 * Date: 19/02/21
 * Time: 21:28
 */

namespace App\Taxes;


class Detector {

//	protected $amount;
//	protected $limit;

	public function __construct(float $limit) {
		$this->limit = $limit;
	}

	public function detect(float $amount): bool {
		if ($amount > $this->limit) {
			return true;
		} return false;

	}
}