# YtVideo for Joomla 6

![Version](https://img.shields.io/badge/VERSION-2.0.1-0366d6.svg?style=for-the-badge)
![Joomla!](https://img.shields.io/badge/Joomla!-6.0+-1A3867.svg?style=for-the-badge)
![PHP](https://img.shields.io/badge/PHP-8.3+-8892BF.svg?style=for-the-badge)
![License](https://img.shields.io/badge/License-GPL_v3-green.svg?style=for-the-badge)

Content plugin for **Joomla! 6** for lazy-loading YouTube video embeds via shortcode.

🇺🇦 [Українська](README.uk.md) | 🇷🇺 [Русский](README.ru.md)

> This is an unofficial fork of [alekvolsk/ytvideo](https://github.com/alekvolsk/ytvideo), fully migrated to Joomla 6 architecture (no backward compatibility plugin required).

---

## Features

- Lazy loading — YouTube iframe loads only on click, not on page load
- Thumbnail pre-cached from YouTube (`cache/plg_content_ytvideo/`)
- WebP thumbnail support
- Configurable aspect ratios
- Optional replacement of existing `<iframe>` embeds and `<a>` links
- Editor button plugin for quick shortcode insertion

---

## Requirements

| Requirement | Version |
|-------------|---------|
| Joomla!     | 6.0+    |
| PHP         | 8.3+    |
| Backward Compatibility Plugin | **Not required** |

---

## Installation

1. Download `ytvideo-joomla-6.zip` from [Releases](../../releases)
2. Go to **Extensions → Manage → Install**
3. Upload the zip file
4. Both plugins are enabled automatically after installation

---

## Shortcode

```
{ytvideo full_url[|ratio][|title]}
```

**Example:**
```
{ytvideo https://www.youtube.com/watch?v=dQw4w9WgXcQ|16-9|Never Gonna Give You Up}
```

Parts are optional but order must be preserved: `url|ratio|title`.

---

## Supported aspect ratios

| Value   | Label                  |
|---------|------------------------|
| `4-3`   | 4:3 (TV)               |
| `5-3`   | 5:3 (Wide TV)          |
| `16-9`  | 16:9 (Standard YouTube, HD) |
| `167-9` | 16.7:9 (Standard films)|
| `18-9`  | 18:9 (iPhone)          |
| `199-9` | 19.9:9 (Wide 70mm)     |
| `235-1` | 2.35:1 (Panavision)    |
| `255-1` | 2.55:1 (Cinemascope)   |
| `27-1`  | 2.7:1 (Ultra Panavision, 2K/4K) |

---

## Thumbnail cache

Thumbnails are downloaded from YouTube and stored in:
```
/cache/plg_content_ytvideo/<video_id>.webp
```
or `.jpg` if WebP is unavailable. The cache folder is created automatically.

**Note:** Clearing the cache via the admin panel is not yet supported, but it may be added in a future update

---

## Joomla 6 Migration notes

Compared to the original Joomla 3/4/5 version, the following changes were made:

- `services/provider.php` DI provider added to both plugins
- Classes moved to `src/Extension/` with proper namespaces
- `SubscriberInterface` implemented with `getSubscribedEvents()`
- `onDisplay()` replaced with `onEditorButtonsSetup` event
- `CMSObject` replaced with `Button` class
- `Factory::getConfig()` → `$app->get()`
- `Factory::getDocument()` → `$app->getDocument()`
- `Joomla\CMS\Filesystem\*` → `Joomla\Filesystem\*`
- Assets registered via `WebAssetManager`
- Editor button JS rewritten as ES module using `JoomlaEditorButton.registerAction()`
- Installer scripts use `InstallerScriptInterface` with `InstallerAdapter`
- `php_minimum` updated to `8.3`

---

## Repository structure

```
├── plg_content_ytvideo/          # Content plugin
│   ├── src/Extension/Ytvideo.php
│   ├── services/provider.php
│   ├── tmpl/default.php
│   ├── assets/
│   ├── language/
│   ├── script.php
│   └── ytvideo.xml
│
├── plg_editors-xtd_ytvideobtn/   # Editor button plugin
│   ├── src/Extension/Ytvideobtn.php
│   ├── services/provider.php
│   ├── media/js/ytvideobtn.js
│   ├── tmpl/default.php
│   ├── language/
│   ├── script.php
│   └── ytvideobtn.xml
│
├── language/                     # Package-level language files
├── pkg_ytvideo.xml               # Package manifest
├── pkg_script.php                # Package installer script
└── LICENSE
```

---

## Building the install package

```bash
# From the repo root:
cd plg_content_ytvideo && zip -r ../plg_content_ytvideo.zip . && cd ..
cd plg_editors-xtd_ytvideobtn && zip -r ../plg_editors-xtd_ytvideobtn.zip . && cd ..

mkdir packages
mv plg_content_ytvideo.zip packages/
mv plg_editors-xtd_ytvideobtn.zip packages/

zip -r ytvideo-joomla-6.zip pkg_ytvideo.xml pkg_script.php language/ packages/
```

---

## Credits

Original plugin by [Aleksey A. Morozov](https://alekvolsk.pw).

Joomla 6 migration by [Vitaliy Magnum](https://www.magnumblog.space).

## License

[GNU General Public License v3.0](LICENSE)
