<?php

namespace Shopsys\ProductFeed\HeurekaBundle\Model\Product;

use Doctrine\ORM\EntityManager;
use Shopsys\FrameworkBundle\Model\Product\ProductRepository;

class HeurekaProductDomainFacade
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\ProductFeed\HeurekaBundle\Model\Product\HeurekaProductDomainRepository
     */
    private $heurekaProductDomainRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductRepository
     */
    private $productRepository;

    /**
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Shopsys\ProductFeed\HeurekaBundle\Model\Product\HeurekaProductDomainRepository $heurekaProductDomainRepository
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductRepository $productRepository
     */
    public function __construct(
        EntityManager $em,
        HeurekaProductDomainRepository $heurekaProductDomainRepository,
        ProductRepository $productRepository
    ) {
        $this->em = $em;
        $this->heurekaProductDomainRepository = $heurekaProductDomainRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * @param int $productId
     * @return \Shopsys\ProductFeed\HeurekaBundle\Model\Product\HeurekaProductDomain[]|null
     */
    public function findByProductId($productId)
    {
        return $this->heurekaProductDomainRepository->findByProductId($productId);
    }

    /**
     * @param array $productsIds
     * @param int $domainId
     * @return \Shopsys\ProductFeed\HeurekaBundle\Model\Product\HeurekaProductDomain[]
     */
    public function getHeurekaProductDomainsByProductsIdsDomainIdIndexedByProductId($productsIds, $domainId)
    {
        return $this->heurekaProductDomainRepository->getHeurekaProductDomainsByProductsIdsDomainIdIndexedByProductId(
            $productsIds,
            $domainId
        );
    }

    /**
     * @param $productId
     */
    public function delete($productId)
    {
        $heurekaProductDomains = $this->heurekaProductDomainRepository->findByProductId($productId);

        foreach ($heurekaProductDomains as $heurekaProductDomain) {
            $this->em->remove($heurekaProductDomain);
            $this->em->flush();
        }
    }

    /**
     * @param $productId
     * @param \Shopsys\ProductFeed\HeurekaBundle\Model\Product\HeurekaProductDomainData[] $heurekaProductDomainsData
     */
    public function saveHeurekaProductDomainsForProductId($productId, array $heurekaProductDomainsData)
    {
        $existingHeurekaProductDomains = $this->heurekaProductDomainRepository->findByProductId($productId);

        $this->removeOldHeurekaProductDomainsForProductId($existingHeurekaProductDomains, $heurekaProductDomainsData);

        foreach ($heurekaProductDomainsData as $heurekaProductDomainData) {
            $this->saveHeurekaProductDomain($productId, $heurekaProductDomainData);
        }
    }

    /**
     * @param $productId
     * @param \Shopsys\ProductFeed\HeurekaBundle\Model\Product\HeurekaProductDomainData $heurekaProductDomainData
     */
    public function saveHeurekaProductDomain($productId, HeurekaProductDomainData $heurekaProductDomainData)
    {
        $product = $this->productRepository->getById($productId);
        $heurekaProductDomainData->product = $product;

        $existingHeurekaProductDomain = $this->heurekaProductDomainRepository->findByProductIdAndDomainId(
            $productId,
            $heurekaProductDomainData->domainId
        );

        if ($existingHeurekaProductDomain !== null) {
            $existingHeurekaProductDomain->edit($heurekaProductDomainData);
        } else {
            $newHeurekaProductDomain = new HeurekaProductDomain($heurekaProductDomainData);
            $this->em->persist($newHeurekaProductDomain);
        }

        $this->em->flush();
    }

    /**
     * @param \Shopsys\ProductFeed\HeurekaBundle\Model\Product\HeurekaProductDomain[] $existingHeurekaProductDomains
     * @param \Shopsys\ProductFeed\HeurekaBundle\Model\Product\HeurekaProductDomainData[] $newHeurekaProductDomainsData
     */
    private function removeOldHeurekaProductDomainsForProductId(
        array $existingHeurekaProductDomains,
        array $newHeurekaProductDomainsData
    ) {
        $domainsIdsWithNewHeurekaProductDomains = [];
        foreach ($newHeurekaProductDomainsData as $newHeurekaProductDomainData) {
            $domainsIdsWithNewHeurekaProductDomains[$newHeurekaProductDomainData->domainId] = $newHeurekaProductDomainData->domainId;
        }

        foreach ($existingHeurekaProductDomains as $existingHeurekaProductDomain) {
            if (!array_key_exists($existingHeurekaProductDomain->getDomainId(), $domainsIdsWithNewHeurekaProductDomains)) {
                $this->em->remove($existingHeurekaProductDomain);
            }
        }
    }
}
