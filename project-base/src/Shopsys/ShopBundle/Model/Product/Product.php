<?php

namespace Shopsys\ShopBundle\Model\Product;

use Doctrine\Common\Collections\Collection;
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
     * @var string null
     *
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    protected $vat;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Shopsys\FrameworkBundle\Model\Customer\User")
     * @ORM\JoinTable(name="products_users")
     */
    protected $users;

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

    /**
     * @return \Shopsys\FrameworkBundle\Model\Customer\User[]
     */
    public function getUsers()
    {
        return $this->users->toArray();
    }

}
