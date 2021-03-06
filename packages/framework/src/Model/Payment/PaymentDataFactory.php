<?php

namespace Shopsys\FrameworkBundle\Model\Payment;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatFacade;

class PaymentDataFactory implements PaymentDataFactoryInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Payment\PaymentFacade
     */
    protected $paymentFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Vat\VatFacade
     */
    protected $vatFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    protected $domain;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Payment\PaymentFacade $paymentFacade
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Vat\VatFacade $vatFacade
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     */
    public function __construct(
        PaymentFacade $paymentFacade,
        VatFacade $vatFacade,
        Domain $domain
    ) {
        $this->paymentFacade = $paymentFacade;
        $this->vatFacade = $vatFacade;
        $this->domain = $domain;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Payment\PaymentData
     */
    public function create(): PaymentData
    {
        $paymentData = new PaymentData();
        $this->fillNew($paymentData);

        return $paymentData;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Payment\PaymentData $paymentData
     */
    protected function fillNew(PaymentData $paymentData): void
    {
        $paymentData->vat = $this->vatFacade->getDefaultVat();

        foreach ($this->domain->getAllIds() as $domainId) {
            $paymentData->enabled[$domainId] = true;
        }

        foreach ($this->domain->getAllLocales() as $locale) {
            $paymentData->name[$locale] = null;
            $paymentData->description[$locale] = null;
            $paymentData->instructions[$locale] = null;
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Payment\Payment $payment
     * @return \Shopsys\FrameworkBundle\Model\Payment\PaymentData
     */
    public function createFromPayment(Payment $payment): PaymentData
    {
        $paymentData = new PaymentData();
        $this->fillFromPayment($paymentData, $payment);

        return $paymentData;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Payment\PaymentData $paymentData
     * @param \Shopsys\FrameworkBundle\Model\Payment\Payment $payment
     */
    protected function fillFromPayment(PaymentData $paymentData, Payment $payment)
    {
        $paymentData->vat = $payment->getVat();
        $paymentData->hidden = $payment->isHidden();
        $paymentData->czkRounding = $payment->isCzkRounding();
        $paymentData->transports = $payment->getTransports()->toArray();

        $translations = $payment->getTranslations();

        $names = [];
        $descriptions = [];
        $instructions = [];

        foreach ($translations as $translate) {
            $names[$translate->getLocale()] = $translate->getName();
            $descriptions[$translate->getLocale()] = $translate->getDescription();
            $instructions[$translate->getLocale()] = $translate->getInstructions();
        }

        $paymentData->name = $names;
        $paymentData->description = $descriptions;
        $paymentData->instructions = $instructions;

        foreach ($this->domain->getAllIds() as $domainId) {
            $paymentData->enabled[$domainId] = $payment->isEnabled($domainId);
        }

        foreach ($payment->getPrices() as $paymentPrice) {
            $paymentData->pricesByCurrencyId[$paymentPrice->getCurrency()->getId()] = $paymentPrice->getPrice();
        }
    }
}
