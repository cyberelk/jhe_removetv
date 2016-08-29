<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 Jari-Hermann Ernst, M.A. <jari-hermann.ernst@bad-gmbh.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */



require_once(PATH_t3lib . 'class.t3lib_scbase.php');
$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
// DEFAULT initialization of a module [END]
$LANG->includeLLFile('EXT:jhe_removetv/mod1/locallang.xml');


/**
 * Module 'JHE Remove TV' for the 'jhe_removetv' extension.
 *
 * @author	Jari-Hermann Ernst, M.A. <jari-hermann.ernst@bad-gmbh.de>
 * @package	TYPO3
 * @subpackage	tx_jheremovetv
 */
class  tx_jheremovetv_module1 extends t3lib_SCbase {

	var $pageinfo;

	/**
	 * Initializes the Module
	 * @return	void
	 */
	function init()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		parent::init();

		/*
		if (t3lib_div::_GP('clear_all_cache'))	{
			$this->include_once[] = PATH_t3lib.'class.t3lib_tcemain.php';
		}
		*/
	}

	/**
	 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
	 *
	 * @return	void
	 */
	function menuConfig()	{
		global $LANG;

		$this->MOD_MENU = Array (
			'function' => Array (
				'1' => $LANG->getLL('function1'),
				'2' => $LANG->getLL('function2'),
				'3' => $LANG->getLL('function3'),
			)
		);
		parent::menuConfig();
	}

	/**
	 * Main function of the module. Write the content to $this->content
	 * If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
	 *
	 * @return	[type]		...
	 */
	function main()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		//Access check!
		// The page will show only if there is a valid page and if this page may be viewed by the user
		$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
		$access = is_array($this->pageinfo) ? 1 : 0;

		if (($this->id && $access) || ($BE_USER->user['admin'] && !$this->id))	{

			// Draw the header.
			$this->doc = t3lib_div::makeInstance('bigDoc');

			//$this->doc->styleSheetFile2 = t3lib_extMgm::extRelPath('jhe_persorg_fe') . 'mod1/style.css';

			$this->doc->backPath = $BACK_PATH;
			$this->doc->form='<form action="" method="post" enctype="multipart/form-data">';

			$this->doc->loadJavascriptLib('contrib/prototype/prototype.js');
			$this->doc->loadJavascriptLib('js/common.js');

			//JavaScript
			$this->doc->JScode = '
				<script language="javascript" type="text/javascript">
					script_ended = 0;
					function jumpToUrl(URL){
						document.location = URL;
					}
				</script>
			';
			$this->doc->postCode='
				<script language="javascript" type="text/javascript">
					script_ended = 1;
					if (top.fsMod) top.fsMod.recentIds["web"] = 0;
				</script>
			';

			$headerSection = $this->doc->getHeader('pages',$this->pageinfo,$this->pageinfo['_thePath']).'<br />'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.path').': '.t3lib_div::fixed_lgd_pre($this->pageinfo['_thePath'],50);

			$this->content.=$this->doc->startPage($LANG->getLL('title'));
			$this->content.=$this->doc->header($LANG->getLL('title'));
			$this->content.=$this->doc->spacer(5);
			$this->content.=$this->doc->section('',$this->doc->funcMenu($headerSection,t3lib_BEfunc::getFuncMenu($this->id,'SET[function]',$this->MOD_SETTINGS['function'],$this->MOD_MENU['function'])));
			$this->content.=$this->doc->divider(5);

			//Render content:
			$this->moduleContent();

			//ShortCut
			if ($BE_USER->mayMakeShortcut()){
				$this->content.=$this->doc->spacer(20).$this->doc->section('',$this->doc->makeShortcutIcon('id',implode(',',array_keys($this->MOD_MENU)),$this->MCONF['name']));
			}

			$this->content.=$this->doc->spacer(10);
		} else {
			//If no access or if ID == zero

			$this->doc = t3lib_div::makeInstance('mediumDoc');
			$this->doc->backPath = $BACK_PATH;

			$this->content.=$this->doc->startPage($LANG->getLL('title'));
			$this->content.=$this->doc->header($LANG->getLL('title'));
			$this->content.=$this->doc->spacer(5);
			$this->content.=$this->doc->spacer(10);
		}

	}

	/**
	 * Prints out the module HTML
	 *
	 * @return	void
	 */
	function printContent()	{
		$this->content.=$this->doc->endPage();
		echo $this->content;
	}

	/**
	 * Generates the module content
	 *
	 * @return	void
	 */
	function moduleContent()
	{
		global $GLOBALS, $LANG, $BACK_PATH;
		$content = '';
		$function = $_GET['SET']['function'];


		if (!$function || $function == 1) {
			$content = 'This is just a simple function to retrieve all the used types of the extension.';

			//SELECT `uid`, `pid`, `tx_templavoila_ds`, `tx_templavoila_to`, `tx_templavoila_flex`, `tx_templavoila_pito` FROM `tt_content` WHERE `CType` = 'templavoila_pi1' AND `tx_templavoila_ds` = '8' AND `hidden` = '0' AND `deleted` = '0'

			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid, pid, header, tx_templavoila_flex, tx_templavoila_ds, tx_templavoila_to, tx_templavoila_pito', 'tt_content', 'CType = \'templavoila_pi1\' AND hidden = \'0\' AND deleted = \'0\'');

			if ($res !== false) {

				$content .= '<table>';
				$content .= '<tr><td>uid</td><td>pid</td><td>header</td><td>type</td></tr>';

				while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {

					$flexformContent = t3lib_div::xml2array($row['tx_templavoila_flex']);
					//t3lib_utility_Debug::debug($flexformContent['data']['sDEF']['lDEF']);

					$output = '';
					if(is_array($flexformContent)){
						foreach($flexformContent['data']['sDEF']['lDEF'] as $key => $value){
							$output .= $key .': ' . $value['vDEF'] . ' | ';
						}
					} else {
						$output .= ' KOMISCHES VERHALTEN! ';
					}



					$content .= '<tr><td>' . $row['uid'] . '</td><td>' . $row['pid'] . '</td><td>' . $row['header'] . '</td><td>' . $output . '</td></tr>';
				}
				$content .= '</table>';
			}

		} else if ($function == 2) {

		} else if ($function == 3) {

		}

		$this->content .= $this->doc->section('', $content, 0, 1);
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/jhe_removetv/mod1/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/jhe_removetv/mod1/index.php']);
}

// Make instance:
$SOBE = t3lib_div::makeInstance('tx_jheremovetv_module1');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();

?>