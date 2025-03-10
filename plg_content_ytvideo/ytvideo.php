<?php

/*
 * @package     Joomla.Plugin
 * @subpackage  Content.ytvideo
 * @copyright   Copyright (C) Aleksey A. Morozov. All rights reserved.
 * @license     GNU General Public License version 3 or later; see http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\FileSystem\Path;
use Joomla\CMS\Uri\Uri;
use Joomla\Filesystem\Folder;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class PlgContentYtvideo extends CMSPlugin
{
    public function onContentPrepare($context, &$article, &$params, $page = 0)
    {
        if ($context == 'com_finder.indexer') {
            return false;
        }

        if ($this->params->get('oldframes') == '1') {
            $matches = [];
            preg_match_all(
                '~<iframe.+?src="h?t?t?p?s?:?//w?w?w?.?youtu.?be(?:-nocookie)?.?c?o?m?' .
                    '/embed/([a-zA-Z0-9_-]{11}).+?"[^>].*?></iframe>~i',
                $article->text,
                $matches
            );
            if (count($matches[0])) {
                foreach ($matches[0] as $key => $res) {
                    $article->text = str_replace(
                        $res,
                        '<div>{ytvideo https://youtube.com/watch?v=' . $matches[1][$key] . '|}</div>',
                        $article->text
                    );
                }
            }
            unset($matches);
        }

        if ($this->params->get('oldlinks') == '1') {
            $_alllinks = [];
            preg_match_all('~<a\s.*?href="(.+?)".*?>(.+?)</a>~im', $article->text, $_alllinks);
            if (count($_alllinks[0])) {
                foreach ($_alllinks[0] as $key => $res) {
                    if (strpos(strtolower($res), 'data-no-ytvideo') !== false) {
                        continue;
                    }
                    $match = [];
                    preg_match(
                        '~(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/' .
                            '|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})~i',
                        $res,
                        $match
                    );
                    if (count($match)) {
                        $title = mb_strpos($_alllinks[2][$key], '://') === false ? strip_tags($_alllinks[2][$key]) : '';
                        $article->text = str_replace(
                            $res,
                            '{ytvideo https://youtube.com/watch?v=' . $match[1] . ($title ? '|' . $title : '') . '}',
                            $article->text
                        );
                    }
                }
            }
        }

        $results = [];
        preg_match_all('~{ytvideo.+([^>}]*?)}~imU', $article->text, $results);
        foreach ($results as $k => $result) {
            if (!$result) {
                unset($results[$k]);
            }
        }
        if (!$results) {
            return false;
        }

        $cachFolder = Path::clean(Factory::getConfig()->get('cache_path', JPATH_CACHE));
        $cachFolder = str_replace('administrator' . DIRECTORY_SEPARATOR, '', $cachFolder);
        $cachFolder = $cachFolder . DIRECTORY_SEPARATOR . 'plg_content_ytvideo' . DIRECTORY_SEPARATOR;
        if ($cachFolder && !is_dir($cachFolder)) {
            Folder::create($cachFolder, 0755);
        }

        $layout = PluginHelper::getLayoutPath('content', 'ytvideo');
        $format = $this->params->get('format', '16-9');
        $mute = (int) $this->params->get('mute', 0);

        Factory::getDocument()->addScriptDeclaration('window.ytvideo_mute = ' . $mute);

        HTMLHelper::script('plugins/content/ytvideo/assets/ytvideo.js', [], ['options' => ['version' => 'auto']]);

        if ($this->params->get('includes') == '1') {
            $css = str_replace(JPATH_ROOT, '', dirname($layout) . '/' . basename($layout, '.php') . '.css');
            if (!file_exists(JPATH_ROOT . $css)) {
                $css = 'plugins/content/ytvideo/assets/ytvideo.css';
            }
            $css = str_replace('\\', '/', $css);
            HTMLHelper::stylesheet($css, [], ['options' => ['version' => 'auto']]);
        }

        foreach ($results[1] as $key => $link) {
            $tmp = explode('|', strip_tags($link));

            $link = trim($tmp[0]);
            unset($tmp[0]);

            $ratio = $format;
            if (count($tmp) && isset($tmp[1])) {
                $r_tmp = str_replace([':', ' ', '.'], ['-', '', ''], preg_replace('/[0-9.]-:[0-9]/', '', $tmp[1]));
                if (
                    in_array($r_tmp, [
                        '4-3', '4:3',
                        '5-3', '5:3',
                        '16-9', '16:9',
                        '167-9', '16.7:9',
                        '18-9', '18:9',
                        '199-9', '19.9:9',
                        '235-1', '2.35:1',
                        '255-1', '2.55:1',
                        '27-1', '2.7:1'
                    ])
                ) {
                    $ratio = $r_tmp;
                    unset($tmp[1]);
                }
            }

            $title = '';
            if (count($tmp)) {
                $title = trim(strip_tags(implode(' ', $tmp)));
            }

            $match = [];
            preg_match(
                '~(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})~i',
                $link,
                $match
            );

            $images = ['maxresdefault', 'hq720', 'sddefault', 'hqdefault', 'mqdefault', 'default'];

            if (count($match) > 1) {
                $resultImage = false;
                $id = $match[1];
                $cachedImage = $cachFolder . $id . '.webp';
                if (!file_exists($cachedImage)) {
                    $cachedImage = $cachFolder . $id . '.jpg';
                }
                if (!file_exists($cachedImage)) {
                    foreach ($images as $img) {
                        if ($resultImage) {
                            break;
                        }
                        foreach (['.webp', '.jpg'] as $ext) {
                            $image = 'https://i.ytimg.com/vi/' . $id . '/' . $img . $ext;
                            $headers = get_headers($image);
                            if (is_array($headers) && strpos($headers[0], ' 200') > 0) {
                                $buffer = file_get_contents($image);
                                if ((bool) $buffer !== false) {
                                    $resultImage = true;
                                    if ($cachFolder) {
                                        file_put_contents($cachedImage, $buffer);
                                        $image = Uri::base(true) .
                                            str_replace(
                                                '\\',
                                                '/',
                                                str_replace(Path::clean(JPATH_ROOT), '', $cachedImage)
                                            );
                                    }
                                    break;
                                }
                            }
                        }
                    }
                    if (!$resultImage || !file_exists($cachedImage)) {
                        $image = Uri::base(true) . '/' . $this->params->get(
                            'emptyimg',
                            'plugins/content/ytvideo/assets/empty.webp'
                        );
                    }
                } else {
                    $image = Uri::base(true) .
                        str_replace('\\', '/', str_replace(Path::clean(JPATH_ROOT), '', $cachedImage));
                }

                ob_start();
                include $layout;
                $article->text = str_replace($results[0][$key], ob_get_clean(), $article->text);
            }
        }
        $article->text = str_replace(
            ['<p><div', '</div></p>', "</div>\n</p>", '<p></p>'],
            ['<div', '</div>', '</div>', ''],
            $article->text
        );
    }
}
