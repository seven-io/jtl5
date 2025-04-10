<?php

declare(strict_types=1);

namespace Plugin\seven_jtl5;

use JTL\Customer\CustomerGroup;
use JTL\Helpers\Request;
use JTL\Language\LanguageHelper;
use JTL\Plugin\PluginInterface;
use JTL\Router\Controller\Backend\AbstractBackendController;
use JTL\Shop;
use JTL\Smarty\JTLSmarty;
use Laminas\Diactoros\Response;
use Plugin\seven_jtl5\lib\FormHelper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use JTL\Helpers\Form;

class BackendBulkController extends AbstractBackendController
{
    public int $menuID = 0;
    public PluginInterface $plugin;

    /**
     * @inheritdoc
     */
    public function getResponse(ServerRequestInterface $request, array $args, JTLSmarty $smarty): ResponseInterface
    {
        $this->smarty = $smarty;
        $backendURL = $this->plugin->getPaths()->getBackendURL();
        $adminPath = $this->plugin->getPaths()->getAdminPath();
        $this->route = \str_replace(Shop::getAdminURL(), '', $backendURL);
        $this->smarty->assign('route', $this->route);
        $tab = Request::getVar('action', 'Massenversand');
        $smarty->assign('backendURL', $backendURL);
        $smarty->assign('step', $tab)
            ->assign('tab', $tab)
            ->assign('action', $backendURL);

        $body = (array)$request->getParsedBody();

        if ($tab === 'Massenversand') {
            if ($request->getMethod() === 'POST' && ($body['Setting'] ?? '') !== '1' && Form::validateToken()) {
                $apiKey = FormHelper::getApiKey();

                if ('' === ($apiKey ?: '')) {
                    $this->alertService->addDanger(__('missingApiKey'), 'missingApiKey');
                }
                else {
                    $apiResponses = FormHelper::sendBulk($apiKey, FormHelper::buildBulkParams(), $body['msgType']);
                    for ($i = 0; $i < count($apiResponses); $i++) {
                        $this->alertService->addSuccess($apiResponses[$i], 'apiResponse' . $i);
                    }
                }
            }
        }
        else {
            $smarty->assign('defaultTabbertab', $this->menuID);
        }

        $smarty->assign('customerGroups', CustomerGroup::getGroups());
        $smarty->assign('filterLanguages', LanguageHelper::getAllLanguages());
        $smarty->assign('sessionToken', Shop::getAdminSessionToken());

        $res = (new Response())->withStatus(200);
        $res->getBody()->write($smarty->fetch($adminPath . '/templates/bulk.tpl'));
        return $res;
    }
}
