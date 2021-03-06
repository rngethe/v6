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

## GLOBAL ##
JText::script('COM_EMUNDUS_ONBOARD_MODIFY');
JText::script('COM_EMUNDUS_ONBOARD_VISUALIZE');
JText::script('COM_EMUNDUS_ONBOARD_OK');
JText::script('COM_EMUNDUS_ONBOARD_CANCEL');
## END ##

## ACTIONS ##
JText::script('COM_EMUNDUS_ONBOARD_ACTION');
JText::script('COM_EMUNDUS_ONBOARD_ACTION_PUBLISH');
JText::script('COM_EMUNDUS_ONBOARD_ACTION_UNPUBLISH');
JText::script('COM_EMUNDUS_ONBOARD_ACTION_DUPLICATE');
JText::script('COM_EMUNDUS_ONBOARD_ACTION_DELETE');
JText::script('COM_EMUNDUS_ONBOARD_ARCHIVE');
JText::script('COM_EMUNDUS_ONBOARD_ARCHIVED');
JText::script('COM_EMUNDUS_ONBOARD_RESTORE');
## END ##

## FILTERS ##
JText::script('COM_EMUNDUS_ONBOARD_FILTER');
JText::script('COM_EMUNDUS_ONBOARD_FILTER_ALL');
JText::script('COM_EMUNDUS_ONBOARD_FILTER_OPEN');
JText::script('COM_EMUNDUS_ONBOARD_FILTER_CLOSE');
JText::script('COM_EMUNDUS_ONBOARD_FILTER_PUBLISH');
JText::script('COM_EMUNDUS_ONBOARD_FILTER_UNPUBLISH');
JText::script('COM_EMUNDUS_ONBOARD_SELECT');
JText::script('COM_EMUNDUS_ONBOARD_DESELECT');
JText::script('COM_EMUNDUS_ONBOARD_TOTAL');
JText::script('COM_EMUNDUS_ONBOARD_SORT');
JText::script('COM_EMUNDUS_ONBOARD_SORT_CREASING');
JText::script('COM_EMUNDUS_ONBOARD_SORT_DECREASING');
JText::script('COM_EMUNDUS_ONBOARD_RESULTS');
JText::script('COM_EMUNDUS_ONBOARD_ALL_RESULTS');
JText::script('COM_EMUNDUS_ONBOARD_SEARCH');
## END ##

## FORM ##
JText::script('COM_EMUNDUS_ONBOARD_NOFORM');
JText::script('COM_EMUNDUS_ONBOARD_ADD_FORM');
JText::script('COM_EMUNDUS_ONBOARD_FORMDELETE');
JText::script('COM_EMUNDUS_ONBOARD_FORMDELETED');
JText::script('COM_EMUNDUS_ONBOARD_FORMUNPUBLISH');
JText::script('COM_EMUNDUS_ONBOARD_FORMUNPUBLISHED');
JText::script('COM_EMUNDUS_ONBOARD_FORMPUBLISHED');
JText::script('COM_EMUNDUS_ONBOARD_FORMDUPLICATE');
JText::script('COM_EMUNDUS_ONBOARD_FORMDUPLICATED');
## END ##
?>

<list id="em-list-vue" type="form"/>

<script src="media/com_emundus_onboard/app.js"></script>
