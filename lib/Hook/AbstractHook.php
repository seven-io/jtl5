<?php

namespace Plugin\seven_jtl5\lib\Hook;

use JTL\Plugin\Helper;
use JTL\Plugin\PluginInterface;
use JTL\Shop;
use Plugin\seven_jtl5\lib\FormHelper;
use Plugin\seven_jtl5\lib\MessageType;

abstract class AbstractHook {
    protected static function getPlugin(): PluginInterface {
        return Helper::getPluginById('seven_jtl5');
    }

    protected static function trans(string $key, string $lang = null): ?string {
        return self::getPlugin()->getLocalization()->getTranslation($key, $lang);
    }

    protected static function message(
        object $customer, string $text, string $setting): void {
        $apiKey = self::getPlugin()->getConfig()->getValue('apiKey');
        $logger = Shop::Container()->getLogService();

        if (!$apiKey) {
            $logger->notice('seven.missing.apiKey.for.sending.msg.' . $setting);
            return;
        }

        $phone = FormHelper::getCustomerPhone($customer);
        if ('' === $phone) {
            $logger->notice('seven.missing.phone.for.sending.msg.' . $setting);
            return;
        }

        $payload = [
            'text' => FormHelper::replacePlaceholders($text,
                FormHelper::parsePlaceholders(self::trans($text)), $customer),
            'to' => $phone
        ];
        switch ((int)self::getPlugin()->getConfig()->getValue($setting)) {
            case MessageType::SMS:
                $endpoint = 'sms';
                break;
            case MessageType::VOICE:
                $endpoint = 'voice';
                break;
            default:
                return;
        }

        $ch = curl_init('https://gateway.seven.io/api/' . $endpoint);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Content-type: application/json',
            'SentWith: JTL',
            'X-Api-Key: ' . $apiKey
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $logger->notice('seven.msg.sent.' . $setting, (array)$result);
    }
}
