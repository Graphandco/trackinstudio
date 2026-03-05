# Trackinstudio Content Blocks Module

Module PrestaShop pour la gestion de blocs de contenu avec titre, description formatée et image.

## Fonctionnalités

- Gestion complète des blocs depuis le backoffice
- **Bouton configurable** : lien vers une page CMS, catégorie ou fiche produit avec texte personnalisable par langue
- Support multilingue pour les titres et descriptions
- Éditeur WYSIWYG (TinyMCE) natif de PrestaShop pour formater les descriptions
- Upload et gestion d'images pour chaque bloc
- Champ **page** (texte) pour organiser les blocs par page
- **Slug** : identifiant stable pour cohérence dev/prod (ne pas modifier après création)
- **Affichage par slug ou ID** : possibilité d'afficher un bloc par son slug (recommandé) ou son ID
- **Templates multiples** : création de templates alternatifs pour différents designs
- Activation/désactivation des blocs

## Installation

1. Copier le dossier `trackinstudio_contentblocks` dans le répertoire `modules/` de PrestaShop
2. Aller dans le backoffice > Modules > Modules Manager
3. Rechercher "Trackinstudio - Blocs de contenu"
4. Cliquer sur "Installer"

## Configuration

Les styles sont gérés dans le thème, pas dans le module. Le fichier `themes/trackinstudio/src/scss/modules/_contentblocks.scss` contient les styles par défaut.

## Affichage d'un bloc

### Par slug (recommandé, cohérent entre dev et prod)

```php
$module = Module::getInstanceByName('trackinstudio_contentblocks');
echo $module->renderBlockBySlug('hero-accueil');
echo $module->renderBlockBySlug('hero-accueil', 'contentblocks-compact');
```

```smarty
{hook h='displayContentBlock' slug='hero-accueil'}
{hook h='displayContentBlock' slug='hero-accueil' template='contentblocks-compact'}
```

### Par ID (toujours supporté)

```php
echo $module->renderBlockById(1);
```

```smarty
{hook h='displayContentBlock' id_contentblock=1}
```

### Blocs d'une page

```php
$module = Module::getInstanceByName('trackinstudio_contentblocks');
echo $module->renderBlocksByPage('home');  // Tous les blocs dont page = 'home'
```

```smarty
{assign var="contentblocks_page" value="home"}
{hook h='displayContentBlocks'}
```

## Templates

- `contentblocks-default.tpl` : template par défaut (design alterné image gauche/droite)
- `contentblocks-compact.tpl` : template alternatif compact

Pour créer un nouveau template, ajoutez un fichier `contentblocks-votredesign.tpl` dans `views/templates/hook/` et appelez :
`$module->renderBlockById($id, 'contentblocks-votredesign');`

## Structure des fichiers

```
trackinstudio_contentblocks/
├── trackinstudio_contentblocks.php    # Fichier principal
├── sql/
│   ├── install.php
│   └── uninstall.php
├── views/templates/hook/
│   ├── contentblocks-default.tpl
│   └── contentblocks-compact.tpl
├── img/                               # Images uploadées
├── translations/fr.php
├── config.xml
└── README.md
```

## Base de données

- `ps_trackinstudio_contentblocks` : table principale (id_contentblock, slug, page, active, image_filename)
- `ps_trackinstudio_contentblocks_lang` : traductions

## Auteur

Graph and Co
