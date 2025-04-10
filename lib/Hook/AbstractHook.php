<?php

namespace Plugin\seven_jtl5\lib\Hook;

use JTL\Plugin\Helper;
use JTL\Shop;
use Plugin\seven_jtl5\lib\FormHelper;
use Plugin\seven_jtl5\lib\MessageType;
abstract class AbstractHook {
    protected static function message(object $customer, string $text, string $setting, object $order): void {
        $plugin = Helper::getPluginById('seven_jtl5');
        $cfg = $plugin->getConfig();
        $apiKey = $cfg->getValue('apiKey');
        $localization = $plugin->getLocalization();
        $logger = Shop::Container()->getLogService();

        if (!$apiKey) {
            $logger->warning('seven.missing.apiKey.for.sending.msg.' . $setting);
            return;
        }

        $phone = FormHelper::getCustomerPhone($customer);
        if ('' === $phone) {
            $logger->warning('seven.missing.phone.for.sending.msg.' . $setting);
            return;
        }

        $translatedText = $localization->getTranslation($text);
        $matches = FormHelper::parsePlaceholders($translatedText);
        $payloadText = FormHelper::replacePlaceholders($translatedText, $matches, $customer);
        $payloadText = FormHelper::replacePlaceholders($payloadText, $matches, $order);
        $payload = [
            'text' => $payloadText,
            'to' => $phone
        ];

        switch ((int)$cfg->getValue($setting)) {
            case MessageType::SMS:
                $endpoint = 'sms';
                break;
            case MessageType::VOICE:
                $endpoint = 'voice';
                break;
            case MessageType::RCS:
                $endpoint = 'rcs/messages';
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
