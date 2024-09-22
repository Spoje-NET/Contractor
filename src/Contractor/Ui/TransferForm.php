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
            $buttons = new \Ease\TWB5\Navbar($contract->getRecordIdent());
            $buttons->addTagClass('navbar-expand-lg navbar-light bg-dark');
            $buttons->addMenuItem(new \Ease\Html\DivTag(new \Ease\TWB5\LinkButton('transfer.php?format=html&kod='.$contract->getRecordIdent(), '🖨️&nbsp;'._('Print Transfer Protocol'), 'success', ['target' => '_blank']), ['class' => '']));
            $buttons->addMenuItem(new \Ease\Html\DivTag(new \Ease\TWB5\LinkButton('transfer.php?format=pdf&kod='.$contract->getRecordIdent(), '<img height="20" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+CjwhLS0gQ3JlYXRlZCB3aXRoIElua3NjYXBlIChodHRwOi8vd3d3Lmlua3NjYXBlLm9yZy8pIC0tPgo8c3ZnCiAgICB4bWxuczppbmtzY2FwZT0iaHR0cDovL3d3dy5pbmtzY2FwZS5vcmcvbmFtZXNwYWNlcy9pbmtzY2FwZSIKICAgIHhtbG5zOnJkZj0iaHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyIKICAgIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIKICAgIHhtbG5zOnNvZGlwb2RpPSJodHRwOi8vc29kaXBvZGkuc291cmNlZm9yZ2UubmV0L0RURC9zb2RpcG9kaS0wLmR0ZCIKICAgIHhtbG5zOmNjPSJodHRwOi8vY3JlYXRpdmVjb21tb25zLm9yZy9ucyMiCiAgICB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIKICAgIHhtbG5zOmRjPSJodHRwOi8vcHVybC5vcmcvZGMvZWxlbWVudHMvMS4xLyIKICAgIHhtbG5zOnN2Zz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciCiAgICB4bWxuczpuczE9Imh0dHA6Ly9zb3ppLmJhaWVyb3VnZS5mciIKICAgIGlkPSJzdmczNDkxIgogICAgc29kaXBvZGk6ZG9jbmFtZT0iTmV3IGRvY3VtZW50IDIiCiAgICB2aWV3Qm94PSIwIDAgNjYuMzIzIDY4LjAzIgogICAgdmVyc2lvbj0iMS4xIgogICAgaW5rc2NhcGU6dmVyc2lvbj0iMC40OC4xIHI5NzYwIgogID4KICA8ZGVmcwogICAgICBpZD0iZGVmczM0OTMiCiAgICA+CiAgICA8ZmlsdGVyCiAgICAgICAgaWQ9ImZpbHRlcjQ0MDIiCiAgICAgICAgY29sb3ItaW50ZXJwb2xhdGlvbi1maWx0ZXJzPSJzUkdCIgogICAgICAgIGlua3NjYXBlOmNvbGxlY3Q9ImFsd2F5cyIKICAgICAgPgogICAgICA8ZmVHYXVzc2lhbkJsdXIKICAgICAgICAgIGlkPSJmZUdhdXNzaWFuQmx1cjQ0MDQiCiAgICAgICAgICBzdGREZXZpYXRpb249IjEuMjYyNSIKICAgICAgICAgIGlua3NjYXBlOmNvbGxlY3Q9ImFsd2F5cyIKICAgICAgLz4KICAgIDwvZmlsdGVyCiAgICA+CiAgICA8bGluZWFyR3JhZGllbnQKICAgICAgICBpZD0ibGluZWFyR3JhZGllbnQ0NTkyIgogICAgICAgIHkyPSIxNTAuMTciCiAgICAgICAgeGxpbms6aHJlZj0iI2xpbmVhckdyYWRpZW50MzkyOC04IgogICAgICAgIGdyYWRpZW50VW5pdHM9InVzZXJTcGFjZU9uVXNlIgogICAgICAgIHgyPSIyOTkuOSIKICAgICAgICBncmFkaWVudFRyYW5zZm9ybT0ibWF0cml4KC41MzA3OCAwIDAgLjUyNjQ0IDI3LjU4MSAtMzM5LjU1KSIKICAgICAgICB5MT0iMjM4LjEzIgogICAgICAgIHgxPSIyOTguNDgiCiAgICAgICAgaW5rc2NhcGU6Y29sbGVjdD0iYWx3YXlzIgogICAgLz4KICAgIDxsaW5lYXJHcmFkaWVudAogICAgICAgIGlkPSJsaW5lYXJHcmFkaWVudDM5MjgtOCIKICAgICAgPgogICAgICA8c3RvcAogICAgICAgICAgaWQ9InN0b3AzOTMwLTIiCiAgICAgICAgICBzdHlsZT0ic3RvcC1jb2xvcjojN2M3YzdjIgogICAgICAgICAgb2Zmc2V0PSIwIgogICAgICAvPgogICAgICA8c3RvcAogICAgICAgICAgaWQ9InN0b3AzOTMyLTAiCiAgICAgICAgICBzdHlsZT0ic3RvcC1jb2xvcjojZTZlM2UzIgogICAgICAgICAgb2Zmc2V0PSIxIgogICAgICAvPgogICAgPC9saW5lYXJHcmFkaWVudAogICAgPgogICAgPGZpbHRlcgogICAgICAgIGlkPSJmaWx0ZXI0Mzk2IgogICAgICAgIHdpZHRoPSIxLjA4MzIiCiAgICAgICAgeT0iLS4xMDc1MCIKICAgICAgICB4PSItLjA0MTYxMyIKICAgICAgICBoZWlnaHQ9IjEuMjE1IgogICAgICAgIGNvbG9yLWludGVycG9sYXRpb24tZmlsdGVycz0ic1JHQiIKICAgICAgICBpbmtzY2FwZTpjb2xsZWN0PSJhbHdheXMiCiAgICAgID4KICAgICAgPGZlR2F1c3NpYW5CbHVyCiAgICAgICAgICBpZD0iZmVHYXVzc2lhbkJsdXI0Mzk4IgogICAgICAgICAgc3RkRGV2aWF0aW9uPSIwLjUzNzQ5OTk5IgogICAgICAgICAgaW5rc2NhcGU6Y29sbGVjdD0iYWx3YXlzIgogICAgICAvPgogICAgPC9maWx0ZXIKICAgID4KICAgIDxsaW5lYXJHcmFkaWVudAogICAgICAgIGlkPSJsaW5lYXJHcmFkaWVudDQ1ODciCiAgICAgICAgeTI9IjE3Ny40MSIKICAgICAgICB4bGluazpocmVmPSIjbGluZWFyR3JhZGllbnQzOTI4LTgiCiAgICAgICAgZ3JhZGllbnRVbml0cz0idXNlclNwYWNlT25Vc2UiCiAgICAgICAgeDI9IjMxMC42OCIKICAgICAgICBncmFkaWVudFRyYW5zZm9ybT0ibWF0cml4KC41Mjk4MCAwIDAgLjUyOTgwIDI3LjkyIC0zNDAuMDMpIgogICAgICAgIHkxPSIxNjEuNTIiCiAgICAgICAgeDE9IjMyNi4wMSIKICAgICAgICBpbmtzY2FwZTpjb2xsZWN0PSJhbHdheXMiCiAgICAvPgogIDwvZGVmcwogID4KICA8c29kaXBvZGk6bmFtZWR2aWV3CiAgICAgIGlkPSJiYXNlIgogICAgICBmaXQtbWFyZ2luLWxlZnQ9IjAiCiAgICAgIGlua3NjYXBlOnNob3dwYWdlc2hhZG93PSJmYWxzZSIKICAgICAgaW5rc2NhcGU6em9vbT0iMTIuNzAwMjgiCiAgICAgIGJvcmRlcm9wYWNpdHk9IjEuMCIKICAgICAgaW5rc2NhcGU6Y3VycmVudC1sYXllcj0ibGF5ZXIxIgogICAgICBpbmtzY2FwZTpjeD0iMzMuMTYxNTEiCiAgICAgIGlua3NjYXBlOmN5PSIzNC4wMTQ5OTkiCiAgICAgIGlua3NjYXBlOndpbmRvdy1tYXhpbWl6ZWQ9IjEiCiAgICAgIHNob3dncmlkPSJmYWxzZSIKICAgICAgZml0LW1hcmdpbi1yaWdodD0iMCIKICAgICAgaW5rc2NhcGU6ZG9jdW1lbnQtdW5pdHM9InB4IgogICAgICBib3JkZXJjb2xvcj0iIzY2NjY2NiIKICAgICAgaW5rc2NhcGU6d2luZG93LXg9IjAiCiAgICAgIGlua3NjYXBlOndpbmRvdy15PSIyNCIKICAgICAgZml0LW1hcmdpbi1ib3R0b209IjAiCiAgICAgIGlua3NjYXBlOndpbmRvdy13aWR0aD0iMTkyMCIKICAgICAgaW5rc2NhcGU6cGFnZW9wYWNpdHk9IjAuMCIKICAgICAgaW5rc2NhcGU6cGFnZXNoYWRvdz0iMiIKICAgICAgcGFnZWNvbG9yPSIjZmZmZmZmIgogICAgICBpbmtzY2FwZTp3aW5kb3ctaGVpZ2h0PSIxMDMzIgogICAgICBzaG93Ym9yZGVyPSJmYWxzZSIKICAgICAgZml0LW1hcmdpbi10b3A9IjAiCiAgLz4KICA8ZwogICAgICBpZD0ibGF5ZXIxIgogICAgICBpbmtzY2FwZTpsYWJlbD0iTGF5ZXIgMSIKICAgICAgaW5rc2NhcGU6Z3JvdXBtb2RlPSJsYXllciIKICAgICAgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTQwMS4xMyAtMjkyLjYyKSIKICAgID4KICAgIDxnCiAgICAgICAgaWQ9Imc0NjEyIgogICAgICAgIHRyYW5zZm9ybT0idHJhbnNsYXRlKDI0Ny44NiA1NTUuNzcpIgogICAgICA+CiAgICAgIDxwYXRoCiAgICAgICAgICBpZD0icGF0aDQ1NjAiCiAgICAgICAgICBzb2RpcG9kaTpub2RldHlwZXM9ImNjY2NjY2NjIgogICAgICAgICAgc3R5bGU9Im9wYWNpdHk6LjY7c3Ryb2tlLWxpbmVqb2luOnJvdW5kO2NvbG9yOiMwMDAwMDA7c3Ryb2tlLWRhc2hvZmZzZXQ6MTE3LjU0O2ZpbHRlcjp1cmwoI2ZpbHRlcjQ0MDIpO3N0cm9rZTojMDAwMDAwO3N0cm9rZS1saW5lY2FwOnJvdW5kO3N0cm9rZS13aWR0aDoyO2ZpbGw6IzAwMDAwMCIKICAgICAgICAgIGlua3NjYXBlOmNvbm5lY3Rvci1jdXJ2YXR1cmU9IjAiCiAgICAgICAgICBkPSJtMTcwLjU2LTI1NS4xNHY4LjA3NzYgMTMuMjEgMzQuNzEyaDQ1di00MC4xMDhsLTE2LjAyMy0xNS44OTJ6IgogICAgICAvPgogICAgICA8cGF0aAogICAgICAgICAgaWQ9InBhdGg0NTYyIgogICAgICAgICAgZD0ibTE2My41Ni0yNjIuMTR2OC4wNzc2IDEzLjIxIDM0LjcxMmg0NXYtNDAuMTA4bC0xNi4wMjMtMTUuODkyeiIKICAgICAgICAgIHNvZGlwb2RpOm5vZGV0eXBlcz0iY2NjY2NjY2MiCiAgICAgICAgICBzdHlsZT0ic3Ryb2tlLWxpbmVqb2luOnJvdW5kO2NvbG9yOiMwMDAwMDA7c3Ryb2tlLWRhc2hvZmZzZXQ6MTE3LjU0O3N0cm9rZTojMDAwMDAwO3N0cm9rZS1saW5lY2FwOnJvdW5kO3N0cm9rZS13aWR0aDoyO2ZpbGw6dXJsKCNsaW5lYXJHcmFkaWVudDQ1OTIpIgogICAgICAgICAgaW5rc2NhcGU6Y29ubmVjdG9yLWN1cnZhdHVyZT0iMCIKICAgICAgLz4KICAgICAgPHJlY3QKICAgICAgICAgIGlkPSJyZWN0NDU2NCIKICAgICAgICAgIHN0eWxlPSJjb2xvcjojMDAwMDAwO2ZpbHRlcjp1cmwoI2ZpbHRlcjQzOTYpO2ZpbGw6IzAwMDAwMCIKICAgICAgICAgIHJ4PSIxIgogICAgICAgICAgcnk9IjEiCiAgICAgICAgICBoZWlnaHQ9IjEyIgogICAgICAgICAgd2lkdGg9IjMxIgogICAgICAgICAgeT0iLTI1MS4xNCIKICAgICAgICAgIHg9IjE1NC41NiIKICAgICAgLz4KICAgICAgPHBhdGgKICAgICAgICAgIGlkPSJwYXRoNDYwNyIKICAgICAgICAgIHN0eWxlPSJzdHJva2UtbGluZWpvaW46YmV2ZWw7c3Ryb2tlOiNmZjAwMDA7c3Ryb2tlLXdpZHRoOjM7ZmlsbDpub25lIgogICAgICAgICAgZD0ibTQ3OCAxNjEuMjVjLTMuNDc3Ny0xLjM3NTkgMS44NTQ3IDMzLjA0OSAxNSAzNC0xMi45OTEtMi4yOTA2LTQwLjczMS0xMi4yNjItNDQgNiAxMS4xOS00LjY0ODkgMjAuMDgzLTI2LjczNCAyOS00MHoiCiAgICAgICAgICBzb2RpcG9kaTpub2RldHlwZXM9ImNjY2MiCiAgICAgICAgICBpbmtzY2FwZTpjb25uZWN0b3ItY3VydmF0dXJlPSIwIgogICAgICAgICAgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTI4NC4zNyAtNDA4LjM5KSIKICAgICAgLz4KICAgICAgPHBhdGgKICAgICAgICAgIGlkPSJwYXRoNDU2OCIKICAgICAgICAgIGQ9Im0yMDguNTYtMjQ2LjE0LTE1Ljk4OS0xNS45ODl2MTUuOTg5aDE1Ljk4OXoiCiAgICAgICAgICBzdHlsZT0ic3Ryb2tlLWxpbmVqb2luOnJvdW5kO2NvbG9yOiMwMDAwMDA7c3Ryb2tlLWRhc2hvZmZzZXQ6MTE3LjU0O3N0cm9rZTojMDAwMDAwO3N0cm9rZS1saW5lY2FwOnJvdW5kO3N0cm9rZS13aWR0aDoyO2ZpbGw6dXJsKCNsaW5lYXJHcmFkaWVudDQ1ODcpIgogICAgICAgICAgaW5rc2NhcGU6Y29ubmVjdG9yLWN1cnZhdHVyZT0iMCIKICAgICAgLz4KICAgICAgPHBhdGgKICAgICAgICAgIGlkPSJwYXRoNDYxMCIKICAgICAgICAgIHNvZGlwb2RpOm5vZGV0eXBlcz0iY2NjY2NjY2MiCiAgICAgICAgICBzdHlsZT0ic3Ryb2tlLWxpbmVqb2luOnJvdW5kO2NvbG9yOiMwMDAwMDA7c3Ryb2tlLWRhc2hvZmZzZXQ6MTE3LjU0O3N0cm9rZTojMDAwMDAwO3N0cm9rZS1saW5lY2FwOnJvdW5kO3N0cm9rZS13aWR0aDoyO2ZpbGw6bm9uZSIKICAgICAgICAgIGlua3NjYXBlOmNvbm5lY3Rvci1jdXJ2YXR1cmU9IjAiCiAgICAgICAgICBkPSJtMTYzLjU2LTI2Mi4xNHY4LjA3NzYgMTMuMjEgMzQuNzEyaDQ1di00MC4xMDhsLTE2LjAyMy0xNS44OTJ6IgogICAgICAvPgogICAgICA8cmVjdAogICAgICAgICAgaWQ9InJlY3Q0NTY2IgogICAgICAgICAgc3R5bGU9ImNvbG9yOiMwMDAwMDA7ZmlsbDojZmYwMDAwIgogICAgICAgICAgcng9IjEiCiAgICAgICAgICByeT0iMSIKICAgICAgICAgIGhlaWdodD0iMTIiCiAgICAgICAgICB3aWR0aD0iMzEiCiAgICAgICAgICB5PSItMjUxLjE0IgogICAgICAgICAgeD0iMTU0LjU2IgogICAgICAvPgogICAgICA8dGV4dAogICAgICAgICAgaWQ9InRleHQ0NTcwIgogICAgICAgICAgc3R5bGU9ImZvbnQtc2l6ZToxMC4yMDhweDtmb250LWZhbWlseTpTYW5zO2ZpbGw6I2ZmZmZmZjtsZXR0ZXItc3BhY2luZzowcHg7dGV4dC1hbmNob3I6bWlkZGxlO2xpbmUtaGVpZ2h0OjEyNSU7d29yZC1zcGFjaW5nOjBweDtmb250LXdlaWdodDpib2xkO3RleHQtYWxpZ246Y2VudGVyIgogICAgICAgICAgaW5rc2NhcGU6ZXhwb3J0LXlkcGk9Ijc1LjA0MjY0MSIKICAgICAgICAgIHhtbDpzcGFjZT0icHJlc2VydmUiCiAgICAgICAgICBpbmtzY2FwZTpleHBvcnQtZmlsZW5hbWU9Ii9ob21lL3JhbXNha2IvZGVzay9jaGVycnloaWxsL3RleHQ2NzE1LnBuZyIKICAgICAgICAgIHRyYW5zZm9ybT0ic2NhbGUoLjg5NTk2IDEuMTE2MSkiCiAgICAgICAgICBpbmtzY2FwZTpleHBvcnQteGRwaT0iNzUuMDQyNjQxIgogICAgICAgICAgc29kaXBvZGk6bGluZXNwYWNpbmc9IjEyNSUiCiAgICAgICAgICB5PSItMjE1LjkxNjgxIgogICAgICAgICAgeD0iMTg5LjQ3NTM5IgogICAgICAgID4KICAgICAgICA8dHNwYW4KICAgICAgICAgICAgaWQ9InRzcGFuNDU3MiIKICAgICAgICAgICAgc29kaXBvZGk6cm9sZT0ibGluZSIKICAgICAgICAgICAgeD0iMTg5LjQ3NTM5IgogICAgICAgICAgICB5PSItMjE1LjkxNjgxIgogICAgICAgICAgPlBERjwvdHNwYW4KICAgICAgICA+CiAgICAgIDwvdGV4dAogICAgICA+CiAgICA8L2cKICAgID4KICA8L2cKICA+CiAgPG1ldGFkYXRhCiAgICA+CiAgICA8cmRmOlJERgogICAgICA+CiAgICAgIDxjYzpXb3JrCiAgICAgICAgPgogICAgICAgIDxkYzpmb3JtYXQKICAgICAgICAgID5pbWFnZS9zdmcreG1sPC9kYzpmb3JtYXQKICAgICAgICA+CiAgICAgICAgPGRjOnR5cGUKICAgICAgICAgICAgcmRmOnJlc291cmNlPSJodHRwOi8vcHVybC5vcmcvZGMvZGNtaXR5cGUvU3RpbGxJbWFnZSIKICAgICAgICAvPgogICAgICAgIDxjYzpsaWNlbnNlCiAgICAgICAgICAgIHJkZjpyZXNvdXJjZT0iaHR0cDovL2NyZWF0aXZlY29tbW9ucy5vcmcvbGljZW5zZXMvcHVibGljZG9tYWluLyIKICAgICAgICAvPgogICAgICAgIDxkYzpwdWJsaXNoZXIKICAgICAgICAgID4KICAgICAgICAgIDxjYzpBZ2VudAogICAgICAgICAgICAgIHJkZjphYm91dD0iaHR0cDovL29wZW5jbGlwYXJ0Lm9yZy8iCiAgICAgICAgICAgID4KICAgICAgICAgICAgPGRjOnRpdGxlCiAgICAgICAgICAgICAgPk9wZW5jbGlwYXJ0PC9kYzp0aXRsZQogICAgICAgICAgICA+CiAgICAgICAgICA8L2NjOkFnZW50CiAgICAgICAgICA+CiAgICAgICAgPC9kYzpwdWJsaXNoZXIKICAgICAgICA+CiAgICAgICAgPGRjOnRpdGxlCiAgICAgICAgICA+ZmlsZS1pY29uLXBkZjwvZGM6dGl0bGUKICAgICAgICA+CiAgICAgICAgPGRjOmRhdGUKICAgICAgICAgID4yMDEyLTA0LTI5VDIyOjU2OjM1PC9kYzpkYXRlCiAgICAgICAgPgogICAgICAgIDxkYzpkZXNjcmlwdGlvbgogICAgICAgICAgPmluc3BpcmVkIGJ5IHN1cmZpbmcgb24gdGhlIHdlYjwvZGM6ZGVzY3JpcHRpb24KICAgICAgICA+CiAgICAgICAgPGRjOnNvdXJjZQogICAgICAgICAgPmh0dHBzOi8vb3BlbmNsaXBhcnQub3JnL2RldGFpbC8xNjk3NTAvZmlsZS1pY29uLXBkZi1ieS1qYWJvbjwvZGM6c291cmNlCiAgICAgICAgPgogICAgICAgIDxkYzpjcmVhdG9yCiAgICAgICAgICA+CiAgICAgICAgICA8Y2M6QWdlbnQKICAgICAgICAgICAgPgogICAgICAgICAgICA8ZGM6dGl0bGUKICAgICAgICAgICAgICA+amFib248L2RjOnRpdGxlCiAgICAgICAgICAgID4KICAgICAgICAgIDwvY2M6QWdlbnQKICAgICAgICAgID4KICAgICAgICA8L2RjOmNyZWF0b3IKICAgICAgICA+CiAgICAgICAgPGRjOnN1YmplY3QKICAgICAgICAgID4KICAgICAgICAgIDxyZGY6QmFnCiAgICAgICAgICAgID4KICAgICAgICAgICAgPHJkZjpsaQogICAgICAgICAgICAgID5maWxlPC9yZGY6bGkKICAgICAgICAgICAgPgogICAgICAgICAgICA8cmRmOmxpCiAgICAgICAgICAgICAgPmljb248L3JkZjpsaQogICAgICAgICAgICA+CiAgICAgICAgICAgIDxyZGY6bGkKICAgICAgICAgICAgICA+cGRmPC9yZGY6bGkKICAgICAgICAgICAgPgogICAgICAgICAgPC9yZGY6QmFnCiAgICAgICAgICA+CiAgICAgICAgPC9kYzpzdWJqZWN0CiAgICAgICAgPgogICAgICA8L2NjOldvcmsKICAgICAgPgogICAgICA8Y2M6TGljZW5zZQogICAgICAgICAgcmRmOmFib3V0PSJodHRwOi8vY3JlYXRpdmVjb21tb25zLm9yZy9saWNlbnNlcy9wdWJsaWNkb21haW4vIgogICAgICAgID4KICAgICAgICA8Y2M6cGVybWl0cwogICAgICAgICAgICByZGY6cmVzb3VyY2U9Imh0dHA6Ly9jcmVhdGl2ZWNvbW1vbnMub3JnL25zI1JlcHJvZHVjdGlvbiIKICAgICAgICAvPgogICAgICAgIDxjYzpwZXJtaXRzCiAgICAgICAgICAgIHJkZjpyZXNvdXJjZT0iaHR0cDovL2NyZWF0aXZlY29tbW9ucy5vcmcvbnMjRGlzdHJpYnV0aW9uIgogICAgICAgIC8+CiAgICAgICAgPGNjOnBlcm1pdHMKICAgICAgICAgICAgcmRmOnJlc291cmNlPSJodHRwOi8vY3JlYXRpdmVjb21tb25zLm9yZy9ucyNEZXJpdmF0aXZlV29ya3MiCiAgICAgICAgLz4KICAgICAgPC9jYzpMaWNlbnNlCiAgICAgID4KICAgIDwvcmRmOlJERgogICAgPgogIDwvbWV0YWRhdGEKICA+Cjwvc3ZnCj4K">️&nbsp;'._('Download Transfer Protocol PDF'), 'success', ['target' => '_blank']), ['class' => '']));
            $buttons->addMenuItem(new \Ease\Html\DivTag(new \Ease\TWB5\LinkButton('transfer.php?format=docx&kod='.$contract->getRecordIdent(), '<img height="20" src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+CjwhLS0gQ3JlYXRlZCB3aXRoIElua3NjYXBlIChodHRwOi8vd3d3Lmlua3NjYXBlLm9yZy8pIC0tPgo8c3ZnCiAgICB4bWxuczppbmtzY2FwZT0iaHR0cDovL3d3dy5pbmtzY2FwZS5vcmcvbmFtZXNwYWNlcy9pbmtzY2FwZSIKICAgIHhtbG5zOnJkZj0iaHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyIKICAgIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIKICAgIHhtbG5zOnNvZGlwb2RpPSJodHRwOi8vc29kaXBvZGkuc291cmNlZm9yZ2UubmV0L0RURC9zb2RpcG9kaS0wLmR0ZCIKICAgIHhtbG5zOmNjPSJodHRwOi8vY3JlYXRpdmVjb21tb25zLm9yZy9ucyMiCiAgICB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIKICAgIHhtbG5zOmRjPSJodHRwOi8vcHVybC5vcmcvZGMvZWxlbWVudHMvMS4xLyIKICAgIHhtbG5zOnN2Zz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciCiAgICB4bWxuczpuczE9Imh0dHA6Ly9zb3ppLmJhaWVyb3VnZS5mciIKICAgIGlkPSJzdmczOTgyIgogICAgc29kaXBvZGk6ZG9jbmFtZT0iaXQtcGRmLWljb24uc3ZnIgogICAgdmlld0JveD0iMCAwIDYxOS41OCA3NzUuNTEiCiAgICB2ZXJzaW9uPSIxLjEiCiAgICBpbmtzY2FwZTp2ZXJzaW9uPSIwLjQ3IHIyMjU4MyIKICA+CiAgPHRpdGxlCiAgICAgIGlkPSJ0aXRsZTQwNTkiCiAgICA+V29yZCBEb2N1bWVudCBpY29uPC90aXRsZQogID4KICA8ZGVmcwogICAgICBpZD0iZGVmczM5ODQiCiAgICA+CiAgICA8ZmlsdGVyCiAgICAgICAgaWQ9ImZpbHRlcjk2MDItMSIKICAgICAgICBjb2xvci1pbnRlcnBvbGF0aW9uLWZpbHRlcnM9InNSR0IiCiAgICAgICAgaW5rc2NhcGU6Y29sbGVjdD0iYWx3YXlzIgogICAgICA+CiAgICAgIDxmZUdhdXNzaWFuQmx1cgogICAgICAgICAgaWQ9ImZlR2F1c3NpYW5CbHVyOTYwNC03IgogICAgICAgICAgc3RkRGV2aWF0aW9uPSIyLjM3NzA4OTQiCiAgICAgICAgICBpbmtzY2FwZTpjb2xsZWN0PSJhbHdheXMiCiAgICAgIC8+CiAgICA8L2ZpbHRlcgogICAgPgogICAgPHJhZGlhbEdyYWRpZW50CiAgICAgICAgaWQ9InJhZGlhbEdyYWRpZW50NjY4NyIKICAgICAgICBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSIKICAgICAgICBjeT0iMTAyOSIKICAgICAgICBjeD0iMTcyMy44IgogICAgICAgIGdyYWRpZW50VHJhbnNmb3JtPSJtYXRyaXgoLjAxMTE5NCAuOTkzODAgLTEuNDM2NyAuMDE2MTg0IDMxMjUuMyAtOTA1LjU5KSIKICAgICAgICByPSI2Mi41IgogICAgICAgIGlua3NjYXBlOmNvbGxlY3Q9ImFsd2F5cyIKICAgICAgPgogICAgICA8c3RvcAogICAgICAgICAgaWQ9InN0b3A5NTUyLTAiCiAgICAgICAgICBzdHlsZT0ic3RvcC1jb2xvcjojZmZmZmZmIgogICAgICAgICAgb2Zmc2V0PSIwIgogICAgICAvPgogICAgICA8c3RvcAogICAgICAgICAgaWQ9InN0b3A5NTU4LTkiCiAgICAgICAgICBzdHlsZT0ic3RvcC1jb2xvcjojZmZmZmZmIgogICAgICAgICAgb2Zmc2V0PSIuNSIKICAgICAgLz4KICAgICAgPHN0b3AKICAgICAgICAgIGlkPSJzdG9wOTU1NC00IgogICAgICAgICAgc3R5bGU9InN0b3AtY29sb3I6IzRkNGQ0ZCIKICAgICAgICAgIG9mZnNldD0iMSIKICAgICAgLz4KICAgIDwvcmFkaWFsR3JhZGllbnQKICAgID4KICAgIDxmaWx0ZXIKICAgICAgICBpZD0iZmlsdGVyOTU3MC04IgogICAgICAgIGNvbG9yLWludGVycG9sYXRpb24tZmlsdGVycz0ic1JHQiIKICAgICAgICBpbmtzY2FwZTpjb2xsZWN0PSJhbHdheXMiCiAgICAgID4KICAgICAgPGZlR2F1c3NpYW5CbHVyCiAgICAgICAgICBpZD0iZmVHYXVzc2lhbkJsdXI5NTcyLTgiCiAgICAgICAgICBzdGREZXZpYXRpb249IjAuMzAxMzM4NDUiCiAgICAgICAgICBpbmtzY2FwZTpjb2xsZWN0PSJhbHdheXMiCiAgICAgIC8+CiAgICA8L2ZpbHRlcgogICAgPgogICAgPGxpbmVhckdyYWRpZW50CiAgICAgICAgaWQ9ImxpbmVhckdyYWRpZW50NjY4OSIKICAgICAgICB5Mj0iOTUyLjQ0IgogICAgICAgIGdyYWRpZW50VW5pdHM9InVzZXJTcGFjZU9uVXNlIgogICAgICAgIHgyPSIxNzgzLjYiCiAgICAgICAgZ3JhZGllbnRUcmFuc2Zvcm09Im1hdHJpeCguNDYwMzUgLjAwNDkwMTYgLS4wMDQ5MDE2IC40NjAzNSA4NzcuNjUgMzQxLjMpIgogICAgICAgIHkxPSI5NjcuNTEiCiAgICAgICAgeDE9IjE3NTkuMSIKICAgICAgICBpbmtzY2FwZTpjb2xsZWN0PSJhbHdheXMiCiAgICAgID4KICAgICAgPHN0b3AKICAgICAgICAgIGlkPSJzdG9wOTU3Ni00IgogICAgICAgICAgc3R5bGU9InN0b3AtY29sb3I6I2YyZjJmMiIKICAgICAgICAgIG9mZnNldD0iMCIKICAgICAgLz4KICAgICAgPHN0b3AKICAgICAgICAgIGlkPSJzdG9wOTU3OC01IgogICAgICAgICAgc3R5bGU9InN0b3AtY29sb3I6IzgwODA4MCIKICAgICAgICAgIG9mZnNldD0iMSIKICAgICAgLz4KICAgIDwvbGluZWFyR3JhZGllbnQKICAgID4KICA8L2RlZnMKICA+CiAgPHNvZGlwb2RpOm5hbWVkdmlldwogICAgICBpZD0iYmFzZSIKICAgICAgYm9yZGVyY29sb3I9IiM2NjY2NjYiCiAgICAgIGlua3NjYXBlOnBhZ2VzaGFkb3c9IjIiCiAgICAgIGlua3NjYXBlOndpbmRvdy15PSItOCIKICAgICAgcGFnZWNvbG9yPSIjZmZmZmZmIgogICAgICBpbmtzY2FwZTp3aW5kb3ctaGVpZ2h0PSI3MDYiCiAgICAgIGlua3NjYXBlOndpbmRvdy1tYXhpbWl6ZWQ9IjEiCiAgICAgIGlua3NjYXBlOnpvb209IjAuMzQ3OTQ5MjIiCiAgICAgIGlua3NjYXBlOndpbmRvdy14PSItOCIKICAgICAgc2hvd2dyaWQ9ImZhbHNlIgogICAgICBib3JkZXJvcGFjaXR5PSIxLjAiCiAgICAgIGlua3NjYXBlOmN1cnJlbnQtbGF5ZXI9ImxheWVyMSIKICAgICAgaW5rc2NhcGU6Y3g9IjQ4Ni4wMjY2NCIKICAgICAgaW5rc2NhcGU6Y3k9IjMwOC43MDEyOSIKICAgICAgaW5rc2NhcGU6d2luZG93LXdpZHRoPSIxMjgwIgogICAgICBpbmtzY2FwZTpwYWdlb3BhY2l0eT0iMC4wIgogICAgICBpbmtzY2FwZTpkb2N1bWVudC11bml0cz0icHgiCiAgLz4KICA8ZwogICAgICBpZD0ibGF5ZXIxIgogICAgICBpbmtzY2FwZTpsYWJlbD0iTGF5ZXIgMSIKICAgICAgaW5rc2NhcGU6Z3JvdXBtb2RlPSJsYXllciIKICAgICAgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTYyLjc5IC03OC4yNzUpIgogICAgPgogICAgPGcKICAgICAgICBpZD0iZzY2NjgiCiAgICAgICAgaW5rc2NhcGU6ZXhwb3J0LXlkcGk9IjcyLjMwMDAwMyIKICAgICAgICBpbmtzY2FwZTpleHBvcnQteGRwaT0iNzIuMzAwMDAzIgogICAgICAgIGlua3NjYXBlOmV4cG9ydC1maWxlbmFtZT0iRzpcU0FGSCBJTlRFUk5BTFxXZWJzaXRlXFdlYiBHcmFwaGljc1xzZWN1cml0eS5wbmciCiAgICAgICAgdHJhbnNmb3JtPSJtYXRyaXgoOC43NDA5IC0uMTUyNTcgLjE1MjU3IDguNzQwOSAtMTQzMDcgLTY0NjEuMikiCiAgICAgID4KICAgICAgPGcKICAgICAgICAgIGlkPSJnNjY2MSIKICAgICAgICA+CiAgICAgICAgPHBhdGgKICAgICAgICAgICAgaWQ9InBhdGg2NTc3IgogICAgICAgICAgICBzdHlsZT0iZmlsdGVyOnVybCgjZmlsdGVyOTYwMi0xKTtmaWxsOiMwMDAwMDAiCiAgICAgICAgICAgIGQ9Im0xOTgzIDczOS41My0yNy41NjggMTEzLjg2IDgxLjMzMiAxOS42OTEgMjIuODIyLTk0LjI2My0xNC44NTUtMjQuMzQ2LTYxLjczLTE0Ljk0NnoiCiAgICAgICAgICAgIGlua3NjYXBlOmV4cG9ydC15ZHBpPSI3MiIKICAgICAgICAgICAgaW5rc2NhcGU6ZXhwb3J0LWZpbGVuYW1lPSJHOlxTQUZIIElOVEVSTkFMXFdlYnNpdGVcV2ViIEdyYXBoaWNzXEltYWdlc1xkb3dubG9hZC1wZGYtYmcucG5nIgogICAgICAgICAgICB0cmFuc2Zvcm09Im1hdHJpeCguNjcwMDcgLS4xNTQ3MCAuMTU0NzAgLjY3MDA3IDE5Ni4wOSA1OTIuMikiCiAgICAgICAgICAgIGlua3NjYXBlOmV4cG9ydC14ZHBpPSI3MiIKICAgICAgICAvPgogICAgICAgIDxwYXRoCiAgICAgICAgICAgIGlkPSJwYXRoNjU3OSIKICAgICAgICAgICAgc3R5bGU9ImZpbGw6I2U2ZTZlNiIKICAgICAgICAgICAgZD0ibTE2MzkuMiA3ODAuOTgtMC44NTc5IDgwLjU2MiA1Ny41NDQgMC42MTI3MSAwLjcxMDEtNjYuNjk0LTEzLjcyLTE0LjAxNi00My42NzYtMC40NjUwN3oiCiAgICAgICAgICAgIGlua3NjYXBlOmV4cG9ydC15ZHBpPSI3MiIKICAgICAgICAgICAgaW5rc2NhcGU6ZXhwb3J0LWZpbGVuYW1lPSJHOlxTQUZIIElOVEVSTkFMXFdlYnNpdGVcV2ViIEdyYXBoaWNzXEltYWdlc1xkb3dubG9hZC1wZGYtYmcucG5nIgogICAgICAgICAgICBpbmtzY2FwZTpleHBvcnQteGRwaT0iNzIiCiAgICAgICAgLz4KICAgICAgICA8cGF0aAogICAgICAgICAgICBpZD0icGF0aDY1ODEiCiAgICAgICAgICAgIHN0eWxlPSJvcGFjaXR5Oi42O2ZpbGw6dXJsKCNyYWRpYWxHcmFkaWVudDY2ODcpIgogICAgICAgICAgICBkPSJtMTYzOS4yIDc4MC45OC0wLjg1NzkgODAuNTYyIDU3LjU0NCAwLjYxMjcxIDAuNzEwMS02Ni42OTQtMTMuNzItMTQuMDE2LTQzLjY3Ni0wLjQ2NTA3eiIKICAgICAgICAgICAgaW5rc2NhcGU6ZXhwb3J0LXlkcGk9IjcyIgogICAgICAgICAgICBpbmtzY2FwZTpleHBvcnQtZmlsZW5hbWU9Ikc6XFNBRkggSU5URVJOQUxcV2Vic2l0ZVxXZWIgR3JhcGhpY3NcSW1hZ2VzXGRvd25sb2FkLXBkZi1iZy5wbmciCiAgICAgICAgICAgIGlua3NjYXBlOmV4cG9ydC14ZHBpPSI3MiIKICAgICAgICAvPgogICAgICAgIDxwYXRoCiAgICAgICAgICAgIGlkPSJwYXRoNjU4MyIKICAgICAgICAgICAgc3R5bGU9ImZpbHRlcjp1cmwoI2ZpbHRlcjk1NzAtOCk7ZmlsbDojNjY2NjY2IgogICAgICAgICAgICBkPSJtMTcyNi43IDcxNC42OXMxLjc4NTggMy43NjEyIDEuNzg1OCA1Ljc4MTJjMCAyLjAyMjEtMS42MDcyIDI0LjM1My0xLjYwNzIgMjQuMzUzczIwLjcyMi0xLjYwNzIgMjMuOTA2LTEuNTE3OWMzLjE4MDggMC4wODkzIDYuMDQ5IDEuNTE3OSA2LjA0OSAxLjUxNzlsLTMwLjEzNC0zMC4xMzR6IgogICAgICAgICAgICBpbmtzY2FwZTpleHBvcnQteWRwaT0iNzIiCiAgICAgICAgICAgIHNvZGlwb2RpOm5vZGV0eXBlcz0iY3pjemNjIgogICAgICAgICAgICBpbmtzY2FwZTpleHBvcnQtZmlsZW5hbWU9Ikc6XFNBRkggSU5URVJOQUxcV2Vic2l0ZVxXZWIgR3JhcGhpY3NcSW1hZ2VzXGRvd25sb2FkLXBkZi1iZy5wbmciCiAgICAgICAgICAgIHRyYW5zZm9ybT0ibWF0cml4KC40NjAzNSAuMDA0OTAxNiAtLjAwNDkwMTYgLjQ2MDM1IDg5MS40NCA0NDQuMjIpIgogICAgICAgICAgICBpbmtzY2FwZTpleHBvcnQteGRwaT0iNzIiCiAgICAgICAgLz4KICAgICAgICA8cGF0aAogICAgICAgICAgICBpZD0icGF0aDY1ODUiCiAgICAgICAgICAgIHN0eWxlPSJmaWxsOnVybCgjbGluZWFyR3JhZGllbnQ2Njg5KSIKICAgICAgICAgICAgZD0ibTE2ODIuOSA3ODEuNDRzMC44MDM4IDEuNzQwMiAwLjc5MzggMi42NzAyYy0wLjAxIDAuOTMwODctMC44NTkyIDExLjIwMy0wLjg1OTIgMTEuMjAzczkuNTQ3Mi0wLjYzODM0IDExLjAxMy0wLjU4MTU4YzEuNDYzOCAwLjA1NjcgMi43NzcyIDAuNzI4NCAyLjc3NzIgMC43Mjg0bC0xMy43MjUtMTQuMDJ6IgogICAgICAgICAgICBpbmtzY2FwZTpleHBvcnQteWRwaT0iNzIiCiAgICAgICAgICAgIHNvZGlwb2RpOm5vZGV0eXBlcz0iY3pjemNjIgogICAgICAgICAgICBpbmtzY2FwZTpleHBvcnQtZmlsZW5hbWU9Ikc6XFNBRkggSU5URVJOQUxcV2Vic2l0ZVxXZWIgR3JhcGhpY3NcSW1hZ2VzXGRvd25sb2FkLXBkZi1iZy5wbmciCiAgICAgICAgICAgIGlua3NjYXBlOmV4cG9ydC14ZHBpPSI3MiIKICAgICAgICAvPgogICAgICA8L2cKICAgICAgPgogICAgICA8ZwogICAgICAgICAgaWQ9Imc2NjQ5IgogICAgICAgICAgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLjg2NjE0IC00LjI5ODQpIgogICAgICAgID4KICAgICAgICA8ZwogICAgICAgICAgICBpZD0iZzY2NDIiCiAgICAgICAgICA+CiAgICAgICAgICA8cmVjdAogICAgICAgICAgICAgIGlkPSJyZWN0NjYwNSIKICAgICAgICAgICAgICBzdHlsZT0iZmlsbDojMDAzMzgwIgogICAgICAgICAgICAgIGhlaWdodD0iNS43MTQzIgogICAgICAgICAgICAgIHdpZHRoPSI0OC4yMTQiCiAgICAgICAgICAgICAgeT0iODUwLjAxIgogICAgICAgICAgICAgIHg9IjE2NDEuOCIKICAgICAgICAgIC8+CiAgICAgICAgICA8cmVjdAogICAgICAgICAgICAgIGlkPSJyZWN0NjYyMyIKICAgICAgICAgICAgICBzdHlsZT0iZmlsbDojMDAzMzgwIgogICAgICAgICAgICAgIGhlaWdodD0iNS43MTQzIgogICAgICAgICAgICAgIHdpZHRoPSI0OC4yMTQiCiAgICAgICAgICAgICAgeT0iODQwLjAxIgogICAgICAgICAgICAgIHg9IjE2NDEuOCIKICAgICAgICAgIC8+CiAgICAgICAgICA8cmVjdAogICAgICAgICAgICAgIGlkPSJyZWN0NjYyNSIKICAgICAgICAgICAgICBzdHlsZT0iZmlsbDojMDAzMzgwIgogICAgICAgICAgICAgIGhlaWdodD0iNS43MTQzIgogICAgICAgICAgICAgIHdpZHRoPSIxNy41IgogICAgICAgICAgICAgIHk9IjgzMC4wMSIKICAgICAgICAgICAgICB4PSIxNjcyLjUiCiAgICAgICAgICAvPgogICAgICAgICAgPHJlY3QKICAgICAgICAgICAgICBpZD0icmVjdDY2MjciCiAgICAgICAgICAgICAgc3R5bGU9ImZpbGw6IzAwMzM4MCIKICAgICAgICAgICAgICBoZWlnaHQ9IjUuNzE0MyIKICAgICAgICAgICAgICB3aWR0aD0iMTcuNSIKICAgICAgICAgICAgICB5PSI4MjAuMDEiCiAgICAgICAgICAgICAgeD0iMTY3Mi41IgogICAgICAgICAgLz4KICAgICAgICAgIDxyZWN0CiAgICAgICAgICAgICAgaWQ9InJlY3Q2NjI5IgogICAgICAgICAgICAgIHN0eWxlPSJmaWxsOiMwMDMzODAiCiAgICAgICAgICAgICAgaGVpZ2h0PSI1LjcxNDMiCiAgICAgICAgICAgICAgd2lkdGg9IjE3LjUiCiAgICAgICAgICAgICAgeT0iODEwLjAxIgogICAgICAgICAgICAgIHg9IjE2NzIuNSIKICAgICAgICAgIC8+CiAgICAgICAgPC9nCiAgICAgICAgPgogICAgICAgIDxnCiAgICAgICAgICAgIGlkPSJnNjYzNyIKICAgICAgICAgID4KICAgICAgICAgIDxyZWN0CiAgICAgICAgICAgICAgaWQ9InJlY3Q2NjMxIgogICAgICAgICAgICAgIHN0eWxlPSJzdHJva2U6IzAwMzM4MDtzdHJva2Utd2lkdGg6Mi40MTEzO2ZpbGw6I2ZmZmZmZiIKICAgICAgICAgICAgICBoZWlnaHQ9IjM0LjE2IgogICAgICAgICAgICAgIHdpZHRoPSIzNC4xNiIKICAgICAgICAgICAgICB5PSI4MDEuMjEiCiAgICAgICAgICAgICAgeD0iMTYzMC40IgogICAgICAgICAgLz4KICAgICAgICAgIDx0ZXh0CiAgICAgICAgICAgICAgaWQ9InRleHQ2NjMzIgogICAgICAgICAgICAgIHN0eWxlPSJmb250LXNpemU6MjkuODcxcHg7Zm9udC1mYW1pbHk6QXJpYWw7ZmlsbDojMDAwMDAwO3RleHQtYW5jaG9yOm1pZGRsZTtsaW5lLWhlaWdodDoxMDAlO3RleHQtYWxpZ246Y2VudGVyIgogICAgICAgICAgICAgIHNvZGlwb2RpOmxpbmVzcGFjaW5nPSIxMDAlIgogICAgICAgICAgICAgIHk9IjgyOC4xOTQyMSIKICAgICAgICAgICAgICB4PSIxNjQ3LjI3MzkiCiAgICAgICAgICAgICAgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIKICAgICAgICAgICAgPgogICAgICAgICAgICA8dHNwYW4KICAgICAgICAgICAgICAgIGlkPSJ0c3BhbjY2MzUiCiAgICAgICAgICAgICAgICB5PSI4MjguMTk0MjEiCiAgICAgICAgICAgICAgICB4PSIxNjQ3LjI3MzkiCiAgICAgICAgICAgICAgICBzdHlsZT0iZm9udC1zaXplOjI5Ljg3MXB4O2ZvbnQtZmFtaWx5OkNhbWJyaWE7ZmlsbDojMDAzMzgwO3RleHQtYW5jaG9yOm1pZGRsZTtsaW5lLWhlaWdodDoxMDAlO2ZvbnQtd2VpZ2h0OmJvbGQ7dGV4dC1hbGlnbjpjZW50ZXIiCiAgICAgICAgICAgICAgICBzb2RpcG9kaTpyb2xlPSJsaW5lIgogICAgICAgICAgICAgID5XPC90c3BhbgogICAgICAgICAgICA+CiAgICAgICAgICA8L3RleHQKICAgICAgICAgID4KICAgICAgICA8L2cKICAgICAgICA+CiAgICAgIDwvZwogICAgICA+CiAgICA8L2cKICAgID4KICA8L2cKICA+CiAgPG1ldGFkYXRhCiAgICA+CiAgICA8cmRmOlJERgogICAgICA+CiAgICAgIDxjYzpXb3JrCiAgICAgICAgPgogICAgICAgIDxkYzpmb3JtYXQKICAgICAgICAgID5pbWFnZS9zdmcreG1sPC9kYzpmb3JtYXQKICAgICAgICA+CiAgICAgICAgPGRjOnR5cGUKICAgICAgICAgICAgcmRmOnJlc291cmNlPSJodHRwOi8vcHVybC5vcmcvZGMvZGNtaXR5cGUvU3RpbGxJbWFnZSIKICAgICAgICAvPgogICAgICAgIDxjYzpsaWNlbnNlCiAgICAgICAgICAgIHJkZjpyZXNvdXJjZT0iaHR0cDovL2NyZWF0aXZlY29tbW9ucy5vcmcvbGljZW5zZXMvcHVibGljZG9tYWluLyIKICAgICAgICAvPgogICAgICAgIDxkYzpwdWJsaXNoZXIKICAgICAgICAgID4KICAgICAgICAgIDxjYzpBZ2VudAogICAgICAgICAgICAgIHJkZjphYm91dD0iaHR0cDovL29wZW5jbGlwYXJ0Lm9yZy8iCiAgICAgICAgICAgID4KICAgICAgICAgICAgPGRjOnRpdGxlCiAgICAgICAgICAgICAgPk9wZW5jbGlwYXJ0PC9kYzp0aXRsZQogICAgICAgICAgICA+CiAgICAgICAgICA8L2NjOkFnZW50CiAgICAgICAgICA+CiAgICAgICAgPC9kYzpwdWJsaXNoZXIKICAgICAgICA+CiAgICAgICAgPGRjOnRpdGxlCiAgICAgICAgICA+aXQtd29yZC1pY29uPC9kYzp0aXRsZQogICAgICAgID4KICAgICAgICA8ZGM6ZGF0ZQogICAgICAgICAgPjIwMTAtMDgtMDZUMjM6NDg6NTk8L2RjOmRhdGUKICAgICAgICA+CiAgICAgICAgPGRjOmRlc2NyaXB0aW9uCiAgICAgICAgLz4KICAgICAgICA8ZGM6c291cmNlCiAgICAgICAgICA+aHR0cHM6Ly9vcGVuY2xpcGFydC5vcmcvZGV0YWlsLzc4MjE3L2l0LXdvcmQtaWNvbi1ieS1zaGVpa2hfdHVoaW48L2RjOnNvdXJjZQogICAgICAgID4KICAgICAgICA8ZGM6Y3JlYXRvcgogICAgICAgICAgPgogICAgICAgICAgPGNjOkFnZW50CiAgICAgICAgICAgID4KICAgICAgICAgICAgPGRjOnRpdGxlCiAgICAgICAgICAgICAgPnNoZWlraF90dWhpbjwvZGM6dGl0bGUKICAgICAgICAgICAgPgogICAgICAgICAgPC9jYzpBZ2VudAogICAgICAgICAgPgogICAgICAgIDwvZGM6Y3JlYXRvcgogICAgICAgID4KICAgICAgICA8ZGM6c3ViamVjdAogICAgICAgICAgPgogICAgICAgICAgPHJkZjpCYWcKICAgICAgICAgICAgPgogICAgICAgICAgICA8cmRmOmxpCiAgICAgICAgICAgICAgPklUPC9yZGY6bGkKICAgICAgICAgICAgPgogICAgICAgICAgICA8cmRmOmxpCiAgICAgICAgICAgICAgPmNvbXB1dGVyPC9yZGY6bGkKICAgICAgICAgICAgPgogICAgICAgICAgICA8cmRmOmxpCiAgICAgICAgICAgICAgPmZpbGU8L3JkZjpsaQogICAgICAgICAgICA+CiAgICAgICAgICAgIDxyZGY6bGkKICAgICAgICAgICAgICA+aWNvbjwvcmRmOmxpCiAgICAgICAgICAgID4KICAgICAgICAgIDwvcmRmOkJhZwogICAgICAgICAgPgogICAgICAgIDwvZGM6c3ViamVjdAogICAgICAgID4KICAgICAgPC9jYzpXb3JrCiAgICAgID4KICAgICAgPGNjOkxpY2Vuc2UKICAgICAgICAgIHJkZjphYm91dD0iaHR0cDovL2NyZWF0aXZlY29tbW9ucy5vcmcvbGljZW5zZXMvcHVibGljZG9tYWluLyIKICAgICAgICA+CiAgICAgICAgPGNjOnBlcm1pdHMKICAgICAgICAgICAgcmRmOnJlc291cmNlPSJodHRwOi8vY3JlYXRpdmVjb21tb25zLm9yZy9ucyNSZXByb2R1Y3Rpb24iCiAgICAgICAgLz4KICAgICAgICA8Y2M6cGVybWl0cwogICAgICAgICAgICByZGY6cmVzb3VyY2U9Imh0dHA6Ly9jcmVhdGl2ZWNvbW1vbnMub3JnL25zI0Rpc3RyaWJ1dGlvbiIKICAgICAgICAvPgogICAgICAgIDxjYzpwZXJtaXRzCiAgICAgICAgICAgIHJkZjpyZXNvdXJjZT0iaHR0cDovL2NyZWF0aXZlY29tbW9ucy5vcmcvbnMjRGVyaXZhdGl2ZVdvcmtzIgogICAgICAgIC8+CiAgICAgIDwvY2M6TGljZW5zZQogICAgICA+CiAgICA8L3JkZjpSREYKICAgID4KICA8L21ldGFkYXRhCiAgPgo8L3N2Zwo+Cg==">️&nbsp;'._('Open Transfer Protocol in Word'), 'success', ['target' => '_blank']), ['class' => '']));
            $this->addItem($buttons);
        }
    }
}
