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
     * @var string|null
     *
     * @ORM\Column(type="string", length=160, nullable=true)
     */
    protected $catnum;

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
