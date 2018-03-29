<?php

namespace Shopsys\ProductFeed\HeurekaBundle\Model\Category;

use Shopsys\Plugin\PluginCrudExtensionInterface;
use Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategoryFacade;
use Symfony\Component\Translation\TranslatorInterface;

class CategoryCrudExtension implements PluginCrudExtensionInterface
{
    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    private $translator;

    /**
     * @var \Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategoryFacade
     */
    private $heurekaCategoryFacade;

    /**
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     * @param \Shopsys\ProductFeed\HeurekaBundle\Model\HeurekaCategory\HeurekaCategoryFacade $heurekaCategoryFacade
     */
    public function __construct(
        TranslatorInterface $translator,
        HeurekaCategoryFacade $heurekaCategoryFacade
    ) {
        $this->translator = $translator;
        $this->heurekaCategoryFacade = $heurekaCategoryFacade;
    }

    /**
     * @return string
     */
    public function getFormTypeClass()
    {
        return CategoryFormType::class;
    }

    /**
     * @return string
     */
    public function getFormLabel()
    {
        return $this->translator->trans('Heureka.cz product feed');
    }

    /**
     * @param int $categoryId
     * @return array
     */
    public function getData($categoryId)
    {
        $heurekaCategory = $this->heurekaCategoryFacade->findByCategoryId($categoryId);

        $pluginData = [];
        if ($heurekaCategory !== null) {
            $pluginData['heureka_category'] = $heurekaCategory->getId();
        }

        return $pluginData;
    }

    /**
     * @param int $categoryId
     * @param array $data
     */
    public function saveData($categoryId, $data)
    {
        if (empty($data) || !array_key_exists('heureka_category', $data) || empty($data['heureka_category'])) {
            $this->heurekaCategoryFacade->removeHeurekaCategoryForCategoryId($categoryId);
        } else {
            $heurekaCategoryId = (int)$data['heureka_category'];

            $heurekaCategory = $this->heurekaCategoryFacade->getOneById($heurekaCategoryId);
            $this->heurekaCategoryFacade->changeHeurekaCategoryForCategoryId($categoryId, $heurekaCategory);
        }
    }

    /**
     * @param int $categoryId
     */
    public function removeData($categoryId)
    {
        $this->heurekaCategoryFacade->removeHeurekaCategoryForCategoryId($categoryId);
    }
}
