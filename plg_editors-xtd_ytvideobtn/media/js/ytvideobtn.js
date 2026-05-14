/**
 * @copyright   Copyright (C) 2026 Vitaliy Magnum (https://www.magnumblog.space). Joomla 6 migration.
 */
import { JoomlaEditorButton } from 'editor-api';

const CSS_MODAL = `
#ytv-overlay{
    position:fixed;top:0;left:0;width:100%;height:100%;
    background:rgba(0,0,0,.5);z-index:99998;display:none;
}
#ytv-overlay.ytv-show{display:block;}
#ytv-box{
    position:fixed;top:50%;left:50%;
    transform:translate(-50%,-50%);
    width:520px;max-width:calc(100vw - 30px);
    background:#fff;border-radius:6px;
    box-shadow:0 8px 40px rgba(0,0,0,.35);
    z-index:99999;display:none;
    font-family:inherit;font-size:14px;
}
#ytv-box.ytv-show{display:block;}
#ytv-head{
    display:flex;align-items:center;justify-content:space-between;
    padding:14px 20px;border-bottom:1px solid #dee2e6;
}
#ytv-head h3{margin:0;font-size:16px;font-weight:600;}
#ytv-btn-x{
    background:none;border:none;font-size:22px;line-height:1;
    cursor:pointer;color:#666;padding:0 4px;
}
#ytv-btn-x:hover{color:#000;}
#ytv-body{padding:20px;}
#ytv-body .ytv-row{margin-bottom:14px;}
#ytv-body label{display:block;margin-bottom:5px;font-weight:500;}
#ytv-body input[type=text],
#ytv-body select{
    display:block;width:100%;box-sizing:border-box;
    padding:7px 10px;border:1px solid #ced4da;
    border-radius:4px;font-size:14px;
}
#ytv-foot{
    padding:12px 20px;border-top:1px solid #dee2e6;
    display:flex;justify-content:flex-end;gap:8px;
}
#ytv-btn-insert{
    padding:8px 20px;background:#0d6efd;color:#fff;
    border:none;border-radius:4px;cursor:pointer;font-size:14px;
}
#ytv-btn-insert:hover{background:#0b5ed7;}
#ytv-btn-cancel{
    padding:8px 20px;background:#6c757d;color:#fff;
    border:none;border-radius:4px;cursor:pointer;font-size:14px;
}
#ytv-btn-cancel:hover{background:#5c636a;}
`;

function ytvideoInit() {
    if (document.getElementById('ytv-box')) return;

    // Inject CSS
    const style = document.createElement('style');
    style.id = 'ytv-styles';
    style.textContent = CSS_MODAL;
    document.head.appendChild(style);

    // Overlay (backdrop)
    const overlay = document.createElement('div');
    overlay.id = 'ytv-overlay';
    document.body.appendChild(overlay);

    // Modal box
    const box = document.createElement('div');
    box.id = 'ytv-box';
    box.setAttribute('role', 'dialog');
    box.innerHTML = `
        <div id="ytv-head">
            <h3 id="ytv-title"></h3>
            <button id="ytv-btn-x" type="button" aria-label="Close">&#x00D7;</button>
        </div>
        <div id="ytv-body">
            <div class="ytv-row">
                <label for="ytv-url" id="ytv-label-url"></label>
                <input type="text" id="ytv-url" value="">
            </div>
            <div class="ytv-row">
                <label for="ytv-ratio" id="ytv-label-ratio"></label>
                <select id="ytv-ratio">
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
            <div class="ytv-row">
                <label for="ytv-title-input" id="ytv-label-title"></label>
                <input type="text" id="ytv-title-input" value="">
            </div>
        </div>
        <div id="ytv-foot">
            <button type="button" id="ytv-btn-insert"></button>
            <button type="button" id="ytv-btn-cancel"></button>
        </div>
    `;
    document.body.appendChild(box);

    // Fill i18n
    const scriptTag = document.querySelector('script[data-ytv-i18n-data]');
    if (scriptTag) {
        const i18n = JSON.parse(scriptTag.dataset.ytvI18nData);
        document.getElementById('ytv-title').textContent        = i18n.title;
        document.getElementById('ytv-label-url').textContent    = i18n.labelUrl;
        document.getElementById('ytv-label-ratio').textContent  = i18n.labelRatio;
        document.getElementById('ytv-label-title').textContent  = i18n.labelTitle;
        document.getElementById('ytv-btn-insert').textContent   = i18n.btnInsert;
        document.getElementById('ytv-btn-cancel').textContent   = i18n.btnCancel;
    }

    // Close handlers
    document.getElementById('ytv-btn-x').addEventListener('click', ytvideoHide);
    document.getElementById('ytv-btn-cancel').addEventListener('click', ytvideoHide);
    overlay.addEventListener('click', ytvideoHide);
}

function ytvideoShow() {
    document.getElementById('ytv-box').classList.add('ytv-show');
    document.getElementById('ytv-overlay').classList.add('ytv-show');
    document.body.style.overflow = 'hidden';
}

function ytvideoHide() {
    document.getElementById('ytv-box').classList.remove('ytv-show');
    document.getElementById('ytv-overlay').classList.remove('ytv-show');
    document.body.style.overflow = '';
}

function urlCheck(url) {
    return /https?:\/\/[-\w.]{3,}\.[A-Za-z]{2,}/.test(url);
}

JoomlaEditorButton.registerAction('ytvideo-insert', (editor) => {
    ytvideoInit();
    ytvideoShow();

    // Replace insert button to clear old listeners
    const oldBtn = document.getElementById('ytv-btn-insert');
    const newBtn = oldBtn.cloneNode(true);
    oldBtn.parentNode.replaceChild(newBtn, oldBtn);

    newBtn.addEventListener('click', () => {
        const url   = document.getElementById('ytv-url').value.trim();
        const ratio = document.getElementById('ytv-ratio').value;
        const title = document.getElementById('ytv-title-input').value.trim();

        if (url && urlCheck(url)) {
            editor.replaceSelection('{ytvideo ' + url + '|' + ratio + (title ? '|' + title : '') + '}');
        }

        document.getElementById('ytv-url').value         = '';
        document.getElementById('ytv-title-input').value = '';
        ytvideoHide();
    });
});
