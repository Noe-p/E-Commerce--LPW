<?php

namespace App\Controller;

use App\Entity\CommandLine;
use App\Repository\ProductRepository;
use App\Entity\Order;
use App\Repository\CommandLineRepository;
use App\Repository\CustomerAddressRepository;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;




class OrderController extends AbstractController
{
    #[Route('/order', name: 'order', methods: ['GET'])]
    public function index(CustomerAddressRepository $customerAddressRepository, EntityManagerInterface $entityManager, OrderRepository $orderRepository, CommandLineRepository $commandLineRepository): Response
    {
        $allItems = [];
        $totalPrice = 0;
        $orderUser = $orderRepository->findOneBy(array('user' => $this->getUser(), 'Valid' => '0'));
        $allUserAddress = $customerAddressRepository->findBy(array('user' => $this->getUser()));

        if ($orderUser) {
            $commandLines = $commandLineRepository->findBy(array('order_id' => $orderUser->getId()));

            foreach ($commandLines as $item) {
                array_push($allItems, $item);
                $totalPrice += $item->getProduct()->getPrice() * $item->getQuantity();
            }

            $orderUser->setTotalPrice($totalPrice);
            $entityManager->persist($orderUser);
            $entityManager->flush();
        }

        return $this->render('order/index.html.twig', [
            'controller_name' => 'OrderController',
            'commandLines' => $allItems,
            'totalPrice' => $totalPrice,
            'allUserAddress' => $allUserAddress,
        ]);
    }

    #[Route('/order/new', name: 'order_new', methods: ['GET'])]
    public function new(CommandLineRepository $commandLineRepository, ProductRepository $productRepository, OrderRepository $orderRepository, EntityManagerInterface $entityManager): Response
    {
        if ($_GET) {
            if ($_GET["product"]) {
                $orderUser = $orderRepository->findOneBy(array('user' => $this->getUser(), 'Valid' => '0'));
                $product = $productRepository->findOneBy(array('id' => $_GET["product"]));

                //Si un panier existe On ajoute une nouvelle ligne de commande
                if ($orderUser) {
                    $commandLine = $commandLineRepository->findOneBy(array('order_id' => $orderUser->getId(), 'product' => $product));

                    if ($commandLine) {
                        $commandLine->setQuantity($commandLine->getQuantity() + 1);
                        $entityManager->persist($commandLine);
                    } else {
                        $newCommandLine = new CommandLine();
                        $newCommandLine->setQuantity(1);
                        $newCommandLine->setProduct($product);
                        $newCommandLine->setOrderId($orderUser);
                        $entityManager->persist($newCommandLine);
                    }
                    $entityManager->flush();
                    //Si le panier n'existe pas encore, on créer un nouveau panier puis on ajoute une ligne de commande
                } else {
                    $order = new Order();
                    $order->setValid(0);
                    $order->setDate(new \DateTime());
                    $order->setTotalPrice(0);
                    $order->setUser($this->getUser());
                    $entityManager->persist($order);
                    $entityManager->flush();

                    $newCommandLine = new CommandLine();
                    $newCommandLine->setQuantity(1);
                    $newCommandLine->setProduct($product);
                    $newCommandLine->setOrderId($order);
                    $entityManager->persist($newCommandLine);
                    $entityManager->flush();
                }
            }
        }
        return $this->redirectToRoute('order', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/order/remove', name: 'commandLine_delete', methods: ['GET'])]
    public function delete(EntityManagerInterface $entityManager, CommandLineRepository $commandLineRepository): Response
    {
        if ($_GET) {
            $commandLine = $commandLineRepository->findOneBy(array('id' => $_GET["commandLineId"]));
            $entityManager->remove($commandLine);
            $entityManager->flush();
        }

        return $this->redirectToRoute('order', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/order/update', name: 'commandLine_update', methods: ['POST', 'GET'])]
    public function update(Request $request, EntityManagerInterface $entityManager, CommandLineRepository $commandLineRepository): Response
    {
        if ($_POST && $_GET) {
            $commandLine = $commandLineRepository->findOneBy(array('id' => $_GET["id"]));
            $commandLine->setQuantity((int) $_POST['quantity']);
            $entityManager->flush();
        }

        return $this->redirectToRoute('order', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/order/command', name: 'order_command', methods: ['POST', 'GET'])]
    public function command(EntityManagerInterface $entityManager, CommandLineRepository $commandLineRepository, OrderRepository $orderRepository, CustomerAddressRepository $customerAddressRepository): Response
    {
        $orderUser = $orderRepository->findOneBy(array('user' => $this->getUser(), 'Valid' => '0'));
        $allItems = [];
        $commandLines = $commandLineRepository->findBy(array('order_id' => $orderUser->getId()));

        foreach ($commandLines as $item) {
            array_push($allItems, $item);
        }

        $orderUser->setValid('1');
        $entityManager->persist($orderUser);
        $entityManager->flush();

        return $this->render('order/command.html.twig', [
            'controller_name' => 'OrderController',
            'order' => $orderUser,
            'allItems' => $allItems,
            'userAddress' => $customerAddressRepository->findOneBy(array('user' => $this->getUser(), 'isSelect' => '1')),

        ]);
    }
}
