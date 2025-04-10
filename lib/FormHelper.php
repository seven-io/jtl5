<?php

namespace Plugin\seven_jtl5\lib;

use Exception;
use Illuminate\Support\Collection;
use JTL\Plugin\Helper;
use JTL\Customer\Customer;
use JTL\Shop;

abstract class FormHelper {
    public static function getApiKey(): ?string {
        return Helper::getPluginById('seven_jtl5')->getConfig()->getValue('apiKey');
    }

    public static function sendBulk(string $apiKey, array $params, string $msgType): array {
        $messages = [];

        foreach ($params as $p) {
            try {
                switch ($msgType) {
                    case 'rcs':
                        $endpoint = 'rcs/messages';
                        break;
                    case 'sms':
                        $endpoint = 'sms';
                        break;
                    case 'voice':
                        $endpoint = 'voice';
                        break;
                    default:
                        return [];
                }

                $ch = curl_init('https://gateway.seven.io/api/' . $endpoint);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($p));
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Accept: application/json',
                    'Content-type: application/json',
                    'SentWith: JTL',
                    'X-Api-Key: ' . $apiKey
                ]);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($ch);
                curl_close($ch);

                $messages[] = json_encode($result, JSON_PRETTY_PRINT);
            } catch (Exception $e) {
                $messages[] = $e->getMessage();
            }
        }

        return $messages;
    }

    private static function buildBaseSmsParams(): array {
        return [
            'delay' => $_POST['delay'] ?? '',
            'flash' => intval($_POST['flash'] ?? 0),
            'foreign_id' => $_POST['foreignId'] ?? '',
            'label' => $_POST['label'] ?? '',
            'performance_tracking' =>intval($_POST['performanceTracking'] ?? 0),
        ];
    }

    private static function buildBaseVoiceParams(): array {
        return [];
    }

    public static function buildBulkParams(): array {
        $customers = self::getCustomers();
        $text = $_POST['text'];
        $isSMS = 'sms' === $_POST['msgType'];
        $param = [...$isSMS ? self::buildBaseSmsParams() : self::buildBaseVoiceParams(), 'from' => $_POST['from']];

        $params = [];

        if ('' === $_POST['to']) {
            $matches = FormHelper::parsePlaceholders($text);

            if (empty($matches)) {
                $to = [];

                foreach ($customers as $c) {
                    $to[] = FormHelper::getCustomerPhone($c);
                }

                foreach ($isSMS ? [implode(',', $to)] : $to as $t) {
                    $params[] = [...$param, 'text' => $text, 'to' => $t];
                }
            } else {
                foreach ($customers as $c) {
                    $params[] = [
                         ...$param,
                        'text' => FormHelper::replacePlaceholders($text, $matches, $c),
                        'to' => FormHelper::getCustomerPhone($c)
                    ];
                }
            }
        } else $params[] = [...$param, 'text' => $text, 'to' => $_POST['to']];

        return $params;
    }

    /**
     * @return Customer[]|Collection
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
        /** @var Customer $customer */
        return '' === $customer->cMobil ? $customer->cTel : $customer->cMobil;
    }

    public static function replacePlaceholders(string $text, array $matches, object $obj): string {
        $cryptoService = Shop::Container()->getCryptoService();
        foreach ($matches as $match) {
            $key = trim($match, '{}');

            if (property_exists($obj, $key)) {
                $replace = $cryptoService->decryptXTEA($obj->$key);
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
