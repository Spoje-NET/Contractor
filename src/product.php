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

use AbraFlexi\RO;
use Ease\Html\ATag;
use Ease\WebPage;
use Mpdf\Mpdf;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Html as HTMLParser;

require './init.php';

$oPage = WebPage::singleton();
$kod = WebPage::getRequestValue('kod');
$format = WebPage::getRequestValue('format');

if (empty($kod)) {
    $oPage->addStatusMessage(_('Bad call'), 'warning');
    $oPage->addItem(new ATag('install.php', _('Please setup your AbraFlexi connection')));
} else {
    try {
        $contractor = new Contract(RO::code($kod));
        $oPage->setPageTitle($contractor->getRecordIdent());

        if ($oPage->isPosted()) {
            //          $invoicer->convertSelected($_REQUEST);
        }

        $oPage->body->addItem(new Ui\ProductForm($contractor, true));

        switch ($format) {
            case 'pdf':
                header('Content-Type: application/pdf');
                $filename = _('ProductSpecification').'-'.\AbraFlexi\Functions::uncode($contractor->getRecordCode()).'.pdf';
                header('Content-Disposition: attachment; filename='.$filename);

                $html = $oPage->getRendered();
                $mpdf = new Mpdf(['tempDir' => sys_get_temp_dir()]);
                $mpdf->WriteHTML($html);
                $mpdf->Output($filename, 'I');

                break;
            case 'docx':
                header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessing‌ml.document'); // you should look for the real header that you need if it's not Word 2007!!!
                $filename = _('ProductSpecification').'-'.\AbraFlexi\Functions::uncode($contractor->getRecordCode()).'.docx';
                header('Content-Disposition: inline; filename='.$filename);

                $phpWord = new PhpWord();
                $section = $phpWord->addSection();
                HTMLParser::addHtml($section, $oPage->getRendered(), true);

                $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
                $objWriter->save('php://output');

                break;

            default:
                echo $oPage;

                break;
        }
    } catch (\AbraFlexi\Exception $exc) {
        if ($exc->getCode() === 401) {
            $oPage->body->addItem(new \Ease\Html\H2Tag(_('Session Expired')));
        } else {
            $oPage->addItem(new \Ease\Html\H1Tag($exc->getMessage()));
            $oPage->addItem(new \Ease\Html\PreTag($exc->getTraceAsString()));
        }

        echo $oPage;
    }
}
