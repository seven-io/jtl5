<?php namespace Plugin\seven_jtl5\lib\Hook;

use Exception;
use JTL\Checkout\Bestellung;
use JTL\Customer\Customer;
use JTL\Shop;

class PostChangeOrder extends AbstractHook {
    /**
     * @throws Exception
     */
    public static function onShipping(array $args_arr): void {
        $data = self::isPassingPreChecks($args_arr, BESTELLUNG_STATUS_VERSANDT);
        if ($data === null) return;
        self::message($data[0], 'text_on_shipping', 'onShipping', $data[1]);
    }

    /**
     * @throws Exception
     */
    public static function onPartialShipping(array $args_arr): void {
        $data = self::isPassingPreChecks($args_arr, BESTELLUNG_STATUS_TEILVERSANDT);
        if ($data === null) return;
        self::message($data[0], 'text_on_partial_shipping', 'onPartialShipping', $data[1]);
    }

    private static function isPassingPreChecks(array $args_arr, int $status): ?array {
        $logger = Shop::Container()->getLogService();
        
        /** @var Bestellung $oldOrder */
        $oldOrder = $args_arr['oBestellungAlt'] ?? null;
        if (!$oldOrder) {
            $logger->warning('seven hook is not passing pre checks because oldOrder is null', $args_arr);
            return null;
        }
        /** @var Bestellung $order */
        $order = $args_arr['oBestellung'] ?? null;
        if (!$order) {
            $logger->warning('seven hook is not passing pre checks because order is null', $args_arr);
            return null;
        }
        /** @var Customer $customer */
        $customer = $args_arr['oKunde'] ?? null;
        if (!$customer) {
            $logger->warning('seven hook is not passing pre checks as the customer is null', $args_arr);
            return null;
        }
        if ('' === $customer->cMobil) {
            $logger->warning('seven hook is not passing pre checks as the customer has no mobile phone', $args_arr);
            return null;
        }
        
        if ($order->cStatus === $oldOrder->cStatus) {
            $logger->warning('seven hook is not passing pre checks because the status is unchanged', $args_arr);
            return null;
        }

        return $status === $order->cStatus ? [$customer, $order] : null;
    }

    /**
     * @throws Exception
     */
    public static function onPayment(array $args_arr): void {
        $data = self::isPassingPreChecks($args_arr, BESTELLUNG_STATUS_BEZAHLT);
        if ($data === null) return;
        self::message($data[0], 'text_on_payment', 'onPayment', $data[1]);
    }
}
