<?php namespace Plugin\seven_jtl5\lib\Hook;

use Exception;
use JTL\Checkout\Bestellung;
use JTL\Customer\Customer;
class PostChangeOrder extends AbstractHook {
    /**
     * @throws Exception
     */
    public static function onShipping(array &$args_arr): void {
        $data = self::isPassingPreChecks($args_arr, BESTELLUNG_STATUS_VERSANDT);
        if ($data === null) return;
        self::message($data[0], 'text_on_shipping', 'onShipping', $data[1]);
    }

    /**
     * @throws Exception
     */
    public static function onPartialShipping(array &$args_arr): void {
        $data = self::isPassingPreChecks($args_arr, BESTELLUNG_STATUS_TEILVERSANDT);
        if ($data === null) return;
        self::message($data[0], 'text_on_partial_shipping', 'onPartialShipping', $data[1]);
    }

    private static function isPassingPreChecks(array $args_arr, int $status): ?array {
        /** @var Bestellung $oldOrder */
        $oldOrder = $args_arr['oBestellungAlt'] ?? null;
        /** @var Bestellung $order */
        $order = $args_arr['oBestellung'] ?? null;
        /** @var Customer $customer */
        $customer = $args_arr['oKunde'] ?? null;
        if (!$order || !$customer || !$oldOrder || '' === $customer->cMobil || $order->cStatus === $oldOrder->cStatus)
            return null;

        return $status === $order->cStatus ? [$customer, $order] : null;
    }

    /**
     * @throws Exception
     */
    public static function onPayment(array &$args_arr): void {
        $data = self::isPassingPreChecks($args_arr, BESTELLUNG_STATUS_BEZAHLT);
        if ($data === null) return;
        self::message($data[0], 'text_on_payment', 'onPayment', $data[1]);
    }
}
