<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Expr\Throw_;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class CategoryController extends AbstractController
{
	protected $category_repository;

	public function __construct(CategoryRepository $category_repository) {
		$this->category_repository = $category_repository;
	}

	public function renderMenuList( ) {
		$categories = $this->category_repository->findAll();

		return $this->render( 'category/_menu.html.twig', [
			'categories' => $categories
		]);
	}
	
    /**
     * @Route("/admin/category/create", name="category_create")
     */
    public function create(EntityManagerInterface $em, SluggerInterface $slugger, Request $request): Response
    {
    	$category = new Category();

    	$form = $this->createForm(CategoryType::class, $category);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$category->setSlug(strtolower($slugger->slug($category->getName())))
			;
			$em->persist($category);
			$em->flush();

			return $this->redirectToRoute('homepage');
		}

		$formView = $form->createView();

        return $this->render('category/create.html.twig', [
        	'formView' => $formView
        ]);
    }

	/**
	 * @Route("/admin/category/{id}/edit", name="category_edit")
	 */
	public function edit($id, Request $request, CategoryRepository $repo,
		EntityManagerInterface $em, SluggerInterface $slugger) {

		$category = $repo->find($id);

		if (!$category) {
			throw new NotFoundHttpException("Cette catégorie n'existe pas");
		}

		// AUTORISATION
		// par les Voter, plus simple:
//		$security->isGranted('CAN_EDIT', $category);
//		$this->denyAccessUnlessGranted( 'CAN_EDIT', $category, "Vous n'êtes pas le propriétaire de cette catégorie");

		// ACCES A UN OBJET
//		// Le créateur de la catégorie est le seul à pouvoir la modifier // todo faire différemment
//		$user = $this->getUser(); // Security->getUser()
//
//		if (!$user) {
//			return $this->redirectToRoute('security_login');
//		}
//
//		if ($user !== $category->getOwner()) {
//			throw new AccessDeniedHttpException("Vous n'êtes pas le propriétaire de cette catégorie");
//			// Ou redirection !
//		}

		$form = $this->createForm(CategoryType::class, $category);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
//			$category->setSlug(strtolower($slugger->slug($category->getName())));

			$em->flush();

			return $this->redirectToRoute('homepage');
		}

		$formView = $form->createView();

		return $this->render('category/edit.html.twig', [
			'category' => $category,
			'formView' => $formView
		]);
	}
}
