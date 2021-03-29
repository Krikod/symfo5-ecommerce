<?php

namespace App\Security\Voter;

use App\Repository\CategoryRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CategoryVoter extends Voter
{
//	protected $categoryRepository;
//
//	public function __construct(CategoryRepository $categoryRepository) {
//		$this->categoryRepository = $categoryRepository;
//	}

	protected function supports($attribute, $subject)
    {
        return in_array($attribute, ['CAN_EDIT'])
            && $subject instanceof \App\Entity\Category;
//	    && is_numeric($subject); // pour choper l'id
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser(); // On prend déjà l'utilisateur connecté
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }
        // On prend la catégorie:
//	    $category = $this->categoryRepository->find( $subject);
//        if (!$category) {
//        	return false;
//        }

        switch ($attribute) {
	        case 'CAN_EDIT':
		        return $subject->getOwner() === $user; // true si owner
//		        return $category->getOwner() === $user; // true si owner
        }
        return false;
    }
}
