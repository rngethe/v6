<?php
/**
 * @version 2: emundusisapplicationsent 2018-12-04 Hugo Moracchini
 * @package Fabrik
 * @copyright Copyright (C) 2018 emundus.fr. All rights reserved.
 * @license GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 * @description Locks access to a file if the file is not of a certain status.
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

// Require the abstract plugin class
require_once COM_FABRIK_FRONTEND . '/models/plugin-form.php';

/**
 * Create a Joomla user from the forms data
 *
 * @package     Joomla.Plugin
 * @subpackage  Fabrik.form.juseremundus
 * @since       3.0
 */

class PlgFabrik_FormEmundusisevaluationconfirmed extends plgFabrik_Form {


	/**
	 * Status field
	 *
	 * @var  string
	 */
	protected $URLfield = '';

	/**
	 * Get an element name
	 *
	 * @param   string  $pname  Params property name to look up
	 * @param   bool    $short  Short (true) or full (false) element name, default false/full
	 *
	 * @return	string	element full name
	 */
	public function getFieldName($pname, $short = false) {
		$params = $this->getParams();

		if ($params->get($pname) == '') {
			return '';
		}

		$elementModel = FabrikWorker::getPluginManager()->getElementPlugin($params->get($pname));

		return $short ? $elementModel->getElement()->name : $elementModel->getFullName();
	}

	/**
	 * Get the fields value regardless of whether its in joined data or no
	 *
	 * @param   string  $pname    Params property name to get the value for
	 * @param   array   $data     Posted form data
	 * @param   mixed   $default  Default value
	 *
	 * @return  mixed  value
	 */
	public function getParam($pname, $default = '') {
		$params = $this->getParams();

		if ($params->get($pname) == '') {
			return $default;
		}

		return $params->get($pname);
	}

	/**
	 * Main script.
	 *
	 * @return  bool
	 * @throws Exception
	 */
	public function onLoad() {

		$app = JFactory::getApplication();

		if (!$app->isAdmin()) {
			require_once (JPATH_SITE.DS.'components'.DS.'com_emundus'.DS.'helpers'.DS.'access.php');

			jimport('joomla.log.log');
			JLog::addLogger(['text_file' => 'com_emundus.isEvaluationConfirmed.php'], JLog::ALL, ['com_emundus']);

			$user = JFactory::getSession()->get('emundusUser');
			if (empty($user)) {
				$user = JFactory::getUser();
			}

			$jinput = $app->input;
			$fnum = $jinput->get->get('jos_emundus_evaluations___fnum');
			$rowid = $jinput->get->getInt('rowid');

			if ((!empty($fnum[0]) || !empty($rowid)) && !EmundusHelperAccess::asCoordinatorAccessLevel($user->id)) {

				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->select($db->quoteName('confirm'))->from($db->quoteName('jos_emundus_evaluations'));

				if (!empty($rowid)) {
					$query->where($db->quoteName('id').' = '.$rowid);
				} else {
					$query->where($db->quoteName('fnum').' LIKE '.$db->quote($fnum[0]).' AND '.$db->quoteName('user').' = '.$user->id);
				}

				try {
					$db->setQuery($query);
					$confirm = $db->loadResult();
				} catch (Exception $e) {
					JLog::add('Error getting confirmation of evaluation -> '.$e->getMessage(), JLog::ERROR, 'com_emundus');
					return false;
				}

				if (!empty($confirm)) {
					echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>';
					die("<script>
						Swal.fire({
							position: 'top',
							type: 'info',
							title: '".JText::_('COM_EMUNDUS_EVALUATION_ALREADY_CONFIRMED')."',
							text: '".JText::_('COM_EMUNDUS_EVALUATION_ALREADY_CONFIRMED_DESC')."',
							showConfirmButton: false,
							allowOutsideClick: false,
							allowEscapeKey: false,
							allowEnterKey: false
						})
					</script>");
				}
			}
		}
		return true;
	}
}