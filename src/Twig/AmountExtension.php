<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AmountExtension extends AbstractExtension {
	public function getFilters(  ): array {
		return [
			new TwigFilter('amount', [$this, 'amount'] )
		];
	}

	public function amount($value, string $symbol = ' €', string $decsep = ',', string $thousandsep = ' ') {
//		dd( $value);
		$finalValue = $value / 100;
		$finalValue = number_format($finalValue, 2, $decsep, $thousandsep);
		// 192,29

		return $finalValue . $symbol;
	}
}