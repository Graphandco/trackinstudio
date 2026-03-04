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

$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'trackinstudio_contentblocks_lang`;';
$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'trackinstudio_contentblocks`;';

foreach ($sql as $query) {
    Db::getInstance()->execute($query);
}

return true;
