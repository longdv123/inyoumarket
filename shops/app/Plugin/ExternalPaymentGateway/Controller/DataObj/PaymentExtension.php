<?php
/*
 * Copyright(c) 2015 Telecom and Et Payment Gateway, Inc. All rights reserved.
 */

namespace Plugin\ExternalPaymentGateway\Controller\DataObj;

use Eccube\Entity\Payment;

/**
 * Extra object contains payment info (dtb_payment) and related informations
 */
class PaymentExtension extends \Eccube\Entity\Payment
{

    /**
     *
     * @var type Plugin\ExternalPaymentGateway\Entity\ExternalPaymentMethod
     */
    private $ExternalPaymentMethod;

    /**
     * Set payment
     *
     * @param  Eccube\Entity\Payment
     * @return PaymentExtension
     */
    public function setExternalPaymentMethod(\Plugin\ExternalPaymentGateway\Entity\ExternalPaymentMethod $Payment)
    {
        $this->ExternalPaymentMethod = $Payment;

        return $this;
    }

    /**
     * Get payment code
     *
     * @return string
     */
    public function getExternalPaymentMethod()
    {
        return $this->ExternalPaymentMethod;
    }

}
