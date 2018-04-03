<?php

namespace Shopsys\ShopBundle\Model\Product;

use Shopsys\FrameworkBundle\Model\Product\ProductData;
use Shopsys\FrameworkBundle\Model\Product\ProductDataFactory;
use Shopsys\FrameworkBundle\Model\Product\ProductFactory as BaseProductFactory;
use Shopsys\FrameworkBundle\Model\Product\ProductFactoryInterface;

class ProductFactory extends BaseProductFactory implements ProductFactoryInterface
{

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductDataFactory
     */
    private $productDataFactory;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductDataFactory $productDataFactory
     */
    public function __construct(ProductDataFactory $productDataFactory)
    {
        parent::__construct($productDataFactory);
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
     * @return \Shopsys\ShopBundle\Model\Product\Product
     */
    public function create(ProductData $productData)
    {
        $product = Product::create($productData);
        $product->setExtId(10);

        return $product;
    }

    public function createMainVariant(ProductData $productData, array $variants)
    {
        $product = Product::createMainVariant($productData, $variants);
        $product->setExtId(10);

        return $product;
    }
}
