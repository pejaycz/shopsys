<?php

namespace Shopsys\ShopBundle\Model\Order;

use Doctrine\ORM\Mapping as ORM;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderItem as BaseOrderItem;

/**
 * @ORM\Entity
 * @ORM\Table(name="order_items")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *     "payment" = "\Shopsys\ShopBundle\Model\Order\OrderPayment",
 *     "product" = "\Shopsys\ShopBundle\Model\Order\OrderProduct",
 *     "transport" = "\Shopsys\ShopBundle\Model\Order\OrderTransport"
 * })
 */
abstract class OrderItem extends BaseOrderItem
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $extId;

}
