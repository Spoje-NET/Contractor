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

/**
 * Description of DataTree.
 *
 * @author Vitex <info@vitexsoftware.cz>
 */
class DataTree extends \Ease\Html\DivTag
{
    #[\Override]
    public function __construct($content = null, $properties = [])
    {
        parent::__construct($content, $properties);
    }
}
