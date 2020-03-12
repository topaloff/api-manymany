<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


// A Mettre pour serialiser le retour du service en json
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ProductController extends AbstractController
{
 /**
     * @Route("/products", name="products", methods={"GET"})
     */
    public function index(ProductRepository $productRepository):Response
    {
        $encoders = [new JsonEncoder()]; // If no need for XmlEncoder
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $products = $productRepository->findAll();

        $products = $serializer->serialize($products, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]); 
        return new Response($products, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/product", name="product", methods={"POST"})
     */
    public function add(Request $request, CategoryRepository $categoryRepository, TagRepository $tagRepository)
    {
        $data= json_decode($request->getContent(), true);
        $category = $categoryRepository->find($data['category']);
        $product = new product($data['name'],$data['price'],$data['picture'],$category);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($product);


        $entityManager->flush();
        foreach($data['tag'] as $tag){
            $tag = $tagRepository->find($tag);
            $product->addTag($tag);   
        }
        return $this->json($data);
    }

    /**
     * @Route("/product/{id}", name="product_delete", methods={"DELETE"})
     */
    public function delete(ProductRepository $productRepository, $id)
    {
        $product = $productRepository->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($product);
        $entityManager->flush();       
        return $this->json("product deleted");
    }


    /**
     * @Route("/product/edit/{id}", name="product_edit", methods={"PUT"})
     */
    public function edit(ProductRepository $productRepository, Request $request, $id)
    {
        $data= json_decode($request->getContent(), true);
        $product = $productRepository->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        if (!$product) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }      
        $product->setName($data['name']);
        $entityManager->flush();
        return $this->json("product updated");
    }
}
