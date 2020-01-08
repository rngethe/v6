<?php
/**
 * @version 1: ExceliaPrice 2019-10-30 James Dean
 * @author  James Dean
 * @package Hikashop
 * @copyright Copyright (C) 2018 emundus.fr. All r
 * @license GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have
 * to the GNU General Public License, and as distr
 * is derivative of works licensed under the GNU G
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and det
 * @description Sets the price to 0 depending if the excelia user exists
 */
// No direct access
defined('_JEXEC') or die('Restricted access');


class PlgHikashopEmundus_hikashop extends JPlugin
{
    public function onAfterOrderCreate(&$order){

        // We get the emundus payment type from the config
        $eMConfig = JComponentHelper::getParams('com_emundus');
        $em_application_payment = $eMConfig->get('application_payment', 'user');



        $session = JFactory::getSession()->get('emundusUser');
        $order_id = $order->order_id;

        
        if (!empty($session)) {
            $user = $session->id;
            $fnum = $session->fnum;
            $cid = $session->campaign_id;
        }
        else {
            return false;
        }

        $db = JFactory::getDbo();
        $config = hikashop_config();
        $confirmed_statuses = explode(',', trim($config->get('invoice_order_statuses','confirmed,shipped'), ','));



        if(empty($confirmed_statuses)) {
            $confirmed_statuses = array('confirmed','shipped');
        }

        $query = $db->getQuery(true);

        switch ($em_application_payment) {
            case 'user':
                $query
                    ->clear()
                    ->select('*')
                    ->from($db->quoteName('#__emundus_hikashop'))
                    ->where($db->quoteName('order_id') . ' = ' . $order_id . ' OR ' . $db->quoteName('user_id') . ' = ' . $user);
            break;

            case 'campaign':
                $query
                    ->clear()
                    ->select('*')
                    ->from($db->quoteName('#__emundus_hikashop'))
                    ->where($db->quoteName('order_id') . ' = ' . $order_id . ' OR (' . $db->quoteName('campaign_id') . ' = ' . $cid . ' AND ' . $db->quoteName('user') . ' = ' . $user .' ) ');
            break;

            case 'fnum':
                $query
                    ->clear()
                    ->select('*')
                    ->from($db->quoteName('#__emundus_hikashop'))
                    ->where($db->quoteName('order_id') . ' = ' . $order_id . ' OR ' . $db->quoteName('fnum') . ' LIKE ' . $db->quote($fnum));
            break;

        }
        try {
            $db->setQuery($query);

            $em_hika = $db->loadObject();

            if(empty($em_hika)) {

                $columns = ['user', 'fnum', 'campaign_id', 'order_id'];
                $values = [$user, $db->quote($fnum), $cid, $order_id];

                $query
                    ->clear()
                    ->insert($db->quoteName('#__emundus_hikashop'))
                    ->columns($db->quoteName($columns))
                    ->values(implode(',', $values));

                $db->setQuery($query);


                if ($db->execute()) {
                    return true;
                }

                return false;
            }
            else {

                $fields = array(
                    $db->quoteName('order_id') . ' = ' . $db->quote($order_id)
                );

                $update_conditions = array(
                    $db->quoteName('order_id') . ' = ' . $em_hika->order_id
                );

                // Prepare the insert query.
                $query
                    ->clear()
                    ->update($db->quoteName('#__emundus_hikashop'))
                    ->set($fields)
                    ->where($update_conditions);


                $db->setQuery($query);


                if ($db->execute()) {
                    return true;
                }

                return false;
            }
            return false;
        } catch (Exception $exception) {
            return false;
        }
    }

    public function onAfterOrderUpdate(&$order){
        $this->onAfterOrderCreate($order);
    }
}