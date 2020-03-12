<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


class TagController extends AbstractController
{
 /**
     * @Route("/tags", name="tags", methods={"GET"})
     */
    public function index(TagRepository $tagRepository)
    {
        $tags = $tagRepository->findAll();
        return $this->json($tags);
    }

    /**
     * @Route("/tag", name="tag", methods={"POST"})
     */
    public function add(Request $request)
    {
        $data= json_decode($request->getContent(), true);
        $tag = new tag($data['name']);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($tag);
        $entityManager->flush();
        return $this->json($data);
    }

    /**
     * @Route("/tag/{id}", name="tag_delete", methods={"DELETE"})
     */
    public function delete(TagRepository $tagRepository, $id)
    {
        $tag = $tagRepository->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($tag);
        $entityManager->flush();       
        return $this->json("tag deleted");
    }


    /**
     * @Route("/tag/edit/{id}", name="tag_edit", methods={"PUT"})
     */
    public function edit(TagRepository $tagRepository, Request $request, $id)
    {
        $data= json_decode($request->getContent(), true);
        $tag = $tagRepository->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        if (!$tag) {
            throw $this->createNotFoundException(
                'No tag found for id '.$id
            );
        }      
        $tag->setName($data['name']);
        $entityManager->flush();
        return $this->json("tag updated");
    }
}
