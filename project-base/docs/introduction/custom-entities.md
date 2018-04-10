# Custom Entities

The system is prepared and configured for custom entities.
If we keep following instructions, entities work without any further effort.

* Place entities in the namespace `Shopsys\ShopBundle\Model` (directory `src/Shopsys/ShopsysBundle/Model`).
* Use annotations for Doctrine mapping.
More in [annotations reference](https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/annotations-reference.html).

For example `CustomEntity` can look like:

```php
// src/Shopsys/ShopsysBundle/Model/CustomEntity.php

namespace Shopsys\ShopBundle\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class CustomEntity
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    //...
}
```

When the entity is completed, we have to ensure the system registers it properly.
A convinient way is to [generate migration](phing-targets.md#db-migrations-generate)
and manually check the migration is correct.
If the migration is fine, we can continue the work and eventually
[execute migrations](phing-targets.md#db-migrations).

If the system doesn't generate the migration, the entity is probably in an incorrect namespace or has wrong Doctrine annotation mapping.
