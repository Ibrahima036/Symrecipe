<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeFormType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class RecipeController extends AbstractController
{
    /**
     * Cet controlleur affiche la liste des recettes
     *
     * @param RecipeRepository $repository
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    #[Route('/recipe', name: 'recipe.index', methods: ['GET'])]
    public function index(RecipeRepository $repository, Request $request, PaginatorInterface $paginator): Response
    {
        $recipes = $paginator->paginate(
            $repository->findBy(['user' => $this->getUser()]),
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $recipes
        ]);
    }
    /**
     * cet controller permet de faire l'ajout d'une recette
     * et rédiriger sur la page des recettes
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('/recipe/new', name: 'recipe.new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        /** @var Recipe */
        $recipe = new Recipe();
        $form = $this->createForm(RecipeFormType::class, $recipe);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();
            $recipe->setUser($this->getUser());
            $em->persist($recipe);
            $em->flush();
            return $this->redirectToRoute('recipe.index');
        }
        return $this->render('pages/recipe/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/recipe/edit/{id}', name: 'recipe.edit', methods: ['GET', 'POST'])]
    public function edit($id, EntityManagerInterface $em, Request $request, RecipeRepository $repository): Response
    {
        $recipe = $repository->findOneBy(['id' => $id]);
        $form = $this->createForm(RecipeFormType::class, $recipe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();
            $em->persist($recipe);
            $em->flush();
            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('pages/recipe/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
    #[Route('/recipe/delete/{id}', name: 'recipe.delete', methods: ['GET'])]
    public function delete($id, EntityManagerInterface $em, RecipeRepository $repository, Request $request): Response
    {
        $recipe = $repository->findOneBy(['id' => $id]);
        if (!$recipe) {
            throw new NotFoundHttpException("désolé le produit avec $id n'existe pas");
        } else {
            $em->remove($recipe);
            $em->flush();
            return $this->redirectToRoute('recipe.index');
        }
    }
}
