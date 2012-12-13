<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2012 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  Janosch Skuplik
 * @author     Janosch Skuplik <kontakt@janosch-skuplik.de>
 * @package    rest
 * @license    LGPL
 * @filesource
 */


/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_restauthentification']['title'] 								= array('Titel / Name','Titel oder Name des Clients');
$GLOBALS['TL_LANG']['tl_restauthentification']['authkey'] 							= array('Athentifizierungs-Key','Authentifizierungs-Key, der für diesen Clienten gilt. Dieser sollte möglichst komplex sein.');
$GLOBALS['TL_LANG']['tl_restauthentification']['allowed_fields'] 				= array('Erlaubte Felder','Dem Clienten stehen nur die ausgewählten Felder zur Verfügung.');
$GLOBALS['TL_LANG']['tl_restauthentification']['active']								= array('Client aktivieren','Soll dem Clienten Zugriff auf die API gestattet sein.');


/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_restauthentification']['title_legend'] 					= 'Title und Auth-Key';
$GLOBALS['TL_LANG']['tl_restauthentification']['allowed_fields_legend'] = 'Erlaubte Felder';
$GLOBALS['TL_LANG']['tl_restauthentification']['active_legend'] 				= 'Veröffentlichung / Zugriff';

/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_restauthentification']['new']        						= array('Neuen Client registrieren', 'Einen neuen Client registieren');
$GLOBALS['TL_LANG']['tl_restauthentification']['show']       						= array('Clientdetails', 'Die Details des Clients ID %s anzeigen');
$GLOBALS['TL_LANG']['tl_restauthentification']['edit']       						= array('Client bearbeiten', 'Client ID %s bearbeiten');
$GLOBALS['TL_LANG']['tl_restauthentification']['copy']       						= array('Client duplizieren', 'Client ID %s duplizieren');
$GLOBALS['TL_LANG']['tl_restauthentification']['delete']     						= array('Client löschen', 'Client ID %s löschen');


?>