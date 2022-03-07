<?php

namespace App\Controller;

use App\Repository\CustomerAddressRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\OrderRepository;

class UserPageController extends AbstractController
{
    #[Route('/userPage', name: 'user_page')]
    public function index(OrderRepository $orderRepository, EntityManagerInterface $entityManager, CustomerAddressRepository $customerAddressRepository): Response
    {
        $allAddress = $customerAddressRepository->findBy(array('user' => $this->getUser()));

        if ($_GET) {
            if ($_GET["idAddress"]) {
                foreach ($allAddress as $address) {
                    $address->setIsSelect(0);
                }
                $address = $customerAddressRepository->findOneBy(array('id' => $_GET["idAddress"]))->setIsSelect(1);
                $entityManager->persist($address);
                $entityManager->flush();
            }
            if ($_GET['path']) {
                return $this->redirectToRoute('order', [], Response::HTTP_SEE_OTHER);
            }
        }


        return $this->render('user_page/index.html.twig', [
            'controller_name' => 'UserPageController',
            'allAddress' => $allAddress,
            'allCommand' => $orderRepository->findBy(array('user' => $this->getUser(), 'Valid' => '1')),
        ]);
    }
}