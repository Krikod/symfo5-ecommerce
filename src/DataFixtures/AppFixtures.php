<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
	protected $slugger;

	public function __construct(SluggerInterface $slugger) {
		$this->slugger = $slugger;
	}

	public function load(ObjectManager $manager)
    {
    	$faker = Factory::create('fr_FR');
    	$faker->addProvider(new \Liior\Faker\Prices($faker));
		$faker->addProvider(new \Bezhanov\Faker\Provider\Commerce($faker));

        for ($p = 0; $p < 100; $p++) {
        	$product = new Product();
        	$product->setName($faker->productName) // Bezhanov
		        ->setPrice($faker->price(4000, 20000)) // lib Liior
		        ->setSlug(strtolower($this->slugger->slug($product->getName()))); // construct: fait slug à partir du nom du produit
        	$manager->persist($product);
        }

        $manager->flush();
    }
}
