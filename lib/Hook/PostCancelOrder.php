<?php

namespace Plugin\seven_jtl5\lib\Hook;

use Exception;
class PostCancelOrder extends AbstractHook {
    /**
     * @throws Exception
     */
    public static function execute(array &$args_arr): void {
        $order = $args_arr['oBestellung'] ?? null;
        $customer = $args_arr['oKunde'] ?? null;
        if (!$order || !$customer) return;

        self::message($customer, 'text_on_cancel', 'onCancel');
    }
}
