<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Voucher;
use App\Repository\ProductRepository;
use App\Repository\VoucherRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $em): Response
    {
        /** @var ProductRepository $productsRepo */
        $productsRepo = $em->getRepository(Product::class);
        $products = $productsRepo->findAll();

        /** @var VoucherRepository $vouchersRepo */
        $vouchersRepo = $em->getRepository(Voucher::class);
        $vouchers = $vouchersRepo->findAll();

        return $this->render('index.html.twig', [
            'products' => $products,
            'vouchers' => $vouchers,
        ]);
    }
}
