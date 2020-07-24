<?php
/**
 * @package     Joomla
 * @subpackage  com_emundus_onboard
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
$document = JFactory::getDocument();
$document->addScript('media/com_emundus_onboard/chunk-vendors.js');
$document->addStyleSheet('media/com_emundus_onboard/app.css');

require_once (JPATH_COMPONENT.DS.'helpers'.DS.'access.php');

// GLOBAL
JText::script('COM_EMUNDUS_ONBOARD_ADD_RETOUR');
JText::script('COM_EMUNDUS_ONBOARD_ADD_CONTINUER');
JText::script('COM_EMUNDUS_ONBOARD_OK');
JText::script('COM_EMUNDUS_ONBOARD_CANCEL');
JText::script('COM_EMUNDUS_ONBOARD_NEXT');
JText::script('COM_EMUNDUS_ONBOARD_LOAD_FILE');

// MENUS
JText::script('COM_EMUNDUS_ONBOARD_STATUSDESCRIPTION');
JText::script('COM_EMUNDUS_ONBOARD_STYLINGDESCRIPTION');
JText::script('COM_EMUNDUS_ONBOARD_TAGSDESCRIPTION');
JText::script('COM_EMUNDUS_ONBOARD_HOMEDESCRIPTION');
JText::script('COM_EMUNDUS_ONBOARD_SETTINGS_ADDTAG');
JText::script('COM_EMUNDUS_ONBOARD_TRANSLATE_ENGLISH');
JText::script('COM_EMUNDUS_ONBOARD_COLORS');
JText::script('COM_EMUNDUS_ONBOARD_UPDATE_LOGO');
JText::script('COM_EMUNDUS_ONBOARD_DROP_HERE');
JText::script('COM_EMUNDUS_ONBOARD_REMOVE_FILE');
JText::script('COM_EMUNDUS_ONBOARD_CANCEL_UPLOAD');
JText::script('COM_EMUNDUS_ONBOARD_CANCEL_UPLOAD_CONFIRMATION');
JText::script('COM_EMUNDUS_ONBOARD_INVALID_FILE_TYPE');
JText::script('COM_EMUNDUS_ONBOARD_FILE_TOO_BIG');
JText::script('COM_EMUNDUS_ONBOARD_MAX_FILES_EXCEEDED');
JText::script('COM_EMUNDUS_ONBOARD_ERROR');
JText::script('COM_EMUNDUS_ONBOARD_PRIMARY_COLOR');
JText::script('COM_EMUNDUS_ONBOARD_SECONDARY_COLOR');
JText::script('COM_EMUNDUS_ONBOARD_BUILDER_UPDATE');
JText::script('COM_EMUNDUS_ONBOARD_COLOR_SUCCESS');
JText::script('COM_EMUNDUS_ONBOARD_CREATE_DATAS');
JText::script('COM_EMUNDUS_ONBOARD_IMPORT_DATAS');
JText::script('COM_EMUNDUS_ONBOARD_FIRSTNAME');
JText::script('COM_EMUNDUS_ONBOARD_FIRSTNAME_REQUIRED');
JText::script('COM_EMUNDUS_ONBOARD_LASTNAME');
JText::script('COM_EMUNDUS_ONBOARD_LASTNAME_REQUIRED');
JText::script('COM_EMUNDUS_ONBOARD_ADDCAMP_DESCRIPTION');
JText::script('COM_EMUNDUS_ONBOARD_VALUES');
JText::script('COM_EMUNDUS_ONBOARD_USERSDESCRIPTIONSETTINGS');
JText::script('COM_EMUNDUS_ONBOARD_LAST_CONNECTED');
JText::script('COM_EMUNDUS_ONBOARD_EMAIL');
JText::script('COM_EMUNDUS_ONBOARD_EMAIL_REQUIRED');
JText::script('COM_EMUNDUS_ONBOARD_DOSSIERS_STATUS');
JText::script('COM_EMUNDUS_ONBOARD_SEARCH');
JText::script('COM_EMUNDUS_ONBOARD_ACTIVATED');
JText::script('COM_EMUNDUS_ONBOARD_BLOCKED');
JText::script('COM_EMUNDUS_ONBOARD_NO_RESULTS_FOUND');
JText::script('COM_EMUNDUS_ONBOARD_ACTIONS');
JText::script('COM_EMUNDUS_ONBOARD_PROGRAM_ADDUSER');
JText::script('COM_EMUNDUS_ONBOARD_RESET_PASSWORD');
JText::script('COM_EMUNDUS_ONBOARD_RESET_PASSWORD_MESSAGE');
JText::script('COM_EMUNDUS_ONBOARD_LOCK_USER');
JText::script('COM_EMUNDUS_ONBOARD_UNLOCK_USER');
JText::script('COM_EMUNDUS_ONBOARD_UPDATE_DATAS');
JText::script('COM_EMUNDUS_ONBOARD_BLOCKED_USERS');
JText::script('COM_EMUNDUS_ONBOARD_COLUMNS');
JText::script('COM_EMUNDUS_ONBOARD_MY_COLUMNS');
JText::script('COM_EMUNDUS_ONBOARD_CSV_COLUMNS');
JText::script('COM_EMUNDUS_ONBOARD_CSV_ASSOCIATION');
JText::script('COM_EMUNDUS_ONBOARD_LEAST_ONE_COLUMN_REQUIRED');


$lang = JFactory::getLanguage();
$actualLanguage = substr($lang->getTag(), 0, 2);

$user = JFactory::getUser();
$coordinator_access = EmundusonboardHelperAccess::isCoordinator($user->id);
?>

<div id="em-globalSettings-vue" actualLanguage="<?= $actualLanguage ?>" coordinatorAccess="<?= $coordinator_access ?>"></div>

<script src="media/com_emundus_onboard/app.js"></script>
