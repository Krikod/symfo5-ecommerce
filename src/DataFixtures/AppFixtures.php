<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use Bezhanov\Faker\Provider\Commerce;
use Bluemmb\Faker\PicsumPhotosProvider;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Liior\Faker\Prices;
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
    	$faker->addProvider(new Prices($faker));
		$faker->addProvider(new Commerce($faker));
		$faker->addProvider(new PicsumPhotosProvider($faker));

		for ($c = 0; $c < 3; $c++) {
			$category = new Category();
			$category->setName($faker->department) // Bezhanov
				->setSlug(strtolower($this->slugger->slug($category->getName())));

			$manager->persist($category);

			for ($p = 0; $p < mt_rand(15, 20); $p++) {
				$product = new Product();
				$product->setName($faker->productName) // Bezhanov
				        ->setPrice($faker->price(4000, 20000)) // lib Liior
						// construct: slug à partir du nom du produit
						->setSlug(strtolower($this->slugger->slug($product->getName())))
						->setCategory($category)
						->setShortDescription($faker->paragraph())
						// bluemmb, true=images différentes
						->setMainPicture($faker->imageUrl(400, 400, true));

				$manager->persist($product);
			}
        }

        $manager->flush();
    }
}
