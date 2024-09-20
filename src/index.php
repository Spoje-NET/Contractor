<?php

declare(strict_types=1);

/**
 * This file is part of the AbraflexiContractor package
 *
 * https://github.com/VitexSoftware/AbraFlexi-Contractor
 *
 * (c) Vítězslav Dvořák <http://vitexsoftware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AbraFlexi\Contractor;

use AbraFlexi\Contractor\Ui\PageBottom;
use AbraFlexi\Contractor\Ui\PageTop;
use AbraFlexi\Contractor\Ui\WebPage;
use AbraFlexi\Exception;
use AbraFlexi\RO;
use Ease\Html\ATag;
use Ease\Html\H1Tag;
use Ease\Html\H2Tag;
use Ease\Html\PreTag;

require './init.php';

$kod = WebPage::getRequestValue('kod');

$oPage->addItem(new PageTop(_('Multi Flexi')));

if (empty($kod)) {
    $oPage->addStatusMessage(_('Bad call'), 'warning');
    $oPage->addItem(new ATag('install.php', _('Please setup your AbraFlexi connection')));
} else {
    try {
        $contractor = new Contract(RO::code($kod));
        $oPage->setPageTitle($contractor->getRecordIdent());

        switch ($contractor->getDataValue('typSml')) {
            case 'code:INTERNET':
                $formTabs = new \Ease\TWB5\Tabs();
                $formTabs->addTab(_('Contract'), new Ui\ContractForm($contractor));
                $formTabs->addTab(_('Product'), new Ui\ProductForm($contractor));
                $formTabs->addTab(_('Transfer protocol'), new Ui\TransferForm($contractor));
                $formTabs->addTab(_('Summary'), new Ui\SummaryForm($contractor));

                $oPage->container->addItem($formTabs);

                break;

            default:
                $oPage->container->addItem(new \Ease\TWB5\Badge(sprintf(_('Unsupported contract type: %s'), \AbraFlexi\Functions::uncode($contractor->getDataValue('typSml'))), 'warning'));

                break;
        }

        if ($oPage->isPosted()) {
            //          $invoicer->convertSelected($_REQUEST);
        }
    } catch (Exception $exc) {
        if ($exc->getCode() === 401) {
            $oPage->body->addItem(new H2Tag(_('Session Expired')));
        } else {
            $oPage->addItem(new H1Tag($exc->getMessage()));
            $oPage->addItem(new PreTag($exc->getTraceAsString()));
        }
    }
}

$oPage->addItem(new PageBottom());
echo $oPage;
