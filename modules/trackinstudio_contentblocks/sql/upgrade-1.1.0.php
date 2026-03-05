<?php
/**
 * Upgrade to 1.1.0 - Ajout du bouton configurable
 */

$table_main = _DB_PREFIX_ . 'trackinstudio_contentblocks';
$table_lang = _DB_PREFIX_ . 'trackinstudio_contentblocks_lang';

// Ajouter button_type et button_target à la table principale si absents
$columns = Db::getInstance()->executeS('SHOW COLUMNS FROM `' . pSQL($table_main) . '`');
$col_names = array_column($columns, 'Field');

if (!in_array('button_type', $col_names)) {
    Db::getInstance()->execute('ALTER TABLE `' . pSQL($table_main) . '` ADD `button_type` varchar(20) NOT NULL DEFAULT \'\'');
}
if (!in_array('button_target', $col_names)) {
    Db::getInstance()->execute('ALTER TABLE `' . pSQL($table_main) . '` ADD `button_target` varchar(255) NOT NULL DEFAULT \'\'');
}

// Ajouter button_label à la table lang si absent
$columns_lang = Db::getInstance()->executeS('SHOW COLUMNS FROM `' . pSQL($table_lang) . '`');
$col_lang_names = array_column($columns_lang, 'Field');

if (!in_array('button_label', $col_lang_names)) {
    Db::getInstance()->execute('ALTER TABLE `' . pSQL($table_lang) . '` ADD `button_label` varchar(255) NOT NULL DEFAULT \'\'');
}

return true;
