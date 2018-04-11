<?php

namespace Tests\FrameworkBundle\Unit\Model;


use DOMDocument;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Customer\BillingAddress;
use Shopsys\FrameworkBundle\Model\Customer\BillingAddressData;
use Shopsys\FrameworkBundle\Model\Customer\CustomerFacade;
use Shopsys\FrameworkBundle\Model\Customer\User;
use Shopsys\FrameworkBundle\Model\Customer\UserData;
use Shopsys\FrameworkBundle\Model\Localization\TranslatableListener;
use Shopsys\FrameworkBundle\Model\Newsletter\NewsletterFacade;
use Shopsys\FrameworkBundle\Model\Newsletter\NewsletterSubscriber;
use Shopsys\FrameworkBundle\Model\Order\Order;
use Shopsys\FrameworkBundle\Model\Order\OrderData;
use Shopsys\FrameworkBundle\Model\Order\OrderFacade;
use Shopsys\ProductFeed\DomainConfigInterface;
use Tests\ShopBundle\Test\FunctionalTestCase;

class PersonalDataExportXmlTest  extends FunctionalTestCase {

    const EMAIL = 'no-reply@netdevelo.cz';
    const EXPECTED_XML_FILE_NAME = 'test-a.xml';
    const DOMAIN_ID_FIRST = Domain::FIRST_DOMAIN_ID;

    /**
     * @var \Twig_Environment
     */
    private $twig;



    public function testExportXml()
    {
        $this->getContainer()->get(TranslatableListener::class)->setCurrentLocale('en');
        $this->twig = $this->getContainer()->get('twig');
        $userFacade = $this->getContainer()->get(CustomerFacade::class);
        /** @var CustomerFacade $facade */
        $newsletterFacade = $this->getContainer()->get(NewsletterFacade::class);
        /* @var \Shopsys\FrameworkBundle\Model\Newsletter\NewsletterFacade $newsletterFacade  */
        $orderFacade = $this->getContainer()->get(OrderFacade::class);
        /* @var \Shopsys\FrameworkBundle\Model\Order\OrderFacade $orderFacade */


        $user = $userFacade->findUserByEmailAndDomain(self::EMAIL, self::DOMAIN_ID_FIRST );
        $newsletterSubscriber = $newsletterFacade->findNewsletterSubscriberByEmailAndDomainId(
            self::EMAIL,
            self::DOMAIN_ID_FIRST
        );

        $orders = $orderFacade->getOrderListForEmailByDomainId(self::EMAIL, self::DOMAIN_ID_FIRST);

        $generatedXml = $this->twig->render('@ShopsysShop/Front/Content/PersonalData/export.xml.twig', [
            'user' => $user,
            'orders' => $orders,
            'newsletterSubscriber' => $newsletterSubscriber
        ]);

        $generatedXml = $this->normalizeXml($generatedXml);
        dump($generatedXml);
        file_put_contents(__DIR__ . '/Resources/' . self::EXPECTED_XML_FILE_NAME, $generatedXml);

        $expectedXml = file_get_contents(__DIR__ . '/Resources/' . self::EXPECTED_XML_FILE_NAME);
        dump($expectedXml);
        exit();
        $this->assertEquals($expectedXml, $generatedXml);
    }

    /**
     * @param int $domainId
     * @param string $url
     * @param string $locale
     * @return \Shopsys\ProductFeed\DomainConfigInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function createDomainConfigMock()
    {
        $domainConfigMock = $this->createMock(DomainConfigInterface::class);
        $domainConfigMock->method('getId')->willReturn(self::DOMAIN_ID_FIRST);
        $domainConfigMock->method('getUrl')->willReturn('http://www.example.com/');
        $domainConfigMock->method('getLocale')->willReturn('en');

        return $domainConfigMock;
    }

    /**
     * @param $feedContent
     * @return string
     */
    private function normalizeXml($feedContent)
    {
        $document = new DOMDocument('1.0');
        $document->preserveWhiteSpace = false;
        $document->formatOutput = true;

        $document->loadXML($feedContent);
        $generatedXml = $document->saveXML();

        return $generatedXml;
    }
}