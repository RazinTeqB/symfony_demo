<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Voucher;
use App\Repository\ProductRepository;
use App\Repository\VoucherRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/clear/cart', name: 'clear_cart')]
    public function clearCart(SessionInterface $session): Response
    {
        $session->set('cart', []);
        return $this->redirectToRoute('app_home'); // to avoid form resubmission on page refresh
    }

    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $em, Request $request): Response
    {
        /** @var ProductRepository $productsRepo */
        $productsRepo = $em->getRepository(Product::class);
        $products = $productsRepo->findAll();

        /** @var VoucherRepository $vouchersRepo */
        $vouchersRepo = $em->getRepository(Voucher::class);
        $vouchers = $vouchersRepo->findAll();

        $session = $request->getSession();
        $cart = $session->get('cart') ?? [];
        $cartItems = $cart['items'] ?? [];
        $cartVouchers = $cart['vouchers'] ?? [];

        if ($request->isMethod('POST')) {
            $data = $request->request->all();
            if (array_key_exists('action', $data)) {
                if (isset($data['product_id'])) {
                    if ($data['action'] == 'addToCart') {
                        $item = $productsRepo->find($data['product_id']);
                        if ($item instanceof Product) {
                            $key = array_search($item->getId(), array_column($cartItems, 'id'));

                            if ($key === false) {
                                $tempData = array_merge($item->jsonSerialize(), ['quantity' => 1]);
                                $cartItems[] = $tempData;
                            } else {
                                $cartItems[$key]['quantity'] = array_key_exists('quantity', $cartItems[$key]) ? ((int) $cartItems[$key]['quantity']) + 1 : 1;
                            }
                            $session->set('cart', [
                                'items' => $cartItems,
                                'vouchers' => $cartVouchers,
                            ]);
                        }
                    } elseif ($data['action'] == 'removeAllFromCart') {
                        $key = array_search($data['product_id'], array_column($cartItems, 'id'));
                        if ($key !== false) {
                            unset($cartItems[$key]);
                            $session->set('cart', [
                                'items' => array_values($cartItems),
                                'vouchers' => $cartVouchers,
                            ]);
                        }
                    } elseif ($data['action'] == 'removeOneFromCart') {
                        $key = array_search($data['product_id'], array_column($cartItems, 'id'));
                        if ($key !== false) {
                            if ($cartItems[$key]['quantity'] > 1) {
                                --$cartItems[$key]['quantity'];
                            } else {
                                unset($cartItems[$key]);
                                $cartItems = array_values($cartItems);
                            }
                            $session->set('cart', [
                                'items' => $cartItems,
                                'vouchers' => $cartVouchers,
                            ]);
                        }
                    } elseif ($data['action'] == 'addOneToCart') {
                        $key = array_search($data['product_id'], array_column($cartItems, 'id'));
                        if ($key !== false) {
                            ++$cartItems[$key]['quantity'];
                            $session->set('cart', [
                                'items' => $cartItems,
                                'vouchers' => $cartVouchers,
                            ]);
                        }
                    }
                }
                if (isset($data['voucher_id'])) {
                    $key = array_search($data['voucher_id'], array_column($cartVouchers, 'id'));
                    if ($data['action'] == 'applyVoucher') {
                        if ($key === false) {
                            $voucher = $vouchersRepo->find($data['voucher_id']);
                            $cartVouchers[] = $voucher->jsonSerialize();
                        }
                    } elseif ($data['action'] == 'removeVoucher') {
                        if ($key !== false) {
                            unset($cartVouchers[$key]);
                            $cartVouchers = array_values($cartVouchers);
                        }
                    }
                    $session->set('cart', [
                        'items' => $cartItems,
                        'vouchers' => $cartVouchers,
                    ]);
                }
            }
            return $this->redirectToRoute('app_home'); // to avoid form resubmission on page refresh
        }

        $discounts = [
            ["id" => 1, "value" => 0],
            ["id" => 2, "value" => 0],
            ["id" => 3, "value" => 0],
        ];

        $cartVouchersIds = array_column($cartVouchers, 'id');;
        $items = ($request->getSession()->get('cart'))['items'] ?? [];
        /** If no items added to cart then no point calculating vouchers */
        if (is_countable($items) && count($items) > 0) {
            foreach ($items as $item) {
                if ($item['id'] == 1 && $item['quantity'] > 1 && in_array(1, $cartVouchersIds)) {
                    $discountPrice = ($item['price'] * 10) / 100;
                    /**
                     * If we were saving cart discounts in db then we can find in array with this
                     * $index = array_search(1, array_column($discounts, 'id'));
                     * but as it is static we can fully avoid this at the moment.
                     */
                    $discounts[0]['value'] = $discountPrice;
                }

                /**
                 * Check voucher 2 (Voucher R) applied
                 * And Product B exist in cart
                 * Here 2 is the id of the Product B
                 * As we have only 3 products and are inserted in db with the fixture,
                 * It expect the id 2 for Product B.
                 */
                if ($item['id'] == 2 && in_array(2, $cartVouchersIds)) {
                    // Total of 5€ discount on Product B.
                    $discountPrice = 5;
                    $discounts[1]['value'] = $discountPrice;
                }
            }
            /**
             * Calculate Voucher S discount
             * If Cart total - other voucher discount amount is greater than 40
             */
            $cartTotal = array_sum(array_map(function ($item) {
                return ($item['quantity'] * $item['price']);
            }, $items));
            $totalDiscount = array_sum(array_column($discounts, 'value'));
            $currentCartValue = $cartTotal - $totalDiscount;

            if (in_array(3, $cartVouchersIds) && ($currentCartValue) > 40) {
                $discountPrice = (($currentCartValue) * 5) / 100;
                $discounts[2]['value'] = $discountPrice;
            }
        }

        return $this->render('index.html.twig', [
            'products' => $products,
            'vouchers' => $vouchers,
            'cart' => $request->getSession()->get('cart'),
            'cartVouchersIds' => $cartVouchersIds,
            'discounts' => $discounts,
        ]);
    }
}
