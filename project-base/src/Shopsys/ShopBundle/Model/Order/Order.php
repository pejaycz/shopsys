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

    /**
     * @var \Shopsys\ShopBundle\Model\Order\Code
     *
     * @ORM\OneToOne(targetEntity="Shopsys\ShopBundle\Model\Order\Code")
     * @ORM\JoinColumn(name="code_id", referencedColumnName="id", nullable=true)
     */
    protected $code;

}
