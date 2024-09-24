<?php

declare(strict_types=1);

/**
 * This file is part of the AbraflexiContractor package
 *
 * https://github.com/VitexSoftware/Spoje-contractor
 *
 * (c) Vítězslav Dvořák <http://vitexsoftware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AbraFlexi\Contractor;

use Ease\Html\ATag;
use Ease\WebPage;

require './init.php';

$oPage = WebPage::singleton();
$kod = WebPage::getRequestValue('kod');
$oPage->addItem(new Ui\PageTop(_('Multi Flexi')));

if (empty($kod)) {
    $oPage->addStatusMessage(_('Bad call'), 'warning');
    $oPage->addItem(new ATag('install.php', _('Please setup your AbraFlexi connection')));
} else {
    try {
        $contractor = new Contract(\AbraFlexi\Functions::code($kod));
        $oPage->setPageTitle($contractor->getRecordIdent());
        $oPage->body->addItem(new \Ease\Html\PreTag(print_r($contractor->getData(), true)));
    } catch (\AbraFlexi\Exception $exc) {
        if ($exc->getCode() === 401) {
            $oPage->body->addItem(new \Ease\Html\H2Tag(_('Session Expired')));
        } else {
            $oPage->addItem(new \Ease\Html\H1Tag($exc->getMessage()));
            $oPage->addItem(new \Ease\Html\PreTag($exc->getTraceAsString()));
        }
    }
}

$oPage->addItem(new Ui\PageBottom());
echo $oPage;
