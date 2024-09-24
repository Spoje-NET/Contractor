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
 * Description of Customer.
 *
 * @author Vitex <info@vitexsoftware.cz>
 */
class Customer extends \AbraFlexi\Adresar
{
    public function __construct($init, $options = [])
    {
        $this->defaultUrlParams = [
            'relations' => 'kontakty',
            'detail' => 'full',
        ];
        $this->nativeTypes = false;
        parent::__construct($init, $options);
    }
}
