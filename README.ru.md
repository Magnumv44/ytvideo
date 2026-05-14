# YtVideo для Joomla 6

![Version](https://img.shields.io/badge/VERSION-2.0.0-0366d6.svg?style=for-the-badge)
![Joomla!](https://img.shields.io/badge/Joomla!-6.0+-1A3867.svg?style=for-the-badge)
![PHP](https://img.shields.io/badge/PHP-8.3+-8892BF.svg?style=for-the-badge)
![License](https://img.shields.io/badge/License-GPL_v3-green.svg?style=for-the-badge)

Контент-плагин **Joomla! 6** для отображения видео с YouTube через шорткод.

> Это неофициальный форк [alekvolsk/ytvideo](https://github.com/alekvolsk/ytvideo), полностью мигрированный на архитектуру Joomla 6 (плагин обратной совместимости не требуется).

---

## Возможности

- Ленивая загрузка (*Lazy loading*) — iframe YouTube загружается только после клика, а не при загрузке страницы
- Предварительное кэширование миниатюры с YouTube (`cache/plg_content_ytvideo/`)
- Поддержка WebP миниатюр
- Настраиваемые соотношения сторон
- Опциональная замена существующих вставок `<iframe>` и ссылок `<a>`
- Плагин кнопки редактора для быстрой вставки шорткода

---

## Требования

| Требование | Версия |
|------------|--------|
| Joomla! | 6.0+ |
| PHP | 8.3+ |
| Плагин обратной совместимости | **Не требуется** |

---

## Установка

1. Скачайте `ytvideo-joomla-6.zip` из раздела [Releases](../../releases)
2. Перейдите в **Расширения → Управление → Установка**
3. Загрузите zip-файл
4. Оба плагина включаются автоматически после установки

---

## Шорткод

```
{ytvideo полный_адрес[|соотношение][|заголовок]}
```

**Пример:**
```
{ytvideo https://www.youtube.com/watch?v=dQw4w9WgXcQ|16-9|Название видео}
```

Части являются необязательными, кроме адреса видео, но порядок должен сохраняться: `адрес|соотношение|заголовок`.

---

## Поддерживаемые соотношения сторон

| Значение | Описание |
|----------|----------|
| `4-3`   | 4:3 (Телевизор) |
| `5-3`   | 5:3 (Широкий ТВ) |
| `16-9`  | 16:9 (Стандарт YouTube, HD) |
| `167-9` | 16.7:9 (Стандартные фильмы) |
| `18-9`  | 18:9 (iPhone) |
| `199-9` | 19.9:9 (Широкий 70мм) |
| `235-1` | 2.35:1 (Panavision) |
| `255-1` | 2.55:1 (Cinemascope) |
| `27-1`  | 2.7:1 (Ultra Panavision, 2K/4K) |

---

## Кэш миниатюр

Миниатюры скачиваются с YouTube и сохраняются в:
```
/cache/plg_content_ytvideo/<id_видео>.webp
```
или `.jpg` если WebP недоступен. Папка кэша создаётся автоматически.

Для очистки кэша миниатюр — просто удалите содержимое этой папки. При следующем просмотре материалов миниатюры загрузятся снова.

Примечание: Удаление кэша через админ панель пока не предусмотрено, но может быть добавлено в будущем обновлении.

---

## Заметки по миграции на Joomla 6

По сравнению с оригинальной версией для Joomla 3/4/5 были внесены следующие изменения:

- Добавлен `services/provider.php` DI-провайдер к обоим плагинам
- Классы перенесены в `src/Extension/` с соответствующими неймспейсами
- Реализован `SubscriberInterface` с `getSubscribedEvents()`
- `onDisplay()` заменён на событие `onEditorButtonsSetup`
- `CMSObject` заменён на класс `Button`
- `Factory::getConfig()` → `$app->get()`
- `Factory::getDocument()` → `$app->getDocument()`
- `Joomla\CMS\Filesystem\*` → `Joomla\Filesystem\*`
- Ресурсы регистрируются через `WebAssetManager`
- JS кнопки редактора переписан как ES-модуль с `JoomlaEditorButton.registerAction()`
- Скрипты установки используют `InstallerScriptInterface` с `InstallerAdapter`
- `php_minimum` обновлён до `8.3`

---

## Структура репозитория

```
├── plg_content_ytvideo/          # Контент-плагин
│   ├── src/Extension/Ytvideo.php
│   ├── services/provider.php
│   ├── tmpl/default.php
│   ├── assets/
│   ├── language/
│   ├── script.php
│   └── ytvideo.xml
│
├── plg_editors-xtd_ytvideobtn/   # Плагин кнопки редактора
│   ├── src/Extension/Ytvideobtn.php
│   ├── services/provider.php
│   ├── media/js/ytvideobtn.js
│   ├── tmpl/default.php
│   ├── language/
│   ├── script.php
│   └── ytvideobtn.xml
│
├── language/                     # Языковые файлы пакета
├── pkg_ytvideo.xml               # Манифест пакета
├── pkg_script.php                # Скрипт установки пакета
└── LICENSE
```

---

## Сборка установочного пакета

```bash
# Из корня репозитория:
cd plg_content_ytvideo && zip -r ../plg_content_ytvideo.zip . && cd ..
cd plg_editors-xtd_ytvideobtn && zip -r ../plg_editors-xtd_ytvideobtn.zip . && cd ..

mkdir packages
mv plg_content_ytvideo.zip packages/
mv plg_editors-xtd_ytvideobtn.zip packages/

zip -r ytvideo-joomla-6.zip pkg_ytvideo.xml pkg_script.php language/ packages/
```

Или просто запушьте тег — GitHub Actions соберёт и опубликует релиз автоматически:
```bash
git tag v2.0.0
git push origin v2.0.0
```

---

## Благодарности

Оригинальный плагин от [Aleksey A. Morozov](https://alekvolsk.pw).

Миграция на Joomla 6 [Виталий Магнум](https://www.magnumblog.space).

## Лицензия

[GNU General Public License v3.0](LICENSE)
