<?php

namespace Shopsys\FrameworkBundle\Model\PersonalData;

use Shopsys\FrameworkBundle\Component\Breadcrumb\BreadcrumbGeneratorInterface;
use Shopsys\FrameworkBundle\Component\Breadcrumb\BreadcrumbItem;

class PersonalDataBreadcrumbResolverFactory implements BreadcrumbGeneratorInterface
{
    /**
     * @inheritdoc
     */
    public function getBreadcrumbItems($routeName, array $routeParameters = [])
    {
        if (strpos($routeName, 'export'))
        {
            $breadcrumbItem = new BreadcrumbItem(t('Export customer data'));
        }else{
            $breadcrumbItem =  new BreadcrumbItem(t('Personal information overview'));
        }
     return [$breadcrumbItem];
    }

    /**
     * @inheritdoc
     */
    public function getRouteNames()
    {
        return [
            'front_personal_data',
            'front_personal_data_access',
            'front_personal_data_export',
            'front_personal_data_access_export',
        ];
    }
}
