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

namespace AbraFlexi\Contractor\Ui;

use AbraFlexi\Contractor\Contract;
use AbraFlexi\Contractor\UndefinedTransfer;
use ByJG\JinjaPhp\Template;
use Ease\TWB5\Form;

class TransferForm extends Form
{
    private Contract $contract;

    public function __construct(Contract $contract, bool $printMode = false)
    {
        parent::__construct();
        $this->contract = $contract;

        $templateString = file_get_contents('../templates/transfer.html.j2');

        $template = new Template($templateString);
        $template->withUndefined(new UndefinedTransfer($contract));  // Default is StrictUndefined

        $contractData = ['contract' => $contract->getData()];

        $this->addItem($template->render($contractData));

        if ($printMode === false) {
            $this->addItem(new \Ease\Html\DivTag(new \Ease\TWB5\LinkButton('transfer.php?kod='.$contract->getRecordIdent(), '🖨️&nbsp;'._('Print Transfer Protocol'), 'success btn-lg', ['target' => '_blank']), ['class' => 'd-grid gap-2']));
        }
    }
}
