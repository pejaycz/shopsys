<?php

namespace Shopsys\ShopBundle\Controller\Front;

use DOMDocument;
use Shopsys\FrameworkBundle\Component\Controller\FrontBaseController;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Setting\Setting;
use Shopsys\FrameworkBundle\Model\Customer\CustomerFacade;
use Shopsys\FrameworkBundle\Model\Newsletter\NewsletterFacade;
use Shopsys\FrameworkBundle\Model\Order\OrderFacade;
use Shopsys\FrameworkBundle\Model\PersonalData\Mail\PersonalDataAccessMailFacade;
use Shopsys\FrameworkBundle\Model\PersonalData\PersonalDataAccessRequest;
use Shopsys\FrameworkBundle\Model\PersonalData\PersonalDataAccessRequestFacade;
use Shopsys\ShopBundle\Form\Front\PersonalData\PersonalDataFormType;
use SplFileObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PersonalDataController extends FrontBaseController
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Setting\Setting
     */
    private $setting;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Customer\CustomerFacade
     */
    private $customerFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Order\OrderFacade
     */
    private $orderFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Newsletter\NewsletterFacade
     */
    private $newsletterFacade;

    /*
     * @var \Shopsys\FrameworkBundle\Model\PersonalData\PersonalDataAccessRequestFacade
     */
    private $personalDataAccessRequestFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\PersonalData\Mail\PersonalDataAccessMailFacade
     */
    private $personalDataAccessMailFacade;

    public function __construct(
        Setting $setting,
        Domain $domain,
        CustomerFacade $customerFacade,
        OrderFacade $orderFacade,
        NewsletterFacade $newsletterFacade,
        PersonalDataAccessMailFacade $personalDataAccessMailFacade,
        PersonalDataAccessRequestFacade $personalDataAccessRequestFacade
    ) {
        $this->setting = $setting;
        $this->domain = $domain;
        $this->customerFacade = $customerFacade;
        $this->orderFacade = $orderFacade;
        $this->newsletterFacade = $newsletterFacade;
        $this->personalDataAccessMailFacade = $personalDataAccessMailFacade;
        $this->personalDataAccessRequestFacade = $personalDataAccessRequestFacade;
    }

    public function indexAction(Request $request)
    {
        $form = $this->createForm(PersonalDataFormType::class);
        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {
            $data = $form->getData();
            $data->type = PersonalDataAccessRequest::PERSONAL_DATA_DISPLAY;
            $personalData = $this->personalDataAccessRequestFacade->createPersonalDataAccessRequest(
                $data,
                $this->domain->getId()
            );
            $this->personalDataAccessMailFacade->sendMail($personalData);
            $this->getFlashMessageSender()->addSuccessFlash(t('Email with link to personal data access site was sent'));
        }

        return $this->render('@ShopsysShop/Front/Content/PersonalData/index.html.twig', [
            'personalDataSiteContent' => $this->getPersonalDataSiteContent(Setting::PERSONAL_DATA_DISPLAY_SITE_CONTENT),
            'title' => t('Personal information overview'),
            'form' => $form->createView(),
        ]);
    }

    public function exportAction(Request $request)
    {
        $form = $this->createForm(PersonalDataFormType::class);
        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {
            $personalData = $form->getData();
            $personalData->type = PersonalDataAccessRequest::PERSONAL_DATA_EXPORT;
            $personalData = $this->personalDataAccessRequestFacade->createPersonalDataAccessRequest(
                $personalData,
                $this->domain->getId()
            );
            $this->personalDataAccessMailFacade->sendMail($personalData);
            $this->getFlashMessageSender()->addSuccessFlash(t('Email with link to personal data access site was sent'));
        }

        return $this->render('@ShopsysShop/Front/Content/PersonalData/index.html.twig', [
            'personalDataSiteContent' => $this->getPersonalDataSiteContent(Setting::PERSONAL_DATA_EXPORT_SITE_CONTENT),
            'title' => t('Export customer data'),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param string $hash
     */
    public function accessDisplayAction($hash)
    {
        $personalDataAccessRequest = $this->personalDataAccessRequestFacade->findEmailByHashAndDomainId(
            $hash,
            PersonalDataAccessRequest::PERSONAL_DATA_DISPLAY,
            $this->domain->getId()
        );

        if ($personalDataAccessRequest !== null) {
            list($user, $orders, $newsletterSubscriber) = $this->getPersonalData($personalDataAccessRequest->getEmail());

            return $this->render('@ShopsysShop/Front/Content/PersonalData/detail.html.twig', [
                'personalDataAccessRequest' => $personalDataAccessRequest,
                'orders' => $orders,
                'user' => $user,
                'newsletterSubscriber' => $newsletterSubscriber,
            ]);
        }

        throw new NotFoundHttpException();
    }

    public function accessExportAction($hash)
    {
        $personalDataAccessRequest = $this->personalDataAccessRequestFacade->findEmailByHashAndDomainId(
            $hash,
            PersonalDataAccessRequest::PERSONAL_DATA_EXPORT,
            $this->domain->getId()
        );

        if ($personalDataAccessRequest !== null) {
            $user = $this->customerFacade->findUserByEmailAndDomain($personalDataAccessRequest->getEmail(), $this->domain->getId());
            $newsletterSubscriber = $this->newsletterFacade->findNewsletterSubscriberByEmailAndDomainId(
                $personalDataAccessRequest->getEmail(),
                $this->domain->getId()
            );

            $orders = $this->orderFacade->getOrderCountForEmailAndDomain(
                $personalDataAccessRequest->getEmail(),
                $this->domain->getId()
            );

            return $this->render('@ShopsysShop/Front/Content/PersonalData/export.html.twig', [
                    'personalDataSiteContent' => $this->getPersonalDataSiteContent(Setting::PERSONAL_DATA_EXPORT_SITE_CONTENT),
                    'hash' => $hash,
                    'user' => $user,
                    'newslettersubscriber' => $newsletterSubscriber,
                    'orders' => $orders,
                ]);
        }

        throw new NotFoundHttpException();
    }

    public function exportXmlAction($hash)
    {
        $personalDataAccessRequest = $this->personalDataAccessRequestFacade->findEmailByHashAndDomainId(
            $hash,
            PersonalDataAccessRequest::PERSONAL_DATA_EXPORT,
            $this->domain->getId()
        );

        if ($personalDataAccessRequest !== null) {
            list($user, $orders, $newsletterSubscriber) = $this->getPersonalData($personalDataAccessRequest->getEmail());
            $response = new StreamedResponse();
            $response->headers->set('Content-Type', 'xml');
            $response->headers->set('Content-Disposition', 'attachment; filename="'
                . $personalDataAccessRequest->getEmail() . '.xml"');

            $xmlContent = $this->render('@ShopsysShop/Front/Content/PersonalData/export.xml.twig', [
                'user' => $user,
                'newsletterSubscriber' => $newsletterSubscriber,
                'orders' => $orders,

            ])->getContent();

            $xmlContent = $this->normalizeXml($xmlContent);

            $response->setCallback(function () use (&$xmlContent) {
                $output = new SplFileObject('php://output', 'w+');
                $output->fwrite($xmlContent);
            });

            return $response;
        }

        throw new NotFoundHttpException();
    }

    /**
     * @param string $settingKey
     * @return string|null
     */
    private function getPersonalDataSiteContent($settingKey)
    {
        return $this->setting->getForDomain($settingKey, $this->domain->getId());
    }

    /**
     * @param string $content
     * @return string
     */
    private function normalizeXml($content)
    {
        $document = new DOMDocument('1.0');
        $document->preserveWhiteSpace = false;
        $document->formatOutput = true;

        $document->loadXML($content);
        $generatedXml = $document->saveXML();

        return $generatedXml;
    }

    /**
     * @param string $email
     * @return array
     */
    private function getPersonalData($email)
    {
        $user = $this->customerFacade->findUserByEmailAndDomain($email, $this->domain->getId());
        $orders = $this->orderFacade->getOrderListForEmailByDomainId($email, $this->domain->getId());
        $newsletterSubscriber = $this->newsletterFacade->findNewsletterSubscriberByEmailAndDomainId($email, $this->domain->getId());

        return [$user, $orders, $newsletterSubscriber];
    }
}
