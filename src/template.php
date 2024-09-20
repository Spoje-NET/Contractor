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
use Ease\Html\ATag;
use Ease\Html\H1Tag;
use Ease\Html\H2Tag;
use Ease\Html\PreTag;

require './init.php';

$kod = WebPage::getRequestValue('kod');

$oPage->addItem(new PageTop(_('Template')));

if (empty($kod)) {
    $oPage->addStatusMessage(_('Bad call'), 'warning');
    $oPage->addItem(new ATag('install.php', _('Please setup your AbraFlexi connection')));
} else {
    try {
        $templateTabs = new \Ease\TWB5\Tabs();

        $templates = glob('../templates/*.j2');

        foreach ($templates as $templateFile) {
            $templateTabs->addTab(basename($templateFile), new Ui\TemplateEditor($templateFile));
        }

        $oPage->container->addItem($templateTabs);

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
