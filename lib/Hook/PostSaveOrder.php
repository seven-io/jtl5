<?php namespace Plugin\seven_jtl5\lib\Hook;

use Exception;
use JTL\Customer\Customer;
class PostSaveOrder extends AbstractHook {
    /**
     * @throws Exception
     */
    public static function execute(array &$args_arr): void {
        $order = $args_arr['oBestellung'] ?? null;
        if (!$order) return;

        self::message(new Customer($order->kKunde), 'text_on_order', 'onOrder');
    }
}
