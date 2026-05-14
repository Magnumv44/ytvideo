# YtVideo для Joomla 6

![Version](https://img.shields.io/badge/VERSION-2.0.0-0366d6.svg?style=for-the-badge)
![Joomla!](https://img.shields.io/badge/Joomla!-6.0+-1A3867.svg?style=for-the-badge)
![PHP](https://img.shields.io/badge/PHP-8.3+-8892BF.svg?style=for-the-badge)
![License](https://img.shields.io/badge/License-GPL_v3-green.svg?style=for-the-badge)

Контент-плагін **Joomla! 6** для відображення відео з YouTube через шорткод.

> Це неофіційний форк [alekvolsk/ytvideo](https://github.com/alekvolsk/ytvideo), повністю мігрований на архітектуру Joomla 6 (плагін зворотної сумісності не потрібен).

---

## Можливості

- Ліниве завантаження (*Lazy loading*) — iframe YouTube завантажується лише після кліку, не при завантаженні сторінки
- Попереднє кешування мініатюри з YouTube (`cache/plg_content_ytvideo/`)
- Підтримка WebP мініатюр
- Налаштовувані співвідношення сторін
- Опціональна заміна наявних вставок `<iframe>` та посилань `<a>`
- Плагін кнопки редактора для швидкого вставлення шорткоду

---

## Вимоги

| Вимога | Версія |
|--------|--------|
| Joomla! | 6.0+ |
| PHP | 8.3+ |
| Плагін зворотної сумісності | **Не потрібен** |

---

## Встановлення

1. Завантажте `ytvideo-joomla-6.zip` з розділу [Releases](../../releases)
2. Перейдіть до **Розширення → Управління → Встановлення**
3. Завантажте zip-файл
4. Обидва плагіни вмикаються автоматично після встановлення

---

## Шорткод

```
{ytvideo повна_адреса[|співвідношення][|заголовок]}
```

**Приклад:**
```
{ytvideo https://www.youtube.com/watch?v=dQw4w9WgXcQ|16-9|Назва відео}
```

Частини є необов'язковими, окрім посилання на відео, але порядок має зберігатися: `адреса|співвідношення|заголовок`.

---

## Підтримувані співвідношення сторін

| Значення | Опис |
|----------|------|
| `4-3`   | 4:3 (Телевізор) |
| `5-3`   | 5:3 (Широкий ТВ) |
| `16-9`  | 16:9 (Стандарт YouTube, HD) |
| `167-9` | 16.7:9 (Стандартні фільми) |
| `18-9`  | 18:9 (iPhone) |
| `199-9` | 19.9:9 (Широкий 70мм) |
| `235-1` | 2.35:1 (Panavision) |
| `255-1` | 2.55:1 (Cinemascope) |
| `27-1`  | 2.7:1 (Ultra Panavision, 2K/4K) |

---

## Кеш мініатюр

Мініатюри завантажуються з YouTube і зберігаються у:
```
/cache/plg_content_ytvideo/<id_відео>.webp
```
або `.jpg` якщо WebP недоступний. Папка кешу створюється автоматично.

Для очищення кешу мініатюр — просто видаліть вміст цієї папки. При наступному перегляді матеріалів мініатюри завантажаться знову.

**Примітка:** Видалення кешу через адмін панель поки не передбачено, але може бути додано в майбутньому оновленні.

---

## Нотатки щодо міграції на Joomla 6

Порівняно з оригінальною версією для Joomla 3/4/5 були внесені такі зміни:

- Додано `services/provider.php` DI-провайдер до обох плагінів
- Класи перенесено до `src/Extension/` з відповідними неймспейсами
- Реалізовано `SubscriberInterface` з `getSubscribedEvents()`
- `onDisplay()` замінено на подію `onEditorButtonsSetup`
- `CMSObject` замінено на клас `Button`
- `Factory::getConfig()` → `$app->get()`
- `Factory::getDocument()` → `$app->getDocument()`
- `Joomla\CMS\Filesystem\*` → `Joomla\Filesystem\*`
- Ресурси реєструються через `WebAssetManager`
- JS кнопки редактора переписано як ES-модуль з `JoomlaEditorButton.registerAction()`
- Скрипти встановлення використовують `InstallerScriptInterface` з `InstallerAdapter`
- `php_minimum` оновлено до `8.3`

---

## Структура репозиторію

```
├── plg_content_ytvideo/          # Контент-плагін
│   ├── src/Extension/Ytvideo.php
│   ├── services/provider.php
│   ├── tmpl/default.php
│   ├── assets/
│   ├── language/
│   ├── script.php
│   └── ytvideo.xml
│
├── plg_editors-xtd_ytvideobtn/   # Плагін кнопки редактора
│   ├── src/Extension/Ytvideobtn.php
│   ├── services/provider.php
│   ├── media/js/ytvideobtn.js
│   ├── tmpl/default.php
│   ├── language/
│   ├── script.php
│   └── ytvideobtn.xml
│
├── language/                     # Мовні файли пакету
├── pkg_ytvideo.xml               # Маніфест пакету
├── pkg_script.php                # Скрипт встановлення пакету
└── LICENSE
```

---

## Збірка інсталяційного пакету

```bash
# З кореня репозиторію:
cd plg_content_ytvideo && zip -r ../plg_content_ytvideo.zip . && cd ..
cd plg_editors-xtd_ytvideobtn && zip -r ../plg_editors-xtd_ytvideobtn.zip . && cd ..

mkdir packages
mv plg_content_ytvideo.zip packages/
mv plg_editors-xtd_ytvideobtn.zip packages/

zip -r ytvideo-joomla-6.zip pkg_ytvideo.xml pkg_script.php language/ packages/
```

Або просто запушіть тег — GitHub Actions збере та опублікує реліз автоматично:
```bash
git tag v2.0.0
git push origin v2.0.0
```

---

## Подяки

Оригінальний плагін від [Aleksey A. Morozov](https://alekvolsk.pw).

Міграція на Joomla 6 [Виталій Магнум](https://www.magnumblog.space).

## Ліцензія

[GNU General Public License v3.0](LICENSE)
