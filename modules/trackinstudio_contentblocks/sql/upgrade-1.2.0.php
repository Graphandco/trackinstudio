<?php
/**
 * Upgrade to 1.2.0 - Ajout du slug pour identification stable entre dev/prod
 */

$table_main = _DB_PREFIX_ . 'trackinstudio_contentblocks';

$columns = Db::getInstance()->executeS('SHOW COLUMNS FROM `' . pSQL($table_main) . '`');
$col_names = array_column($columns, 'Field');

if (!in_array('slug', $col_names)) {
    Db::getInstance()->execute('ALTER TABLE `' . pSQL($table_main) . '` ADD `slug` varchar(100) NOT NULL DEFAULT \'temp\' AFTER `id_contentblock`');
    Db::getInstance()->execute('UPDATE `' . pSQL($table_main) . '` SET `slug` = CONCAT(\'block-\', `id_contentblock`)');
    Db::getInstance()->execute('ALTER TABLE `' . pSQL($table_main) . '` MODIFY `slug` varchar(100) NOT NULL');
    Db::getInstance()->execute('ALTER TABLE `' . pSQL($table_main) . '` ADD UNIQUE KEY `slug` (`slug`)');
}

return true;
