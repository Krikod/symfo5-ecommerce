<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Purchase;
use App\Entity\User;
use Bezhanov\Faker\Provider\Commerce;
use Bluemmb\Faker\PicsumPhotosProvider;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Liior\Faker\Prices;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
	protected $slugger;
	protected $encoder;

	public function __construct(SluggerInterface $slugger, UserPasswordEncoderInterface $encoder) {
		$this->slugger = $slugger;
		$this->encoder = $encoder;
	}

	public function load(ObjectManager $manager)
    {
    	$faker = Factory::create('fr_FR');
    	$faker->addProvider(new Prices($faker));
		$faker->addProvider(new Commerce($faker));
		$faker->addProvider(new PicsumPhotosProvider($faker));


//		Création des utilisateurs
		$admin = new User();

		$hash = $this->encoder->encodePassword( $admin, "password" );

		$admin->setEmail( "admin@gmail.com")
			->setPassword($hash )
			->setFullName( "Admin")
			->setRoles( ['ROLE_ADMIN']);

		$manager->persist( $admin);

		$users = [];

		for ($u = 0; $u < 5; $u++) {
			$user = new User();
			$hash = $this->encoder->encodePassword( $user, "password");
			$user->setEmail( "user-$u@gmail.com")
			     ->setFullName( $faker->name())
				->setPassword( $hash);

			$users[] = $user;

			$manager->persist( $user);
		}

//		Création des catégories
		for ($c = 0; $c < 3; $c++) {
			$category = new Category();
			$category->setName($faker->department) // Bezhanov
				->setSlug(strtolower($this->slugger->slug($category->getName())));

			$manager->persist($category);
//      Création des produits
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

        // Purchase
		for ($p = 0; $p < mt_rand(20, 40); $p++) {
			$purchase = new Purchase();
			$purchase->setFullName( $faker->name)
				->setAddress( $faker->streetAddress)
				->setPostalCode( $faker->postcode)
				->setCity( $faker->city)
				->setUser( $faker->randomElement($users))
				->setTotal( mt_rand(2000, 30000))
				->setPurchasedAt( $faker->dateTimeInInterval('-6 months'));

			if ($faker->boolean(90)) {
				$purchase->setStatus(Purchase::STATUS_PAID);
			}

			$manager->persist( $purchase);
		}

        $manager->flush();
    }
}
