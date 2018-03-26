<?php

namespace Shopsys\FrameworkBundle\Model\Product;

use Shopsys\FrameworkBundle\Model\Product\ProductFactory as BaseProductFactory;

class ProductFactory implements ProductFactoryInterface
{

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductDataFactory
     */
    private $productDataFactory;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductDataFactory $productDataFactory
     */
    public function __construct(ProductDataFactory $productDataFactory) {
        $this->productDataFactory = $productDataFactory;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Product
     */
    public function createDefault()
    {
        return Product::create(
            $this->productDataFactory->createDefault()
        );
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductData $productData
     * @return \Shopsys\FrameworkBundle\Model\Product\Product
     */
    public function create(ProductData $productData) {
        return Product::create($productData);
    }
}