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
 * Description of Contract.
 *
 * @author Vitex <info@vitexsoftware.cz>
 */
class Contract extends \AbraFlexi\Smlouva
{
    public function __construct($init, array $options = [])
    {
        $this->defaultUrlParams = [
            'relations' => 'polozkySmlouvy,prilohy,udalosti,ucely,firma',
            'includes' => '/smlouva/firma/,/smlouva/polozkySmlouvy/smlouva-polozka/cenik/',
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

        if ($firmaData['kontakt'] && \array_key_exists('datNaroz', $firmaData['kontakt']) && $firmaData['kontakt']['datNaroz']) {
            $firmaData['kontakt']['datNaroz'] = date('j.n.Y', strtotime($firmaData['kontakt']['datNaroz']));
        }

        $firmaData['mistoUrceni'] = current($firmaData['mistaUrceni']);
        unset($firmaData['mistaUrceni']);
        $this->setDataValue('firma', $firmaData);

        $this->ensureDate('datumPodepsani');
        $this->ensureDate('datumUcinnosti');
        $this->ensureDate('smlouvaOd');
        $this->ensureDate('smlouvaDo');

        $frequence = $this->getDataValue('frekFakt');
        $this->setDataValue('frekFakt', $frequence === 1 ? '1 měsíc' : (string) $frequence.' měsíců');

        $productor = new \AbraFlexi\Cenik(null, ['ignore404' => true, 'detail' => 'full', 'nativeTypes' => false]);
        $productor->defaultUrlParams['includes'] = '/atribut/typAtributu';
        $productor->nativeTypes = false;
        $celkem['sdph'] = 0;
        $celkem['bezdph'] = 0;

        foreach ($this->getDataValue('polozkySmlouvy') as $position => $contractItem) {
            $celkem['sdph'] += $contractItem['cenik'][0]['cenaZakl'] * (1 + (float) $contractItem['cenik'][0]['szbDph'] / 100);
            $celkem['bezdph'] += $contractItem['cenik'][0]['cenaZakl'];
            $this->data['polozkySmlouvy'][$position]['cenik'][0]['atributy'] = $productor->performRequest($contractItem['cenik'][0]['id'].'/atributy')['atribut'];
        }

        $this->ensureNonempty('cisSmlProti');
        $celkem['sdph'] = round($celkem['sdph']);
        $this->setDataValue('celkem', $celkem);
    }

    public function ensureDate(string $fieldName): void
    {
        if ($this->getDataValue($fieldName)) {
            $this->setDataValue($fieldName, date('j.n.Y', strtotime($this->getDataValue($fieldName))));
        } else {
            $this->setDataValue($fieldName, null);
        }
    }

    public function ensureNonempty(string $fieldName): void
    {
        if (empty($this->getDataValue($fieldName))) {
            $this->setDataValue($fieldName, null);
        }
    }
}
