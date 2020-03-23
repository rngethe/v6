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

JText::script('COM_EMUNDUSONBOARD_FROM');
JText::script('COM_EMUNDUSONBOARD_TO');
JText::script('COM_EMUNDUSONBOARD_SINCE');
?>

<div id="em-addForm-vue" form="<?= $this->id ;?>"></div>

<script src="media/com_emundus_onboard/app.js"></script>
