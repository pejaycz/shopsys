<?php

namespace Tests\ShopBundle\Crawler\ResponseTest;

use Shopsys\ShopBundle\Component\DataFixture\PersistentReferenceFacade;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Component\Router\CurrentDomainRouter;
use Shopsys\ShopBundle\Component\Router\Security\RouteCsrfProtector;
use Shopsys\ShopBundle\Controller\Front\ProductController;
use Shopsys\ShopBundle\DataFixtures\Base\PricingGroupDataFixture;
use Shopsys\ShopBundle\DataFixtures\Base\UnitDataFixture as BaseUnitDataFixture;
use Shopsys\ShopBundle\DataFixtures\Base\VatDataFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\OrderDataFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\UnitDataFixture as DemoUnitDataFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\UserDataFixture;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class UrlsProvider
{
    const SEARCH_KEYWORD = 'a';
    const ROUTE_NAME_KEY = 'routeName';
    const ROUTE_PARAMETERS_KEY = 'routeParameters';
    const EXPECTED_STATUS_CODE_KEY = 'expectedStatusCode';

    /**
     * @var \Shopsys\ShopBundle\Component\DataFixture\PersistentReferenceFacade
     */
    private $persistentReferenceFacade;

    /**
     * @var \Shopsys\ShopBundle\Component\Router\CurrentDomainRouter
     */
    private $router;

    /**
     * @var \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface
     */
    private $tokenManager;

    /**
     * @var \Shopsys\ShopBundle\Component\Router\Security\RouteCsrfProtector
     */
    private $routeCsrfProtector;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var string[]
     */
    private $ignoredRouteNames = [
        // protected by csrf token
        'admin_customer_loginasuser',
        // used only for internal setting of selected domain by tab control in admin
        'admin_domain_selectdomain',
        // do not rewrite XML feed by test products
        'admin_feed_generate',
        // used by firewall to catch login requests
        // http://symfony.com/doc/current/reference/configuration/security.html#check-path
        'admin_login_check',
        // when tests are processed, there are no images in the shop
        'front_image',
        // when tests are processed, there are no images in the shop
        'front_image_without_type',
        // used by firewall to catch login requests
        // http://symfony.com/doc/current/reference/configuration/security.html#check-path
        'front_login_check',
        // in TEST environment is different security configuration
        'admin_logout',
        // temporarily not tested until it will be optimized at US-1517
        'admin_unit_delete',
    ];

    /**
     * @var string[]
     */
    private $ignoredRouteNamesForSingledomain = [
        'admin_domain_list',
    ];

    /**
     * @var string[]
     */
    private $frontAsLoggedRouteNames = [
        'front_customer_edit',
        'front_customer_orders',
        'front_customer_order_detail_registered',
    ];

    /**
     * @var int[]
     */
    private $expectedStatusCodesByRouteName = [
        'admin_login' => 302,
        'admin_login_sso' => 302,
        'front_customer_login_as_remembered_user' => 302,
        'front_logout' => 302,
        'front_order_index' => 302,
        'front_order_sent' => 302,
        'front_promo_code_remove' => 302,
        'front_registration_set_new_password' => 302,
    ];

    /**
     * @param \Symfony\Component\Routing\Route $route
     * @param string $routeName
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    private function getRouteParameters(Route $route, $routeName)
    {
        switch ($routeName) {
            case 'admin_category_edit':
                // category ID 1 is special root category, therefore we use ID 2
                return ['id' => 2];

            case 'admin_bestsellingproduct_detail':
                // category ID 1 is special root category, therefore we use ID 2
                return ['categoryId' => 2, 'domainId' => 1];

            case 'front_logout':
                return ['_csrf_token' => '{frontend_logout}'];

            case 'admin_superadmin_icondetail':
                return ['icon' => 'delete'];

            case 'admin_pricinggroup_delete':
                return ['id' => $this->persistentReferenceFacade->getReference(PricingGroupDataFixture::PRICING_GROUP_PARTNER_DOMAIN_1)->getId()];

            case 'admin_unit_delete':
                return [
                    'id' => $this->persistentReferenceFacade->getReference(BaseUnitDataFixture::UNIT_PIECES)->getId(),
                    'newId' => $this->persistentReferenceFacade->getReference(DemoUnitDataFixture::UNIT_CUBIC_METERS)->getId(),
                ];

            case 'admin_vat_delete':
                return [
                    'id' => $this->persistentReferenceFacade->getReference(VatDataFixture::VAT_SECOND_LOW)->getId(),
                    'newId' => $this->persistentReferenceFacade->getReference(VatDataFixture::VAT_LOW)->getId(),
                ];

            case 'front_article_detail':
                return ['id' => 1];

            case 'front_brand_detail':
                return ['id' => 1];

            case 'front_customer_order_detail_unregistered':
                return ['urlHash' => $this->persistentReferenceFacade->getReference(OrderDataFixture::ORDER_PREFIX . '1')->getUrlHash()];

            case 'front_customer_order_detail_registered':
                return ['orderNumber' => $this->persistentReferenceFacade->getReference(OrderDataFixture::ORDER_PREFIX . '1')->getNumber()];

            case 'front_error_page':
            case 'front_error_page_format':
                return ['code' => 404, '_format' => 'html'];

            case 'front_product_detail':
                return ['id' => 1];

            case 'front_product_list':
                return ['id' => 2];

            case 'front_registration_set_new_password':
                return ['email' => 'no-reply@netdevelo.cz', 'hash' => 'invalidHash'];

            case 'admin_administrator_edit':
                // admin ID 1 is reserved for superadmin, which is not editable
                return ['id' => 2];

            default:
                $parameters = [];
                foreach ($this->getRouteParametersNames($route) as $parameterName) {
                    if (!$route->hasDefault($parameterName) && preg_match('/(^id|.+Id)$/', $parameterName)) {
                        $parameters[$parameterName] = 1;
                    }
                }
                return $parameters;
        }
    }

    /**
     * @param \Shopsys\ShopBundle\Component\DataFixture\PersistentReferenceFacade $persistentReferenceFacade
     * @param \Shopsys\ShopBundle\Component\Router\CurrentDomainRouter $router
     * @param \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface $tokenManager
     * @param \Shopsys\ShopBundle\Component\Router\Security\RouteCsrfProtector $routeCsrfProtector
     * @param \Shopsys\ShopBundle\Component\Domain\Domain $domain
     */
    public function __construct(
        PersistentReferenceFacade $persistentReferenceFacade,
        CurrentDomainRouter $router,
        CsrfTokenManagerInterface $tokenManager,
        RouteCsrfProtector $routeCsrfProtector,
        Domain $domain
    ) {
        $this->persistentReferenceFacade = $persistentReferenceFacade;
        $this->router = $router;
        $this->tokenManager = $tokenManager;
        $this->routeCsrfProtector = $routeCsrfProtector;
        $this->domain = $domain;
    }

    /**
     * @return array
     */
    public function getAdminTestableUrlsProviderData()
    {
        $urls = [];
        foreach ($this->getAdminTestableRoutesData() as $adminTestableRouteData) {
            $routeName = $adminTestableRouteData[self::ROUTE_NAME_KEY];
            $routeParameters = $adminTestableRouteData[self::ROUTE_PARAMETERS_KEY];
            $urls[] = [
                $routeName,
                $this->router->generate($routeName, $routeParameters, RouterInterface::RELATIVE_PATH),
                $adminTestableRouteData[self::EXPECTED_STATUS_CODE_KEY],
                true,
            ];
        }

        return $urls;
    }

    /**
     * @return array
     */
    public function getFrontTestableUrlsProviderData()
    {
        $urls = [];
        foreach ($this->getFrontTestableRoutesData() as $frontTestableRouteData) {
            $routeName = $frontTestableRouteData[self::ROUTE_NAME_KEY];
            $routeParameters = $frontTestableRouteData[self::ROUTE_PARAMETERS_KEY];
            $urls[] = [
                $routeName,
                $this->router->generate($routeName, $routeParameters, RouterInterface::RELATIVE_PATH),
                $frontTestableRouteData[self::EXPECTED_STATUS_CODE_KEY],
                in_array($routeName, $this->frontAsLoggedRouteNames, true),
            ];
        }

        return $urls;
    }

    /**
     * @return array
     */
    private function getAdminTestableRoutesData()
    {
        $routesData = [];
        foreach ($this->router->getRouteCollection() as $routeName => $route) {
            if ($this->isTestableRoute($route, $routeName) && $this->isAdminRouteName($routeName)) {
                $routeParameters = $this->getRouteParameters($route, $routeName);
                $routeParameters = $this->addRouteCsrfParameter($routeName, $routeParameters);
                $expectedStatusCode = $this->getExpectedStatusCode($route, $routeName);
                $routesData[] = [
                    self::ROUTE_NAME_KEY => $routeName,
                    self::ROUTE_PARAMETERS_KEY => $routeParameters,
                    self::EXPECTED_STATUS_CODE_KEY => $expectedStatusCode,
                ];
            }
        }
        $routesData[] = $this->getSuperadminEditRouteData();

        return $routesData;
    }

    /**
     * @return array
     */
    private function getFrontTestableRoutesData()
    {
        $routesData = [];
        foreach ($this->router->getRouteCollection() as $routeName => $route) {
            if ($this->isTestableRoute($route, $routeName) && $this->isFrontRouteName($routeName)) {
                $routeParameters = $this->getRouteParameters($route, $routeName);
                $routeParameters = $this->addRouteCsrfParameter($routeName, $routeParameters);
                $expectedStatusCode = $this->getExpectedStatusCode($route, $routeName);
                $routesData[] = [
                    self::ROUTE_NAME_KEY => $routeName,
                    self::ROUTE_PARAMETERS_KEY => $routeParameters,
                    self::EXPECTED_STATUS_CODE_KEY => $expectedStatusCode,
                ];
            }
        }
        $routesData[] = $this->getProductListInCategoryWith500ProductsRouteData();
        $routesData[] = $this->getProductListInCategoryWith7600ProductsRouteData();
        $routesData[] = $this->getProductListInCategoryWith13600ProductsRouteData();
        $routesData[] = $this->getProductListWithFilteringInCategoryWith500ProductsRouteData();
        $routesData[] = $this->getProductListWithFilteringInCategoryWith7600ProductsRouteData();
        $routesData[] = $this->getProductWithListFilteringInCategoryWith13600ProductsRouteData();
        $routesData[] = $this->getSearchFilteringRouteData();
        $routesData[] = $this->getMainVariantDetailRouteData();
        $routesData[] = $this->getNewPasswordWithValidHashRouteData();

        return $routesData;
    }

    /**
     * @param string $routeName
     * @param array $routeParameters
     * @return array
     */
    private function addRouteCsrfParameter($routeName, $routeParameters)
    {
        if (preg_match('@_delete$@', $routeName)) {
            $routeParameters[RouteCsrfProtector::CSRF_TOKEN_REQUEST_PARAMETER] =
                '{'
                . $this->routeCsrfProtector->getCsrfTokenId($routeName)
                . '}';
        }

        return $routeParameters;
    }

    /**
     * Each url creates new client with clean TokenManager.
     * Csrf token must be generated for the new client before request is created.
     *
     * @param string $url
     * @return string
     */
    public function replaceCsrfTokensInUrl($url)
    {
        return preg_replace_callback(
            '@\%7B([^%]+)\%7D@',
            function ($matches) {
                return $this->tokenManager->getToken($matches[1])->getValue();
            },
            $url
        );
    }

    /**
     * @param \Symfony\Component\Routing\Route $route
     * @return string[]
     */
    private function getRouteParametersNames(Route $route)
    {
        $matches = [];
        preg_match_all('/\{([^\}]+)\}/', $route->getPath(), $matches);

        return $matches[1];
    }

    /**
     * @param string $routeName
     * @return bool
     */
    private function isAdminRouteName($routeName)
    {
        return strpos($routeName, 'admin_') === 0;
    }

    /**
     * @param string $routeName
     * @return string
     */
    private function isFrontRouteName($routeName)
    {
        return strpos($routeName, 'front_') === 0;
    }

    /**
     * @param \Symfony\Component\Routing\Route $route
     * @param string $routeName
     * @return bool
     */
    private function isTestableRoute(Route $route, $routeName)
    {
        if (in_array($routeName, $this->ignoredRouteNames, true)
            || count($route->getMethods()) > 0 && !in_array('GET', $route->getMethods(), true)
            || strpos($route->getPath(), '/_') === 0
            || strpos($route->getPath(), '/admin/_') === 0
            || $route->getCondition() === 'request.isXmlHttpRequest()'
        ) {
            return false;
        }

        return !in_array($routeName, $this->ignoredRouteNamesForSingledomain, true);
    }

    /**
     * @param \Symfony\Component\Routing\Route $route
     * @param string $routeName
     * @return int
     */
    private function getExpectedStatusCode(Route $route, $routeName)
    {
        if (array_key_exists($routeName, $this->expectedStatusCodesByRouteName)) {
            return $this->expectedStatusCodesByRouteName[$routeName];
        } elseif (strpos($route->getPath(), '/delete/') !== false) {
            return 302;
        }

        return 200;
    }

    /**
     * @return array
     */
    private function getProductListInCategoryWith500ProductsRouteData()
    {
        $productListRouteParameters = [
            'id' => 8,
        ];

        return [
            self::ROUTE_NAME_KEY => 'front_product_list',
            self::ROUTE_PARAMETERS_KEY => $productListRouteParameters,
            self::EXPECTED_STATUS_CODE_KEY => 200,
        ];
    }

    /**
     * @return array
     */
    private function getProductListWithFilteringInCategoryWith500ProductsRouteData()
    {
        $routeData = $this->getProductListInCategoryWith500ProductsRouteData();

        $routeData[self::ROUTE_PARAMETERS_KEY]['product_filter_form'] = [
            'inStock' => '1',
            'parameters' => [
                41 => [58],
            ],
        ];

        return $routeData;
    }

    /**
     * @return array
     */
    private function getProductListInCategoryWith7600ProductsRouteData()
    {
        $productListRouteParameters = [
            'id' => 3,
        ];

        return [
            self::ROUTE_NAME_KEY => 'front_product_list',
            self::ROUTE_PARAMETERS_KEY => $productListRouteParameters,
            self::EXPECTED_STATUS_CODE_KEY => 200,
        ];
    }

    /**
     * @return array
     */
    private function getProductListWithFilteringInCategoryWith7600ProductsRouteData()
    {
        $routeData = $this->getProductListInCategoryWith7600ProductsRouteData();

        $routeData[self::ROUTE_PARAMETERS_KEY]['product_filter_form'] = [
            'minimalPrice' => '100',
            'inStock' => '1',
            'parameters' => [
                1 => ['1'],
            ],
        ];

        return $routeData;
    }

    /**
     * @return array
     */
    private function getProductListInCategoryWith13600ProductsRouteData()
    {
        $productListRouteParameters = [
            'id' => 11,
        ];

        return [
            self::ROUTE_NAME_KEY => 'front_product_list',
            self::ROUTE_PARAMETERS_KEY => $productListRouteParameters,
            self::EXPECTED_STATUS_CODE_KEY => 200,
        ];
    }

    /**
     * @return array
     */
    private function getProductWithListFilteringInCategoryWith13600ProductsRouteData()
    {
        $routeData = $this->getProductListInCategoryWith13600ProductsRouteData();

        $routeData[self::ROUTE_PARAMETERS_KEY]['product_filter_form'] = [
            'minimalPrice' => '100',
            'inStock' => '1',
        ];

        return $routeData;
    }

    /**
     * @return array
     */
    private function getSearchFilteringRouteData()
    {
        $productSearchFilterData = [
            'inStock' => '1',
            'flags' => ['2'],
            'brands' => ['2', '19'],
        ];
        $productSearchParameters = [
            ProductController::SEARCH_TEXT_PARAMETER => self::SEARCH_KEYWORD,
            'product_filter_form' => $productSearchFilterData,
        ];

        return [
            self::ROUTE_NAME_KEY => 'front_product_search',
            self::ROUTE_PARAMETERS_KEY => $productSearchParameters,
            self::EXPECTED_STATUS_CODE_KEY => 200,
        ];
    }

    /**
     * @return array
     */
    private function getSuperadminEditRouteData()
    {
        return [
            self::ROUTE_NAME_KEY => 'admin_administrator_edit',
            self::ROUTE_PARAMETERS_KEY => ['id' => 1],
            self::EXPECTED_STATUS_CODE_KEY => 404,
        ];
    }

    /**
     * @return array
     */
    private function getMainVariantDetailRouteData()
    {
        return [
            self::ROUTE_NAME_KEY => 'front_product_detail',
            self::ROUTE_PARAMETERS_KEY => ['id' => 150],
            self::EXPECTED_STATUS_CODE_KEY => 200,
        ];
    }

    /**
     * @return array
     */
    private function getNewPasswordWithValidHashRouteData()
    {
        $customer = $this->persistentReferenceFacade->getReference(UserDataFixture::USER_WITH_RESET_PASSWORD_HASH);
        /** @var $customer \Shopsys\ShopBundle\Model\Customer\User */

        return [
            self::ROUTE_NAME_KEY => 'front_registration_set_new_password',
            self::ROUTE_PARAMETERS_KEY => [
                'email' => $customer->getEmail(),
                'hash' => $customer->getResetPasswordHash(),
            ],
            self::EXPECTED_STATUS_CODE_KEY => 200,
        ];
    }
}
