<?php

/**
 * @copyright 2021-2022 sms77 e.K. ; 2023-present seven communications GmbH & Co. KG
 * @link https://www.seven.io
 */

namespace Plugin\seven_jtl5\lib;

use Exception;
use Illuminate\Support\Collection;
use JTL\Plugin\Helper;
use Kunde;
use Shop;
use Sms77\Api\Client;
use Sms77\Api\Params\SmsParams;
use Sms77\Api\Params\VoiceParams;

abstract class FormHelper {
    public static function getApiKey(): ?string {
        return Helper::getPluginById('seven_jtl5')->getConfig()->getValue('apiKey');
    }

    public static function initClient(string $apiKey = null): Client {
        return new Client($apiKey ?: self::getApiKey(), 'JTL');
    }

    /**
     * @param string $apiKey
     * @param array $params
     * @return array
     */
    public static function sendBulk(string $apiKey, array $params): array {
        $messages = [];
        $client = self::initClient($apiKey);

        foreach ($params as $p) {
            try {
                $json = $p instanceof SmsParams ? $client->smsJson($p)
                    : $client->voiceJson($p);
                $messages[] = json_encode($json, JSON_PRETTY_PRINT);
            } catch (Exception $e) {
                $messages[] = $e->getMessage();
            }
        }

        return $messages;
    }

    private static function buildBaseSmsParams(): SmsParams {
        return (new SmsParams)
            ->setDelay($_POST['delay'])
            ->setFlash(isset($_POST['flash']))
            ->setForeignId($_POST['foreignId'])
            ->setLabel($_POST['label'])
            ->setNoReload(isset($_POST['noReload']))
            ->setPerformanceTracking(isset($_POST['performanceTracking']));
    }

    private static function buildBaseVoiceParams(): VoiceParams {
        return (new VoiceParams)->setXml(isset($_POST['xml']));
    }

    public static function buildBulkParams(): array {
        $customers = self::getCustomers();
        $text = $_POST['text'];
        $isSMS = 'sms' === $_POST['msgType'];
        $param = ($isSMS ? self::buildBaseSmsParams() : self::buildBaseVoiceParams())
            ->setDebug(isset($_POST['debug']))
            ->setFrom($_POST['from']);

        $params = [];

        if ('' === $_POST['to']) {
            $matches = FormHelper::parsePlaceholders($text);

            if (empty($matches)) {
                $to = [];

                foreach ($customers as $c) $to[] = FormHelper::getCustomerPhone($c);

                foreach ($isSMS ? [implode(',', $to)] : $to as $t)
                    $params[] = (clone $param)->setText($text)->setTo($t);
            } else foreach ($customers as $c) $params[] = (clone $param)
                ->setText(FormHelper::replacePlaceholders($text, $matches, $c))
                ->setTo(FormHelper::getCustomerPhone($c));
        } else $params[] = (clone $param)->setText($text)->setTo($_POST['to']);

        return $params;
    }

    /**
     * @return Kunde[]|Collection
     */
    public static function getCustomers(): Collection {
        $stmt = 'SELECT * FROM tkunde WHERE (cMobil<>"" OR cTel<>"")';
        $params = [];

        if (isset($_POST['filter']['active'])) {
            $stmt .= ' AND cAktiv = :active';
            $params['active'] = 'Y';
        }

        $group = $_POST['filter']['group'];
        if ('' !== $group) {
            $stmt .= ' AND kKundengruppe = :group';
            $params['group'] = $group;
        }

        $language = $_POST['filter']['language'];
        if ('' !== $language) {
            $stmt .= ' AND kSprache = :language';
            $params['language'] = $language;
        }

        return Shop::Container()->getDB()->getCollection($stmt, $params);
    }

    public static function getCustomerPhone(object $customer): string {
        /** @var Kunde $customer */
        return '' === $customer->cMobil ? $customer->cTel : $customer->cMobil;
    }

    public static function replacePlaceholders(
        string $text, array $matches, object $obj): string {
        foreach ($matches as $match) {
            $key = trim($match, '{}');

            if (property_exists($obj, $key)) {
                $replace = Shop::Container()->getCryptoService()
                    ->decryptXTEA($obj->$key);
                if ('' === $replace) $replace = $obj->$key;
                $text = str_replace($match, trim($replace), $text);
                $text = preg_replace('/\s+/', ' ', $text);
                $text = str_replace(' ,', ',', $text);
            }
        }

        return $text;
    }

    public static function parsePlaceholders(string $text): array {
        $matches = [];
        preg_match_all('{{{+[A-Za-z]+}}}', $text, $matches);
        return is_array($matches) ? $matches[0] : [];
    }
}
