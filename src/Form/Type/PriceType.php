<?php

namespace App\Form\Type;

use App\Form\DataTransformer\CentimesTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PriceType extends AbstractType {

	public function buildForm( FormBuilderInterface $builder, array $options) {
		// $options = toutes les valeurs par defaut de toute la famille (dans ProductType(PriceType), divide, NumberType
//		dd( $options);

		if ($options['divide'] === false) { return; } // False ds ProductType/PriceType

		// Pour un seul champ
		$builder->addModelTransformer(new CentimesTransformer());
	}

	public function getParent(  ) {

		return NumberType::class; // S'inscrit dans la filiation de ce Type.
	}

	public function configureOptions( OptionsResolver $resolver ) {

		//Mettre en place une option divide.
		$resolver->setDefaults([
			'divide' => true // Par défaut il faudra diviser
			// on peut le mettre a false dans ProductType
		]);

	}
}