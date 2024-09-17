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
 * Description of MainMenu.
 *
 * @author vitex
 */
class MainMenu extends \Ease\Html\NavTag
{
    public function __construct()
    {
        $kod = WebPage::getRequestValue('kod');
        $logoLink = new \Ease\Html\ATag('index.php?kod='.$kod, new \Ease\Html\ImgTag('images/spoje-net_logo.gif', 'SpojeNet', ['height' => '24', 'class' => 'd-inline-block align-text-top']), ['class' => 'navbar-brand']);
        $logoLink->addItem('&nbsp;Contractor');
        $container = new \Ease\TWB5\Container($logoLink);

        //        $container->addItem(new \Ease\TWB5\LinkButton('contract.php?kod='.$kod, _('Contract'), 'primary'));
        //        $container->addItem(new \Ease\TWB5\LinkButton('product.php?kod='.$kod, _('Product'), 'secondary'));
        $container->addItem(new \Ease\TWB5\LinkButton('data.php?kod='.$kod, '⚙️&nbsp;'._('Data'), 'warning'));

        parent::__construct($container, ['class' => 'navbar navbar-expand-lg navbar-light bg-light']);
    }

    public function navBarToggler()
    {
        return new \Ease\Html\ButtonTag(new \Ease\Html\SpanTag(null, ['class' => 'navbar-toggler-icon']), [
            'class' => 'navbar-toggler',
            'type' => 'button',
            'data-bs-toggle' => 'collapse',
            'data-bs-target' => '#navbarNav',
            'aria-controls' => 'navbarNav',
            'aria-expanded' => 'false',
            'aria-label' => _('Toggle navigation'),
        ]);
    }

    public function navBarCollapse()
    {
        $navbarNav = new \Ease\Html\UlTag(null, ['class' => 'navbar-nav ms-auto flex-nowrap navbar-expand mb-2 mb-lg-0', 'style' => '--bs-scroll-height: 100px;']);

        $navbarNav->addItemSmart(new \Ease\Html\ATag('myapps.php', new \Ease\Html\ImgTag('images/apps.svg', 'apps', ['height' => 20]).' '._('My Apps'), ['class' => 'nav-link']), ['class' => 'nav-item']);
        $navbarNav->addItemSmart(new \Ease\Html\ATag('app.php', '➕ '._('Submit'), ['class' => 'nav-link']), ['class' => 'nav-item']);

        return new \Ease\Html\DivTag($navbarNav, ['class' => 'collapse navbar-collapse', 'id' => 'navbarNav']);
    }
}
