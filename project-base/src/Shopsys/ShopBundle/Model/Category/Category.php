<?php

namespace Shopsys\ShopBundle\Model\Category;

use Doctrine\ORM\Mapping as ORM;
use Shopsys\FrameworkBundle\Model\Category\Category as BaseCategory;

/**
 * @ORM\Entity
 * @ORM\Table(name="categories")
 */
class Category extends BaseCategory
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Administrator\Administrator|null
     *
     * @ORM\ManyToOne(targetEntity="Shopsys\FrameworkBundle\Model\Administrator\Administrator")
     * @ORM\JoinColumn(name="admin_id", referencedColumnName="id", nullable = true, onDelete="CASCADE")
     */
    protected $admin;

    public function getAdmin()
    {
        return $this->admin;
    }
}
