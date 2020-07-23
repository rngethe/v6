<?php
/**
 * Messages model used for the new message dialog.
 *
 * @package    Joomla
 * @subpackage eMundus
 *             components/com_emundus/emundus.php
 * @link       http://www.emundus.fr
 * @license    GNU/GPL
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');
use Joomla\CMS\Date\Date;

class EmundusonboardModelsettings extends JModelList {
    function getColorClasses(){
        return array(
            'lightpurple' => '#DCC6E0',
            'purple' => '#947CB0',
            'darkpurple' => '#663399',
            'lightblue' => '#6BB9F0',
            'blue' => '#19B5FE',
            'darkblue' => '#013243',
            'lightgreen' => '#7BEFB2',
            'green' => '#3FC380',
            'darkgreen' => '#1E824C',
            'lightyellow' => '#FFFD7E',
            'yellow' => '#FFFD54',
            'darkyellow' => '#F7CA18',
            'lightorange' => '#FABE58',
            'orange' => '#E87E04',
            'darkorange' => '#D35400',
            'lightred' => '#EC644B',
            'red' => '#CF000F',
            'darkred' => '#E5283B',
            'lightpink' => '#E08283',
            'pink' => '#D2527F',
            'darkpink' => '#DB0A5B',
            'default' => '#999999',
        );
    }

    function clean($string) {
        $string = str_replace(' ', '_', $string); // Replaces all spaces with hyphens.

        return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    }

    function getStatus() {
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        $query->select('*')
            ->from ($db->quoteName('#__emundus_setup_status'))
            ->order('step ASC');

        try {
            $db->setQuery($query);
            $status = $db->loadObjectList();
            foreach ($status as $statu){
                $statu->value_en = '';
                $statu->value_fr = '';

                $query->clear()
                    ->select('value')
                    ->from($db->quoteName('#__falang_content'))
                    ->where(array(
                        $db->quoteName('reference_id') . ' = ' . $db->quote($statu->step),
                        $db->quoteName('reference_table') . ' = ' . $db->quote('emundus_setup_status'),
                        $db->quoteName('reference_field') . ' = ' . $db->quote('value'),
                        $db->quoteName('language_id') . ' = 1'
                    ));
                $db->setQuery($query);
                $en_value = $db->loadResult();

                $query->clear()
                    ->select('value')
                    ->from($db->quoteName('#__falang_content'))
                    ->where(array(
                        $db->quoteName('reference_id') . ' = ' . $db->quote($statu->step),
                        $db->quoteName('reference_table') . ' = ' . $db->quote('emundus_setup_status'),
                        $db->quoteName('reference_field') . ' = ' . $db->quote('value'),
                        $db->quoteName('language_id') . ' = 2'
                    ));
                $db->setQuery($query);
                $fr_value = $db->loadResult();

                if ($en_value != null) {
                    $statu->value_en = $en_value;
                }
                if ($fr_value != null) {
                    $statu->value_fr = $fr_value;
                }
            }

            return $status;
        } catch(Exception $e) {
            JLog::add($e->getMessage(), JLog::ERROR, 'com_emundus_onboard');
            return false;
        }
    }

    function getTags() {
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        $query->select('*')
            ->from($db->quoteName('#__emundus_setup_action_tag'));

        try {
            $db->setQuery($query);
            return $db->loadObjectList();
        } catch(Exception $e) {
            JLog::add($e->getMessage(), JLog::ERROR, 'com_emundus_onboard');
            return false;
        }
    }

    function deleteTag($id) {
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        $query->delete($db->quoteName('#__emundus_setup_action_tag'))
            ->where($db->quoteName('id') . ' = ' . $id);

        try {
            $db->setQuery($query);
            return $db->execute();
        } catch(Exception $e) {
            JLog::add($e->getMessage(), JLog::ERROR, 'com_emundus_onboard');
            return false;
        }
    }

    function createTag() {
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        $query->insert('#__emundus_setup_action_tag')
            ->set($db->quoteName('label') . ' = ' . $db->quote('Nouvelle étiquette'))
            ->set($db->quoteName('class') . ' = ' . $db->quote('label-default'));

        try {
            $db->setQuery($query);
            $db->execute();
            $newtagid = $db->insertid();

            $query->clear()
                ->select('*')
                ->from ($db->quoteName('#__emundus_setup_action_tag'))
                ->where($db->quoteName('id') . ' = ' . $db->quote($newtagid));

            $db->setQuery($query);
            return $db->loadObject();

        } catch(Exception $e) {
            JLog::add($e->getMessage(), JLog::ERROR, 'com_emundus_onboard');
            return false;
        }
    }

    function updateStatus($status) {
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        $classes = $this->getColorClasses();
        $results = [];

        foreach($status as $statu) {
            $class = array_search($statu['class'], $classes);
            $query->clear()
                ->update('#__emundus_setup_status')
                ->set($db->quoteName('class') . ' = ' . $db->quote($class))
                ->where($db->quoteName('id') . ' = ' . $db->quote($statu['id']));
            $db->setQuery($query);
            $results[] = $db->execute();

            $query->clear()
                ->update('#__falang_content')
                ->set($db->quoteName('value') . ' = ' . $db->quote($class))
                ->where(array(
                    $db->quoteName('reference_id') . ' = ' . $db->quote($statu['step']),
                    $db->quoteName('reference_table') . ' = ' . $db->quote('emundus_setup_status'),
                    $db->quoteName('reference_field') . ' = ' . $db->quote('class'),
                    $db->quoteName('language_id') . ' = 2'
                ));
            $db->setQuery($query);
            $results[] = $db->execute();

            $query->clear()
                ->update('#__falang_content')
                ->set($db->quoteName('value') . ' = ' . $db->quote($statu['value_fr']))
                ->where(array(
                    $db->quoteName('reference_id') . ' = ' . $db->quote($statu['step']),
                    $db->quoteName('reference_table') . ' = ' . $db->quote('emundus_setup_status'),
                    $db->quoteName('reference_field') . ' = ' . $db->quote('value'),
                    $db->quoteName('language_id') . ' = 2'
                ));
            $db->setQuery($query);
            $results[] = $db->execute();

            $query->clear()
                ->update('#__falang_content')
                ->set($db->quoteName('value') . ' = ' . $db->quote($statu['value_en']))
                ->where(array(
                    $db->quoteName('reference_id') . ' = ' . $db->quote($statu['step']),
                    $db->quoteName('reference_table') . ' = ' . $db->quote('emundus_setup_status'),
                    $db->quoteName('reference_field') . ' = ' . $db->quote('value'),
                    $db->quoteName('language_id') . ' = 1'
                ));
            $db->setQuery($query);
            $results[] = $db->execute();
        }

        return $results;
    }

    function updateTags($tags) {
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        $classes = $this->getColorClasses();
        $results = [];

        foreach($tags as $tag) {
            $class = array_search($tag['class'], $classes);
            $query->clear()
                ->update('#__emundus_setup_action_tag')
                ->set($db->quoteName('label') . ' = ' . $db->quote($tag['label']))
                ->set($db->quoteName('class') . ' = ' . $db->quote('label-' . $class))
                ->where($db->quoteName('id') . ' = ' . $db->quote($tag['id']));
            $db->setQuery($query);
            $results[] = $db->execute();
        }

        return $results;
    }

    function getHomepageArticle() {
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        $query->select('*')
            ->from($db->quoteName('#__content'))
            ->where($db->quoteName('id') . ' = 52');

        try {
            $db->setQuery($query);
            $homepage = $db->loadObject();

            $homepage->title_en = '';
            $homepage->introtext_en = '';

            $query->clear()
                ->select('value')
                ->from($db->quoteName('#__falang_content'))
                ->where(array(
                    $db->quoteName('reference_id') . ' = 52',
                    $db->quoteName('reference_table') . ' = ' . $db->quote('content'),
                    $db->quoteName('reference_field') . ' = ' . $db->quote('title'),
                    $db->quoteName('language_id') . ' = 1'
                ));
            $db->setQuery($query);
            $en_title = $db->loadResult();

            $query->clear()
                ->select('value')
                ->from($db->quoteName('#__falang_content'))
                ->where(array(
                    $db->quoteName('reference_id') . ' = 52',
                    $db->quoteName('reference_table') . ' = ' . $db->quote('content'),
                    $db->quoteName('reference_field') . ' = ' . $db->quote('introtext'),
                    $db->quoteName('language_id') . ' = 1'
                ));
            $db->setQuery($query);
            $en_introtext = $db->loadResult();

            if ($en_title != null) {
                $homepage->title_en = $en_title;
            }
            if ($en_introtext != null) {
                $homepage->introtext_en = $en_introtext;
            }

            return $homepage;
        } catch(Exception $e) {
            JLog::add($e->getMessage(), JLog::ERROR, 'com_emundus_onboard');
            return false;
        }
    }

    function updateHomepage($content) {
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        $results = [];

        $query->update($db->quoteName('#__content'))
            ->set($db->quoteName('introtext') . ' = ' . $db->quote($content['fr']))
            ->where($db->quoteName('id') . ' = ' . 52);

        try {
            $db->setQuery($query);
            $results[] = $db->execute();

            $query->clear()
                ->update('#__falang_content')
                ->set($db->quoteName('value') . ' = ' . $db->quote($content['en']))
                ->where(array(
                    $db->quoteName('reference_id') . ' = 52',
                    $db->quoteName('reference_table') . ' = ' . $db->quote('content'),
                    $db->quoteName('reference_field') . ' = ' . $db->quote('introtext'),
                    $db->quoteName('language_id') . ' = 1'
                ));
            $db->setQuery($query);
            $results[] = $db->execute();
        } catch(Exception $e) {
            JLog::add($e->getMessage(), JLog::ERROR, 'com_emundus_onboard');
            return false;
        }

        return $results;
    }

    function onAfterCreateCampaign($user_id) {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $query->select('count(id)')
            ->from($db->quoteName('#__emundus_setup_campaigns'));
        $db->setQuery($query);

        try {
            if ($db->loadResult() === '1') {
                $this->removeParam('first_login',$user_id);
                return $this->createParam('first_form', $user_id);
            }
            return true;
        } catch (Exception $e) {
            JLog::add('Error getting candidatures -> '.$e->getMessage(), JLog::ERROR, 'com_emundus.saas');
            return false;
        }
    }

    function onAfterCreateForm($user_id) {
        try {
            $this->removeParam('first_form',$user_id);
            $this->createParam('first_formbuilder', $user_id);
            $this->createParam('first_documents', $user_id);
        } catch (Exception $e) {
            JLog::add('Error getting candidatures -> '.$e->getMessage(), JLog::ERROR, 'com_emundus.saas');
            return false;
        }
    }


    /**
     * @param         $param String The param to be saved in the user account.
     *
     * @param   null  $user_id
     *
     * @return bool
     * @since version
     */
    private function createParam($param, $user_id) {

        $user = JFactory::getUser($user_id);

        $table = JTable::getInstance('user', 'JTable');
        $table->load($user->id);

        // Check if the param exists but is false, this avoids accidetally resetting a param.
        $params = $user->getParameters();
        if (!$params->get($param, true)) {
            return true;
        }

        // Store token in User's Parameters
        $user->setParam($param, true);

        // Get the raw User Parameters
        $params = $user->getParameters();

        // Set the user table instance to include the new token.
        $table->params = $params->toString();

        // Save user data
        if (!$table->store()) {
            JLog::add('Error saving params : '.$table->getError(), JLog::ERROR, 'mod_emundus.saas');
            return false;
        }
        return true;
    }

    function removeParam($param, $user_id) {

        $user = JFactory::getUser($user_id);

        $table = JTable::getInstance('user', 'JTable');
        $table->load($user->id);

        // Check if the param exists but is false, this avoids accidetally resetting a param.
        $params = $user->getParameters();
        if (!$params->get($param, true)) {
            return true;
        }

        // Store token in User's Parameters
        $user->setParam($param, false);

        // Get the raw User Parameters
        $params = $user->getParameters();

        // Set the user table instance to include the new token.
        $table->params = $params->toString();

        // Save user data
        if (!$table->store()) {
            JLog::add('Error saving params : '.$table->getError(), JLog::ERROR, 'mod_emundus.saas');
            return false;
        }
        return true;
    }

    function getDatasFromTable($table){
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        if(strpos($table, 'data_') !== false){

            $query->select('join_column_val,translation')
                ->from($db->quoteName('#__emundus_datas_library'))
                ->where($db->quoteName('database_name') . ' LIKE ' . $db->quote($table));
            $db->setQuery($query);
            $columntodisplay = $db->loadObject();

            if(boolval($columntodisplay->translation)){
                $columntodisplay->join_column_val = $columntodisplay->join_column_val . '_en,' . $columntodisplay->join_column_val . '_fr';
            }

            $query->clear()
                ->select($columntodisplay->join_column_val)
                ->from($db->quoteName($table));
            $db->setQuery($query);

            try {
                return $db->loadAssocList();
            } catch (Exception $e) {
                JLog::add('Error : '.$e->getMessage(), JLog::ERROR, 'com_emundus_onboard');
                return false;
            }
        } else {
            return false;
        }
    }

    function saveDatas($form){
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        $name = strtolower($this->clean($form['label']));

        // Check if a table already get the same name and increment them
        $query->clear()
            ->select('COUNT(*)')
            ->from($db->quoteName('information_schema.tables'))
            ->where($db->quoteName('table_name') . ' LIKE ' . $db->quote('%data_' . $name . '%'));
        $db->setQuery($query);
        $result = $db->loadResult();

        $increment = '00';
        if ($result < 10) {
            $increment = '0' . strval($result);
        } elseif ($result > 10) {
            $increment = strval($result);
        }

        $table_name = 'data_' . $name . '_' . $increment;
        //

        $query->insert($db->quoteName('#__emundus_datas_library'));
        $query->set($db->quoteName('database_name') . ' = ' . $db->quote($table_name))
            ->set($db->quoteName('join_column_val') . ' = ' . $db->quote('value'))
            ->set($db->quoteName('label') . ' = ' . $db->quote($form['label']))
            ->set($db->quoteName('description') . ' = ' . $db->quote($form['desc']))
            ->set($db->quoteName('created') . ' = ' . $db->quote(date('Y-m-d H:i:s')));
        $db->setQuery($query);
        try {
            $db->execute();

            // Create the new table
            $table_query = "CREATE TABLE IF NOT EXISTS " . $table_name . " (
            id int(11) NOT NULL AUTO_INCREMENT,
            value_fr varchar(255) NOT NULL,
            value_en varchar(255) NOT NULL,
            PRIMARY KEY (id)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8";
            $db->setQuery($table_query);
            $db->execute();
            //

            // Insert values
            $query = $db->getQuery(true);
            foreach($form['db_values'] as $values) {
                $query->clear()
                    ->insert($db->quoteName($table_name));
                $query->set($db->quoteName('value_fr') . ' = ' . $db->quote($values['fr']))
                    ->set($db->quoteName('value_en') . ' = ' . $db->quote($values['en']));
                $db->setQuery($query);
                $db->execute();
            }
            //

            return true;
        } catch (Exception $e) {
            JLog::add('Error : '.$e->getMessage(), JLog::ERROR, 'com_emundus_onboard');
            return false;
        }
    }

    function unlockUser($user_id){
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        $query->update('#__users')
            ->set($db->quoteName('block') . ' = 0')
            ->where($db->quoteName('id') . ' = ' . $db->quote($user_id));

        try {
            $db->setQuery($query);
            return $db->execute();
        } catch (Exception $e) {
            JLog::add('Error : '.$e->getMessage(), JLog::ERROR, 'com_emundus_onboard');
            return false;
        }
    }

    function lockUser($user_id){
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        $user = JFactory::getUser();

        if($user_id != 62 && $user_id != $user->id) {
            $query->update('#__users')
                ->set($db->quoteName('block') . ' = 1')
                ->where($db->quoteName('id') . ' = ' . $db->quote($user_id));

            try {
                $db->setQuery($query);
                return $db->execute();
            } catch (Exception $e) {
                JLog::add('Error : ' . $e->getMessage(), JLog::ERROR, 'com_emundus_onboard');
                return false;
            }
        } else {
            return false;
        }
    }

    function checkFirstDatabaseJoin($user_id) {
        $user = JFactory::getUser($user_id);

        $table = JTable::getInstance('user', 'JTable');
        $table->load($user->id);

        // Check if the param exists but is false, this avoids accidetally resetting a param.
        $params = $user->getParameters();
        return $params->get('first_databasejoin', true);
    }
}
