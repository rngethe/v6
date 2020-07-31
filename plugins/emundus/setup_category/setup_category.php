<?php
/**
 * @package	eMundus
 * @version	6.6.5
 * @author	eMundus.fr
 * @copyright (C) 2020 eMundus SOFTWARE. All rights reserved.
 * @license	GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');

class plgEmundusSetup_category extends JPlugin {

    var $db;
    var $query;

    function __construct(&$subject, $config) {
        parent::__construct($subject, $config);

        $this->db = JFactory::getDbo();
        $this->query = $this->db->getQuery(true);

        jimport('joomla.log.log');
        JLog::addLogger(array('text_file' => 'com_emundus.setupCategory.php'), JLog::ALL, array('com_emundus_setupCategory'));
    }


    function onCampaignCreate($id) {
        try {
            $app = JFactory::getApplication();
            $label = $app->input->getString("jos_emundus_setup_campaigns___label");
            $nom = JFilterOutput::stringURLSafe($label);


            $this->query
                ->clear()
                ->select($this->db->quoteName('id'))
                ->from($this->db->quoteName('jos_categories'))
                ->where('json_extract(`params`, "$.idCampaign") LIKE ' . $this->db->quote('"'.$id.'"'))
                ->andWhere($this->db->quoteName('extension') . ' LIKE ' .$this->db->quote('com_dropfiles'));

            $this->db->setQuery($this->query);

            $cat_id = $this->db->loadResult();

            if(!$cat_id) {
                $table = JTable::getInstance('category');

                $data = array();
                $data['path'] = $nom;
                $data['title'] = $label;
                $data['parent_id'] = 1;
                $data['extension'] = "com_dropfiles";
                $data['published'] = 1;
                $data['params'] = json_encode(array("idCampaign" =>"".$id));
                $table->setLocation($data['parent_id'], 'last-child');
                $table->bind($data);

                if ($table->check()) {
                    $table->store();
                } else {
                    JLog::add('Could not Insert data into jos_categories.', JLog::ERROR, 'com_emundus_setupCategory');
                    return false;
                }

                // Insert columns.
                $columns = array('id', 'type', 'cloud_id', 'path', 'params', 'theme');

                // Insert values.
                $values = array($table->id, $this->db->quote('default'), $this->db->quote(''), $this->db->quote(''), $this->db->quote('{\"usergroup\":[\"1\"],\"ordering\":\"ordering\",\"orderingdir\":\"asc\",\"marginleft\":\"10\",\"margintop\":\"10\",\"marginright\":\"10\",\"marginbottom\":\"10\",\"columns\":\"2\",\"showsize\":\"1\",\"showtitle\":\"1\",\"showversion\":\"1\",\"showhits\":\"1\",\"showdownload\":\"1\",\"bgdownloadlink\":\"#76bc58\",\"colordownloadlink\":\"#ffffff\",\"showdateadd\":\"1\",\"showdatemodified\":\"0\",\"showsubcategories\":\"1\",\"showcategorytitle\":\"1\",\"showbreadcrumb\":\"1\",\"showfoldertree\":\"0\"}'), $this->db->quote(''));

                // Prepare the insert query.
                $this->query
                    ->clear()
                    ->insert($this->db->quoteName('#__dropfiles'))
                    ->columns($this->db->quoteName($columns))
                    ->values(implode(',', $values));

                $this->db->setQuery($this->query);
                $this->db->execute();

            } else {

                // Fields to update.
                $fields = array(
                    $this->db->quoteName('path') . ' = ' . $this->db->quote($nom),
                    $this->db->quoteName('title') . ' = ' . $this->db->quote($label),
                    $this->db->quoteName('alias') . ' = ' . $this->db->quote($nom)
                );

                // Conditions for which records should be updated.
                $conditions = array(
                    'json_extract(`params`, "$.idCampaign") LIKE ' . $this->db->quote('"'.$id.'"'),
                    $this->db->quoteName('extension') . ' LIKE ' . $this->db->quote('com_dropfiles')
                );

                $this->query
                    ->clear()
                    ->update($this->db->quoteName('#__categories'))
                    ->set($fields)
                    ->where($conditions);

                $this->db->setQuery($this->query);
                $this->db->execute();

                $this->query
                    ->clear()
                    ->update($this->db->quoteName('#__categories'))
                    ->set($this->db->quoteName('title') . ' = ' . $this->db->quote($label))
                    ->where($this->db->quoteName('name') . ' LIKE ' . $this->db->quote('com_dropfiles.category'.$cat_id));

                $this->db->setQuery($this->query);
                $this->db->execute();
            }

            return true;
        } catch (Exception $e) {
            JLog::add(str_replace("\n", "", $this->query.' -> '.$e->getMessage()), JLog::ERROR, 'com_emundus_setupCategory');
            return false;
        }
    }

    function onCampaignDelete($ids) {

        $ids = is_array($ids) ? $ids : array($ids);
        if (empty($ids)) {
            return false;
        }

        try {
            $app = JFactory::getApplication();

            $table = JTable::getInstance('category');

            foreach ($ids AS $id) {

                $this->query
                    ->clear()
                    ->select($this->db->quoteName('id'))
                    ->from($this->db->quoteName('jos_categories'))
                    ->where('json_extract(`params`, "$.idCampaign") LIKE ' . $this->db->quote('"'.$id.'"'));

                $this->db->setQuery($this->query);
                $idCategory = $this->db->loadResult();

                if($idCategory) {
                    $table->load($idCategory);
                    $table->delete();

                    $this->query
                        ->clear()
                        ->delete($this->db->quoteName('jos_dropfiles'))
                        ->where($this->db->quoteName('id') . ' = '.$idCategory);

                    $this->db->setQuery($this->query);
                    $this->db->execute();

                    $this->query
                        ->clear()
                        ->delete($this->db->quoteName('jos_dropfiles_files'))
                        ->where($this->db->quoteName('catid') . ' = '.$idCategory);

                    $this->db->setQuery($this->query);
                    $this->db->execute();
                }
            }

            return true;
        } catch (Exception $e) {
            JLog::add(str_replace("\n", "", $this->query.' -> '.$e->getMessage()), JLog::ERROR, 'com_emundus_setupCategory');
            return false;
        }
    }
}