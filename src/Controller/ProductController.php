<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductForm;
use App\Repository\ProductRepository;
use App\Entity\AddProductHistory;
use App\Repository\AddProductHistoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/editor/product')]
final class ProductController extends AbstractController
{
    #[Route(name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductForm::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();

            if ($image) {
                $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalName);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();
                try {
                    $image->move(
                        $this->getParameter('image_directory'),
                        $newFilename
                    );
                } catch (\Exception $e) {
                    $this->addFlash('danger', 'Erreur lors de l\'upload de l\'image.');
                    return $this->redirectToRoute('app_product_index');
                }
                $product->setImage($newFilename); 
            }
            $entityManager->persist($product);
            $entityManager->flush();

            $stockHistory = new AddProductHistory();
            $stockHistory->setQte($product->getStock());
            $stockHistory->setProduct($product);
            $stockHistory->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($stockHistory);
            $entityManager->flush();

            $this->addFlash('success', 'Produit créé avec succès.');
            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ProductForm::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();

            if ($image) {
                $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalName);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();
                try {
                    $image->move(
                        $this->getParameter('image_directory'),
                        $newFilename
                    );
                } catch (\Exception $e) {
                    $this->addFlash('danger', 'Erreur lors de l\'upload de l\'image.');
                    return $this->redirectToRoute('app_product_index');
                }
                $product->setImage($newFilename); 
            }
            $entityManager->flush();

            $this->addFlash('success', 'Produit mis à jour avec succès.');
            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
        }

        $this->addFlash('danger', 'Produit supprimé avec succès.');
        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/add/product/{id}', name: 'app_product_stock_add', methods: ['GET'])]
    public function addStock($id, EntityManagerInterface $entityManager, Request $request):Response
    {
        $addstock = new AddProductStock();
        $form = $this->createForm(AddProductStockType::class, $addstock);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $product = $entityManager->getRepository(Product::class)->find($id);
            if (!$product) {
                $this->addFlash('danger', 'Produit non trouvé.');
                return $this->redirectToRoute('app_product_stock_add', ['id' => $id]);
            }

            $addstock->setProduct($product);
            $addstock->setCreatedAt(new \DateTimeImmutable());
            $entityManager->persist($addstock);
            $entityManager->flush();

            $this->addFlash('success', 'Stock ajouté avec succès.');
            return $this->redirectToRoute('app_product_index');
        }
    }
}
