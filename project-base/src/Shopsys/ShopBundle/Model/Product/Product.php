<?php

namespace Shopsys\ShopBundle\Model\Product;

use Doctrine\ORM\Mapping as ORM;
use Shopsys\FrameworkBundle\Model\Product\Product as BaseProduct;

/**
 * @ORM\Entity
 * @ORM\Table(name="products")
 */
class Product extends BaseProduct
{

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected $extId;

    /**
     * @return int
     */
    public function getExtId()
    {
        return $this->extId;
    }

    /**
     * @param int $extId
     */
    public function setExtId(int $extId)
    {
        $this->extId = $extId;
    }
}
