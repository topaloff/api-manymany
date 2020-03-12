<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


class CategoryController extends AbstractController
{
 /**
     * @Route("/categories", name="categories", methods={"GET"})
     */
    public function index(CategoryRepository $categoryRepository)
    {
        $categories = $categoryRepository->findAll();
        return $this->json($categories);
    }

    /**
     * @Route("/category", name="category", methods={"POST"})
     */
    public function add(Request $request)
    {
        $data= json_decode($request->getContent(), true);
        $category = new Category($data['name']);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($category);
        $entityManager->flush();
        return $this->json($data);
    }

    /**
     * @Route("/Category/{id}", name="Category_delete", methods={"DELETE"})
     */
    public function delete(CategoryRepository $categoryRepository, $id)
    {
        $category = $categoryRepository->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($category);
        $entityManager->flush();       
        return $this->json("Category deleted");
    }


    /**
     * @Route("/Category/edit/{id}", name="Category_edit", methods={"PUT"})
     */
    public function edit(CategoryRepository $categoryRepository, Request $request, $id)
    {
        $data= json_decode($request->getContent(), true);
        $category = $categoryRepository->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        if (!$category) {
            throw $this->createNotFoundException(
                'No Category found for id '.$id
            );
        }      
        $ategory->setName($data['name']);
        $entityManager->flush();
        return $this->json("Category updated");
    }
}
