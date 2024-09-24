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

/**
 * Description of UndefinedField.
 *
 * @author Vitex <info@vitexsoftware.cz>
 */
class UndefinedField implements \ByJG\JinjaPhp\Undefined\UndefinedInterface
{
    public static $problems = [];
    protected $message = 'NOT_FOUND';
    private Contract $contract;

    public function __construct(Contract $contract)
    {
        $this->contract = $contract;
        $this->message = _('Not Found');
    }

    #[\Override]
    public function render($varName)
    {
        //        return "{{ {$this->message}: {$varName} }}";
        return '🎯'.new \Ease\TWB5\Badge("{$this->message}: {$varName}", 'danger', ['style' => 'color: yellow']).'';
    }
}
