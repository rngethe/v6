<?php
/**
 * @package     Joomla
 * @subpackage  eMundus
 * @link        http://www.emundus.fr
 * @copyright   Copyright (C) 2016 eMundus. All rights reserved.
 * @license     GNU/GPL
 * @author      James Dean
 */

// No direct access

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

/**
 * Settings Controller
 *
 * @package    Joomla
 * @subpackage eMundus
 * @since      5.0.0
 */
class EmundusonboardControllersettings extends JControllerLegacy {

    var $model = null;
    public function __construct($config = array()) {
        require_once (JPATH_COMPONENT.DS.'helpers'.DS.'access.php');
        parent::__construct($config);
        $this->model = $this->getModel('settings');
    }

    public function getstatus() {
        $user = JFactory::getUser();

        if (!EmundusonboardHelperAccess::asCoordinatorAccessLevel($user->id)) {
            $result = 0;
            $tab = array('status' => $result, 'msg' => JText::_("ACCESS_DENIED"));
        } else {

	        $m_settings = $this->model;
            $status = $m_settings->getStatus();

            if (!empty($status)) {
                $tab = array('status' => 1, 'msg' => JText::_('STATUS_RETRIEVED'), 'data' => $status);
            } else {
                $tab = array('status' => 0, 'msg' => JText::_('ERROR_CANNOT_RETRIEVE_STATUS'), 'data' => $status);
            }
        }
        echo json_encode((object)$tab);
        exit;
    }

    public function gettags() {
        $user = JFactory::getUser();

        if (!EmundusonboardHelperAccess::asCoordinatorAccessLevel($user->id)) {
            $result = 0;
            $tab = array('status' => $result, 'msg' => JText::_("ACCESS_DENIED"));
        } else {

        	$m_settings = $this->model;
            $status = $m_settings->getTags();

            if (!empty($status)) {
                $tab = array('status' => 1, 'msg' => JText::_('STATUS_RETRIEVED'), 'data' => $status);
            } else {
                $tab = array('status' => 0, 'msg' => JText::_('ERROR_CANNOT_RETRIEVE_STATUS'), 'data' => $status);
            }
        }
        echo json_encode((object)$tab);
        exit;
    }

    public function createtag() {
        $user = JFactory::getUser();

        if (!EmundusonboardHelperAccess::asCoordinatorAccessLevel($user->id)) {
            $result = 0;
            $changeresponse = array('status' => $result, 'msg' => JText::_("ACCESS_DENIED"));
        } else {
	        $m_settings = $this->model;
            $changeresponse = $m_settings->createTag();
        }
        echo json_encode((object)$changeresponse);
        exit;
    }

    public function deletetag() {
        $user = JFactory::getUser();

        if (!EmundusonboardHelperAccess::asCoordinatorAccessLevel($user->id)) {
            $result = 0;
            $changeresponse = array('status' => $result, 'msg' => JText::_("ACCESS_DENIED"));
        } else {
	        $m_settings = $this->model;
	        $jinput = JFactory::getApplication()->input;
	        $id = $jinput->getRaw('id');
            $changeresponse = $m_settings->deleteTag($id);
        }
        echo json_encode((object)$changeresponse);
        exit;
    }

    public function updatestatus() {
        $user = JFactory::getUser();

        if (!EmundusonboardHelperAccess::asCoordinatorAccessLevel($user->id)) {
            $result = 0;
            $changeresponse = array('status' => $result, 'msg' => JText::_("ACCESS_DENIED"));
        } else {
	        $m_settings = $this->model;
	        $jinput = JFactory::getApplication()->input;
	        $status = $jinput->getRaw('status');
            $changeresponse = $m_settings->updateStatus($status);
        }
        echo json_encode((object)$changeresponse);
        exit;
    }

    public function updatetags() {
        $user = JFactory::getUser();

        if (!EmundusonboardHelperAccess::asCoordinatorAccessLevel($user->id)) {
            $result = 0;
            $changeresponse = array('status' => $result, 'msg' => JText::_("ACCESS_DENIED"));
        } else {

        	$m_settings = $this->model;
	        $jinput = JFactory::getApplication()->input;
	        $tags = $jinput->getRaw('tags');

            $changeresponse = $m_settings->updateTags($tags);
        }
        echo json_encode((object)$changeresponse);
        exit;
    }

    public function gethomepagearticle() {
        $user = JFactory::getUser();

        if (!EmundusonboardHelperAccess::asCoordinatorAccessLevel($user->id)) {
            $result = 0;
            $tab = array('status' => $result, 'msg' => JText::_("ACCESS_DENIED"));
        } else {
	        $m_settings = $this->model;
            $content = $m_settings->getHomepageArticle();
            if (!empty($content)) {
                $tab = array('status' => 1, 'msg' => JText::_('STATUS_RETRIEVED'), 'data' => $content);
            } else {
                $tab = array('status' => 0, 'msg' => JText::_('ERROR_CANNOT_RETRIEVE_STATUS'), 'data' => $content);
            }
        }
        echo json_encode((object)$tab);
        exit;
    }

    public function updatehomepage() {
        $user = JFactory::getUser();

        if (!EmundusonboardHelperAccess::asCoordinatorAccessLevel($user->id)) {
            $result = 0;
            $changeresponse = array('status' => $result, 'msg' => JText::_("ACCESS_DENIED"));
        } else {

        	$m_settings = $this->model;
	        $jinput = JFactory::getApplication()->input;
	        $content = $jinput->getRaw('content');

            $changeresponse = $m_settings->updateHomepage($content);
        }
        echo json_encode((object)$changeresponse);
        exit;
    }

    public function updatelogo() {
        $user = JFactory::getUser();

        if (!EmundusonboardHelperAccess::asCoordinatorAccessLevel($user->id)) {
            $result = 0;
            $tab = array('status' => $result, 'msg' => JText::_("ACCESS_DENIED"));
        } else {
            $jinput = JFactory::getApplication()->input;
            $image = $jinput->files->get('file');

            if(isset($image)) {
                $target_dir = "images/custom/";
                $target_file = $target_dir . basename('logo.png');

                if (move_uploaded_file($image["tmp_name"], $target_file)) {
                    $tab = array('status' => 1, 'msg' => JText::_('LOGO_UPDATED'));
                } else {
                    $tab = array('status' => 0, 'msg' => JText::_('LOGO_NOT_UPDATED'));
                }
            } else {
                $tab = array('status' => 0, 'msg' => JText::_('LOGO_NOT_UPDATED'));
            }
            echo json_encode((object)$tab);
            exit;
        }
    }

    public function getappcolors(){
        $user = JFactory::getUser();

        if (!EmundusonboardHelperAccess::asCoordinatorAccessLevel($user->id)) {
            $result = 0;
            $tab = array('status' => $result, 'msg' => JText::_("ACCESS_DENIED"));
        } else {
            $yaml = \Symfony\Component\Yaml\Yaml::parse(file_get_contents('templates/g5_helium/custom/config/default/styles.yaml'));

            $primary = $yaml['base']['primary-color'];
            $secondary = $yaml['base']['secondary-color'];
            $tab = array('status' => '1', 'msg' => JText::_("SUCCESS"), 'primary' => $primary, 'secondary' => $secondary);
        }
        echo json_encode((object)$tab);
        exit;
    }

    public function updatecolor(){
        $user = JFactory::getUser();

        if (!EmundusonboardHelperAccess::asCoordinatorAccessLevel($user->id)) {
            $result = 0;
            $tab = array('status' => '0', 'msg' => JText::_("ACCESS_DENIED"));
        } else {
            $jinput = JFactory::getApplication()->input;
            $type = $jinput->post->getString('type');
            $color = $jinput->post->getString('color');

            $yaml = \Symfony\Component\Yaml\Yaml::parse(file_get_contents('templates/g5_helium/custom/config/default/styles.yaml'));
            $yaml['base'][$type . '-color'] = $color;
            if($type == 'primary'){
                $yaml['accent']['color-1'] = $color;
            } else {
                $yaml['accent']['color-2'] = $color;
            }

            $new_yaml = \Symfony\Component\Yaml\Yaml::dump($yaml, 5);

            file_put_contents('templates/g5_helium/custom/config/default/styles.yaml', $new_yaml);

            $tab = array('status' => '1', 'msg' => JText::_("SUCCESS"));
        }
        echo json_encode((object)$tab);
        exit;
    }

    public function getdatasfromtable() {
        $user = JFactory::getUser();

        if (!EmundusonboardHelperAccess::asCoordinatorAccessLevel($user->id)) {
            $result = 0;
            $response = array('status' => $result, 'msg' => JText::_("ACCESS_DENIED"));
        } else {

            $m_settings = $this->model;
            $jinput = JFactory::getApplication()->input;
            $dbtable = $jinput->getString('db');

            $datas = $m_settings->getDatasFromTable($dbtable);
            $response = array('status' => '1', 'msg' => 'SUCCESS', 'data' => $datas);
        }
        echo json_encode((object)$response);
        exit;
    }
}

