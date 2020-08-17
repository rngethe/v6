<?php

/**
 * @version     1.0.0
 * @package     com_jcrm
 * @copyright   Copyright (C) 2014. Tous droits réservés.
 * @license     GNU General Public License version 2 ou version ultérieure ; Voir LICENSE.txt
 * @author      Décision Publique <dev@emundus.fr> - http://www.emundus.fr
 */
// No direct access
defined('_JEXEC') or die;

require_once JPATH_COMPONENT . '/controller.php';

/**
 * Email controller class.
 */
class JcrmControllerEmailForm extends JcrmController {

    /**
     * Method to check out an item for editing and redirect to the edit form.
     *
     * @since	1.6
     */
    public function edit() {
        $app = JFactory::getApplication();

        // Get the previous edit id (if any) and the current edit id.
        $previousId = (int) $app->getUserState('com_jcrm.edit.email.id');
        $editId = JFactory::getApplication()->input->getInt('id', null, 'array');

        // Set the user id for the user to edit in the session.
        $app->setUserState('com_jcrm.edit.email.id', $editId);

        // Get the model.
        $m_emailform = $this->getModel('EmailForm', 'JcrmModel');

        // Check out the item
        if ($editId) {
            $m_emailform->checkout($editId);
        }

        // Check in the previous user.
        if ($previousId) {
            $m_emailform->checkin($previousId);
        }

        // Redirect to the edit screen.
        $this->setRedirect(JRoute::_('index.php?option=com_jcrm&view=emailform&layout=edit', false));
    }

	/**
	 * Method to save a user's profile data.
	 *
	 * @return bool
	 * @throws Exception
	 * @since    1.6
	 */
    public function save() {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Initialise variables.
        $app = JFactory::getApplication();
        $m_emailform = $this->getModel('EmailForm', 'JcrmModel');

        // Get the user data.
        $data = JFactory::getApplication()->input->get('jform', array(), 'array');

        // Validate the posted data.
        $form = $m_emailform->getForm();
        if (!$form) {
            JError::raiseError(500, $m_emailform->getError());
            return false;
        }

        // Validate the posted data.
        $data = $m_emailform->validate($form, $data);

        // Check for errors.
        if ($data === false) {
            // Get the validation messages.
            $errors = $m_emailform->getErrors();

            // Push up to three validation messages out to the user.
            for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
                if ($errors[$i] instanceof Exception) {
                    $app->enqueueMessage($errors[$i]->getMessage(), 'warning');
                } else {
                    $app->enqueueMessage($errors[$i], 'warning');
                }
            }

            $input = $app->input;
            $jform = $input->get('jform', array(), 'ARRAY');

            // Save the data in the session.
            $app->setUserState('com_jcrm.edit.email.data', $jform, array());

            // Redirect back to the edit screen.
            $id = (int) $app->getUserState('com_jcrm.edit.email.id');
            $this->setRedirect(JRoute::_('index.php?option=com_jcrm&view=emailform&layout=edit&id=' . $id, false));
            return false;
        }

        // Attempt to save the data.
        $return = $m_emailform->save($data);

        // Check for errors.
        if ($return === false) {
            // Save the data in the session.
            $app->setUserState('com_jcrm.edit.email.data', $data);

            // Redirect back to the edit screen.
            $id = (int) $app->getUserState('com_jcrm.edit.email.id');
            $this->setMessage(JText::sprintf('Save failed', $m_emailform->getError()), 'warning');
            $this->setRedirect(JRoute::_('index.php?option=com_jcrm&view=emailform&layout=edit&id=' . $id, false));
            return false;
        }


        // Check in the profile.
        if ($return) {
            $m_emailform->checkin($return);
        }

        // Clear the profile id from the session.
        $app->setUserState('com_jcrm.edit.email.id', null);

        // Redirect to the list screen.
        $this->setMessage(JText::_('COM_JCRM_ITEM_SAVED_SUCCESSFULLY'));
        $menu = JFactory::getApplication()->getMenu();
        $item = $menu->getActive();
        $url = (empty($item->link) ? 'index.php?option=com_jcrm&view=emails' : $item->link);
        $this->setRedirect(JRoute::_($url, false));

        // Flush the data from the session.
        $app->setUserState('com_jcrm.edit.email.data', null);
    }

    function cancel() {
        
        $app = JFactory::getApplication();

        // Get the current edit id.
        $editId = (int) $app->getUserState('com_jcrm.edit.email.id');

        // Get the model.
        $m_emailform = $this->getModel('EmailForm', 'JcrmModel');

        // Check in the item
        if ($editId) {
            $m_emailform->checkin($editId);
        }
        
        $menu = JFactory::getApplication()->getMenu();
        $item = $menu->getActive();
        $url = (empty($item->link) ? 'index.php?option=com_jcrm&view=emails' : $item->link);
        $this->setRedirect(JRoute::_($url, false));
    }

    public function remove() {

        // Initialise variables.
        $app = JFactory::getApplication();
        $m_emailform = $this->getModel('EmailForm', 'JcrmModel');

        // Get the user data.
        $data = array();
        $data['id'] = $app->input->getInt('id');

        // Check for errors.
        if (empty($data['id'])) {
            // Get the validation messages.
            $errors = $m_emailform->getErrors();

            // Push up to three validation messages out to the user.
            for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
                if ($errors[$i] instanceof Exception) {
                    $app->enqueueMessage($errors[$i]->getMessage(), 'warning');
                } else {
                    $app->enqueueMessage($errors[$i], 'warning');
                }
            }

            // Save the data in the session.
            $app->setUserState('com_jcrm.edit.email.data', $data);

            // Redirect back to the edit screen.
            $id = (int) $app->getUserState('com_jcrm.edit.email.id');
            $this->setRedirect(JRoute::_('index.php?option=com_jcrm&view=email&layout=edit&id=' . $id, false));
            return false;
        }

        // Attempt to save the data.
        $return = $m_emailform->delete($data);

        // Check for errors.
        if ($return === false) {
            // Save the data in the session.
            $app->setUserState('com_jcrm.edit.email.data', $data);

            // Redirect back to the edit screen.
            $id = (int) $app->getUserState('com_jcrm.edit.email.id');
            $this->setMessage(JText::sprintf('Delete failed', $m_emailform->getError()), 'warning');
            $this->setRedirect(JRoute::_('index.php?option=com_jcrm&view=email&layout=edit&id=' . $id, false));
            return false;
        }


        // Check in the profile.
        if ($return) {
            $m_emailform->checkin($return);
        }

        // Clear the profile id from the session.
        $app->setUserState('com_jcrm.edit.email.id', null);

        // Redirect to the list screen.
        $this->setMessage(JText::_('COM_JCRM_ITEM_DELETED_SUCCESSFULLY'));
        $menu = JFactory::getApplication()->getMenu();
        $item = $menu->getActive();
        $url = (empty($item->link) ? 'index.php?option=com_jcrm&view=emails' : $item->link);
        $this->setRedirect(JRoute::_($url, false));

        // Flush the data from the session.
        $app->setUserState('com_jcrm.edit.email.data', null);
    }

}
