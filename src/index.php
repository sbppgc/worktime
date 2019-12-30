<?php
namespace Sbppgc\Worktime;

/**
 * Get employee working time service.
 *
 * @author Sergey Prisyazhnyuk <sbpmail@ya.ru>
 * @package sbppgc/worktime
 */

try {
    $oFactory = new Factory('config/config.php');

    $oRouter = $oFactory->getRouter();

    $oEndpointController = $oRouter->getEndpointController($_SERVER['REQUEST_URI']);

    $aDependences = $oFactory->getDependences($oEndpointController->getDependencesList());

    $aRes = $oEndpointController->process($aDependences);

    echo json_encode($aRes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (\Exception $e) {
    echo json_encode(array('error' => $e->getMessage()));
}
