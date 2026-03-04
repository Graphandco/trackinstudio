<?php
/**
 * 2007-2024 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 *
 * @author    Régis Daum
 * @copyright 2007-2024
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'trackinstudio_contentblocks` (
    `id_contentblock` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `page` varchar(255) NOT NULL DEFAULT \'\',
    `active` tinyint(1) unsigned NOT NULL DEFAULT 1,
    `image_filename` varchar(255) NOT NULL,
    `date_add` datetime NOT NULL,
    `date_upd` datetime NOT NULL,
    PRIMARY KEY (`id_contentblock`),
    KEY `page` (`page`),
    KEY `active` (`active`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'trackinstudio_contentblocks_lang` (
    `id_contentblock` int(10) unsigned NOT NULL,
    `id_lang` int(10) unsigned NOT NULL,
    `title` varchar(255) NOT NULL,
    `description` text,
    PRIMARY KEY (`id_contentblock`, `id_lang`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}

return true;
