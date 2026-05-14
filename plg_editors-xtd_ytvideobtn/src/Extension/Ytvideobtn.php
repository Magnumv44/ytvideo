<?php

/*
 * @package     Joomla.Plugin
 * @subpackage  Editors-xtd.ytvideobtn
 * @copyright   Copyright (C) Aleksey A. Morozov. All rights reserved.
 * @copyright   Copyright (C) 2026 Vitaliy Magnum (https://www.magnumblog.space). Joomla 6 migration.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Joomla\Plugin\EditorsXtd\Ytvideobtn\Extension;

\defined('_JEXEC') or die;

use Joomla\CMS\Editor\Button\Button;
use Joomla\CMS\Event\Editor\EditorButtonsSetupEvent;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\SubscriberInterface;

final class Ytvideobtn extends CMSPlugin implements SubscriberInterface
{
    protected $autoloadLanguage = true;

    public static function getSubscribedEvents(): array
    {
        return [
            'onEditorButtonsSetup' => 'onEditorButtonsSetup',
        ];
    }

    public function onEditorButtonsSetup(EditorButtonsSetupEvent $event): void
    {
        $subject  = $event->getButtonsRegistry();
        $disabled = $event->getDisabledButtons();

        if (\in_array('ytvideobtn', $disabled)) {
            return;
        }

        $wa = $this->getApplication()->getDocument()->getWebAssetManager();

        // i18n рядки передаємо через data-атрибут на script-тезі
        $i18n = json_encode([
            'title'      => Text::_('PLG_EDITORS-XTD_YTVIDEOBTN_MODAL_TITLE'),
            'labelUrl'   => Text::_('PLG_EDITORS-XTD_YTVIDEOBTN_LABEL_URL'),
            'labelRatio' => Text::_('PLG_EDITORS-XTD_YTVIDEOBTN_LABEL_RATIO'),
            'labelTitle' => Text::_('PLG_EDITORS-XTD_YTVIDEOBTN_LABEL_TITLE'),
            'btnInsert'  => Text::_('PLG_EDITORS-XTD_YTVIDEOBTN_MODAL_BTN_INSERT'),
            'btnCancel'  => Text::_('PLG_EDITORS-XTD_YTVIDEOBTN_MODAL_BTN_CANCEL'),
        ], \JSON_HEX_TAG | \JSON_HEX_APOS | \JSON_HEX_QUOT | \JSON_UNESCAPED_UNICODE);

        // Реєструємо ES-модуль з залежністю від 'editors' (editor-api)
        $wa->registerScript(
            'plg_editors_xtd_ytvideobtn.button',
            'plugins/editors-xtd/ytvideobtn/media/js/ytvideobtn.js',
            ['version' => 'auto'],
            ['type' => 'module', 'data-ytv-i18n' => '1', 'data-ytv-i18n-data' => $i18n],
            ['editors']
        );
        $wa->useScript('plg_editors_xtd_ytvideobtn.button');

        $button = new Button(
            'ytvideobtn',
            [
                'action'  => 'ytvideo-insert',
                'class'   => 'btn btn-danger',
                'text'    => Text::_('PLG_EDITORS-XTD_YTVIDEOBTN_BUTTON_TEXT'),
                'name'    => 'ytvideobtn',
                'icon'    => 'youtube',
                'iconSVG' => '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 68 48" width="32" height="17">'
                    . '<path d="M66.52,7.74c-0.78-2.93-2.49-5.41-5.42-6.19C55.79,.13,34,0,34,0S12.21,.13,6.9,1.55'
                    . ' C3.97,2.33,2.27,4.81,1.48,7.74C0.06,13.05,0,24,0,24s0.06,10.95,1.48,16.26c0.78,2.93,2.49,5.41,5.42,6.19'
                    . ' C12.21,47.87,34,48,34,48s21.79-0.13,27.1-1.55c2.93-0.78,4.64-3.26,5.42-6.19C67.94,34.95,68,24,68,24'
                    . 'S67.94,13.05,66.52,7.74z" fill="#f00"></path>'
                    . '<path d="M 45,24 27,14 27,34" fill="#fff"></path></svg>',
            ]
        );

        $subject->add($button);
    }
}
