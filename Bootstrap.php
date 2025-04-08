<?php declare(strict_types=1);

namespace Plugin\seven_jtl5;

use JTL\Events\Dispatcher;
use JTL\Plugin\Bootstrapper;
use JTL\Shop;
use Laminas\Diactoros\ServerRequestFactory;
use Plugin\seven_jtl5\lib\Hook\PostCancelOrder;
use Plugin\seven_jtl5\lib\Hook\PostChangeOrder;
use Plugin\seven_jtl5\lib\Hook\PostSaveOrder;
use Plugin\seven_jtl5\lib\MessageType;
use JTL\Smarty\JTLSmarty;
use function Functional\first;

class Bootstrap extends Bootstrapper
{
    public function boot(Dispatcher $dispatcher): void
    {
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

    public function renderAdminMenuTab(string $tabName, int $menuID, JTLSmarty $smarty): string
    {
        $plugin = $this->getPlugin();
        $backendURL = \method_exists($plugin->getPaths(), 'getBackendURL')
            ? $plugin->getPaths()->getBackendURL()
            : Shop::getAdminURL() . '/plugin.php?kPlugin=' . $plugin->getID();
        $smarty->assign('menuID', $menuID);
        $smarty->assign('backendURL', $backendURL);

        $tpl = '';
        switch ($tabName) {
            case 'Massenversand';
                $controller = new BackendBulkController(
                    $this->getDB(),
                    $this->getCache(),
                    Shop::Container()->getAlertService(),
                    Shop::Container()->getAdminAccount(),
                    Shop::Container()->getGetText()
                );
                $controller->menuID = $menuID;
                $controller->plugin = $this->getPlugin();
                $request = ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
                $response = $controller->getResponse($request, [], $smarty);
                if (\count($response->getHeader('location')) > 0) {
                    \header('Location:' . first($response->getHeader('location')));
                    exit();
                }

               return (string)$response->getBody();
        }

        return $smarty->assign('backendURL', $backendURL)
            ->fetch($this->getPlugin()->getPaths()->getAdminPath() . '/templates/' . $tpl);
    }

    private function shouldListen(string $name): bool
    {
        return MessageType::NONE
            !== (int)$this->getPlugin()->getConfig()->getValue($name);
    }
}
