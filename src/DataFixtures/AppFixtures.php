<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\Voucher;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Doctrine\DBAL\Connection;

class AppFixtures extends Fixture
{
    public function __construct(
        private EntityManagerInterface $em
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        try {
            /** @var Connection db */
            $db = $this->em->getConnection();
            $db->beginTransaction();

            $product = new Product();
            $product->setName('Product A');
            $product->setPrice('10');
            $manager->persist($product);

            $product = new Product();
            $product->setName('Product B');
            $product->setPrice('8');
            $manager->persist($product);

            $product = new Product();
            $product->setName('Product C');
            $product->setPrice('12');
            $manager->persist($product);

            $voucher = new Voucher();
            $voucher->setName('Voucher V');
            $voucher->setDescription('10% off discount voucher for the second unit applying only to Product A');
            $manager->persist($voucher);

            $voucher = new Voucher();
            $voucher->setName('Voucher R');
            $voucher->setDescription('5€ off discount on product type B');
            $manager->persist($voucher);

            $voucher = new Voucher();
            $voucher->setName('Voucher S');
            $voucher->setDescription('5% discount on a cart value over 40€');
            $manager->persist($voucher);

            $manager->flush();
            $db->commit();
            $db->beginTransaction(); // https://github.com/doctrine/DoctrineFixturesBundle/issues/363#issuecomment-1328319066
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
