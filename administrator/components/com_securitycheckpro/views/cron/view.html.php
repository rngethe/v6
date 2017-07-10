<?php
/**
* Cron View para el Componente Securitycheckpro
* @ author Jose A. Luque
* @ Copyright (c) 2011 - Jose A. Luque
* @license GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
*/

// Chequeamos si el archivo est� incluido en Joomla!
defined('_JEXEC') or die();
jimport( 'joomla.application.component.view' );
jimport( 'joomla.plugin.helper' );

// Load plugin language
$lang = JFactory::getLanguage();
$lang->load('plg_system_securitycheckpro_cron');


class SecuritycheckprosViewCron extends SecuritycheckproView
{

protected $state;

function __construct() 	{
	parent::__construct();
	
	JToolBarHelper::title( JText::_( 'Securitycheck Pro' ).' | ' .JText::_('PLG_SECURITYCHECKPRO_CRON_SCHEDULE_LABEL'), 'securitycheckpro' );	
}

/**
* Securitycheckpros Cron m�todo 'display'
**/
function display($tpl = null)
{

// Filtro
$this->state= $this->get('State');
$lists = $this->state->get('filter.lists_search');

// Obtenemos el modelo
$model = $this->getModel();

//  Par�metros del plugin
$items= $model->getCronConfig();

// Extraemos los elementos que nos interesan...
$tasks= null;
$launch_time = null;
$periodicity = null;

if ( !is_null($items['tasks']) ) {
	$tasks = $items['tasks'];	
}

if ( !is_null($items['launch_time']) ) {
	$launch_time = $items['launch_time'];	
}

if ( !is_null($items['periodicity']) ) {
	$periodicity = $items['periodicity'];	
}


// ... y los ponemos en el template
$this->assignRef('tasks',$tasks);
$this->assignRef('launch_time',$launch_time);
$this->assignRef('periodicity',$periodicity);

parent::display($tpl);
}
}