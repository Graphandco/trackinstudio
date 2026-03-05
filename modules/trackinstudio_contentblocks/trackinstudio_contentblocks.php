<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class Trackinstudio_Contentblocks extends Module
{
    public function __construct()
    {
        $this->name = 'trackinstudio_contentblocks';
        $this->version = '1.1.0';
        $this->author = 'Graph and Co';
        $this->tab = 'others';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Trackinstudio - Blocs de contenu');
        $this->description = $this->l('Gestion de blocs de contenu avec titre, description et image. Affichage par ID avec templates multiples.');
        $this->confirmUninstall = $this->l('Etes-vous sûr de vouloir désinstaller le module ?');
        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];
    }

    public function install(): bool
    {
        include(dirname(__FILE__).'/sql/install.php');
        Configuration::updateValue('TRACKINSTUDIO_CONTENTBLOCKS_VERSION', $this->version);

        return parent::install()
            && $this->registerHook('displayContentBlock')
            && $this->registerHook('displayContentBlocks');
    }

    public function uninstall(): bool
    {
        $this->deleteAllImages();
        include(dirname(__FILE__).'/sql/uninstall.php');
        Configuration::deleteByName('TRACKINSTUDIO_CONTENTBLOCKS_VERSION');

        return parent::uninstall();
    }

    private function runUpgrade()
    {
        $installed_version = Configuration::get('TRACKINSTUDIO_CONTENTBLOCKS_VERSION');
        if (version_compare($installed_version, '1.1.0', '<')) {
            include(dirname(__FILE__).'/sql/upgrade-1.1.0.php');
            Configuration::updateValue('TRACKINSTUDIO_CONTENTBLOCKS_VERSION', $this->version);
        }
    }

    public function getContent()
    {
        $this->runUpgrade();
        $output = '';

        if (Tools::isSubmit('statustrackinstudio_contentblocks')) {
            $id_contentblock = (int)Tools::getValue('id_contentblock');
            if ($id_contentblock > 0) {
                $block = $this->getBlockById($id_contentblock);
                if ($block) {
                    $new_status = $block['active'] ? 0 : 1;
                    $sql = 'UPDATE `'._DB_PREFIX_.'trackinstudio_contentblocks` 
                            SET `active` = '.(int)$new_status.' 
                            WHERE `id_contentblock` = '.(int)$id_contentblock;
                    if (Db::getInstance()->execute($sql)) {
                        $output .= $this->displayConfirmation($this->l('Statut mis à jour avec succès.'));
                    }
                }
            }
        }

        if (Tools::isSubmit('submitContentblock')) {
            $id_contentblock = (int)Tools::getValue('id_contentblock');
            $active = (int)Tools::getValue('active');
            $page = pSQL(Tools::getValue('page'));
            $button_target_combo = pSQL(Tools::getValue('button_target'));
            list($button_type, $button_target) = $this->parseButtonTargetCombo($button_target_combo);
            $languages = Language::getLanguages(false);
            $image_filename = '';

            if ($id_contentblock > 0) {
                $block = $this->getBlockById($id_contentblock);
                if ($block) {
                    $image_filename = $block['image_filename'];
                }
            }

            if (isset($_FILES['block_image']) && $_FILES['block_image']['error'] === UPLOAD_ERR_OK) {
                $result = $this->processImageUpload($id_contentblock);
                if (!$result['success']) {
                    $output .= $this->displayError($result['message']);
                } else {
                    $image_filename = $result['filename'];
                }
            }

            if ($id_contentblock > 0) {
                $sql = 'UPDATE `'._DB_PREFIX_.'trackinstudio_contentblocks` 
                        SET `active` = '.(int)$active.', 
                            `page` = \''.pSQL($page).'\', 
                            `image_filename` = \''.pSQL($image_filename).'\',
                            `button_type` = \''.pSQL($button_type).'\',
                            `button_target` = \''.pSQL($button_target).'\',
                            `date_upd` = NOW()
                        WHERE `id_contentblock` = '.(int)$id_contentblock;
                if (!Db::getInstance()->execute($sql)) {
                    $output .= $this->displayError($this->l('Erreur lors de la mise à jour du bloc.'));
                } else {
                    foreach ($languages as $language) {
                        $title = Tools::getValue('title_'.$language['id_lang']);
                        $description = Tools::getValue('description_'.$language['id_lang']);

                        $sql = 'SELECT * FROM `'._DB_PREFIX_.'trackinstudio_contentblocks_lang` 
                                WHERE `id_contentblock` = '.(int)$id_contentblock.' AND `id_lang` = '.(int)$language['id_lang'];
                        $existing = Db::getInstance()->getRow($sql);

                        $button_label = Tools::getValue('button_label_'.$language['id_lang']);
                        if ($existing) {
                            $sql = 'UPDATE `'._DB_PREFIX_.'trackinstudio_contentblocks_lang` 
                                    SET `title` = \''.pSQL($title, true).'\',
                                        `description` = \''.pSQL($description, true).'\',
                                        `button_label` = \''.pSQL($button_label, true).'\'
                                    WHERE `id_contentblock` = '.(int)$id_contentblock.' AND `id_lang` = '.(int)$language['id_lang'];
                        } else {
                            $sql = 'INSERT INTO `'._DB_PREFIX_.'trackinstudio_contentblocks_lang` 
                                    (`id_contentblock`, `id_lang`, `title`, `description`, `button_label`) 
                                    VALUES ('.(int)$id_contentblock.', '.(int)$language['id_lang'].', \''.pSQL($title, true).'\', \''.pSQL($description, true).'\', \''.pSQL($button_label, true).'\')';
                        }
                        Db::getInstance()->execute($sql);
                    }
                    $output .= $this->displayConfirmation($this->l('Bloc mis à jour avec succès.'));
                }
            } else {
                $sql = 'INSERT INTO `'._DB_PREFIX_.'trackinstudio_contentblocks` 
                        (`page`, `active`, `image_filename`, `button_type`, `button_target`, `date_add`, `date_upd`) 
                        VALUES (\''.pSQL($page).'\', '.(int)$active.', \''.pSQL($image_filename).'\', \''.pSQL($button_type).'\', \''.pSQL($button_target).'\', NOW(), NOW())';
                if (!Db::getInstance()->execute($sql)) {
                    $output .= $this->displayError($this->l('Erreur lors de l\'ajout du bloc.'));
                } else {
                    $id_contentblock = Db::getInstance()->Insert_ID();
                    if (!empty($image_filename)) {
                        $sql = 'UPDATE `'._DB_PREFIX_.'trackinstudio_contentblocks` 
                                SET `image_filename` = \''.pSQL($image_filename).'\'
                                WHERE `id_contentblock` = '.(int)$id_contentblock;
                        Db::getInstance()->execute($sql);
                    }
                    foreach ($languages as $language) {
                        $title = Tools::getValue('title_'.$language['id_lang']);
                        $description = Tools::getValue('description_'.$language['id_lang']);
                        $button_label = Tools::getValue('button_label_'.$language['id_lang']);

                        $sql = 'INSERT INTO `'._DB_PREFIX_.'trackinstudio_contentblocks_lang` 
                                (`id_contentblock`, `id_lang`, `title`, `description`, `button_label`) 
                                VALUES ('.(int)$id_contentblock.', '.(int)$language['id_lang'].', \''.pSQL($title, true).'\', \''.pSQL($description, true).'\', \''.pSQL($button_label, true).'\')';
                        Db::getInstance()->execute($sql);
                    }
                    $output .= $this->displayConfirmation($this->l('Bloc ajouté avec succès.'));
                }
            }
        }

        if (Tools::isSubmit('deletetrackinstudio_contentblocks') || Tools::getValue('deletetrackinstudio_contentblocks')) {
            $id_contentblock = (int)Tools::getValue('id_contentblock');
            if ($id_contentblock > 0 && $this->deleteBlock($id_contentblock)) {
                $output .= $this->displayConfirmation($this->l('Bloc supprimé avec succès.'));
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true) . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules'));
            } else {
                $output .= $this->displayError($this->l('Erreur lors de la suppression du bloc.'));
            }
        }

        if (Tools::getValue('id_contentblock') !== false && !Tools::isSubmit('deletetrackinstudio_contentblocks') && !Tools::getValue('deletetrackinstudio_contentblocks')) {
            $id_contentblock = (int)Tools::getValue('id_contentblock');
            if ($id_contentblock >= 0) {
                return $output . $this->renderForm();
            }
        }

        return $output . $this->renderList();
    }

    /**
     * Affiche un bloc par son ID (méthode publique pour appel direct)
     *
     * @param int $id_contentblock ID du bloc
     * @param string $template Nom du template (sans .tpl, ex: contentblocks-default)
     * @return string HTML du bloc
     */
    public function renderBlockById($id_contentblock, $template = 'contentblocks-default')
    {
        $block = $this->getBlockById($id_contentblock);
        if (!$block || !$block['active']) {
            return '';
        }

        $id_lang = $this->context->language->id;
        $translations = isset($block['translations'][$id_lang]) ? $block['translations'][$id_lang] : reset($block['translations']);
        if (!$translations || empty($translations['title'])) {
            return '';
        }

        $block['title'] = $translations['title'];
        $block['description'] = $translations['description'];
        $block['button_label'] = $translations['button_label'] ?? '';
        $block['button_url'] = $this->buildButtonUrl(
            $block['button_type'] ?? '',
            $block['button_target'] ?? '',
            $id_lang
        );

        $this->context->smarty->assign([
            'block' => $block,
            'images_dir' => $this->_path . 'img/',
        ]);

        $template_path = 'views/templates/hook/' . $template . '.tpl';
        if (!file_exists(dirname(__FILE__) . '/' . $template_path)) {
            $template_path = 'views/templates/hook/contentblocks-default.tpl';
        }

        return $this->display(__FILE__, $template_path);
    }

    /**
     * Affiche les blocs d'une page donnée
     *
     * @param string $page Identifiant de la page
     * @param string $template Nom du template
     * @return string HTML des blocs
     */
    public function renderBlocksByPage($page, $template = 'contentblocks-default')
    {
        $id_lang = $this->context->language->id;

        $sql = 'SELECT b.*, bl.`title`, bl.`description`, bl.`button_label` 
                FROM `'._DB_PREFIX_.'trackinstudio_contentblocks` b
                LEFT JOIN `'._DB_PREFIX_.'trackinstudio_contentblocks_lang` bl ON (b.`id_contentblock` = bl.`id_contentblock` AND bl.`id_lang` = '.(int)$id_lang.')
                WHERE b.`active` = 1 AND b.`page` = \''.pSQL($page).'\'
                ORDER BY b.`id_contentblock` ASC';

        $blocks = Db::getInstance()->executeS($sql);

        if (!$blocks) {
            return '';
        }

        $blocks = array_filter($blocks, function($block) {
            return !empty($block['title']);
        });

        foreach ($blocks as &$block) {
            $block['button_url'] = $this->buildButtonUrl(
                $block['button_type'] ?? '',
                $block['button_target'] ?? '',
                $id_lang
            );
        }

        $this->context->smarty->assign([
            'blocks' => $blocks,
            'images_dir' => $this->_path . 'img/',
        ]);

        $template_path = 'views/templates/hook/' . $template . '.tpl';
        if (!file_exists(dirname(__FILE__) . '/' . $template_path)) {
            $template_path = 'views/templates/hook/contentblocks-default.tpl';
        }

        return $this->display(__FILE__, $template_path);
    }

    private function renderList()
    {
        $blocks = $this->getAllBlocks();
        $languages = Language::getLanguages(false);

        $fields_list = [
            'id_contentblock' => [
                'title' => $this->l('ID'),
                'width' => 50,
                'type' => 'text',
            ],
            'title' => [
                'title' => $this->l('Titre'),
                'width' => 'auto',
                'type' => 'text',
            ],
            'page' => [
                'title' => $this->l('Page'),
                'width' => 150,
                'type' => 'text',
            ],
            'active' => [
                'title' => $this->l('Actif'),
                'width' => 50,
                'active' => 'status',
                'type' => 'bool',
            ],
        ];

        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->identifier = 'id_contentblock';
        $helper->actions = ['edit', 'delete'];
        $helper->show_toolbar = true;
        $helper->toolbar_btn['new'] = [
            'href' => $this->context->link->getAdminLink('AdminModules', true) . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&id_contentblock=0',
            'desc' => $this->l('Ajouter un bloc')
        ];
        $helper->title = $this->l('Liste des blocs');
        $helper->table = 'trackinstudio_contentblocks';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules');

        $list = [];
        foreach ($blocks as $block) {
            $title = '';
            foreach ($languages as $lang) {
                if (isset($block['translations'][$lang['id_lang']])) {
                    $title = $block['translations'][$lang['id_lang']]['title'];
                    break;
                }
            }
            if (empty($title)) {
                $title = $this->l('(Sans titre)');
            }

            $list[] = [
                'id_contentblock' => $block['id_contentblock'],
                'title' => $title,
                'page' => $block['page'],
                'active' => $block['active'],
            ];
        }

        return $helper->generateList($list, $fields_list);
    }

    private function renderForm()
    {
        $id_contentblock = (int)Tools::getValue('id_contentblock');
        $languages = Language::getLanguages(false);
        $default_language = (int)Configuration::get('PS_LANG_DEFAULT');

        $block_data = null;
        if ($id_contentblock > 0) {
            $block_data = $this->getBlockById($id_contentblock);
        }

        $default_values = [
            'id_contentblock' => $id_contentblock,
            'active' => $block_data ? $block_data['active'] : 1,
            'page' => $block_data ? $block_data['page'] : '',
            'button_target' => $block_data && !empty($block_data['button_type']) && $block_data['button_target'] !== ''
                ? $block_data['button_type'] . '_' . $block_data['button_target']
                : '',
        ];

        foreach ($languages as $language) {
            $default_values['title'][$language['id_lang']] = $block_data && isset($block_data['translations'][$language['id_lang']]) 
                ? $block_data['translations'][$language['id_lang']]['title'] 
                : '';
            $default_values['description'][$language['id_lang']] = $block_data && isset($block_data['translations'][$language['id_lang']]) 
                ? $block_data['translations'][$language['id_lang']]['description'] 
                : '';
            $default_values['button_label'][$language['id_lang']] = $block_data && isset($block_data['translations'][$language['id_lang']]) 
                ? ($block_data['translations'][$language['id_lang']]['button_label'] ?? '') 
                : '';
        }

        $button_options = $this->getButtonTargetOptions($default_language);

        $image_url = '';
        if ($block_data && !empty($block_data['image_filename'])) {
            $thumbnail_path = dirname(__FILE__) . '/img/miniature-' . $block_data['image_filename'];
            if (file_exists($thumbnail_path)) {
                $image_url = $this->_path . 'img/miniature-' . $block_data['image_filename'];
            } else {
                $image_url = $this->_path . 'img/' . $block_data['image_filename'];
            }
        }

        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $id_contentblock > 0 ? $this->l('Modifier le bloc') : $this->l('Ajouter un bloc'),
                    'icon' => 'icon-picture'
                ],
                'input' => [
                    [
                        'type' => 'hidden',
                        'name' => 'id_contentblock',
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Page'),
                        'name' => 'page',
                        'desc' => $this->l('Identifiant de la page où afficher le bloc (ex: home, contact). Peut être laissé vide.'),
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Actif'),
                        'name' => 'active',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Oui')
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Non')
                            ]
                        ],
                    ],
                    [
                        'type' => 'file',
                        'label' => $this->l('Image'),
                        'name' => 'block_image',
                        'thumb' => $image_url,
                        'desc' => $this->l('Image du bloc (JPG, PNG, GIF, WebP - max 5MB)'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Titre'),
                        'name' => 'title',
                        'lang' => true,
                        'required' => true,
                    ],
                    [
                        'type' => 'textarea',
                        'label' => $this->l('Description'),
                        'name' => 'description',
                        'lang' => true,
                        'required' => true,
                        'autoload_rte' => true,
                        'rows' => 10,
                        'cols' => 100,
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Bouton - Cible du lien'),
                        'name' => 'button_target',
                        'desc' => $this->l('Sélectionnez une page, catégorie ou fiche produit pour le bouton (optionnel).'),
                        'options' => $button_options,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Texte du bouton'),
                        'name' => 'button_label',
                        'lang' => true,
                        'desc' => $this->l('Texte affiché sur le bouton (par langue).'),
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Enregistrer'),
                    'class' => 'btn btn-default pull-right'
                ],
                'buttons' => [
                    [
                        'href' => $this->context->link->getAdminLink('AdminModules', true) . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                        'title' => $this->l('Retour à la liste'),
                        'icon' => 'process-icon-back'
                    ]
                ]
            ],
        ];

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = 'trackinstudio_contentblocks';
        $helper->module = $this;
        $helper->default_form_language = $default_language;
        $helper->allow_employee_form_lang = $default_language;
        $helper->identifier = 'id_contentblock';
        $helper->submit_action = 'submitContentblock';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules');
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'fields_value' => $default_values,
            'languages' => $languages,
            'id_language' => $default_language,
        ];

        return $helper->generateForm([$fields_form]);
    }

    private function processImageUpload($id_contentblock = 0)
    {
        if (!isset($_FILES['block_image']) || $_FILES['block_image']['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => $this->l('Erreur lors de l\'upload du fichier.')];
        }

        $file = $_FILES['block_image'];
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime_type, $allowed_types)) {
            return ['success' => false, 'message' => $this->l('Le fichier doit être une image (JPG, PNG, GIF ou WebP).')];
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            return ['success' => false, 'message' => $this->l('Le fichier est trop volumineux (maximum 5MB).')];
        }

        $img_dir = dirname(__FILE__) . '/img/';
        if (!file_exists($img_dir)) {
            if (!mkdir($img_dir, 0755, true)) {
                return ['success' => false, 'message' => $this->l('Impossible de créer le dossier de destination.')];
            }
        }

        if ($id_contentblock > 0) {
            $block = $this->getBlockById($id_contentblock);
            if ($block && !empty($block['image_filename'])) {
                $old_file_path = $img_dir . $block['image_filename'];
                $old_thumbnail_path = $img_dir . 'miniature-' . $block['image_filename'];
                if (file_exists($old_file_path)) {
                    @unlink($old_file_path);
                }
                if (file_exists($old_thumbnail_path)) {
                    @unlink($old_thumbnail_path);
                }
            }
        }

        $file_info = pathinfo($file['name']);
        $extension = strtolower($file_info['extension']);
        $filename = 'block_' . uniqid() . '_' . time() . '.' . $extension;
        $destination = $img_dir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return ['success' => false, 'message' => $this->l('Impossible de déplacer le fichier uploadé.')];
        }

        $thumbnail_filename = 'miniature-' . $filename;
        $thumbnail_path = $img_dir . $thumbnail_filename;
        ImageManager::resize($destination, $thumbnail_path, 300, 200);

        if ($id_contentblock > 0) {
            $sql = 'UPDATE `'._DB_PREFIX_.'trackinstudio_contentblocks` 
                    SET `image_filename` = \''.pSQL($filename).'\', `date_upd` = NOW()
                    WHERE `id_contentblock` = '.(int)$id_contentblock;
            if (!Db::getInstance()->execute($sql)) {
                @unlink($destination);
                return ['success' => false, 'message' => $this->l('Erreur lors de l\'enregistrement en base de données.')];
            }
        }

        return ['success' => true, 'message' => $this->l('Image uploadée avec succès.'), 'filename' => $filename];
    }

    private function getAllBlocks()
    {
        $sql = 'SELECT b.* FROM `'._DB_PREFIX_.'trackinstudio_contentblocks` b 
                ORDER BY b.`id_contentblock` ASC';
        $blocks = Db::getInstance()->executeS($sql);

        if (!$blocks) {
            return [];
        }

        foreach ($blocks as &$block) {
            $block['translations'] = $this->getBlockTranslations($block['id_contentblock']);
        }

        return $blocks;
    }

    /**
     * Récupère un bloc par son ID (méthode publique pour usage externe)
     */
    public function getBlockById($id_contentblock)
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'trackinstudio_contentblocks` WHERE `id_contentblock` = '.(int)$id_contentblock;
        $block = Db::getInstance()->getRow($sql);

        if ($block) {
            $block['translations'] = $this->getBlockTranslations($id_contentblock);
        }

        return $block;
    }

    private function getBlockTranslations($id_contentblock)
    {
        $sql = 'SELECT * FROM `'._DB_PREFIX_.'trackinstudio_contentblocks_lang` WHERE `id_contentblock` = '.(int)$id_contentblock;
        $translations = Db::getInstance()->executeS($sql);

        $result = [];
        foreach ($translations as $translation) {
            $result[$translation['id_lang']] = [
                'title' => $translation['title'],
                'description' => $translation['description'],
                'button_label' => isset($translation['button_label']) ? $translation['button_label'] : '',
            ];
        }

        return $result;
    }

    private function parseButtonTargetCombo($combo)
    {
        if (empty($combo) || strpos($combo, '_') === false) {
            return ['', ''];
        }
        $parts = explode('_', $combo, 2);
        return [$parts[0], $parts[1]];
    }

    private function getButtonTargetOptions($id_lang)
    {
        $options = [['id' => '', 'name' => $this->l('-- Aucun bouton --')]];
        $prefix_page = $this->l('[Page] ');
        $prefix_cat = $this->l('[Catégorie] ');
        $prefix_prod = $this->l('[Produit] ');

        if (class_exists('CMS')) {
            $sql = 'SELECT c.id_cms, l.meta_title FROM `'._DB_PREFIX_.'cms` c
                    INNER JOIN `'._DB_PREFIX_.'cms_lang` l ON c.id_cms = l.id_cms AND l.id_lang = '.(int)$id_lang.'
                    WHERE c.active = 1 ORDER BY l.meta_title ASC';
            $cms_pages = Db::getInstance()->executeS($sql);
            if ($cms_pages) {
                foreach ($cms_pages as $row) {
                    $options[] = ['id' => 'page_'.(int)$row['id_cms'], 'name' => $prefix_page.$row['meta_title']];
                }
            }
        }

        $system_pages = [
            'contact' => $this->l('Contact'),
            'cart' => $this->l('Panier'),
            'my-account' => $this->l('Mon compte'),
            'sitemap' => $this->l('Plan du site'),
            'index' => $this->l('Accueil'),
        ];
        foreach ($system_pages as $controller => $label) {
            $options[] = ['id' => 'page_'.$controller, 'name' => $prefix_page.$label];
        }

        $sql_cat = 'SELECT c.id_category, cl.name FROM `'._DB_PREFIX_.'category` c
                    INNER JOIN `'._DB_PREFIX_.'category_lang` cl ON c.id_category = cl.id_category AND cl.id_lang = '.(int)$id_lang.'
                    WHERE c.active = 1 AND c.id_category NOT IN (1, 2) ORDER BY cl.name ASC LIMIT 300';
        $categories = Db::getInstance()->executeS($sql_cat);
        if ($categories) {
            foreach ($categories as $cat) {
                $options[] = ['id' => 'category_'.(int)$cat['id_category'], 'name' => $prefix_cat.$cat['name']];
            }
        }

        $sql = 'SELECT p.id_product, pl.name FROM `'._DB_PREFIX_.'product` p
                INNER JOIN `'._DB_PREFIX_.'product_lang` pl ON p.id_product = pl.id_product AND pl.id_lang = '.(int)$id_lang.'
                WHERE p.active = 1 ORDER BY pl.name ASC LIMIT 300';
        $products = Db::getInstance()->executeS($sql);
        if ($products) {
            foreach ($products as $prod) {
                $options[] = ['id' => 'product_'.(int)$prod['id_product'], 'name' => $prefix_prod.$prod['name']];
            }
        }

        return [
            'query' => $options,
            'id' => 'id',
            'name' => 'name',
        ];
    }

    private function buildButtonUrl($button_type, $button_target, $id_lang)
    {
        if (empty($button_type) || $button_target === '' || $button_target === null) {
            return '';
        }
        $link = $this->context->link;
        try {
            if ($button_type === 'page') {
                if (is_numeric($button_target)) {
                    $cms = new CMS((int)$button_target, $id_lang);
                    if (Validate::isLoadedObject($cms)) {
                        return $link->getCMSLink($cms, null, null, $id_lang);
                    }
                } else {
                    return $link->getPageLink($button_target, true, $id_lang, [], false);
                }
            } elseif ($button_type === 'category') {
                $category = new Category((int)$button_target, $id_lang);
                if (Validate::isLoadedObject($category)) {
                    return $link->getCategoryLink($category, null, $id_lang);
                }
            } elseif ($button_type === 'product') {
                $product = new Product((int)$button_target, false, $id_lang);
                if (Validate::isLoadedObject($product)) {
                    return $link->getProductLink($product, null, null, null, false, false, $id_lang);
                }
            }
        } catch (Exception $e) {
        }
        return '';
    }

    private function deleteBlock($id_contentblock)
    {
        $block = $this->getBlockById($id_contentblock);

        if ($block && !empty($block['image_filename'])) {
            $file_path = dirname(__FILE__) . '/img/' . $block['image_filename'];
            $thumbnail_path = dirname(__FILE__) . '/img/miniature-' . $block['image_filename'];
            if (file_exists($file_path)) {
                @unlink($file_path);
            }
            if (file_exists($thumbnail_path)) {
                @unlink($thumbnail_path);
            }
        }

        $sql = 'DELETE FROM `'._DB_PREFIX_.'trackinstudio_contentblocks_lang` WHERE `id_contentblock` = '.(int)$id_contentblock;
        Db::getInstance()->execute($sql);

        $sql = 'DELETE FROM `'._DB_PREFIX_.'trackinstudio_contentblocks` WHERE `id_contentblock` = '.(int)$id_contentblock;
        return Db::getInstance()->execute($sql);
    }

    private function deleteAllImages()
    {
        try {
            $table_name = _DB_PREFIX_ . 'trackinstudio_contentblocks';
            $sql = 'SHOW TABLES LIKE \'' . pSQL($table_name) . '\'';
            $table_exists = Db::getInstance()->getValue($sql);

            if ($table_exists) {
                $sql = 'SELECT * FROM `' . $table_name . '`';
                $blocks = Db::getInstance()->executeS($sql);
                if ($blocks) {
                    foreach ($blocks as $block) {
                        if (isset($block['image_filename']) && !empty($block['image_filename'])) {
                            $file_path = dirname(__FILE__) . '/img/' . $block['image_filename'];
                            $thumbnail_path = dirname(__FILE__) . '/img/miniature-' . $block['image_filename'];
                            if (file_exists($file_path)) {
                                @unlink($file_path);
                            }
                            if (file_exists($thumbnail_path)) {
                                @unlink($thumbnail_path);
                            }
                        }
                    }
                }
            }
        } catch (Exception $e) {
        }
    }

    /**
     * Hook displayContentBlock - Affiche un bloc par ID (id_contentblock doit être assigné dans le template/contexte)
     */
    public function hookDisplayContentBlock($params)
    {
        $id_contentblock = isset($params['id_contentblock']) ? (int)$params['id_contentblock'] : 0;
        if ($id_contentblock === 0) {
            $id_contentblock = (int)$this->context->smarty->getTemplateVars('id_contentblock');
        }
        $template = isset($params['template']) ? $params['template'] : 'contentblocks-default';

        if ($id_contentblock > 0) {
            return $this->renderBlockById($id_contentblock, $template);
        }

        return '';
    }

    /**
     * Hook displayContentBlocks - Affiche les blocs d'une page (page doit être assignée)
     */
    public function hookDisplayContentBlocks($params)
    {
        $page = isset($params['page']) ? $params['page'] : $this->context->smarty->getTemplateVars('contentblocks_page');
        $template = isset($params['template']) ? $params['template'] : 'contentblocks-default';

        if (!empty($page)) {
            return $this->renderBlocksByPage($page, $template);
        }

        return '';
    }
}
