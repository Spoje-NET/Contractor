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

/**
 * Description of Contract.
 *
 * @author Vitex <info@vitexsoftware.cz>
 */
class Contract extends \AbraFlexi\Smlouva
{
    public function __construct($init, $options = [])
    {
        $this->defaultUrlParams = [
            'relations' => 'polozkySmlouvy,prilohy,udalosti,ucely,firma',
            'includes' => '/smlouva/firma/',
            'detail' => 'full',
        ];
        $this->nativeTypes = false;
        parent::__construct($init, $options);
    }

    public function loadFromAbraFlexi($id = null): void
    {
        parent::loadFromAbraFlexi($id);
        $firmaData = current($this->getDataValue('firma'));
        $firmaData['kontakt'] = current($firmaData['kontakty']);
        unset($firmaData['kontakty']);

        if ($firmaData['kontakt']['datNaroz']) {
            $firmaData['kontakt']['datNaroz'] = date('j.n.Y', strtotime($firmaData['kontakt']['datNaroz']));
        }

        $firmaData['mistoUrceni'] = current($firmaData['mistaUrceni']);
        unset($firmaData['mistaUrceni']);
        $this->setDataValue('firma', $firmaData);

        $this->setDataValue('datumPodepsani', date('j.n.Y', strtotime($this->getDataValue('datumPodepsani'))));

        if ($this->getDataValue('datumUcinnosti')) {
            $this->setDataValue('datumUcinnosti', date('j.n.Y', strtotime($this->getDataValue('datumUcinnosti'))));
        }

        if ($this->getDataValue('smlouvaOd')) {
            $this->setDataValue('smlouvaOd', date('j.n.Y', strtotime($this->getDataValue('smlouvaOd'))));
        }

        if ($this->getDataValue('smlouvaDo')) {
            $this->setDataValue('smlouvaDo', date('j.n.Y', strtotime($this->getDataValue('smlouvaDo'))));
        }

        $frequence = $this->getDataValue('frekFakt');
        $this->setDataValue('frekFakt', $frequence === 1 ? '1 měsíc' : (string) $frequence.' měsíců');
    }
}
