<?php

namespace Shopsys\FrameworkBundle\Model\Order;

class OrderTotalPrice
{
    /**
     * @var string
     */
    protected $priceWithVat;

    /**
     * @var string
     */
    protected $priceWithoutVat;

    /**
     * @var string
     */
    protected $productPriceWithVat;

    /**
     * @param string $priceWithVat
     * @param string $priceWithoutVat
     * @param string $productPriceWithVat
     */
    public function __construct($priceWithVat, $priceWithoutVat, $productPriceWithVat)
    {
        $this->priceWithVat = $priceWithVat;
        $this->priceWithoutVat = $priceWithoutVat;
        $this->productPriceWithVat = $productPriceWithVat;
    }

    /**
     * @return string
     */
    public function getPriceWithVat()
    {
        return $this->priceWithVat;
    }

    /**
     * @return string
     */
    public function getPriceWithoutVat()
    {
        return $this->priceWithoutVat;
    }

    /**
     * @return string
     */
    public function getProductPriceWithVat()
    {
        return $this->productPriceWithVat;
    }
}
