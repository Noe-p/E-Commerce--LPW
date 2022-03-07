<?php

namespace App\Controller;

use App\Entity\CommandLine;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;



class HomePageController extends AbstractController
{
    #[Route('/', name: 'home_page', methods: ['GET'])]
    public function index(ProductRepository $productRepository, CategoryRepository $categoryRepository): Response
    {
        //Gérer les filtres
        if ($_GET) {
            $query = $productRepository->findBy(array('category' => $_GET["category"]));
        } else {
            $query = $productRepository->findAll();
        }

        return $this->render('home_page/index.html.twig', [
            'controller_name' => 'HomePageController',
            'products' => $query,
            'categories' => $categoryRepository->findAll(),
        ]);
    }
}