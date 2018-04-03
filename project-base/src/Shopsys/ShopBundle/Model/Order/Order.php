<?php

namespace Shopsys\ShopBundle\Model\Order;

use Doctrine\ORM\Mapping as ORM;
use Shopsys\FrameworkBundle\Model\Order\Order as BaseOrder;

/**
 * @ORM\Entity
 * @ORM\Table(name="orders")
 */
class Order extends BaseOrder
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $coolAttribute;

}
