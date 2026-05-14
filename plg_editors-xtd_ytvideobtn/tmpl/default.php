<?php

/*
 * @package     Joomla.Plugin
 * @subpackage  Editors-xtd.ytvideobtn
 * @copyright   Copyright (C) Aleksey A. Morozov. All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
?>
<template id="ytvideo-modal-template">
    <div id="ytvideo-modal" class="joomla-modal modal fade" tabindex="-1" role="dialog" aria-hidden="true" style="display:none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title"><?php echo Text::_('PLG_EDITORS-XTD_YTVIDEOBTN_MODAL_TITLE'); ?></h3>
                    <button type="button" class="btn-close ytvideo-close" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="ytvideo_url" class="form-label"><?php echo Text::_('PLG_EDITORS-XTD_YTVIDEOBTN_LABEL_URL'); ?></label>
                        <input type="text" id="ytvideo_url" class="form-control" value="">
                    </div>
                    <div class="mb-3">
                        <label for="ytvideo_ratio" class="form-label"><?php echo Text::_('PLG_EDITORS-XTD_YTVIDEOBTN_LABEL_RATIO'); ?></label>
                        <select id="ytvideo_ratio" class="form-select">
                            <option value="4-3">4:3 (TV)</option>
                            <option value="5-3">5:3 (Wide TV)</option>
                            <option value="16-9" selected>16:9 (Standard YouTube, HD)</option>
                            <option value="167-9">16.7:9 (Standard films)</option>
                            <option value="18-9">18:9 (iPhone)</option>
                            <option value="199-9">19.9:9 (Wide 70mm)</option>
                            <option value="235-1">2.35:1 (Panavision)</option>
                            <option value="255-1">2.55:1 (Cinemascope)</option>
                            <option value="27-1">2.7:1 (Ultra Panavision, 2K/4K)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="ytvideo_title" class="form-label"><?php echo Text::_('PLG_EDITORS-XTD_YTVIDEOBTN_LABEL_TITLE'); ?></label>
                        <input type="text" id="ytvideo_title" class="form-control" value="">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="ytvideo_ins"><?php echo Text::_('PLG_EDITORS-XTD_YTVIDEOBTN_MODAL_BTN_INSERT'); ?></button>
                    <button type="button" class="btn btn-secondary ytvideo-close"><?php echo Text::_('PLG_EDITORS-XTD_YTVIDEOBTN_MODAL_BTN_CANCEL'); ?></button>
                </div>
            </div>
        </div>
    </div>
</template>
