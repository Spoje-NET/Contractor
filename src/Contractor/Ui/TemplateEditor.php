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

namespace AbraFlexi\Contractor\Ui;

/**
 * Description of TemplateEditor.
 *
 * @author Vitex <info@vitexsoftware.cz>
 */
class TemplateEditor extends \Ease\Html\DivTag
{
    #[\Override]
    public function __construct(string $file, $properties = [])
    {
        $properties['style'] = 'width: 100%; max-width: 1500px;';
        parent::__construct(new \Ease\Html\TextareaTag(basename($file), file_get_contents($file), ['rows' => 50, 'style' => 'width: 100%; height: 100%']), $properties);
    }
}
