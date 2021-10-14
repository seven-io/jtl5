<?php

/**
 * @copyright 2021 sms77 e.K.
 * @link https://www.sms77.io
 */

namespace Plugin\sms77_jtl5;

use JTL\Events\Dispatcher;
use JTL\Plugin\Bootstrapper;
use Plugin\sms77_jtl5\lib\Hook\PostCancelOrder;
use Plugin\sms77_jtl5\lib\Hook\PostChangeOrder;
use Plugin\sms77_jtl5\lib\Hook\PostSaveOrder;
use Plugin\sms77_jtl5\lib\MessageType;

require_once __DIR__ . '/vendor/autoload.php';

class Bootstrap extends Bootstrapper {
    /**
     * @param Dispatcher $dispatcher
     */
    public function boot(Dispatcher $dispatcher): void {
        parent::boot($dispatcher);

        if ($this->shouldListen('onOrder')) $dispatcher->listen(
            'shop.hook.' . HOOK_BESTELLABSCHLUSS_INC_BESTELLUNGINDB_ENDE,
            [PostSaveOrder::class, 'execute']);

        if ($this->shouldListen('onCancel')) $dispatcher->listen(
            'shop.hook.' . HOOK_BESTELLUNGEN_XML_BEARBEITESTORNO,
            [PostCancelOrder::class, 'execute']);

        if ($this->shouldListen('onShipping')) $dispatcher->listen(
            'shop.hook.' . HOOK_BESTELLUNGEN_XML_BEARBEITEUPDATE,
            [PostChangeOrder::class, 'onShipping']);

        if ($this->shouldListen('onPayment')) $dispatcher->listen('shop.hook.'
            . HOOK_BESTELLUNGEN_XML_BEARBEITEUPDATE,
            [PostChangeOrder::class, 'onPayment']);

        if ($this->shouldListen('onPartialShipping')) $dispatcher->listen('shop.hook.'
            . HOOK_BESTELLUNGEN_XML_BEARBEITEUPDATE,
            [PostChangeOrder::class, 'onPartialShipping']);
    }

    private function shouldListen(string $name): bool {
        return MessageType::NONE
            !== (int)$this->getPlugin()->getConfig()->getValue($name);
    }
}
