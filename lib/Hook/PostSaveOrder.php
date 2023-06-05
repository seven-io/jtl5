<?php

/**
 * @copyright 2021-2022 sms77 e.K. ; 2023-present seven communications GmbH & Co. KG
 * @link https://www.seven.io
 */

namespace Plugin\seven_jtl5\lib\Hook;

use Exception;
use Kunde;

class PostSaveOrder extends AbstractHook {
    /**
     * @param array $args_arr
     * @throws Exception
     */
    public static function execute(array &$args_arr): void {
        $order = $args_arr['oBestellung'] ?? null;
        if (!$order) return;

        self::message(new Kunde($order->kKunde), 'text_on_order', 'onOrder');
    }
}
