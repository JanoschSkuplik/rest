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
 * Table tl_comments
 */
$GLOBALS['TL_DCA']['tl_restauthentification'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'enableVersioning'            => true,
		'onload_callback' => array
		(
			array('tl_restauthentification', 'checkPermission')
		)
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 2,
			'fields'                  => array('title DESC'),
			'flag'                    => 8,
			'panelLayout'             => 'filter;sort,search,limit'
		),
		'label' => array
		(
			'fields'                  => array('title'),
			'format'                  => '%s',
			'label_callback'          => array('tl_restauthentification', 'listRestauthentifikations')
		),
		'global_operations' => array
		(
			'all' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'                => 'act=select',
				'class'               => 'header_edit_all',
				'attributes'          => 'onclick="Backend.getScrollOffset()" accesskey="e"'
			)
		),
		'operations' => array
		(
			'edit' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_restauthentification']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif',
				'button_callback'     => array('tl_restauthentification', 'editRestauthentifikations')
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_restauthentification']['copy'],
				'href'                => 'act=copy',
				'icon'                => 'copy.gif'
			),			
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_restauthentification']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
				'button_callback'     => array('tl_restauthentification', 'deleteRestauthentifikations')
			),
			'toggle' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_restauthentification']['toggle'],
				'icon'                => 'visible.gif',
				'attributes'          => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
				'button_callback'     => array('tl_restauthentification', 'toggleIcon')
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_restauthentification']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif'
			)
		)
	),

	// Palettes
	'palettes' => array
	(
		'__selector__'                => array(''),
		'default'                     => '{title_legend},title,authkey;{allowed_fields_legend},allowed_fields;{active_legend},active'
	),

	// Subpalettes
	'subpalettes' => array
	(

	),

	// Fields
	'fields' => array
	(
		'title' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_restauthentification']['title'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('tl_class'=>'w50', 'mandatory'=>true, 'maxlength'=>255)		
		),
		'authkey' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_restauthentification']['authkey'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('tl_class'=>'w50', 'mandatory'=>true, 'maxlength'=>255)		
		),		
		'allowed_fields' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_restauthentification']['allowed_fields'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'options_callback'        => array('tl_restauthentification', 'getExcludedFields'),
			'eval'                    => array('multiple'=>true, 'size'=>36)
		),
		'active' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_restauthentification']['active'],
			'exclude'                 => true,
			'inputType'               => 'checkbox'
		)
	)
);


/**
 * Class tl_comments
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @copyright  Leo Feyer 2005-2012
 * @author     Leo Feyer <http://www.contao.org>
 * @package    Controller
 */
class tl_restauthentification extends Backend
{

	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
	}
	

	/**
	 * Check permissions to edit table tl_comments
	 */
	public function checkPermission()
	{
		switch ($this->Input->get('act'))
		{
			case 'create':
			case 'edit':
			case 'show':
			case 'delete':
			case 'copy':
				break;
			default:
				if (strlen($this->Input->get('act')))
				{
					$this->redirect('contao/main.php?act=error');
				}
				break;
		}
	}

	/**
	 * Return all excluded fields as HTML drop down menu
	 * @return array
	 */
	public function getExcludedFields()
	{
		$included = array();

		foreach ($this->Config->getActiveModules() as $strModule)
		{
			$strDir = sprintf('%s/system/modules/%s/dca/', TL_ROOT, $strModule);

			if (!is_dir($strDir))
			{
				continue;
			}

			foreach (scan($strDir) as $strFile)
			{
				if (in_array($strFile, $included))
				{
					continue;
				}

				$included[] = $strFile;
				$strTable = str_replace('.php', '', $strFile);

				$this->loadLanguageFile($strTable);
				$this->loadDataContainer($strTable);
			}
		}

		$arrReturn = array();

		// Get all excluded fields
		foreach ($GLOBALS['TL_DCA'] as $k=>$v)
		{
			if (is_array($v['fields']))
			{
				foreach ($v['fields'] as $kk=>$vv)
				{
					if ($vv['exclude'] || $vv['orig_exclude'])
					{
						$arrReturn[$k][specialchars($k.'::'.$kk)] = (strlen($vv['label'][0]) ? $vv['label'][0] : $kk);
					}
				}
			}
		}

		// include other tables (like Catalog)
		$arrTables = $this->Database->listTables();
		foreach ($arrTables as $table)
		{
			if (substr($table, 0, 3)!='tl_')
			{
				$arrFields = $this->Database->listFields($table);
				foreach ($arrFields as $field)
				{
					$arrReturn[$table][$table.'::'.$field['name']] = $field['name'];
				}
			}
		}

		ksort($arrReturn);
		return $arrReturn;
	}


	/**
	 * List a particular record
	 * @param array
	 * @return string
	 */
	public function listRestauthentifikations($arrRow)
	{
		
		return '
		 ' . $arrRow['title'] . ' <span style="color:#b3b3b3">[' . $arrRow['authkey'] . ']</span>' . "\n   ";
	}


	/**
	 * Return the edit comment button
	 * @param array
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @return string
	 */
	public function editRestauthentifikations($row, $href, $label, $title, $icon, $attributes)
	{

		return ($this->User->isAdmin) ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.$this->generateImage($icon, $label).'</a> ' : $this->generateImage(preg_replace('/\.gif$/i', '_.gif', $icon)).' ';

	}


	/**
	 * Return the delete comment button
	 * @param array
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @return string
	 */
	public function deleteRestauthentifikations($row, $href, $label, $title, $icon, $attributes)
	{
		return ($this->User->isAdmin) ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.$this->generateImage($icon, $label).'</a> ' : $this->generateImage(preg_replace('/\.gif$/i', '_.gif', $icon)).' ';
	}


	/**
	 * Return the "toggle visibility" button
	 * @param array
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @return string
	 */
	public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
	{
		return '';
	}


	/**
	 * Disable/enable a user group
	 * @param integer
	 * @param boolean
	 */
	public function toggleVisibility($intId, $blnVisible)
	{
		
	}
}

?>