<?php

namespace Shopsys\FrameworkBundle\Model\Product;

interface ProductFactoryInterface
{
    public function createDefault();

    public function create(ProductData $productData);

    public function createMainVariant(ProductData $productData, array $variants);
}
