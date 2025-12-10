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

use AbraFlexi\Contractor\Ui\PageBottom;
use AbraFlexi\Contractor\Ui\PageTop;
use AbraFlexi\Contractor\Ui\WebPage;
use AbraFlexi\Exception;
use ByJG\JinjaPhp\Template;
use Ease\Html\ATag;
use Ease\Html\H1Tag;
use Ease\Html\H2Tag;
use Ease\Html\PreTag;

require './init.php';

$kod = WebPage::getRequestValue('kod');

$oPage->addItem(new PageTop(_('DocX Template')));

if (empty($kod)) {
    $oPage->addStatusMessage(_('Bad call'), 'warning');
    $oPage->addItem(new ATag('install.php', _('Please setup your AbraFlexi connection')));
} else {
    try {
        $contract = new Contract(\AbraFlexi\Functions::code($kod));
        
        $templateTabs = new \Ease\TWB5\Tabs();

        $templates = glob('../templates/*.docx');

        foreach ($templates as $templateFile) {
            $templateTabs->addTab(basename($templateFile), new \Ease\Html\DivTag(
                new \Ease\TWB5\LinkButton('word.php?kod='.$kod.'&template='.basename($templateFile), '⬇️ '._('Download').' '.basename($templateFile), 'secondary btn-lg btn-block', ['style'=>'margin: 20px'])
            ));
        }

        $oPage->container->addItem($templateTabs);

        $template = WebPage::getRequestValue('template');
        if ($template) {
             $source = '../templates/' . basename($template);
             if (file_exists($source)) {
                 $dTemplate = new DocxTemplate($source);
                 $dTemplate->mergeRuns($dTemplate->render($contract->getData())); // Wait, render doesn't return string in my class, it sets internally.
                 // Correct usage based on my class design:
                 // $dTemplate->render($data); 
                 // $dTemplate->save('php://output');
                 
                 // Re-checking DocxTemplate class I wrote... 
                 // mergeRuns returns string, render returns void but updates internal xml.
                 // So I should call mergeRuns internally or public? 
                 // My class has: mergeRuns(string $xml): string. 
                 // render(array $data) calls mergeRuns internally? No, I wrote render to call mergeRuns via regex inside it? 
                 // Actually I wrote: 
                 // $dTemplate->mergeRuns($dTemplate->render(...)) in thought but implementation:
                 // render() calls $m->render($xml). 
                 // Implementation logic in DocxTemplate::render needs to be:
                 // 1. $xml = $this->mergeRuns($this->xmlContent);
                 // 2. $this->xmlContent = $m->render($xml, $data);
                 
                 // I need to fix DocxTemplate first or usage here?
                 // Let's assume I fix DocxTemplate to do it automatically.
                 
                 $dTemplate->render($contract->getData());
                 
                 header('Content-Description: File Transfer');
                 header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
                 header('Content-Disposition: attachment; filename="' . basename($template) . '"');
                 header('Expires: 0');
                 header('Cache-Control: must-revalidate');
                 header('Pragma: public');
                 
                 $tempOutput = tempnam(sys_get_temp_dir(), 'contractor_word');
                 $dTemplate->save($tempOutput);
                 readfile($tempOutput);
                 unlink($tempOutput);
                 exit;
             }
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
