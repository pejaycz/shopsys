<?php

namespace Shopsys\ShopBundle\Model\Product;

use Doctrine\ORM\Mapping as ORM;
use Shopsys\FrameworkBundle\Model\Transport\TransportPrice as BaseTransportPrice;

/**
 * @ORM\Table(name="transport_prices")
 * @ORM\Entity
 */
class TransportPrice extends BaseTransportPrice
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Transport\Transport
     *
     * @ORM\ManyToOne(targetEntity="\Shopsys\ShopBundle\Model\Product\Product", inversedBy="prices")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $product;
}
