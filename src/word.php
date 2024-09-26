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
            $tempDir = sys_get_temp_dir() . '/' . uniqid('template_', true);
            if (mkdir($tempDir)) {

                $zip = new \ZipArchive();
                if ($zip->open($templateFile) === TRUE) {
                    if ($zip->extractTo($tempDir)) {
                        $documentXmlPath = $tempDir . '/word/document.xml';
                        if (file_exists($documentXmlPath)) {

                            $templateString = file_get_contents($documentXmlPath);

                            $template = new Template($templateString);
//                            $template->withUndefined(new UndefinedField($contract));  // Default is StrictUndefined

                            $contractData = ['contract' => $contract->getData()];

                            $rendered = $template->render($contractData);
                            
                            file_put_contents($documentXmlPath, $rendered);

                            $zip2 = new \ZipArchive();
                            if ($zip2->open($templateFile, \ZipArchive::CREATE) === TRUE) {
                                $files = new RecursiveIteratorIterator(
                                    new RecursiveDirectoryIterator($tempDir),
                                    RecursiveIteratorIterator::LEAVES_ONLY
                                );

                                foreach ($files as $name => $file) {
                                    if (!$file->isDir()) {
                                        $filePath = $file->getRealPath();
                                        $relativePath = substr($filePath, strlen($tempDir) + 1);
                                        $zip2->addFile($filePath, $relativePath);
                                    }
                                }
                                $zip2->close();
                            } else {
                                throw new Exception('Failed to open template file for writing: ' . $templateFile);
                            }
                            
                        } else {
                            throw new Exception('Failed to find document.xml in template: ' . $templateFile);
                        }
                    }
                    $zip->close();
                } else {
                    throw new Exception('Failed to open template file: ' . $templateFile);
                }
            }
            $templateTabs->addTab(basename($templateFile), $documentXml);
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
