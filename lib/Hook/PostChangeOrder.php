<?php

/**
 * @copyright 2021-2022 sms77 e.K. ; 2023-present seven communications GmbH & Co. KG
 * @link https://www.seven.io
 */

namespace Plugin\seven_jtl5\lib\Hook;

use Exception;
use JTL\Checkout\Bestellung;
use Kunde;

class PostChangeOrder extends AbstractHook {
    /**
     * @param array $args_arr
     * @throws Exception
     */
    public static function onShipping(array &$args_arr): void {
        if ($customer = self::isPassingPreChecks($args_arr, BESTELLUNG_STATUS_VERSANDT))
            self::message($customer, 'text_on_shipping', 'onShipping');
    }

    /**
     * @param array $args_arr
     * @throws Exception
     */
    public static function onPartialShipping(array &$args_arr): void {
        if ($customer = self::isPassingPreChecks($args_arr,
            BESTELLUNG_STATUS_TEILVERSANDT))
            self::message($customer, 'text_on_partial_shipping', 'onPartialShipping');
    }

    /**
     * @param array $args_arr
     * @param int $status
     * @return object|null
     */
    private static function isPassingPreChecks(array $args_arr, int $status): ?object {
        /** @var Bestellung $oldOrder */
        $oldOrder = $args_arr['oBestellungAlt'] ?? null;
        /** @var Bestellung $order */
        $order = $args_arr['oBestellung'] ?? null;
        /** @var Kunde $customer */
        $customer = $args_arr['oKunde'] ?? null;
        if (!$order || !$customer || !$oldOrder || '' === $customer->cMobil
            || $order->cStatus === $oldOrder->cStatus)
            return null;

        return $status === $order->cStatus ? $customer : null;
    }

    /**
     * @param array $args_arr
     * @throws Exception
     */
    public static function onPayment(array &$args_arr): void {
        if ($customer = self::isPassingPreChecks($args_arr, BESTELLUNG_STATUS_BEZAHLT))
            self::message($customer, 'text_on_payment', 'onPayment');
    }

    private function switchStatus(object $order) {
        switch ($order->cStatus) {
            case BESTELLUNG_STATUS_VERSANDT:
            case BESTELLUNG_STATUS_STORNO:
            case BESTELLUNG_STATUS_OFFEN:
            case BESTELLUNG_STATUS_IN_BEARBEITUNG:
            case BESTELLUNG_STATUS_BEZAHLT:
            case BESTELLUNG_STATUS_TEILVERSANDT:
            case BESTELLUNG_VERSANDBESTAETIGUNG_MAX_TAGE:
            case BESTELLUNG_ZAHLUNGSBESTAETIGUNG_MAX_TAGE:
        }
    }
}
