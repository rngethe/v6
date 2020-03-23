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

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

/**
 * Program Controller
 *
 * @package    Joomla
 * @subpackage eMundus
 * @since      5.0.0
 */
class EmundusonboardControlleremail extends JControllerLegacy {

    var $model = null;
    public function __construct($config = array()) {
        require_once (JPATH_COMPONENT.DS.'helpers'.DS.'access.php');
        parent::__construct($config);
        $this->model = $this->getModel('email');
    }

    public function getemailcount() {
        $user = JFactory::getUser();
        $m_emails = $this->model;
        $filterCount = $_GET['filterCount'];
        $rechercheCount = $_GET['rechercheCount'];

        if (!EmundusonboardHelperAccess::asCoordinatorAccessLevel($user->id)) {
            $result = 0;
            $tab = array('status' => $result, 'msg' => JText::_("ACCESS_DENIED"));
        } else {
            $emails = $m_emails->getEmailCount($user->id, $filterCount, $rechercheCount);

            if ($emails > 0) {
                $tab = array('status' => 1, 'msg' => JText::_('EMAIL_RETRIEVED'), 'data' => $emails);
            } else {
                $tab = array('status' => 0, 'msg' => JText::_('ERROR_CANNOT_RETRIEVE_EMAIL'), 'data' => $emails);
            }
        }
        echo json_encode((object)$tab);
        exit;
    }


    public function getallemail() {
        $user = JFactory::getUser();
        $m_emails = $this->model;

        $filter = $_GET['filter'];
        $sort = $_GET['sort'];
        $recherche = $_GET['recherche'];
        $lim = $_GET['lim'];
        $page = $_GET['page'];

        if (!EmundusonboardHelperAccess::asCoordinatorAccessLevel($user->id)) {
            $result = 0;
            $tab = array('status' => $result, 'msg' => JText::_("ACCESS_DENIED"));
        } else {
            $emails = $m_emails->getAllEmails($user->id, $lim, $page, $filter, $sort, $recherche);

            if (count($emails) > 0) {
                $tab = array('status' => 1, 'msg' => JText::_('EMAIL_RETRIEVED'), 'data' => $emails);
            } else {
                $tab = array('status' => 0, 'msg' => JText::_('ERROR_CANNOT_RETRIEVE_EMAIL'), 'data' => $emails);
            }
        }
        echo json_encode((object)$tab);
        exit;
    }


    public function deleteemail() {
        $user = JFactory::getUser();
        $jinput = JFactory::getApplication()->input;
        $data = $jinput->getRaw('id');
        $m_email = $this->model;

        if (!EmundusonboardHelperAccess::asCoordinatorAccessLevel($user->id)) {
            $result = 0;
            $tab = array('status' => $result, 'msg' => JText::_("ACCESS_DENIED"));
        } else {
            $emails = $m_email->deleteEmail($data);

            if ($emails) {
                $tab = array('status' => 1, 'msg' => JText::_('EMAIL_DELETED'), 'data' => $emails);
            } else {
                $tab = array('status' => 0, 'msg' => JText::_('ERROR_CANNOT_DELETE_EMAIL'), 'data' => $emails);
            }
        }
        echo json_encode((object)$tab);
        exit;
    }

    public function unpublishemail() {
        $user = JFactory::getUser();
        $jinput = JFactory::getApplication()->input;
        $data = $jinput->getRaw('id');
        $m_email = $this->model;

        if (!EmundusonboardHelperAccess::asCoordinatorAccessLevel($user->id)) {
            $result = 0;
            $tab = array('status' => $result, 'msg' => JText::_("ACCESS_DENIED"));
        } else {
            $emails = $m_email->unpublishEmail($data);

            if ($emails) {
                $tab = array('status' => 1, 'msg' => JText::_('EMAIL_UNPUBLISHED'), 'data' => $emails);
            } else {
                $tab = array('status' => 0, 'msg' => JText::_('ERROR_CANNOT_UNPUBLISH_EMAIL'), 'data' => $emails);
            }
        }
        echo json_encode((object)$tab);
        exit;
    }

    public function publishemail() {
        $user = JFactory::getUser();
        $jinput = JFactory::getApplication()->input;
        $data = $jinput->getRaw('id');
        $m_email = $this->model;

        if (!EmundusonboardHelperAccess::asCoordinatorAccessLevel($user->id)) {
            $result = 0;
            $tab = array('status' => $result, 'msg' => JText::_("ACCESS_DENIED"));
        } else {
            $emails = $m_email->publishEmail($data);

            if ($emails) {
                $tab = array('status' => 1, 'msg' => JText::_('EMAIL_PUBLISHED'), 'data' => $emails);
            } else {
                $tab = array('status' => 0, 'msg' => JText::_('ERROR_CANNOT_PUBLISH_EMAIL'), 'data' => $emails);
            }
        }
        echo json_encode((object)$tab);
        exit;
    }

    public function duplicateemail() {
        $user = JFactory::getUser();
        $jinput = JFactory::getApplication()->input;
        $data = $jinput->getRaw('id');
        $m_email = $this->model;

        if (!EmundusonboardHelperAccess::asCoordinatorAccessLevel($user->id)) {
            $result = 0;
            $tab = array('status' => $result, 'msg' => JText::_("ACCESS_DENIED"));
        } else {
            $email = $m_email->duplicateEmail($data);

            if ($email) {
                $this->getallemail();
            } else {
                $tab = array('status' => 0, 'msg' => JText::_('ERROR_CANNOT_DUPLICATE_EMAIL'), 'data' => $email);
            }
        }
        echo json_encode((object)$tab);
        exit;
    }

    public function createemail() {

        $user = JFactory::getUser();
        $jinput = JFactory::getApplication()->input;
        $data = $jinput->getRaw('body');
        $m_email = $this->model;

        if (!EmundusonboardHelperAccess::asCoordinatorAccessLevel($user->id)) {
            $result = 0;
            $tab = array('status' => $result, 'msg' => JText::_("ACCESS_DENIED"));
        } else {
            $result = $m_email->createEmail($data);

            if ($result) {
                $tab = array('status' => 1, 'msg' => JText::_('EMAIL_ADDED'), 'data' => $result);
            } else {
                $tab = array('status' => 0, 'msg' => JText::_('ERROR_CANNOT_ADD_EMAIL'), 'data' => $result);
            }
        }
        echo json_encode((object)$tab);
        exit;
    }


    public function updateemail() {

        $user = JFactory::getUser();
        $jinput = JFactory::getApplication()->input;
        $data = $jinput->getRaw('body');
        $code = $jinput->getRaw('code');
        $m_email = $this->model;

        if (!EmundusonboardHelperAccess::asCoordinatorAccessLevel($user->id)) {
            $result = 0;
            $tab = array('status' => $result, 'msg' => JText::_("ACCESS_DENIED"));
        } else {
            $result = $m_email->updateEmail($code, $data);

            if ($result) {
                $tab = array('status' => 1, 'msg' => JText::_('EMAIL_ADDED'), 'data' => $result);
            } else {
                $tab = array('status' => 0, 'msg' => JText::_('EMAIL'), 'data' => $result);
            }
        }
        echo json_encode((object)$tab);
        exit;
    }

    public function getemailbyid() {
        $user = JFactory::getUser();
        $jinput = JFactory::getApplication()->input;
        $id = $jinput->get->get('id');
        $m_email = $this->model;

        if (!EmundusonboardHelperAccess::asCoordinatorAccessLevel($user->id)) {
            $result = 0;
            $tab = array('status' => $result, 'msg' => JText::_("ACCESS_DENIED"));
        } else {
            $email = $m_email->getEmailById($id);
            if (!empty($email)) {
                $tab = array('status' => 1, 'msg' => JText::_('EMAIL_RETRIEVED'), 'data' => $email);
            } else {
                $tab = array('status' => 0, 'msg' => JText::_('ERROR_CANNOT_RETRIEVE_EMAIL'), 'data' => $email);
            }
        }
        echo json_encode((object)$tab);
        exit;
    }

    public function getemailcategories() {
        $user = JFactory::getUser();
        $m_email = $this->model;

        if (!EmundusonboardHelperAccess::asCoordinatorAccessLevel($user->id)) {
            $result = 0;
            $tab = array('status' => $result, 'msg' => JText::_("ACCESS_DENIED"));
        } else {
            $email = $m_email->getEmailCategories();
            if (!empty($email)) {
                $tab = array('status' => 1, 'msg' => JText::_('EMAIL_RETRIEVED'), 'data' => $email);
            } else {
                $tab = array('status' => 0, 'msg' => JText::_('ERROR_CANNOT_RETRIEVE_EMAIL'), 'data' => $email);
            }
        }
        echo json_encode((object)$tab);
        exit;
    }

    public function getemailtypes() {
        $user = JFactory::getUser();
        $m_email = $this->model;

        if (!EmundusonboardHelperAccess::asCoordinatorAccessLevel($user->id)) {
            $result = 0;
            $tab = array('status' => $result, 'msg' => JText::_("ACCESS_DENIED"));
        } else {
            $email = $m_email->getEmailTypes();
            if (!empty($email)) {
                $tab = array('status' => 1, 'msg' => JText::_('EMAIL_RETRIEVED'), 'data' => $email);
            } else {
                $tab = array('status' => 0, 'msg' => JText::_('ERROR_CANNOT_RETRIEVE_EMAIL'), 'data' => $email);
            }
        }
        echo json_encode((object)$tab);
        exit;
    }
}

