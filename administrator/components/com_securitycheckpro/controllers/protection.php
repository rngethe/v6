<?php
/**
* Protection Controller para Securitycheck Pro
* @ author Jose A. Luque
* @ Copyright (c) 2011 - Jose A. Luque
* @license GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
*/

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Load framework base classes
jimport('joomla.application.component.controller');

/**
* Securitycheckpros  Controller
*
*/
class SecuritycheckprosControllerProtection extends JControllerLegacy
{
/**
* constructor (registers additional tasks to methods)
* @return void
*/
function __construct()
{
parent::__construct();

}

/* Redirecciona las peticiones al componente */
function redireccion()
{
	$this->setRedirect( 'index.php?option=com_securitycheckpro&controller=securitycheckpro' );
}

/* Redirecciona las peticiones al Panel de Control */
function redireccion_control_panel()
{
	$this->setRedirect( 'index.php?option=com_securitycheckpro' );
}

/* Guarda los cambios y redirige al cPanel */
public function save()
{
	JRequest::checkToken() or die('Invalid Token');
	$model = $this->getModel('protection');
	
	// Obtenemos los valores del formulario; hemos de especificar cada uno de ellos porque el campo 'own_code' ha de contener datos en bruto
	$jinput = JFactory::getApplication()->input;
	$data = $jinput->getArray(array(
		'backend_exceptions'	=> '',
		'default_banned_list'	=> '',
		'disable_server_signature'	=> '',
		'disallow_php_eggs'	=>	'',
		'disallow_sensible_files_access'	=> '',
		'exception'	=> '',
		'file_injection_protection'	=>	'',
		'hide_backend_url'	=>	'',
		'own_banned_list'	=>	'',
		'own_code' => 'raw',
		'prevent_access'	=>	'',
		'prevent_mime_attacks'	=>	'',
		'prevent_unauthorized_browsing'	=>	'',
		'self_environ'	=>	'',
		'xframe_options'	=>	'',
		'optimal_expiration_time'	=>	1,
		'redirect_to_www'	=>	1,
		'compress_content'	=>	1,
		'backend_protection_applied'	=>	0,
		'hide_backend_url_redirection'	=>	'not_found'
	));
	$model->saveConfig($data, 'cparams');

	$this->setRedirect('index.php?option=com_securitycheckpro&view=cpanel&'. JSession::getFormToken() .'=1',JText::_('COM_SECURITYCHECKPRO_CONFIGSAVED'));
}

/* Guarda los cambios */
public function apply()
{
	$this->save('cparams');
	$this->setRedirect('index.php?option=com_securitycheckpro&controller=protection&view=protection&'. JSession::getFormToken() .'=1',JText::_('COM_SECURITYCHECKPRO_CONFIGSAVED'));
}

/* Modifica o crear el archivo .htaccess con las configuraciones seleccionadas por el usuario */
function protect()
{
	$model = $this->getModel("protection");

	$status = $model->protect();
	$url = 'index.php?option=com_securitycheckpro&controller=protection&view=protection&'. JSession::getFormToken() .'=1';
	if($status) {
		$this->setRedirect($url,JText::_('COM_SECURITYCHECKPRO_PROTECTION_APPLIED'));
	} else {
		$this->setRedirect($url,JText::_('COM_SECURITYCHECKPRO_PROTECTION_NOTAPPLIED'),'error');
	}
	
}

/* Borra el archivo .htaccess */
function delete_htaccess()
{
	$model = $this->getModel("protection");

	$status = $model->delete_htaccess();
	$url = 'index.php?option=com_securitycheckpro&controller=protection&view=protection&'. JSession::getFormToken() .'=1';
	if($status) {
		$this->setRedirect($url,JText::_('COM_SECURITYCHECKPRO_HTACCESS_DELETED'));
	} else {
		$this->setRedirect($url,JText::_('COM_SECURITYCHECKPRO_HTACCESS_NOT_DELETED'),'error');
	}
	
}

/* Restaura el archivo .htaccess.original */
function restore_htaccess()
{
	$model = $this->getModel("protection");

	$status = $model->restore_htaccess();
	$url = 'index.php?option=com_securitycheckpro&controller=protection&view=protection&'. JSession::getFormToken() .'=1';
	if($status) {
		$this->setRedirect($url,JText::_('COM_SECURITYCHECKPRO_ORIGINAL_HTACCESS_RESTORED'));
	} else {
		$this->setRedirect($url,JText::_('COM_SECURITYCHECKPRO_ORIGINAL_HTACCESS_NOT_RESTORED'),'error');
	}
	
}

/* Muestra las configuraciones escogidas en una ventana, en lugar de aplicarlas mediante un archivo .htaccess.  Esto es necesario en servidores NGINX*/
function generate_rules()
{
	// Inicializamos las variables
	$txt_content = '';
	
	$model = $this->getModel("protection");

	$rules = $model->generate_rules();
	
	$txt_content .= $rules;
	// Mandamos el contenido al navegador
	@ob_end_clean();	
	ob_start();	
	header( 'Content-Type: text/plain' );
	header( 'Content-Disposition: attachment;filename=securitycheckpro_nginx_rules.txt' );
	print $txt_content;
	exit();
		
}

/* Redirecciona las peticiones a System Info */
function redireccion_system_info()
{
	$this->setRedirect( 'index.php?option=com_securitycheckpro&controller=filemanager&view=sysinfo&'. JSession::getFormToken() .'=1' );
}

/* Guarda las modificaciones a los user-agents por defecto */
function save_default_user_agent()
{
	$jinput = JFactory::getApplication()->input;
	$new_user_agents_blacklist = $jinput->get("file_info",null,'raw');
	// Escribimos el nuevo contenido en el fichero 'user_agent_blacklist.inc'
	$pos_header = strpos($new_user_agents_blacklist,"## Begin Securitycheck Pro Default Blacklist");
	$pos_footer = strpos($new_user_agents_blacklist,"## End Securitycheck Pro Default Blacklist");
	// Si no existen las cabeceras y pie de esta opción (necesarias para detectar la configuraación aplicada), las añadimos
	if ( $pos_header === false ) {	
		$new_user_agents_blacklist = "## Begin Securitycheck Pro Default Blacklist" . PHP_EOL . $new_user_agents_blacklist;
	} 
	if ( $pos_footer === false ) {	
		$new_user_agents_blacklist .= PHP_EOL . "## End Securitycheck Pro Default Blacklist";
	} 
	$status = JFile::write(JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'user_agent_blacklist.inc', $new_user_agents_blacklist);
	$url = 'index.php?option=com_securitycheckpro&controller=protection&view=protection&'. JSession::getFormToken() .'=1';
	if($status) {
		$this->setRedirect($url,JText::_('COM_SECURITYCHECKPRO_CHANGES_SAVED'));
	} else {
		$this->setRedirect($url,JText::_('COM_SECURITYCHECKPRO_CHANGES_NOT_SAVED'),'error');
	}
	
}

}