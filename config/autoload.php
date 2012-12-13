<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2012 Leo Feyer
 * 
 * @package Faq
 * @link    http://www.contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Modules
	'RestOutput'        => 'system/modules/rest/classes/RestOutput.php',
	'ModuleRest'				=> 'system/modules/rest/modules/ModuleRest.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(

));
