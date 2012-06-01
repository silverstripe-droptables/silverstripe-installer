<?php

global $project;
$project = 'mysite';

global $database;
$database = 'SS_ssexpress';

require_once('conf/ConfigureFromEnv.php');

MySQLDatabase::set_connection_charset('utf8');

SSViewer::set_theme('ssexpress');

i18n::set_locale('en_NZ');
date_default_timezone_set('Pacific/Auckland');

if (class_exists('SiteTree')) SiteTree::enable_nested_urls();

// Don't allow h1 in the editor
HtmlEditorConfig::get('cms')->setOption('theme_advanced_blockformats', 'p,pre,address,h2,h3,h4,h5,h6');
// Add in start and type attributes for ol
HtmlEditorConfig::get('cms')->setOption('extended_valid_elements', 'img[class|src|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name|usemap],iframe[src|name|width|height|title|align|allowfullscreen|frameborder|marginwidth|marginheight|scrolling],object[width|height|data|type],param[name|value],map[class|name|id],area[shape|coords|href|target|alt],ol[start|type]');
// Macrons
HtmlEditorConfig::get('cms')->enablePlugins(array('ssmacron' => '../../../framework/thirdparty/tinymce_ssmacron/editor_plugin_src.js'));
HtmlEditorConfig::get('cms')->insertButtonsAfter('charmap', 'ssmacron');

Email::setAdminEmail('info@localhost');

// PageComment::enableModeration();
// BlogEntry::allow_wysiwyg_editing();
GD::set_default_quality(90);

FulltextSearchable::enable();

Object::add_extension('SiteConfig', 'CustomSiteConfig');
