<?php

/**
 * @copyright 2021 sms77 e.K.
 * @link https://www.sms77.io
 */

namespace Plugin\sms77_jtl5\lib\Hook;

use JTL\Plugin\Helper;
use JTL\Plugin\PluginInterface;
use JTL\Shop;
use Plugin\sms77_jtl5\lib\FormHelper;
use Plugin\sms77_jtl5\lib\MessageType;
use Sms77\Api\Client;
use Sms77\Api\Params\SmsParams;
use Sms77\Api\Params\VoiceParams;

abstract class AbstractHook {
    protected static function getPlugin(): PluginInterface {
        return Helper::getPluginById('sms77_jtl5');
    }

    protected static function trans(string $key, string $lang = null): ?string {
        return self::getPlugin()->getLocalization()->getTranslation($key, $lang);
    }

    protected static function message(
        object $customer, string $text, string $setting): void {
        $apiKey = self::getPlugin()->getConfig()->getValue('apiKey');
        $logger = Shop::Container()->getLogService();

        if (!$apiKey) {
            $logger->notice('sms77.missing.apiKey.for.sending.msg.' . $setting);
            return;
        }

        $phone = FormHelper::getCustomerPhone($customer);
        if ('' === $phone) {
            $logger->notice('sms77.missing.phone.for.sending.msg.' . $setting);
            return;
        }

        switch ((int)self::getPlugin()->getConfig()->getValue($setting)) {
            case MessageType::SMS:
                $params = new SmsParams;
                $request = function (Client $client, SmsParams $smsParams) {
                    return $client->smsJson($smsParams);
                };
                break;
            case MessageType::VOICE:
                $params = new VoiceParams;
                $request = function (Client $client, VoiceParams $voiceParams) {
                    return $client->voiceJson($voiceParams);
                };
                break;
            default:
                return;
        }

        $params->setTo($phone)->setText(
            FormHelper::replacePlaceholders($text,
                FormHelper::parsePlaceholders(self::trans($text)), $customer));

        $logger->notice('sms77.msg.sent.' . $setting,
            (array)$request(FormHelper::initClient($apiKey), $params));
    }
}
