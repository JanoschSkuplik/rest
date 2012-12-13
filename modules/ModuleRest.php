<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2012 Leo Feyer
 * 
 * @package News
 * @link    http://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Run in a custom namespace, so the class can be replaced
 */


/**
 * Class ModuleNewsReader
 *
 * Front end module "news reader".
 * @copyright  Leo Feyer 2005-2012
 * @author     Leo Feyer <http://contao.org>
 * @package    News
 */
class ModuleRest extends \Module
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'ce_html';
	protected $strTable = false;
	protected $strOffset = 0;
	protected $strLimit = 99999;
	protected $arrWhere = array();
	protected $arrOrder = array();
	protected $useModel = false;

	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### MODULE REST ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		if (Input::get('table'))
		{
			$this->strTable = Input::get('table');
		}
		if (Input::get('limit'))
		{
			$this->strLimit = Input::get('limit');
		}		
		if (Input::get('offset'))
		{
			$this->strOffset = Input::get('offset');
		}	
		if (Input::get('where'))
		{
			$this->arrWhere = Input::get('where');
		}
		if (Input::get('useModel'))
		{
			$this->useModel = true;
		}
		
		// add tl-table-prefix
		//$this->table = $this->table ? ('tl_' . $this->table) : false;
		define('FE_USER_LOGGED_IN', $this->getLoginStatus('FE_USER_AUTH'));

		return parent::generate();
	}


	/**
	 * Generate the module
	 */
	protected function compile()
	{
		// check if token is in get-parameters
		if (!Input::get('authtoken'))
		{
			$this->echoError('Kein Authentifizierungs-Token vorhanden.');
		}
		
		// check the auth-token
		$objAuthtoken = $this->Database->prepare("SELECT * FROM tl_restauthentification WHERE authkey=? AND active=1")
																	 ->limit(1)
																	 ->execute(Input::post('authtoken') ? Input::post('authtoken') : Input::get('authtoken'));
		if (!$objAuthtoken->numRows)
		{
			$this->echoError('Ungültiger Authentifizierungs-Token.');
		}
		if ($this->useModel)
		{
			$modelClass = $this->getModelClassFromTable($this->strTable);
			$modelDaten = $modelClass::findAll();
			while ($modelDaten->next()) 
			{
				$arrTable[] = $modelDaten->row();
			}
		}
		elseif ($this->strTable)
		{
			// generate the array for allowed table-cols
			$arrAllowedFields = array();
			foreach (deserialize($objAuthtoken->allowed_fields) as $allowedField)
			{
				$entryAllowedField = explode('::',$allowedField);
				$arrAllowedFields[$entryAllowedField['0']][$entryAllowedField['1']] = $entryAllowedField['1'];
				}

				// check if table exists
				if(!$this->Database->tableExists($this->strTable))
				{
					$this->echoError('Tabelle nicht gefunden.');
				}

				// check col-rights
				if (sizeof($arrAllowedFields[$this->strTable]) < 1)
				{
					$this->echoError('Keine Felder für diese Tabelle erlaubt.');
				}
				$strFields = '';
				$strFields = 'id,' . implode(',',$arrAllowedFields[$this->strTable]);

				// check if table has field published
				//if ($this->Database->listFields($this->table))
				if ($this->Database->fieldExists('published', $this->strTable))
				{
					$this->arrWhere['published'] = '1';// = ' WHERE published=1';
				}

				// get the table-entries				
				$objTable = $this->Database->prepare("SELECT " . $strFields . " FROM " . $this->strTable . $this->where . " ORDER BY tstamp")
															 		 ->limit($this->strLimit,$this->strOffset)
															 		 ->execute($this->table);		 
				//$arrTable = $objTable->fetchAllAssoc();		
				while ($objTable->next())
				{
					$objOutput = $objTable->row();

					if ($this->strTable == 'tl_news' || $this->strTable == 'tl_article' || $this->strTable == 'tl_events')
					{
						$strText = '';
						$objElement = \ContentModel::findPublishedByPidAndTable($objTable->id, $this->strTable);
						if ($objElement !== null)
						{
							while ($objElement->next())
							{
								$strText .= $this->getContentElement($objElement->id);
							}
						}
						$objOutput['text'] = $strText;
					}
					$arrTable[] = $objOutput;
				}
		} 
		elseif ($this->Input->get('module') && $this->Input->get('module')!='')
		{
			$objModule = $this->Database->prepare("SELECT * FROM tl_module WHERE id=?")
									->limit(1)
									->execute($this->Input->get('module'));
									
			$strClass = $this->findFrontendModule($objModule->type);
			//echo $objModule->type;
			if (!$this->classFileExists($strClass))
			{
				$this->echoError('Modul nicht gefunden.');
			}

			$objModule->typePrefix = 'mod_';
			$objModule = new $strClass($objModule, 'main');
			//echo $objModule->generate();
			$arrTable = $objModule->generate();
		}
		
		
		if (is_array($GLOBALS['TL_HOOKS']['dispatchRest']))
		{
			foreach ($GLOBALS['TL_HOOKS']['dispatchRest'] as $callback)
			{
				$this->import($callback[0]);
				$varValue = $this->$callback[0]->$callback[1]();
				if ($varValue !== false)
				{
					$arrTable = $varValue;
				}
			}
		}
		
		// call the output-success-function
		$this->echoOutput($arrTable);
		
	}
	
	protected function echoOutput($arrTable)
	{
		$arrOutput = array();
		$arrOutput['@entries'] = sizeof($arrTable);
		$arrOutput['@date'] = time();
		$arrOutput['@status'] = 'OK';
		$arrOutput['@table'] = $this->table;
		$arrOutput['response'] = $arrTable;
		
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Content-type: application/json');				
		if (Input::get('callback'))
		{
			echo Input::get('callback') . '(';
		}			
		echo json_encode($arrOutput);
		if (Input::get('callback'))
		{
			echo ');';
		}	
		die();
	}
	
	protected function echoError($errMsg)
	{
		$arrOutput = array();
		$arrOutput['@date'] = time();
		$arrOutput['@status'] = 'ERROR';
		$arrOutput['@table'] = $this->table;
		$arrOutput['message'] = $errMsg;
								
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Content-type: application/json');			
		if (Input::get('callback'))
		{
			echo Input::get('callback') . '(';
		}			
		echo json_encode($arrOutput);
		if (Input::get('callback'))
		{
			echo ');';
		}	
		die();
	}
}
