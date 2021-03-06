<?php
/**
 * Application Model for eMundus Component
 *
 * @package    Joomla
 * @subpackage eMundus
 *             components/com_emundus/emundus.php
 * @link       http://www.emundus.fr
 * @license    GNU/GPL
 * @author     Benjamin Rivalland
 */

// No direct access

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class EmundusModelApplication extends JModelList {
    var $_user = null;
    var $_db = null;

    /**
     * Constructor
     *
     * @since 1.5
     */
    public function __construct() {
        parent::__construct();
        global $option;
        require_once (JPATH_BASE.DS.'components'.DS.'com_emundus'.DS.'models'.DS.'logs.php');
	    require_once (JPATH_BASE.DS.'components'.DS.'com_emundus'.DS.'helpers'.DS.'menu.php');
        require_once (JPATH_BASE.DS.'components'.DS.'com_emundus'.DS.'models'.DS.'profile.php');

        $this->_mainframe = JFactory::getApplication();

        $this->_db = JFactory::getDBO();
        $this->_user = JFactory::getSession()->get('emundusUser');

        $this->locales = substr(JFactory::getLanguage()->getTag(), 0 , 2);
    }

    public function getApplicantInfos($aid, $param) {
        $query = 'SELECT '.implode(",", $param).'
                FROM #__users
                LEFT JOIN #__emundus_users ON #__emundus_users.user_id=#__users.id
                LEFT JOIN #__emundus_personal_detail ON #__emundus_personal_detail.user=#__users.id
                LEFT JOIN #__emundus_setup_profiles ON #__emundus_setup_profiles.id=#__emundus_users.profile
                LEFT JOIN #__emundus_uploads ON (#__emundus_uploads.user_id=#__users.id AND #__emundus_uploads.attachment_id=10)
                WHERE #__users.id='.$aid;
        $this->_db->setQuery($query);
        return $this->_db->loadAssoc();
    }

    public function getApplicantDetails($aid, $ids) {
        $details = @EmundusHelperList::getElementsDetailsByID($ids);
        $select=array();
        foreach ($details as $detail) {
            $select[] = $detail->tab_name.'.'.$detail->element_name.' AS "'.$detail->element_id.'"';
        }

        $query = 'SELECT '.implode(",", $select).'
                FROM #__users as u
                LEFT JOIN #__emundus_users ON #__emundus_users.user_id=u.id
                LEFT JOIN #__emundus_personal_detail ON #__emundus_personal_detail.user=u.id
                LEFT JOIN #__emundus_setup_profiles ON #__emundus_setup_profiles.id=#__emundus_users.profile
                LEFT JOIN #__emundus_uploads ON (#__emundus_uploads.user_id=u.id AND #__emundus_uploads.attachment_id=10)
                WHERE u.id='.$aid;
        $this->_db->setQuery( $query );
        $values =  $this->_db->loadAssoc();

        foreach ($details as $detail) {
            $detail->element_value = $values[$detail->element_id];
        }
        return $details;
    }

    public function getUserCampaigns($id, $cid = null) {
        if ($cid === null) {
            $query = 'SELECT esc.*, ecc.date_submitted, ecc.submitted, ecc.id as campaign_candidature_id, efg.result_sent, efg.date_result_sent, efg.final_grade, ecc.fnum, ess.class, ess.step, ess.value as step_value
            FROM #__emundus_users eu
            LEFT JOIN #__emundus_campaign_candidature ecc ON ecc.applicant_id=eu.user_id
            LEFT JOIN #__emundus_setup_campaigns esc ON ecc.campaign_id=esc.id
            LEFT JOIN #__emundus_final_grade efg ON efg.campaign_id=esc.id AND efg.student_id=eu.user_id
            LEFT JOIN #__emundus_setup_status as ess ON ess.step = ecc.status
            WHERE eu.user_id="'.$id.'" and ecc.published = 1';

            $this->_db->setQuery($query);
            return $this->_db->loadObjectList();
        } else {
            $query = 'SELECT esc.*, ecc.date_submitted, ecc.submitted, ecc.id as campaign_candidature_id, efg.result_sent, efg.date_result_sent, efg.final_grade, ecc.fnum, ess.class, ess.step, ess.value as step_value
            FROM #__emundus_users eu
            LEFT JOIN #__emundus_campaign_candidature ecc ON ecc.applicant_id=eu.user_id
            LEFT JOIN #__emundus_setup_campaigns esc ON ecc.campaign_id=esc.id
            LEFT JOIN #__emundus_final_grade efg ON efg.campaign_id=esc.id AND efg.student_id=eu.user_id
            LEFT JOIN #__emundus_setup_status as ess ON ess.step = ecc.status
            WHERE eu.user_id="'.$id.'" and ecc.published = 1 and esc.id = '.$cid;

            $this->_db->setQuery($query);
            return $this->_db->loadObject();
        }
    }

    public function getCampaignByFnum($fnum) {
        $query = 'SELECT esc.*, ecc.date_submitted, ecc.submitted, ecc.id as campaign_candidature_id, efg.result_sent, efg.date_result_sent, efg.final_grade, ecc.fnum, ess.class, ess.step, ess.value as step_value
            FROM #__emundus_users eu
            LEFT JOIN #__emundus_campaign_candidature ecc ON ecc.applicant_id=eu.user_id
            LEFT JOIN #__emundus_setup_campaigns esc ON ecc.campaign_id=esc.id
            LEFT JOIN #__emundus_final_grade efg ON efg.campaign_id=esc.id AND efg.student_id=eu.user_id
            LEFT JOIN #__emundus_setup_status as ess ON ess.step = ecc.status
            WHERE ecc.fnum like '.$fnum;

        $this->_db->setQuery($query);
        return $this->_db->loadObjectList();
    }

    public function getUserAttachments($id) {

        $query = 'SELECT eu.id AS aid, esa.*, eu.filename, eu.description, eu.timedate, esc.label as campaign_label, esc.year, esc.training
            FROM #__emundus_uploads AS eu
            LEFT JOIN #__emundus_setup_attachments AS esa ON  eu.attachment_id=esa.id
            LEFT JOIN #__emundus_setup_campaigns AS esc ON esc.id=eu.campaign_id
            WHERE eu.user_id = '.$id;'
            ORDER BY esa.ordering';
        $this->_db->setQuery($query);
        return $this->_db->loadObjectList();
    }

    function getUserAttachmentsByFnum($fnum, $search = '') {
	    $eMConfig = JComponentHelper::getParams('com_emundus');
	    $expert_document_id = $eMConfig->get('expert_document_id', '36');

        if (EmundusHelperAccess::isExpert($this->_user->id)) {
        	if (isset($search) && !empty($search)) {
	            $query = 'SELECT eu.id AS aid, esa.*, eu.attachment_id, eu.filename, eu.description, eu.timedate, eu.can_be_deleted, eu.can_be_viewed, eu.is_validated, esc.label as campaign_label, esc.year, esc.training
				            FROM #__emundus_uploads AS eu
				            LEFT JOIN #__emundus_setup_attachments AS esa ON  eu.attachment_id=esa.id
				            LEFT JOIN #__emundus_setup_campaigns AS esc ON esc.id=eu.campaign_id
				            WHERE eu.fnum like ' . $this->_db->Quote($fnum) . ' 
				            AND (eu.attachment_id != '. $expert_document_id .') 
				            AND (esa.value like "%'. $search .'%"
				            OR esa.description like "%'. $search .'%"
				            OR eu.timedate like "%'. $search .'%")
				            ORDER BY esa.value DESC';
            } else {
                $query = 'SELECT eu.id AS aid, esa.*, eu.attachment_id, eu.filename, eu.description, eu.timedate, eu.can_be_deleted, eu.can_be_viewed, eu.is_validated, esc.label as campaign_label, esc.year, esc.training
			                FROM #__emundus_uploads AS eu
			                LEFT JOIN #__emundus_setup_attachments AS esa ON  eu.attachment_id=esa.id
			                LEFT JOIN #__emundus_setup_campaigns AS esc ON esc.id=eu.campaign_id
			                WHERE eu.fnum like ' . $this->_db->Quote($fnum) . ' 
			                AND (eu.attachment_id != ' . $expert_document_id . ') 
			                ORDER BY esa.value ASC';
            }
        } else {
            if (isset($search) && !empty($search)) {
                $query = 'SELECT eu.id AS aid, esa.*, eu.attachment_id, eu.filename, eu.description, eu.timedate, eu.can_be_deleted, eu.can_be_viewed, eu.is_validated, esc.label as campaign_label, esc.year, esc.training
                FROM #__emundus_uploads AS eu
                LEFT JOIN #__emundus_setup_attachments AS esa ON  eu.attachment_id=esa.id
                LEFT JOIN #__emundus_setup_campaigns AS esc ON esc.id=eu.campaign_id
                WHERE eu.fnum like '. $this->_db->Quote($fnum) .'
                AND (esa.value like "%'. $search .'%"
                OR esa.description like "%'. $search .'%"
                OR eu.timedate like "%'. $search .'%")
                ORDER BY esa.value ASC';
            } else {
                $query = 'SELECT eu.id AS aid, esa.*, eu.attachment_id, eu.filename, eu.description, eu.timedate, eu.can_be_deleted, eu.can_be_viewed, eu.is_validated, esc.label as campaign_label, esc.year, esc.training
                FROM #__emundus_uploads AS eu
                LEFT JOIN #__emundus_setup_attachments AS esa ON  eu.attachment_id=esa.id
                LEFT JOIN #__emundus_setup_campaigns AS esc ON esc.id=eu.campaign_id
                WHERE eu.fnum like ' . $this->_db->Quote($fnum) . '
                ORDER BY esa.value ASC';
            }
        }

        $this->_db->setQuery($query);
        $attachments = $this->_db->loadObjectList();

        // Filter out the attachments not visible to the user.
	    $allowed_attachments = EmundusHelperAccess::getUserAllowedAttachmentIDs(JFactory::getUser()->id);
	    if ($allowed_attachments !== true) {
		    foreach ($attachments as $key => $attachment) {
			    if (!in_array($attachment->id, $allowed_attachments)) {
				    unset($attachments[$key]);
			    }
		    }
	    }

        return $attachments;
    }

    public function getUsersComments($id) {
        $query = 'SELECT ec.id, ec.comment_body as comment, ec.reason, ec.date, u.name
                FROM #__emundus_comments ec
                LEFT JOIN #__users u ON u.id = ec.user_id
                WHERE ec.applicant_id ="'.$id.'"
                ORDER BY ec.date DESC ';
        $this->_db->setQuery($query);
        return $this->_db->loadObjectList();
    }

    public function getComment($id) {
        $query = 'SELECT * FROM #__emundus_comments ec WHERE ec.id ='.$id;
        $this->_db->setQuery($query);
        return $this->_db->loadAssoc();
    }

    public function getTag($id) {
        $query = 'SELECT * FROM #__emundus_tag_assoc WHERE id ='.$id;
        $this->_db->setQuery($query);
        return $this->_db->loadAssoc();
    }

    public function getFileComments($fnum) {

        $query = 'SELECT ec.id, ec.comment_body as comment, ec.reason, ec.fnum, ec.user_id, ec.date, u.name
                FROM #__emundus_comments ec
                LEFT JOIN #__users u ON u.id = ec.user_id
                WHERE ec.fnum like '.$this->_db->Quote($fnum).'
                ORDER BY ec.date ASC ';
        $this->_db->setQuery($query);
        return $this->_db->loadObjectList();
    }
    public function getFileOwnComments($fnum,$user_id) {

        $query = 'SELECT ec.id, ec.comment_body as comment, ec.reason, ec.fnum, ec.user_id, ec.date, u.name
                FROM #__emundus_comments ec
                LEFT JOIN #__users u ON u.id = ec.user_id
                WHERE ec.fnum like '.$this->_db->Quote($fnum).'
                AND ec.user_id = '.$user_id.'
                ORDER BY ec.date ASC ';
        $this->_db->setQuery($query);
        return $this->_db->loadObjectList();
    }

    public function editComment($id, $title, $text) {

        try {

            $query = 'UPDATE #__emundus_comments SET reason = '.$this->_db->quote($title).', comment_body = '.$this->_db->quote($text).'  WHERE id = '.$this->_db->quote($id);
            $this->_db->setQuery($query);
            $this->_db->execute();

            // Logging requires the fnum, we have to get this from the comment ID being edited.
            // Only get the fnum if logging is on and comments are in the list of actions to be logged.
            $eMConfig = JComponentHelper::getParams('com_emundus');
            $log_actions = $eMConfig->get('log_action', null);
            if ($eMConfig->get('logs', 0) && (empty($log_actions) || in_array(10, explode(',',$log_actions)))) {

                $query = $this->_db->getQuery(true);
                $query->select($this->_db->quoteName('fnum'))
                    ->from($this->_db->quoteName('#__emundus_comments'))
                    ->where($this->_db->quoteName('id').'='.$id);

                $this->_db->setQuery($query);
                $fnum = $this->_db->loadResult();

                // Log the comment in the eMundus logging system.
                EmundusModelLogs::log(JFactory::getUser()->id, (int) substr($fnum, -7), $fnum, 10, 'u', 'COM_EMUNDUS_LOGS_UPDATE_COMMENT');
            }

            return true;

        } catch (Exception $e) {
            JLog::add('Query: '.$query.' Error:'.$e->getMessage(), JLog::ERROR, 'com_emundus');
            return false;
        }

    }

    public function deleteComment($id, $fnum = null) {

    	$query = $this->_db->getQuery(true);

    	if (empty($fnum)) {
    		$query->select($this->_db->quoteName('fnum'))
			    ->from($this->_db->quoteName('#__emundus_comments'))
			    ->where($this->_db->quoteName('id').' = '.$id);
		    $this->_db->setQuery($query);

		    try {
			    $this->_db->execute();
		    } catch (Exception $e) {
		    	JLog::add('Error getting fnum for comment id '.$id.' in m/application.', JLog::ERROR, 'com_emundus');
		    }
	    }

    	$query->clear()->delete($this->_db->quoteName('#__emundus_comments'))
		    ->where($this->_db->quoteName('id').' = '.$id);
        $this->_db->setQuery($query);
        try {
            $res = $this->_db->execute();
        } catch (Exception $e) {
	        JLog::add('Error deleting comment id '.$id.' in m/application. ERROR -> '.$e->getMessage(), JLog::ERROR, 'com_emundus');
	        return false;
        }

        if ($res && !empty($fnum)) {
            EmundusModelLogs::log(JFactory::getUser()->id, (int) substr($fnum, -7), $fnum, 10, 'd', 'COM_EMUNDUS_LOGS_DELETE_COMMENT');
        }

        return $res;

    }

    public function deleteTag($id_tag, $fnum) {
        $query = 'DELETE FROM #__emundus_tag_assoc WHERE id_tag = '.$id_tag.' AND fnum like '.$this->_db->Quote($fnum);
        $this->_db->setQuery($query);
        $res = $this->_db->execute();

        // Log the action in the eMundus logging system.
        if ($res) {
            EmundusModelLogs::log(JFactory::getUser()->id, (int)substr($fnum, -7), $fnum, 14, 'd', 'COM_EMUNDUS_LOGS_DELETE_TAG');
        }

        return $res;
    }

    public function addComment($row) {

        // Log the comment in the eMundus logging system.
        EmundusModelLogs::log(JFactory::getUser()->id, (int)substr($row['fnum'], -7), $row['fnum'], 10, 'c', 'COM_EMUNDUS_LOGS_ADD_COMMENT');

        $query = 'INSERT INTO `#__emundus_comments` (applicant_id, user_id, reason, date, comment_body, fnum)
                VALUES('.$row['applicant_id'].','.$row['user_id'].','.$this->_db->Quote($row['reason']).',"'.date("Y.m.d H:i:s").'",'.$this->_db->Quote($row['comment_body']).','.$this->_db->Quote(@$row['fnum']).')';
        $this->_db->setQuery($query);

        try {
            $this->_db->execute();
            return $this->_db->insertid();
        } catch(Exception $e) {
	        JLog::add('Error in model/application at query: '.$query, JLog::ERROR, 'com_emundus');
	        return null;
        }
    }

    public function deleteData($id, $table){
        $query = 'DELETE FROM `'.$table.'` WHERE id='.$id;
        $this->_db->setQuery($query);

        return $this->_db->Query();
    }

    public function deleteAttachment($id) {

        try {

            $query = 'SELECT * FROM #__emundus_uploads WHERE id='.$id;
            $this->_db->setQuery($query);
            $file = $this->_db->loadAssoc();

        } catch (Exception $e) {
            JLog::add('Error in model/application at query: '.$query, JLog::ERROR, 'com_emundus');
        }

        $f = EMUNDUS_PATH_ABS.$file['user_id'].DS.$file['filename'];
        @unlink($f);
        /*if(!@unlink($f) && file_exists($f)) {
            // JError::raiseError(500, JText::_('FILE_NOT_FOUND').$file);
            //$this->setRedirect($url, JText::_('FILE_NOT_FOUND'), 'error');
            return -1;
        }*/

        try {

            $query = 'DELETE FROM #__emundus_uploads WHERE id='.$id;
            $this->_db->setQuery($query);
            return $this->_db->Query();

        } catch (Exception $e) {
            JLog::add('Error in model/application at query: '.$query, JLog::ERROR, 'com_emundus');
        }
    }

    public function uploadAttachment($data) {
        try {
           /* $i = 0;
            foreach ($data['value'] as $key=>$value) {
                $data['value'][$i] =  str_replace('"','', $value);
                $i++;
            }*/
            $query = "INSERT INTO #__emundus_uploads (".implode(',', $data["key"]).") VALUES ('".implode("','", $data["value"])."')";
            $this->_db->setQuery( $query );
            $this->_db->execute();
            return $this->_db->insertid();
        }catch (RuntimeException $e) {
            JFactory::getApplication()->enqueueMessage($e->getMessage());

            return false;
        }
    }

    public function getAttachmentByID($id) {
        $query = "SELECT * FROM #__emundus_setup_attachments WHERE id=".$id;
        $this->_db->setQuery($query);

        return $this->_db->loadAssoc();
    }

    public function getAttachmentByLbl($label) {
        $query = "SELECT * FROM #__emundus_setup_attachments WHERE lbl LIKE".$this->_db->Quote($label);
        $this->_db->setQuery($query);

        return $this->_db->loadAssoc();
    }

    public function getUploadByID($id) {
        $query = "SELECT * FROM #__emundus_uploads WHERE id=".$id;
        $this->_db->setQuery($query);

        return $this->_db->loadAssoc();
    }

	/**
	 * @param   string  $fnum
	 *
	 * @return array|bool|false|float
	 *
	 * @since version
	 */
    public function getFormsProgress($fnum = "0") {

        if (empty($fnum) || (!is_array($fnum) && !is_numeric($fnum))) {
            return false;
        }
        
        $session = JFactory::getSession();
        $current_user = $session->get('emundusUser');


        if (!is_array($fnum)) {

            $query = 'SELECT ess.profile
                    FROM #__emundus_setup_status AS ess
                    LEFT JOIN #__emundus_campaign_candidature AS ecc ON ecc.status = ess.step
                    WHERE ecc.fnum like '.$this->_db->Quote($fnum);
            $this->_db->setQuery($query);

            $profile_id = $this->_db->loadResult();

            if (empty($profile_id)) {
                $query = 'SELECT esc.profile_id
                    FROM #__emundus_setup_campaigns AS esc
                    LEFT JOIN #__emundus_campaign_candidature AS ecc ON ecc.campaign_id = esc.id
                    WHERE ecc.fnum like '.$this->_db->Quote($fnum);
                $this->_db->setQuery($query);

                $profile_id = (!empty($current_user->fnums[$fnum]) && $current_user->profile != $this->_db->loadResult()) ? $current_user->profile : $this->_db->loadResult();
            }

            $forms = @EmundusHelperMenu::buildMenuQuery($profile_id);
            $nb = 0;
            $formLst = array();

            foreach ($forms as $form) {
                $query = 'SELECT count(*) FROM '.$form->db_table_name.' WHERE fnum like '.$this->_db->Quote($fnum);
                $this->_db->setQuery($query);
                $cpt = $this->_db->loadResult();
                if ($cpt == 1) {
                    $nb++;
                } else {
                    $formLst[] = $form->label;
                }
            }

            return  @floor(100*$nb/count($forms));

        } else {

            $result = array();
            foreach ($fnum as $f) {

                $query = 'SELECT ess.profile
                    FROM #__emundus_setup_status AS ess
                    LEFT JOIN #__emundus_campaign_candidature AS ecc ON ecc.status = ess.step
                    WHERE ecc.fnum like '.$this->_db->Quote($f);
                $this->_db->setQuery($query);

                if (empty($this->_db->loadResult())) {
                    $query = 'SELECT esc.profile_id
                        FROM #__emundus_setup_campaigns AS esc
                        LEFT JOIN #__emundus_campaign_candidature AS ecc ON ecc.campaign_id = esc.id
                        WHERE ecc.fnum like '.$this->_db->Quote($f);
                    $this->_db->setQuery($query);
                }

                $forms = @EmundusHelperMenu::buildMenuQuery($this->_db->loadResult());
                $nb = 0;
                $formLst = array();
                foreach ($forms as $form) {
                    $query = 'SELECT count(*) FROM '.$form->db_table_name.' WHERE fnum like '.$this->_db->Quote($f);
                    $this->_db->setQuery($query);
                    $cpt = $this->_db->loadResult();
                    if ($cpt==1) {
                        $nb++;
                    } else {
                        $formLst[] = $form->label;
                    }
                }
                $result[$f] = @floor(100*$nb/count($forms));
            }
            return $result;
        }
    }

	/**
	 * @param   null  $fnum
	 *
	 * @return array|bool|false|float
	 *
	 * @since version
	 */
    public function getAttachmentsProgress($fnum = null) {

        if (empty($fnum) || (!is_array($fnum) && !is_numeric($fnum))) {
            return false;
        }

        // Check if column campaign_id exist in emundus_setup_attachment_profiles
        $config = new JConfig();
        $db_name = $config->db;

        $query = $this->_db->getQuery(true);
        $query->select('COUNT(*)')
            ->from($this->_db->quoteName('information_schema.columns'))
            ->where($this->_db->quoteName('table_schema') . ' = ' . $this->_db->quote($db_name))
            ->andWhere($this->_db->quoteName('table_name') . ' = ' . $this->_db->quote('jos_emundus_setup_attachment_profiles'))
            ->andWhere($this->_db->quoteName('column_name') . ' = ' . $this->_db->quote('campaign_id'));
        $this->_db->setQuery($query);
        $exist = $this->_db->loadResult();

        if (!is_array($fnum)) {

            $query = 'SELECT ess.profile AS profile_id, ecc.campaign_id AS campaign_id
                    FROM #__emundus_setup_status AS ess
                    LEFT JOIN #__emundus_campaign_candidature AS ecc ON ecc.status = ess.step
                    WHERE ecc.fnum like '.$this->_db->Quote($fnum);
            $this->_db->setQuery($query);

            if (empty($this->_db->loadObject()->profile_id)) {
                $query = 'SELECT esc.profile_id AS profile_id, ecc.campaign_id AS campaign_id
                FROM #__emundus_setup_campaigns AS esc
                LEFT JOIN #__emundus_campaign_candidature AS ecc ON ecc.campaign_id = esc.id
                WHERE ecc.fnum like '.$this->_db->Quote($fnum);
                $this->_db->setQuery($query);
            }

            $procamp = $this->_db->loadObject();

            $profile_id = $procamp->profile_id;
            $campaign_id = $procamp->campaign_id;

            if (intval($exist) > 0) {
                $query = 'SELECT COUNT(profiles.id)
                    FROM #__emundus_setup_attachment_profiles AS profiles
                    WHERE profiles.campaign_id = ' . intval($campaign_id) . ' AND profiles.displayed = 1';
                $this->_db->setQuery($query);
                $attachments = $this->_db->loadResult();
            }

            if (intval($exist) == 0 || intval($attachments) == 0) {
                $query = 'SELECT IF(COUNT(profiles.attachment_id)=0, 100, 100*COUNT(uploads.attachment_id>0)/COUNT(profiles.attachment_id))
                FROM #__emundus_setup_attachment_profiles AS profiles
                LEFT JOIN #__emundus_uploads AS uploads ON uploads.attachment_id = profiles.attachment_id AND uploads.fnum like '.$this->_db->Quote($fnum).'
                WHERE profiles.profile_id = ' .$profile_id. ' AND profiles.displayed = 1 AND profiles.mandatory = 1' ;
            } else {
                $query = 'SELECT IF(COUNT(profiles.attachment_id)=0, 100, 100*COUNT(uploads.attachment_id>0)/COUNT(profiles.attachment_id))
                FROM #__emundus_setup_attachment_profiles AS profiles
                LEFT JOIN #__emundus_uploads AS uploads ON uploads.attachment_id = profiles.attachment_id AND uploads.fnum like '.$this->_db->Quote($fnum).'
                WHERE profiles.campaign_id = ' .$campaign_id. ' AND profiles.displayed = 1 AND profiles.mandatory = 1' ;
            }

            $this->_db->setQuery($query);
            return floor($this->_db->loadResult());

        } else {

            $result = array();
            foreach ($fnum as $f) {
                $query = 'SELECT ess.profile AS profile_id, ecc.campaign_id AS campaign_id
                    FROM #__emundus_setup_status AS ess
                    LEFT JOIN #__emundus_campaign_candidature AS ecc ON ecc.status = ess.step
                    WHERE ecc.fnum like '.$this->_db->Quote($f);
                $this->_db->setQuery($query);

                if (empty($this->_db->loadObject()->profile_id)) {
                    $query = 'SELECT esc.profile_id AS profile_id, ecc.campaign_id AS campaign_id
                        FROM #__emundus_setup_campaigns AS esc
                        LEFT JOIN #__emundus_campaign_candidature AS ecc ON ecc.campaign_id = esc.id
                        WHERE ecc.fnum like '.$this->_db->Quote($f);
                    $this->_db->setQuery($query);
                }

	            $procamp = $this->_db->loadObject();
                $profile_id = $procamp->profile_id;
                $campaign_id = $procamp->campaign_id;

                if (intval($exist) > 0) {
                    $query = 'SELECT COUNT(profiles.id)
                    FROM #__emundus_setup_attachment_profiles AS profiles
                    WHERE profiles.campaign_id = ' . intval($campaign_id) . ' AND profiles.displayed = 1';
                    $this->_db->setQuery($query);
                    $attachments = $this->_db->loadResult();
                }

                if (intval($exist) == 0 || intval($attachments) == 0) {
                    $query = 'SELECT IF(COUNT(profiles.attachment_id)=0, 100, 100*COUNT(uploads.attachment_id>0)/COUNT(profiles.attachment_id))
                    FROM #__emundus_setup_attachment_profiles AS profiles
                    LEFT JOIN #__emundus_uploads AS uploads ON uploads.attachment_id = profiles.attachment_id AND uploads.fnum like ' . $this->_db->Quote($f) . '
                    WHERE profiles.profile_id = ' . $profile_id . ' AND profiles.displayed = 1 AND profiles.mandatory = 1';
                } else {
                    $query = 'SELECT IF(COUNT(profiles.attachment_id)=0, 100, 100*COUNT(uploads.attachment_id>0)/COUNT(profiles.attachment_id))
                    FROM #__emundus_setup_attachment_profiles AS profiles
                    LEFT JOIN #__emundus_uploads AS uploads ON uploads.attachment_id = profiles.attachment_id AND uploads.fnum like '.$this->_db->Quote($f).'
                    WHERE profiles.campaign_id = ' .$campaign_id. ' AND profiles.displayed = 1 AND profiles.mandatory = 1';
                }

                $this->_db->setQuery($query);
                $result[$f] = floor($this->_db->loadResult());
            }
            return $result;
        }
    }


	/**
	 * @param $aid
	 *
	 * @return bool|mixed
	 *
	 * @since version
	 */
    public function getLogged($aid) {
        $user = JFactory::getUser();
        $query = 'SELECT s.time, s.client_id, u.id, u.name, u.username
                    FROM #__session AS s
                    LEFT JOIN #__users AS u on s.userid = u.id
                    WHERE u.id = "'.$aid.'"';
        $this->_db->setQuery($query);
        $results = $this->_db->loadObjectList();

        // Check for database errors
        if ($error = $this->_db->getErrorMsg()) {
            JError::raiseError(500, $error);
            return false;
        }

        foreach ($results as $k => $result) {
            $results[$k]->logoutLink = '';

            if ($user->authorise('core.manage', 'com_users')) {
                $results[$k]->editLink = JRoute::_('index.php?option=com_emundus&view=users&edit=1&rowid='.$result->id.'&tmpl=component');
                $results[$k]->logoutLink = JRoute::_('index.php?option=com_login&task=logout&uid='.$result->id .'&'. JSession::getFormToken() .'=1');
            }
            $results[$k]->name = $results[$k]->username;
        }

        return $results;
    }


	/**
	 * @param        $formID
	 * @param        $aid
	 * @param   int  $fnum
	 *
	 * @return string|null
	 *
	 * @since version
	 */
    public function getFormByFabrikFormID($formID, $aid, $fnum = 0) {
        $h_access = new EmundusHelperAccess;

        $form = '';

        // Get table by form ID
        $query = 'SELECT fbtables.id AS table_id, fbtables.form_id, fbforms.label, fbtables.db_table_name
                    FROM #__fabrik_forms AS fbforms
                    LEFT JOIN #__fabrik_lists AS fbtables ON fbtables.form_id = fbforms.id
                    WHERE fbforms.id IN ('.implode(',', $formID) . ')';

        try {
            $this->_db->setQuery($query);
            $table = $this->_db->loadObjectList();
        } catch (Exception $e) {
            return null;
        }


        for ($i = 0; $i < sizeof($table); $i++) {
            $form .= '<br><hr><div class="TitleAdmission"><h2>';

            $title = explode('-', $table[$i]->label);
            $form .= !empty($title[1])?JText::_(trim($title[1])):JText::_(trim($title[0]));

            $form .= '</h2>';
            if ($h_access->asAccessAction(1, 'u', $this->_user->id, $fnum) && $table[$i]->db_table_name != "#__emundus_training") {

                $query = 'SELECT count(id) FROM `'.$table[$i]->db_table_name.'` WHERE fnum like '.$this->_db->Quote($fnum);
                try {

                    $this->_db->setQuery( $query );
                    $cpt = $this->_db->loadResult();

                } catch (Exception $e) {
                    return $e->getMessage();
                }


                if ($cpt > 0) {
                    $form .= '<button type="button" id="'.$table[$i]->form_id.'" class="btn btn btn-info btn-sm em-actions-form marginRightbutton" url="index.php?option=com_fabrik&view=form&formid='.$table[$i]->form_id.'&usekey=fnum&rowid='.$fnum.'&tmpl=component" alt="'.JText::_('EDIT').'"><span class="glyphicon glyphicon-edit"></span><i> '.JText::_('EDIT').'</i></button>';
                } else {
                    $form .= '<button type="button" id="'.$table[$i]->form_id.'" class="btn btn-default btn-sm em-actions-form marginRightbutton" url="index.php?option=com_fabrik&view=form&formid='.$table[$i]->form_id.'&'.$table[$i]->db_table_name.'___fnum='.$fnum.'&'.$table[$i]->db_table_name.'___user_raw='.$aid.'&'.$table[$i]->db_table_name.'___user='.$aid.'&sid='.$aid.'&tmpl=component" alt="'.JText::_('EDIT').'"><span class="glyphicon glyphicon-edit"></span><i> '.JText::_('ADD').'</i></button>';
                }
            }
            $form .= '</div>';

            // liste des groupes pour le formulaire d'une table
            $query = 'SELECT ff.id, ff.group_id, fg.id, fg.label, INSTR(fg.params,"\"repeat_group_button\":\"1\"") as repeated, INSTR(fg.params,"\"repeat_group_button\":1") as repeated_1
                                FROM #__fabrik_formgroup ff, #__fabrik_groups fg
                                WHERE ff.group_id = fg.id AND
                                    ff.form_id = '.$table[$i]->form_id.'
                                ORDER BY ff.ordering';
            try {

                $this->_db->setQuery($query);
                $groupes = $this->_db->loadObjectList();

            } catch (Exception $e) {
                return $e->getMessage();
            }

            /*-- Liste des groupes -- */
            foreach ($groupes as $itemg) {
                // liste des items par groupe
                $query = 'SELECT fe.id, fe.name, fe.label, fe.plugin, fe.params
                            FROM #__fabrik_elements fe
                            WHERE fe.published=1 AND fe.hidden=0 AND fe.group_id = "'.$itemg->group_id.'"
                            ORDER BY fe.ordering';

                try {
                    $this->_db->setQuery($query);
                    $elements = $this->_db->loadObjectList();
                } catch (Exception $e) {
                    return $e->getMessage();
                }

                if (count($elements) > 0) {
                    $form .= '<fieldset><legend class="legend">';
                    $form .= JText::_($itemg->label);
                    $form .= '</legend>';

                    if ($itemg->group_id == 14) {

                        foreach ($elements as &$element) {
                            if (!empty($element->label) && $element->label!=' ') {

                                if ($element->plugin == 'date' && $element->content > 0) {

                                    $date_params = json_decode($element->params);
                                    $elt = date($date_params->date_form_format, strtotime($element->content));

                                } elseif (($element->plugin=='birthday' || $element->plugin=='birthday_remove_slashes') && $element->content > 0) {
                                    $format = json_decode($element->params)->list_date_format;

                                    $d = DateTime::createFromFormat($format, $element->content);
                                    if ($d && $d->format($format) == $element->content) {
                                        $elt = JHtml::_('date', $element->content, JText::_('DATE_FORMAT_LC'));
                                    } else {
                                        $elt = JHtml::_('date', $element->content, $format);
                                    }

                                } elseif ($element->plugin == 'databasejoin') {

                                    $params = json_decode($element->params);
                                    $select = !empty($params->join_val_column_concat)?"CONCAT(".$params->join_val_column_concat.")":$params->join_val_column;
                                    $from   = $params->join_db_name;
                                    $where  = $params->join_key_column.'='.$this->_db->Quote($element->content);
                                    $query  = "SELECT ".$select." FROM ".$from." WHERE ".$where;
                                    $query  = preg_replace('#{thistable}#', $from, $query);
                                    $query  = preg_replace('#{shortlang}#', $this->locales, $query);
                                    $query  = preg_replace('#{my->id}#', $aid, $query);

                                    try {

                                        $this->_db->setQuery($query);

                                        $elt = $this->_db->loadResult();

                                    } catch (Exception $e) {
                                        return $e->getMessage();
                                    }

                                } elseif ($element->plugin == 'checkbox') {

                                    $elt = implode(", ", json_decode (@$element->content));

                                } else {
                                    $elt = $element->content;
                                }

                                $form .= '<b>'.JText::_($element->label).': </b>'.JText::_($elt).'<br/>';
                            }
                        }

                        // TABLEAU DE PLUSIEURS LIGNES
                    }
                    elseif ($itemg->repeated > 0 || $itemg->repeated_1 > 0) {

                        $form .= '<table class="table table-bordered table-striped">
                            <thead>
                            <tr> ';

                        // Entrée du tableau
                        $t_elt = array();

                        foreach($elements as &$element) {
                            $t_elt[] = $element->name;
                            $form .= '<th scope="col">'.JText::_($element->label).'</th>';
                        }
                        unset($element);

                        $query = 'SELECT table_join FROM #__fabrik_joins WHERE group_id='.$itemg->group_id.' AND table_join_key like "parent_id"';

                        try {

                            $this->_db->setQuery($query);
                            $r_table = $this->_db->loadResult();

                        } catch (Exception $e) {
                            return $e->getMessage();
                        }

                        $query = 'SELECT `'.implode("`,`", $t_elt).'`, id FROM '.$r_table.' WHERE parent_id=(SELECT id FROM ' . $table[$i]->db_table_name . ' WHERE fnum like '.$this->_db->Quote($fnum).')';


                        try {

                            $this->_db->setQuery($query);
                            $repeated_elements = $this->_db->loadObjectList();

                        } catch (Exception $e) {
                            return $e->getMessage();
                        }

                        unset($t_elt);
                        $form .= '</tr></thead>';

                        // Ligne du tableau
                        if (count($repeated_elements) > 0) {
                            $form .= '<tbody>';

                            foreach ($repeated_elements as $r_element) {
                                $form .= '<tr>';
                                $j = 0;

                                foreach ($r_element as $key => $r_elt) {
                                    if ($key != 'id' && $key != 'parent_id' && isset($elements[$j])) {

                                        if ($elements[$j]->plugin == 'date') {

                                            $date_params = json_decode($elements[$j]->params);
                                            $elt = date($date_params->date_form_format, strtotime($r_elt));

                                        } elseif (($elements[$j]->plugin=='birthday' || $elements[$j]->plugin=='birthday_remove_slashes') && $r_elt > 0) {

                                            $format = json_decode($elements[$j]->params)->list_date_format;
                                            $d = DateTime::createFromFormat($format, $r_elt);
                                            if ($d && $d->format($format) == $r_elt) {
                                                $elt = JHtml::_('date', $r_elt, JText::_('DATE_FORMAT_LC'));
                                            } else {
                                                $elt = JHtml::_('date', $r_elt, $format);
                                            }

                                        } elseif ($elements[$j]->plugin == 'databasejoin') {

                                            $params = json_decode($elements[$j]->params);
                                            $select = !empty($params->join_val_column_concat)?"CONCAT(".$params->join_val_column_concat.")":$params->join_val_column;
                                            $from   = $params->join_db_name;
                                            $where  = $params->join_key_column.'='.$this->_db->Quote($r_elt);
                                            $query  = "SELECT ".$select." FROM ".$from." WHERE ".$where;
                                            $query  = preg_replace('#{thistable}#', $from, $query);
                                            $query  = preg_replace('#{my->id}#', $aid, $query);
                                            $query  = preg_replace('#{shortlang}#', $this->locales, $query);

                                            try {
                                                $this->_db->setQuery($query);
                                                $elt = $this->_db->loadResult();
                                            } catch (Exception $e) {
                                                return $e->getMessage();
                                            }

                                        } elseif ($elements[$j]->plugin == 'checkbox') {

                                            $elt = implode(", ", json_decode (@$r_elt));

                                        } elseif ($elements[$j]->plugin == 'dropdown' || $elements[$j]->plugin == 'radiobutton') {

                                            $params = json_decode($elements[$j]->params);
                                            $index = array_search($r_elt, $params->sub_options->sub_values);
                                            if (strlen($index) > 0) {
                                                $elt = JText::_($params->sub_options->sub_labels[$index]);
                                            } else {
                                                $elt = "";
                                            }

                                        } else {
                                            $elt = $r_elt;
                                        }

                                        $form .= '<td><div id="em_training_'.$r_element->id.'" class="course '.$r_element->id.'"> '.JText::_($elt).'</div></td>';
                                    }
                                    $j++;
                                }
                                $form .= '</tr>';
                            }
                            $form .= '</tbody>';
                        }
                        $form .= '</table>';

                        // AFFICHAGE EN LIGNE
                    } else {
                        $form .='<table class="em-personalDetail-table-inline">';
                        $modulo = 0;
                        foreach ($elements as &$element) {

                            if (!empty($element->label) && $element->label != ' ') {
                                $query = 'SELECT `id`, `'.$element->name .'` FROM `'.$table[$i]->db_table_name.'` WHERE fnum like '.$this->_db->Quote($fnum);

                                try {
                                    $this->_db->setQuery($query);
                                    $res = $this->_db->loadRow();
                                } catch (Exception $e) {
                                    return $e->getMessage();
                                }

                                $element->content = @$res[1];
                                $element->content_id = @$res[0];

                                // Do not display elements with no value inside them.
                                if ($show_empty_fields == 0 && trim($element->content) == '') {
                                    continue;
                                }
                                if ($element->plugin == 'date' && $element->content > 0) {

                                    $date_params = json_decode($element->params);
                                    $elt = date($date_params->date_form_format, strtotime($element->content));

                                } elseif (($element->plugin=='birthday' || $element->plugin=='birthday_remove_slashes') && $element->content > 0) {
                                    $format = json_decode($element->params)->list_date_format;

                                    $d = DateTime::createFromFormat($format, $element->content);
                                    if ($d && $d->format($format) == $element->content) {
                                        $elt = JHtml::_('date', $element->content, JText::_('DATE_FORMAT_LC'));
                                    } else {
                                        $elt = JHtml::_('date', $element->content, $format);
                                    }

                                }
                                elseif ($element->plugin == 'databasejoin') {

                                    $params = json_decode($element->params);
                                    $select = !empty($params->join_val_column_concat)?"CONCAT(".$params->join_val_column_concat.")":$params->join_val_column;

                                    if ($params->database_join_display_type == 'checkbox') {

                                        $elt = implode(", ", json_decode (@$element->content));

                                    } else {

                                        $from   = $params->join_db_name;
                                        $where  = $params->join_key_column.'='.$this->_db->Quote($element->content);
                                        $query  = "SELECT ".$select." FROM ".$from." WHERE ".$where;
                                        $query  = preg_replace('#{thistable}#', $from, $query);
                                        $query  = preg_replace('#{my->id}#', $aid, $query);
                                        $query  = preg_replace('#{shortlang}#', $this->locales, $query);

                                        try {
                                            $this->_db->setQuery($query);
                                            $elt = $this->_db->loadResult();
                                        } catch (Exception $e) {
                                            return $e->getMessage();
                                        }
                                    }

                                } elseif ($element->plugin == 'cascadingdropdown') {

                                    $params = json_decode($element->params);
                                    $cascadingdropdown_id = $params->cascadingdropdown_id;

                                    $r1 = explode('___', $cascadingdropdown_id);
                                    $cascadingdropdown_label = JText::_($params->cascadingdropdown_label);

                                    $r2 = explode('___', $cascadingdropdown_label);

                                    $select = !empty($params->cascadingdropdown_label_concat)?"CONCAT(".$params->cascadingdropdown_label_concat.")":$r2[1];
                                    $from   = $r2[0];
                                    $where  = $r1[1].'='.$this->_db->Quote($element->content);
                                    $query  = "SELECT ".$select." FROM ".$from." WHERE ".$where;
                                    $query  = preg_replace('#{thistable}#', $from, $query);
                                    $query  = preg_replace('#{my->id}#', $aid, $query);
                                    $query  = preg_replace('#{shortlang}#', $this->locales, $query);

                                    try {
                                        $this->_db->setQuery($query);
                                        $elt = $this->_db->loadResult();
                                    } catch (Exception $e) {
                                        return $e->getMessage();
                                    }

                                } elseif ($element->plugin == 'checkbox') {

                                    $elt = implode(", ", json_decode (@$element->content));

                                } elseif ($element->plugin == 'dropdown' || $element->plugin == 'radiobutton') {

                                    $params = json_decode($element->params);
                                    $index  = array_search($element->content, $params->sub_options->sub_values);
                                    if (strlen($index) > 0) {
                                        $elt = JText::_($params->sub_options->sub_labels[$index]);
                                    } else {
                                        $elt = "";
                                    }

                                } else {

                                    $elt = $element->content;
                                }

                                // modulo for strips css
                                if ($modulo%2) {
                                    $form .= '<tr class="table-strip-1"><td style="padding-right:50px;"><b>'.JText::_($element->label).'</b></td> <td> '.JText::_($elt).'</td></tr>';
                                } else {
                                    $form .= '<tr class="table-strip-2"><td style="padding-right:50px;"><b>'.JText::_($element->label).'</b></td> <td> '.JText::_($elt).'</td></tr>';
                                }
                                $modulo++;
                            }
                        }
                    }
                    $form .= '</table>';
                    $form .= '</fieldset>';
                }
            }
        }
        return $form;
    }

    // Get form to display in application page layout view
    public function getForms($aid, $fnum = 0, $pid = 9) {
        $h_menu = new EmundusHelperMenu;
        $h_access = new EmundusHelperAccess;
        $tableuser = $h_menu->buildMenuQuery($pid);

	    $eMConfig = JComponentHelper::getParams('com_emundus');
	    $show_empty_fields = $eMConfig->get('show_empty_fields', 1);

        $forms = '';

        try {

	        if (isset($tableuser)) {

		        $allowed_groups = EmundusHelperAccess::getUserFabrikGroups($this->_user->id);

	            $allowEmbed = $this->allowEmbed(JURI::base().'index.php?lang=en');

	            foreach ($tableuser as $key => $itemt) {

	                $forms .= '<br><hr><div class="TitlePersonalInfo em-personalInfo"><h3>';
	                $title = explode('-', JText::_($itemt->label));

	                if (empty($title[1])) {
		                $forms .= JText::_(trim($itemt->label));
	                } else {
		                $forms .= JText::_(trim($title[1]));
	                }
                    $forms .= '</h3>';
	                if ($h_access->asAccessAction(1, 'u', $this->_user->id, $fnum) && $itemt->db_table_name != "#__emundus_training") {

	                    $query = 'SELECT count(id) FROM `'.$itemt->db_table_name.'` WHERE user='.$aid.' AND fnum like '.$this->_db->Quote($fnum);
	                    $this->_db->setQuery($query);
	                    $cpt = $this->_db->loadResult();

	                    if ($cpt > 0) {

	                        if ($allowEmbed) {
	                           $forms .= ' <button type="button" id="'.$itemt->form_id.'" class="btn btn btn-info btn-sm em-actions-form" url="index.php?option=com_fabrik&view=form&formid='.$itemt->form_id.'&usekey=fnum&rowid='.$fnum.'&tmpl=component" alt="'.JText::_('EDIT').'" target="_blank"><span class="glyphicon glyphicon-edit"></span><i> '.JText::_('EDIT').'</i></button>';
	                        } else {
	                            $forms .= ' <a id="'.$itemt->form_id.'" class="btn btn btn-info btn-sm" href="index.php?option=com_fabrik&view=form&formid='.$itemt->form_id.'&usekey=fnum&rowid='.$fnum.'" alt="'.JText::_('EDIT').'" target="_blank"><span class="glyphicon glyphicon-edit"></span><i> '.JText::_('EDIT').'</i></a>';
	                        }

	                    } else {

	                        if ($allowEmbed) {
	                            $forms .= ' <button type="button" id="'.$itemt->form_id.'" class="btn btn-default btn-sm em-actions-form" url="index.php?option=com_fabrik&view=form&formid='.$itemt->form_id.'&'.$itemt->db_table_name.'___fnum='.$fnum.'&'.$itemt->db_table_name.'___user_raw='.$aid.'&'.$itemt->db_table_name.'___user='.$aid.'&sid='.$aid.'&tmpl=component" alt="'.JText::_('EDIT').'"><span class="glyphicon glyphicon-edit"></span><i> '.JText::_('ADD').'</i></button>';
	                        } else {
	                            $forms .= ' <a type="button" id="'.$itemt->form_id.'" class="btn btn-default btn-sm" href="index.php?option=com_fabrik&view=form&formid='.$itemt->form_id.'&'.$itemt->db_table_name.'___fnum='.$fnum.'&'.$itemt->db_table_name.'___user_raw='.$aid.'&'.$itemt->db_table_name.'___user='.$aid.'&sid='.$aid.'" alt="'.JText::_('EDIT').'" target="_blank"><span class="glyphicon glyphicon-edit"></span><i> '.JText::_('ADD').'</i></a>';
	                        }

	                    }
	                }
                    $forms .= '</div>';

	                // liste des groupes pour le formulaire d'une table
	                $query = 'SELECT ff.id, ff.group_id, fg.id, fg.label, INSTR(fg.params,"\"repeat_group_button\":\"1\"") as repeated, INSTR(fg.params,"\"repeat_group_button\":1") as repeated_1
	                            FROM #__fabrik_formgroup ff, #__fabrik_groups fg
	                            WHERE ff.group_id = fg.id AND
	                                  ff.form_id = "'.$itemt->form_id.'"
	                            ORDER BY ff.ordering';
	                $this->_db->setQuery($query);
	                $groupes = $this->_db->loadObjectList();

	                /*-- Liste des groupes -- */
	                foreach ($groupes as $itemg) {
	                	if ($allowed_groups !== true && !in_array($itemg->group_id, $allowed_groups)) {
			                $forms .= '<fieldset class="em-personalDetail">
											<legend class="legend">'.JText::_($itemg->label).'</legend>
											<table class="em-restricted-group">
												<thead><tr><td>'.JText::_('COM_EMUNDUS_CANNOT_SEE_GROUP').'</td></tr></thead>
											</table>
										</fieldset>';
	                		continue;
		                }

	                    // liste des items par groupe
	                    $query = 'SELECT fe.id, fe.name, fe.label, fe.plugin, fe.params
	                                FROM #__fabrik_elements fe
	                                WHERE fe.published=1 AND
	                                      fe.hidden=0 AND
	                                      fe.group_id = "'.$itemg->group_id.'"
	                                ORDER BY fe.ordering';

                        try {
                            $this->_db->setQuery($query);
                            $elements = $this->_db->loadObjectList();
                        } catch (Exception $e) {
                            JLog::add('Error in model/application at query: '.$query, JLog::ERROR, 'com_emundus');
                            throw $e;
                        }

	                    if (count($elements) > 0) {


	                        if ($itemg->repeated > 0 || $itemg->repeated_1 > 0) {

	                            $query = 'SELECT table_join FROM #__fabrik_joins WHERE list_id='.$itemt->table_id.' AND group_id='.$itemg->group_id.' AND table_join_key like "parent_id"';
                                try {
                                    $this->_db->setQuery($query);
                                    $table = $this->_db->loadResult();
                                } catch (Exception $e) {
                                    JLog::add('Error in model/application at query: '.$query, JLog::ERROR, 'com_emundus');
                                    throw $e;
                                }

                                $check_repeat_groups = $this->checkEmptyRepeatGroups($elements, $table ,$itemt->db_table_name, $fnum);

                                if ($check_repeat_groups) {
                                    // -- Entrée du tableau --
                                    $t_elt = array();
                                    foreach($elements as &$element) {
                                        $t_elt[] = $element->name;
                                    }
                                    unset($element);

                                    $forms .= '<fieldset class="em-personalDetail">';
                                    $forms .= (!empty($itemg->label)) ? '<legend class="legend">' . JText::_($itemg->label) . '</legend>' : '';

                                    $forms .= '<table class="table table-bordered table-striped em-personalDetail-table-multiplleLine"><thead><tr> ';

                                    foreach($elements as &$element) {
                                        if ($element->plugin != 'id') {
                                            $forms .= '<th scope="col">'.JText::_($element->label).'</th>';
                                        }
                                    }

                                    if ($itemg->group_id == 174) {
                                        $query = 'SELECT `'.implode("`,`", $t_elt).'`, id FROM '.$table.'
	                                        WHERE parent_id=(SELECT id FROM '.$itemt->db_table_name.' WHERE user='.$aid.' AND fnum like '.$this->_db->Quote($fnum).') OR applicant_id='.$aid;
                                    } else {
                                        $query = 'SELECT `'.implode("`,`", $t_elt).'`, id FROM '.$table.'
	                                    WHERE parent_id=(SELECT id FROM '.$itemt->db_table_name.' WHERE fnum like '.$this->_db->Quote($fnum).')';
                                    }

                                    try {
                                        $this->_db->setQuery($query);
                                        $repeated_elements = $this->_db->loadObjectList();
                                    } catch (Exception $e) {
                                        JLog::Add('ERROR getting repeated elements in model/application at query: '.$query, JLog::ERROR, 'com_emundus');
                                    }

                                    unset($t_elt);

                                    $forms .= '</tr></thead>';
                                    // -- Ligne du tableau --
                                    if (count($repeated_elements) > 0) {
                                        $forms .= '<tbody>';
                                        foreach ($repeated_elements as $r_element) {
                                            $forms .= '<tr>';
                                            $j = 0;
                                            foreach ($r_element as $key => $r_elt) {

                                                // Do not display elements with no value inside them.
                                                if ($show_empty_fields == 0 && trim($r_elt) == '') {
                                                    $forms .= '<td></td>';
                                                    continue;
                                                }

                                                if (!empty($elements[$j])) {
                                                    $params = json_decode($elements[$j]->params);
                                                }

                                                if ($key != 'id' && $key != 'parent_id' && isset($elements[$j])) {

                                                    if ($elements[$j]->plugin == 'date') {
                                                        $elt = date($params->date_form_format, strtotime($r_elt));
                                                    }

                                                    elseif (($elements[$j]->plugin=='birthday' || $elements[$j]->plugin=='birthday_remove_slashes') && $r_elt>0) {
                                                        $format = $params->list_date_format;

                                                        $d = DateTime::createFromFormat($format, $r_elt);
                                                        if ($d && $d->format($format) == $r_elt) {
                                                            $elt = JHtml::_('date', $r_elt, JText::_('DATE_FORMAT_LC'));
                                                        } else {
                                                            $elt = JHtml::_('date', $r_elt, $format);
                                                        }
                                                    }

                                                    elseif ($elements[$j]->plugin == 'databasejoin') {
                                                        $select = !empty($params->join_val_column_concat)?"CONCAT(".$params->join_val_column_concat.")":$params->join_val_column;
                                                        $from = $params->join_db_name;
                                                        $where = $params->join_key_column.'='.$this->_db->Quote($r_elt);
                                                        $query = "SELECT ".$select." FROM ".$from." WHERE ".$where;

                                                        $query = preg_replace('#{thistable}#', $from, $query);
                                                        $query = preg_replace('#{my->id}#', $aid, $query);
                                                        $query  = preg_replace('#{shortlang}#', $this->locales, $query);

                                                        $this->_db->setQuery( $query );
                                                        $ret = $this->_db->loadResult();
                                                        if (empty($ret)) {
                                                            $ret = $r_elt;
                                                        }
                                                        $elt = JText::_($ret);
                                                    }

                                                    elseif ($elements[$j]->plugin == 'cascadingdropdown') {
                                                        $cascadingdropdown_id = $params->cascadingdropdown_id;
                                                        $r1 = explode('___', $cascadingdropdown_id);
                                                        $cascadingdropdown_label = $params->cascadingdropdown_label;
                                                        $r2 = explode('___', $cascadingdropdown_label);
                                                        $select = !empty($params->cascadingdropdown_label_concat)?"CONCAT(".$params->cascadingdropdown_label_concat.")":$r2[1];
                                                        $from = $r2[0];

                                                        // Checkboxes behave like repeat groups and therefore need to be handled a second level of depth.
                                                        if ($params->cdd_display_type == 'checkbox') {
                                                            $select = !empty($params->cascadingdropdown_label_concat)?" CONCAT(".$params->cascadingdropdown_label_concat.")":'GROUP_CONCAT('.$r2[1].')';

                                                            // Load the Fabrik join for the element to it's respective repeat_repeat table.
                                                            $query = $this->_db->getQuery(true);
                                                            $query
                                                                ->select([$this->_db->quoteName('join_from_table'), $this->_db->quoteName('table_key'), $this->_db->quoteName('table_join'), $this->_db->quoteName('table_join_key')])
                                                                ->from($this->_db->quoteName('#__fabrik_joins'))
                                                                ->where($this->_db->quoteName('element_id').' = '.$elements[$j]->id);
                                                            $this->_db->setQuery($query);
                                                            $f_join = $this->_db->loadObject();

                                                            $where = $r1[1].' IN (
	                                                    SELECT '.$this->_db->quoteName($f_join->table_join.'.'.$f_join->table_key).'
	                                                    FROM '.$this->_db->quoteName($f_join->table_join).' 
	                                                    WHERE '.$this->_db->quoteName($f_join->table_join.'.'.$f_join->table_join_key).' = '.$r_element->id.')';
                                                        } else {
                                                            $where = $r1[1].'='.$this->_db->Quote($r_elt);
                                                        }
                                                        $query = "SELECT ".$select." FROM ".$from." WHERE ".$where;
                                                        $query = preg_replace('#{thistable}#', $from, $query);
                                                        $query = preg_replace('#{my->id}#', $aid, $query);
                                                        $query  = preg_replace('#{shortlang}#', $this->locales, $query);

                                                        $this->_db->setQuery($query);
                                                        $ret = $this->_db->loadResult();
                                                        if (empty($ret)) {
                                                            $ret = $r_elt;
                                                        }
                                                        $elt = JText::_($ret);
                                                    }

                                                    elseif ($elements[$j]->plugin == 'checkbox') {
                                                        $elt = implode(", ", json_decode (@$r_elt));
                                                    }

                                                    elseif ($elements[$j]->plugin == 'dropdown' || $elements[$j]->plugin == 'radiobutton') {
                                                        $index = array_search($r_elt, $params->sub_options->sub_values);
                                                        if (strlen($index) > 0) {
                                                            $elt = JText::_($params->sub_options->sub_labels[$index]);
                                                        } elseif (!empty($params->dropdown_populate)) {
                                                            $elt = $r_elt;
                                                        } else {
                                                            $elt = "";
                                                        }
                                                    } elseif ($elements[$j]->plugin == 'internalid') {
                                                        $elt = '';
                                                    } else {
                                                        $elt = $r_elt;
                                                    }

                                                    $forms .= '<td><div id="em_training_'.$r_element->id.'" class="course '.$r_element->id.'"> '.JText::_($elt).'</div></td>';
                                                }
                                                $j++;
                                            }
                                            $forms .= '</tr>';
                                        }
                                        $forms .= '</tbody>';
                                    }
                                    $forms .= '</table>';
                                }

	                            // AFFICHAGE EN LIGNE
	                        } else {

	                        	$forms .='<table class="em-personalDetail-table-inline"><legend class="legend">'.JText::_($itemg->label).'</legend>';

	                            $modulo = 0;
	                            foreach ($elements as &$element) {

	                                if (!empty(trim($element->label))) {
	                                    $query = 'SELECT `id`, `'.$element->name .'` FROM `'.$itemt->db_table_name.'` WHERE user='.$aid.' AND fnum like '.$this->_db->Quote($fnum);
	                                    $this->_db->setQuery( $query );
	                                    $res = $this->_db->loadRow();

	                                    $element->content = @$res[1];
	                                    $element->content_id = @$res[0];

		                                // Do not display elements with no value inside them.
		                                if ($show_empty_fields == 0 && trim($element->content) == '') {
			                                continue;
		                                }

	                                    if ($element->plugin=='date' && $element->content>0) {
	                                        $date_params = json_decode($element->params);
	                                        $elt = date($date_params->date_form_format, strtotime($element->content));
	                                    }
	                                    elseif (($element->plugin=='birthday' || $element->plugin=='birthday_remove_slashes') && $element->content>0) {
                                            $format = json_decode($element->params)->list_date_format;

                                            $d = DateTime::createFromFormat($format, $element->content);
	                                        if ($d && $d->format($format) == $element->content) {
		                                        $elt = JHtml::_('date', $element->content, JText::_('DATE_FORMAT_LC'));
	                                        } else {
                                                $elt = JHtml::_('date', $element->content, $format);
	                                        }
	                                    }
	                                    elseif ($element->plugin=='databasejoin') {
	                                        $params = json_decode($element->params);
	                                        $select = !empty($params->join_val_column_concat)?"CONCAT(".$params->join_val_column_concat.")":$params->join_val_column;

	                                        if ($params->database_join_display_type == 'checkbox') {
	                                            $parent_id = strlen($element->content_id)>0?$element->content_id:0;
	                                            $query =
	                                            'SELECT `id`, GROUP_CONCAT(' . $element->name . ', ", ") as ' . $element->name . '
	                                                    FROM `' . $itemt->db_table_name . '_repeat_' . $element->name . '`
	                                                    WHERE parent_id=' . $parent_id . ' GROUP BY parent_id';
	                                            try {
                                                    $this->_db->setQuery($query);
	                                                $res = $this->_db->loadRow();
	                                                $elt = $res[1];
	                                            } catch (Exception $e) {
	                                                JLog::add('Line 997 - Error in model/application at query: '.$query, JLog::ERROR, 'com_emundus');
	                                                throw $e;
	                                            }
	                                        } else {
	                                            $from = $params->join_db_name;
	                                            $where = $params->join_key_column.'='.$this->_db->Quote($element->content);
	                                            $query = "SELECT ".$select." FROM ".$from." WHERE ".$where;

	                                            $query = preg_replace('#{thistable}#', $from, $query);
	                                            $query = preg_replace('#{my->id}#', $aid, $query);
                                                $query = preg_replace('#{shortlang}#', $this->locales, $query);

	                                            $this->_db->setQuery( $query );
		                                        $ret = $this->_db->loadResult();
		                                        if (empty($ret)) {
			                                        $ret = $element->content;
		                                        }
		                                        $elt = JText::_($ret);
	                                        }
	                                    }
	                                    elseif ($element->plugin=='cascadingdropdown') {
	                                        $params = json_decode($element->params);
	                                        $cascadingdropdown_id = $params->cascadingdropdown_id;
	                                        $r1 = explode('___', $cascadingdropdown_id);
	                                        $cascadingdropdown_label = JText::_($params->cascadingdropdown_label);
	                                        $r2 = explode('___', $cascadingdropdown_label);
	                                        $select = !empty($params->cascadingdropdown_label_concat)?"CONCAT(".$params->cascadingdropdown_label_concat.")":$r2[1];
	                                        $from = $r2[0];
	                                        $where = $r1[1].'='.$this->_db->Quote($element->content);
	                                        $query = "SELECT ".$select." FROM ".$from." WHERE ".$where;
	                                        $query = preg_replace('#{thistable}#', $from, $query);
	                                        $query = preg_replace('#{my->id}#', $aid, $query);
                                            $query  = preg_replace('#{shortlang}#', $this->locales, $query);

	                                        $this->_db->setQuery( $query );
		                                    $ret = $this->_db->loadResult();
		                                    if (empty($ret)) {
			                                    $ret = $element->content;
		                                    }
		                                    $elt = JText::_($ret);
	                                    }
	                                    elseif ($element->plugin == 'checkbox') {
	                                        $elt = implode(", ", json_decode (@$element->content));
	                                    }
	                                    elseif (($element->plugin == 'dropdown' || $element->plugin == 'radiobutton') && isset($element->content)) {
	                                        $params = json_decode($element->params);
	                                        $index = array_search($element->content, $params->sub_options->sub_values);
	                                        if (strlen($index) > 0) {
	                                            $elt = JText::_($params->sub_options->sub_labels[$index]);
	                                        } elseif (!empty($params->dropdown_populate)) {
		                                        $elt = $element->content;
	                                        } else {
	                                            $elt = "";
	                                        }
	                                    } elseif ($element->plugin == 'internalid') {
		                                    $elt = '';
	                                    } else {
		                                    $elt = $element->content;
	                                    }

	                                    // modulo for strips css //
	                                    if ($modulo%2) {
	                                        $forms .= '<tr class="table-strip-1"><td style="padding-right:50px;"><b>'.JText::_($element->label).'</b></td> <td> '.JText::_($elt).'</td></tr>';
	                                    } else {
	                                        $forms .= '<tr class="table-strip-2"><td style="padding-right:50px;"><b>'.JText::_($element->label).'</b></td> <td> '.JText::_($elt).'</td></tr>';
	                                    }
	                                    $modulo++;
	                                    unset($params);
	                                }
	                            }
	                        }
	                        $forms .='</table>';
	                        $forms .= '</fieldset>';
	                    }
	                }
	            }
	        }
        } catch(Exception $e) {
            error_log($e->getMessage(), 0);
            return $e->getMessage();
        }
        return $forms;
    }

   /* DEBUT DE GETFORMSPDF*/


    // @description  generate HTML to send to PDF librairie
    // @param   int applicant user id
    // @param   int fnum application file number
    // @return  string HTML to send to PDF librairie
    function getFormsPDF($aid, $fnum = 0, $fids = null, $gids = 0, $profile_id = null) {
           /* COULEURS*/
	    $eMConfig = JComponentHelper::getParams('com_emundus');
	    $show_empty_fields = $eMConfig->get('show_empty_fields', 1);
    	$em_breaker = $eMConfig->get('export_application_pdf_breaker', '0');

        $cTitle = $eMConfig->get('export_application_pdf_title_color', '#ee1c25');



    	require_once (JPATH_BASE.DS.'components'.DS.'com_emundus'.DS.'helpers'.DS.'list.php');
        $h_list = new EmundusHelperList;

        $tableuser  = $h_list->getFormsList($aid, $fnum, $fids, $profile_id);

        $forms = "";


        if (isset($tableuser)) {

	        $allowed_groups = EmundusHelperAccess::getUserFabrikGroups($this->_user->id);

            foreach ($tableuser as $key => $itemt) {

            	$breaker = ($em_breaker) ? ($key === 0) ? '' : 'class="breaker"' : '';
                // liste des groupes pour le formulaire d'une table
                $query = 'SELECT ff.id, ff.group_id, fg.id, fg.label, INSTR(fg.params,"\"repeat_group_button\":\"1\"") as repeated, INSTR(fg.params,"\"repeat_group_button\":1") as repeated_1
                            FROM #__fabrik_formgroup ff, #__fabrik_groups fg
                            WHERE ff.group_id = fg.id';

                if (!empty($gids) && $gids != 0) {
                    $query .= ' AND  fg.id IN ('.implode(',',$gids).')';
                }

                $query .= ' AND ff.form_id = "'.$itemt->form_id.'"
                            ORDER BY ff.ordering';
                try {

                    $this->_db->setQuery($query);
                    $groupes = $this->_db->loadObjectList();

                } catch (Exception $e) {
                    JLog::add('Error in model/application at query: '.$query, JLog::ERROR, 'com_emundus');
                    throw $e;
                }

                if (count($groupes) > 0) {
                  /*  $forms .= '<br>';*/
                    $forms .= '<hr class="sections"><h2'.$breaker.'>';
                    $title = explode('-', JText::_($itemt->label));
                    if (empty($title[1])) {
                        $forms .= JText::_(trim($itemt->label));
                    } else {
                        $forms .= JText::_(trim($title[1]));
                    }
                }

                $forms .= '</h2>';
                /*-- Liste des groupes -- */
                foreach ($groupes as $itemg) {

	                if ($allowed_groups !== true && !in_array($itemg->group_id, $allowed_groups)) {
		                $forms .= '<h2>'.JText::_($itemg->label).'</h2>';
		                $forms .= '<table>
										<thead><tr><th>'.JText::_('COM_EMUNDUS_CANNOT_SEE_GROUP').'</th></tr></thead>
									</table>';
		                continue;
	                }

                	// liste des items par groupe
                    $query = 'SELECT fe.id, fe.name, fe.label, fe.plugin, fe.params
                                FROM #__fabrik_elements fe
                                WHERE fe.published=1 AND
                                    fe.hidden=0 AND
                                    fe.group_id = "'.$itemg->group_id.'"
                                ORDER BY fe.ordering';
                    try {
                        $this->_db->setQuery($query);
                        $elements = $this->_db->loadObjectList();
                    } catch (Exception $e) {
                        JLog::add('Error in model/application at query: '.$query, JLog::ERROR, 'com_emundus');
                        throw $e;
                    }

                    if (count($elements) > 0) {

                        $asTextArea = false;
                        foreach ($elements as $key => $element) {
                            if ($element->plugin == 'textarea') {
                                $asTextArea = true;
                            }
                        }

                        $forms .= '<h3>';
                        $forms .= JText::_($itemg->label);
                        $forms .= '</h3>';

                        if ($itemg->group_id == 14) {
                            $forms .= '<table>';
                            foreach ($elements as $element) {
                                if (!empty($element->label) && $element->label != ' ' && !empty($element->content)) {
                                    $forms .= '<tbody><tr><td>'.JText::_($element->label).'</td></tr><tbody>';
                                }
                            }
                            $forms .= '</table>';
                            // TABLEAU DE PLUSIEURS LIGNES avec moins de 7 colonnes
                        }

                        elseif (($itemg->repeated > 0 || $itemg->repeated_1 > 0) && count($elements) < 6 && !$asTextArea) {



                            //-- Entrée du tableau -- */
                            $t_elt = array();
                            foreach ($elements as &$element) {
                                $t_elt[] = $element->name;
                            }
                            unset($element);

                            $query = 'SELECT table_join FROM #__fabrik_joins WHERE list_id='.$itemt->table_id.' AND group_id='.$itemg->group_id.' AND table_join_key like "parent_id"';
                            try {
                                $this->_db->setQuery($query);
                                $table = $this->_db->loadResult();
                            } catch (Exception $e) {
                                JLog::add('Error in model/application at query: '.$query, JLog::ERROR, 'com_emundus');
                                throw $e;
                            }

                            $check_repeat_groups = $this->checkEmptyRepeatGroups($elements, $table ,$itemt->db_table_name, $fnum);

                            if($check_repeat_groups) {
                                $forms .= '<p><table class="adminlist"><thead><tr  class="background"> ';
                                foreach ($elements as &$element) {
                                    $forms .= '<th scope="col" class="background"><strong>'.JText::_($element->label).'</strong></th>';
                                }
                                unset($element);

                                if ($itemg->group_id == 174) {
                                    $query = 'SELECT `'.implode("`,`", $t_elt).'`, id FROM '.$table.'
                                        WHERE parent_id=(SELECT id FROM '.$itemt->db_table_name.' WHERE user='.$aid.' AND fnum like '.$this->_db->Quote($fnum).') OR applicant_id='.$aid;
                                } else {
                                    $query = 'SELECT `'.implode("`,`", $t_elt).'`, id FROM '.$table.'
                                    WHERE parent_id=(SELECT id FROM '.$itemt->db_table_name.' WHERE fnum like '.$this->_db->Quote($fnum).')';
                                }

                                try {
                                    $this->_db->setQuery($query);
                                    $repeated_elements = $this->_db->loadObjectList();
                                } catch (Exception $e) {
                                    JLog::add('Error in model/application at query: '.$query, JLog::ERROR, 'com_emundus');
                                    throw $e;
                                }

                                unset($t_elt);

                                $forms .= '</tr></thead><tbody>';

                                // -- Ligne du tableau --
                                if (count($repeated_elements) > 0) {
                                    foreach ($repeated_elements as $r_element) {
                                        $forms .= '<tr>';
                                        $j = 0;

                                        foreach ($r_element as $key => $r_elt) {

                                            if ($key != 'id' && $key != 'parent_id' && isset($elements[$j]) && $elements[$j]->plugin != 'display') {

                                                // Do not display elements with no value inside them.
                                                if ($show_empty_fields == 0 && trim($r_elt) == '') {
                                                    continue;
                                                }

                                                $params = json_decode($elements[$j]->params);

                                                if ($elements[$j]->plugin == 'date') {
                                                    $dt = new DateTime($r_elt, new DateTimeZone('UTC'));
                                                    $dt->setTimezone(new DateTimeZone(JFactory::getConfig()->get('offset')));
                                                    $elt = $dt->format($params->date_form_format);
                                                }
                                                elseif (($elements[$j]->plugin=='birthday' || $elements[$j]->plugin=='birthday_remove_slashes') && $r_elt > 0) {
                                                    $format = $params->list_date_format;
                                                    $d = DateTime::createFromFormat($format, $r_elt);
                                                    if ($d && $d->format($format) == $r_elt) {
                                                        $elt = JHtml::_('date', $r_elt, JText::_('DATE_FORMAT_LC'));
                                                    } else {
                                                        $elt = JHtml::_('date', $r_elt, $format);
                                                    }
                                                }
                                                elseif ($elements[$j]->plugin == 'databasejoin') {
                                                    $select = !empty($params->join_val_column_concat)?"CONCAT(".$params->join_val_column_concat.")":$params->join_val_column;
                                                    $from = $params->join_db_name;
                                                    $where = $params->join_key_column.'='.$this->_db->Quote($r_elt);
                                                    $query = "SELECT ".$select." FROM ".$from." WHERE ".$where;

                                                    $query = preg_replace('#{thistable}#', $from, $query);
                                                    $query = preg_replace('#{my->id}#', $aid, $query);
                                                    $query  = preg_replace('#{shortlang}#', $this->locales, $query);

                                                    $this->_db->setQuery($query);
                                                    $elt = $this->_db->loadResult();
                                                }
                                                elseif ($elements[$j]->plugin == 'cascadingdropdown') {
                                                    $cascadingdropdown_id = $params->cascadingdropdown_id;
                                                    $r1 = explode('___', $cascadingdropdown_id);
                                                    $cascadingdropdown_label = $params->cascadingdropdown_label;
                                                    $r2 = explode('___', $cascadingdropdown_label);
                                                    $select = !empty($params->cascadingdropdown_label_concat)?"CONCAT(".$params->cascadingdropdown_label_concat.")":$r2[1];

                                                    // Checkboxes behave like repeat groups and therefore need to be handled a second level of depth.
                                                    if ($params->cdd_display_type == 'checkbox') {
                                                        $select = !empty($params->cascadingdropdown_label_concat)?" CONCAT(".$params->cascadingdropdown_label_concat.")":'GROUP_CONCAT('.$r2[1].')';

                                                        // Load the Fabrik join for the element to it's respective repeat_repeat table.
                                                        $query = $this->_db->getQuery(true);
                                                        $query
                                                            ->select([$this->_db->quoteName('join_from_table'), $this->_db->quoteName('table_key'), $this->_db->quoteName('table_join'), $this->_db->quoteName('table_join_key')])
                                                            ->from($this->_db->quoteName('#__fabrik_joins'))
                                                            ->where($this->_db->quoteName('element_id').' = '.$elements[$j]->id);
                                                        $this->_db->setQuery($query);
                                                        $f_join = $this->_db->loadObject();

                                                        $where = $r1[1].' IN (
                                                    SELECT '.$this->_db->quoteName($f_join->table_join.'.'.$f_join->table_key).'
                                                    FROM '.$this->_db->quoteName($f_join->table_join).' 
                                                    WHERE '.$this->_db->quoteName($f_join->table_join.'.'.$f_join->table_join_key).' = '.$r_element->id.')';
                                                    } else {
                                                        $where = $r1[1].'='.$this->_db->Quote($r_elt);
                                                    }

                                                    $from = $r2[0];
                                                    $query = "SELECT ".$select." FROM ".$from." WHERE ".$where;
                                                    $query = preg_replace('#{thistable}#', $from, $query);
                                                    $query = preg_replace('#{my->id}#', $aid, $query);
                                                    $query  = preg_replace('#{shortlang}#', $this->locales, $query);

                                                    $this->_db->setQuery($query);
                                                    $elt = JText::_($this->_db->loadResult());
                                                }
                                                elseif ($elements[$j]->plugin == 'checkbox') {
                                                    $elt = implode(", ", json_decode (@$r_elt));
                                                }
                                                elseif ($elements[$j]->plugin == 'dropdown' || $elements[$j]->plugin == 'radiobutton') {
                                                    $index = array_search($r_elt, $params->sub_options->sub_values);
                                                    if (strlen($index) > 0) {
                                                        $elt = JText::_($params->sub_options->sub_labels[$index]);
                                                    } elseif (!empty($params->dropdown_populate)) {
                                                        $elt = $r_elt;
                                                    } else {
                                                        $elt = "";
                                                    }
                                                } elseif ($elements[$j]->plugin == 'internalid') {
                                                    $elt = '';
                                                } else {
                                                    $elt = JText::_($r_elt);
                                                }

                                                // trick to prevent from blank value in PDF when string is to long without spaces (usually emails)
                                                $elt = str_replace('@', '<br>@', $elt);
                                                $forms .= '<td class="background-light" style="border-right: 1px solid black;"><div id="em_training_'.$r_element->id.'" class="course '.$r_element->id.'">'.JText::_($elt).'</div></td>';
                                            }
                                            $j++;
                                        }
                                        $forms .= '</tr>';
                                    }
                                }
                                $forms .= '</tbody></table></p>';
                            }


                            // TABLEAU DE PLUSIEURS LIGNES sans tenir compte du nombre de lignes
                        }

                        elseif ($itemg->repeated > 0 || $itemg->repeated_1 > 0) {

                            //-- Entrée du tableau -- */
                            $t_elt = array();
                            foreach ($elements as &$element) {
                                $t_elt[] = $element->name;
                            }
                            unset($element);

                            $query = 'SELECT table_join FROM #__fabrik_joins WHERE group_id='.$itemg->group_id.' AND table_join_key like "parent_id"';
                            $this->_db->setQuery($query);
                            $table = $this->_db->loadResult();

                            $check_repeat_groups = $this->checkEmptyRepeatGroups($elements, $table ,$itemt->db_table_name, $fnum);

                            if($check_repeat_groups) {
                                if ($itemg->group_id == 174) {
                                    $query = 'SELECT `'.implode("`,`", $t_elt).'`, id FROM '.$table.'
                                        WHERE parent_id=(SELECT id FROM '.$itemt->db_table_name.' WHERE user='.$aid.' AND fnum like '.$this->_db->Quote($fnum).') OR applicant_id='.$aid;
                                } else {
                                    $query = 'SELECT `'.implode("`,`", $t_elt).'`, id FROM '.$table.'
                                    WHERE parent_id=(SELECT id FROM '.$itemt->db_table_name.' WHERE user='.$aid.' AND fnum like '.$this->_db->Quote($fnum).')';
                                }

                                $this->_db->setQuery($query);
                                $repeated_elements = $this->_db->loadObjectList();
                                unset($t_elt);

                                // -- Ligne du tableau --
                                if (count($repeated_elements) > 0) {
                                    $i = 1;

                                    foreach ($repeated_elements as $r_element) {
                                        $j = 0;
                                        $forms .= '<br>---- '.$i.' ----<br>';
                                        $forms .= '<table>';
                                        foreach ($r_element as $key => $r_elt) {

                                            // Do not display elements with no value inside them.
                                            if ($show_empty_fields == 0 && trim($r_elt) == '') {
                                                $j++;
                                                continue;
                                            }

                                            if (!empty($r_elt) && $key != 'id' && $key != 'parent_id' && isset($elements[$j])  && $elements[$j]->plugin != 'display') {

                                                if ($elements[$j]->plugin == 'date') {
                                                    $date_params = json_decode($elements[$j]->params);
                                                    $dt = new DateTime($r_elt, new DateTimeZone('UTC'));
                                                    $dt->setTimezone(new DateTimeZone(JFactory::getConfig()->get('offset')));
                                                    $elt = $dt->format($date_params->date_form_format);
                                                }
                                                elseif (($elements[$j]->plugin=='birthday' || $elements[$j]->plugin=='birthday_remove_slashes') && $r_elt > 0) {
                                                    $format = json_decode($elements[$j]->params)->list_date_format;
                                                    $d = DateTime::createFromFormat($format, $r_elt);
                                                    if ($d && $d->format($format) == $r_elt) {
                                                        $elt = JHtml::_('date', $r_elt, JText::_('DATE_FORMAT_LC'));
                                                    } else {
                                                        $elt = JHtml::_('date', $r_elt, $format);
                                                    }
                                                }
                                                elseif ($elements[$j]->plugin == 'databasejoin') {
                                                    $params = json_decode($elements[$j]->params);
                                                    $select = !empty($params->join_val_column_concat)?"CONCAT(".$params->join_val_column_concat.")":$params->join_val_column;
                                                    $from = $params->join_db_name;
                                                    $where = $params->join_key_column.'='.$this->_db->Quote($r_elt);
                                                    $query = "SELECT ".$select." FROM ".$from." WHERE ".$where;

                                                    $query = preg_replace('#{thistable}#', $from, $query);
                                                    $query = preg_replace('#{my->id}#', $aid, $query);
                                                    $query  = preg_replace('#{shortlang}#', $this->locales, $query);

                                                    $this->_db->setQuery( $query );
                                                    $elt = JText::_($this->_db->loadResult());
                                                }
                                                elseif (@$elements[$j]->plugin == 'cascadingdropdown') {
                                                    $params = json_decode($elements[$j]->params);
                                                    $cascadingdropdown_id = $params->cascadingdropdown_id;
                                                    $r1 = explode('___', $cascadingdropdown_id);
                                                    $cascadingdropdown_label = $params->cascadingdropdown_label;
                                                    $r2 = explode('___', $cascadingdropdown_label);
                                                    $select = !empty($params->cascadingdropdown_label_concat)?"CONCAT(".$params->cascadingdropdown_label_concat.")":$r2[1];
                                                    $from = $r2[0];
                                                    $where = $r1[1].'='.$this->_db->Quote($element->content);
                                                    $query = "SELECT ".$select." FROM ".$from." WHERE ".$where;
                                                    $query = preg_replace('#{thistable}#', $from, $query);
                                                    $query = preg_replace('#{my->id}#', $aid, $query);
                                                    $query  = preg_replace('#{shortlang}#', $this->locales, $query);

                                                    $this->_db->setQuery( $query );
                                                    $elt = JText::_($this->_db->loadResult());
                                                }
                                                elseif ($elements[$j]->plugin == 'textarea') {
                                                    $elt = JText::_($r_elt);
                                                } elseif ($elements[$j]->plugin == 'checkbox') {
                                                    $elt = JText::_(implode(", ", json_decode(@$r_elt)));
                                                } elseif ($elements[$j]->plugin == 'dropdown' || @$elements[$j] == 'radiobutton') {
                                                    $params = json_decode($elements[$j]->params);
                                                    $index = array_search($r_elt, $params->sub_options->sub_values);
                                                    if (strlen($index) > 0) {
                                                        $elt = JText::_($params->sub_options->sub_labels[$index]);
                                                    } else {
                                                        $elt = "";
                                                    }
                                                } elseif ($elements[$j]->plugin == 'internalid') {
                                                    $elt = '';
                                                } else {
                                                    $elt = JText::_($r_elt);
                                                }

                                                if ($show_empty_fields == 1 || !empty($elt)) {
                                                    if ($elements[$j]->plugin == 'textarea') {
                                                        $forms .= '<tr><td colspan="2" style=" border-right: 1px solid black;"><span style="color: #000071;">'.JText::_($elements[$j]->label).'</span> <br>'.$elt.'</td></tr>';
                                                    } else {
                                                        $forms .= '<tr><td style=" border-right: 1px solid black;"><span style="color: #000071;">'.JText::_($elements[$j]->label).'</span></td> <td > '.$elt.'</td></tr>';
                                                    }
                                                }
                                            }
                                            $j++;
                                        }
                                        $forms .= '</table>';
                                        $i++;
                                    }
                                }
                            }


                            // AFFICHAGE EN LIGNE
                        } else {
                            $forms .= '<table>';
                            foreach ($elements as $element) {

                                $query = 'SELECT `id`, `'.$element->name .'` FROM `'.$itemt->db_table_name.'` WHERE user='.$aid.' AND fnum like '.$this->_db->Quote($fnum);
                                try {
                                    $this->_db->setQuery($query);
                                    $res = $this->_db->loadRow();
                                } catch (Exception $e) {
                                    JLog::add('Error in model/application at query: '.$query, JLog::ERROR, 'com_emundus');
                                    throw $e;
                                }

                                if (count($res) > 1) {
                                    $element->content = $res[1];
                                    $element->content_id = $res[0];
                                } else {
                                    $element->content = '';
                                    $element->content_id = -1;
                                }

	                            // Do not display elements with no value inside them.
	                            if ($show_empty_fields == 0 && trim($element->content) == '') {
		                            continue;
	                            }

                                $params = json_decode($element->params);
                                if (!empty($element->content) || (isset($params->database_join_display_type) && $params->database_join_display_type == 'checkbox')) {

                                    if (!empty($element->label) && $element->label!=' ') {

                                        if ($element->plugin=='date' && $element->content>0) {

                                        	// Empty date elements are set to 0000-00-00 00:00:00 in DB.
                                        	if ($show_empty_fields == 0 && $element->content == '0000-00-00 00:00:00') {
                                        		continue;
	                                        }

                                        	$dt = new DateTime($element->content, new DateTimeZone('UTC'));
	                                        $dt->setTimezone(new DateTimeZone(JFactory::getConfig()->get('offset')));
	                                        $elt = $dt->format($params->date_form_format);
                                        }

                                        elseif (($element->plugin=='birthday' || $element->plugin=='birthday_remove_slashes') && $element->content > 0) {
                                            $format = $params->list_date_format;
                                            $d = DateTime::createFromFormat($format, $element->content);
                                            if ($d && $d->format($format) == $element->content) {
                                                $elt = JHtml::_('date', $element->content, JText::_('DATE_FORMAT_LC'));
                                            } else {
                                                $elt = JHtml::_('date', $element->content, $format);
                                            }
                                        }

                                        elseif ($element->plugin == 'databasejoin') {
                                            $select = !empty($params->join_val_column_concat)?"CONCAT(".$params->join_val_column_concat.")":$params->join_val_column;

                                            if ($params->database_join_display_type == 'checkbox') {
                                                $parent_id = strlen($element->content_id)>0?$element->content_id:0;
                                                $query =
                                                    'SELECT `id`, GROUP_CONCAT(' . $element->name . ', ", ") as ' . $element->name . '
                                                            FROM `' . $itemt->db_table_name . '_repeat_' . $element->name . '`
                                                            WHERE parent_id=' . $parent_id . ' GROUP BY parent_id';
                                                try {
                                                    $this->_db->setQuery($query);
                                                    $res = $this->_db->loadRow();
                                                } catch (Exception $e) {
                                                    JLog::add('line:1461 - Error in model/application at query: '.$query, JLog::ERROR, 'com_emundus');
                                                    throw $e;
                                                }

                                                if (count($res)>1) {
                                                    $elt = $res[1];
                                                } else {
                                                    $elt = '';
                                                }
                                            } else {
                                                $from = $params->join_db_name;
                                                $where = $params->join_key_column.'='.$this->_db->Quote($element->content);
                                                $query = "SELECT ".$select." FROM ".$from." WHERE ".$where;

                                                $query = preg_replace('#{thistable}#', $from, $query);
                                                $query = preg_replace('#{my->id}#', $aid, $query);
                                                $query  = preg_replace('#{shortlang}#', $this->locales, $query);

                                                $this->_db->setQuery($query);
                                                $elt = JText::_($this->_db->loadResult());
                                            }
                                        } elseif ($element->plugin == 'cascadingdropdown') {
                                            $cascadingdropdown_id = $params->cascadingdropdown_id;
                                            $r1 = explode('___', $cascadingdropdown_id);
                                            $cascadingdropdown_label = $params->cascadingdropdown_label;
                                            $r2 = explode('___', $cascadingdropdown_label);
                                            $select = !empty($params->cascadingdropdown_label_concat)?"CONCAT(".$params->cascadingdropdown_label_concat.")":$r2[1];
                                            $from = $r2[0];
                                            $where = $r1[1].'='.$this->_db->Quote($element->content);
                                            $query = "SELECT ".$select." FROM ".$from." WHERE ".$where;
                                            $query = preg_replace('#{thistable}#', $from, $query);
                                            $query = preg_replace('#{my->id}#', $aid, $query);
                                            $query  = preg_replace('#{shortlang}#', $this->locales, $query);

                                            $this->_db->setQuery($query);
                                            $elt = JText::_($this->_db->loadResult());

                                        } elseif ($element->plugin == 'textarea') {
                                            $elt = JText::_($element->content);
                                        } elseif ($element->plugin == 'checkbox') {
                                            $elt = JText::_(implode(", ", json_decode (@$element->content)));
                                        } elseif ($element->plugin == 'dropdown' || $element->plugin == 'radiobutton') {
                                            $index = array_search($element->content, $params->sub_options->sub_values);
                                            if (strlen($index) > 0) {
                                                $elt = JText::_($params->sub_options->sub_labels[$index]);
                                            } else {
                                                $elt = "";
                                            }
                                        } elseif ($element->plugin == 'internalid') {
		                                    $elt = '';
                                        } else {
                                            $elt = JText::_($element->content);
                                        }

                                        if ($element->plugin == 'textarea') {
                                            $forms .= '<tr><td   colspan="2" ><strong><span style="color: #000000;">'.JText::_($element->label).' : '.'</span></strong>'.JText::_($elt).'<br/></td></tr>';
                                        } else {
                                            $forms .= '<tr ><td ><span style="color: #000000;">'.JText::_($element->label).' : '.'</span></td> <td> '.JText::_($elt).'</td></tr>';
										}
                                    }
                                } elseif (empty($element->content)) {
                                    if (!empty($element->label) && $element->label!=' ') {
                                        $forms .= '<tr><td ><span style="color: #000000;">'.JText::_($element->label).' : '.'</span></td> <td> </td></tr>';
                                    }
                                }
                            }
                            $forms .= '</table><div></div>';
                        }
                    }
                }
            }
        }
        return $forms;
    }

    public function getFormsPDFElts($aid, $elts, $options) {

        $tableuser = @EmundusHelperList::getFormsListByProfileID($options['profile_id']);

        $forms = "<style>
					table {
					    border-spacing: 1px;
					    background-color: #f2f2f2;
					    width: 100%;
					}
					th {
					    border-spacing: 1px; color: #666666;
					}
					td {
					    border-spacing: 1px;
					    background-color: #FFFFFF;
					}
					</style>";
        if (isset($tableuser)) {
            foreach ($tableuser as $key => $itemt) {
                $forms .= ($options['show_list_label']==1)?'<h2>'.JText::_($itemt->label).'</h2>':'';
                // liste des groupes pour le formulaire d'une table
                $query = 'SELECT ff.id, ff.group_id, fg.id, fg.label, INSTR(fg.params,"\"repeat_group_button\":\"1\"") as repeated, INSTR(fg.params,"\"repeat_group_button\":1") as repeated_1
                            FROM #__fabrik_formgroup ff, #__fabrik_groups fg
                            WHERE ff.group_id = fg.id AND
                                  ff.form_id = "'.$itemt->form_id.'"
                            ORDER BY ff.ordering';
                $this->_db->setQuery( $query );
                $groupes = $this->_db->loadObjectList();

                /*-- Liste des groupes -- */
                foreach($groupes as $keyg => $itemg) {
                    // liste des items par groupe
                    $query = 'SELECT fe.id, fe.name, fe.label, fe.plugin, fe.params
                                FROM #__fabrik_elements fe
                                WHERE fe.published=1 AND
                                      fe.hidden=0 AND
                                      fe.group_id = "'.$itemg->group_id.'" AND
                                      fe.id IN ('.implode(',', $elts).')
                                ORDER BY fe.ordering';
                    $this->_db->setQuery( $query );
                    $elements = $this->_db->loadObjectList();
                    if(count($elements)>0) {
                        $forms .= ($options['show_group_label']==1)?'<h3>'.JText::_($itemg->label).'</h3>':'';
                        foreach($elements as &$iteme) {
                            $where = 'user='.$aid;
                            $where .= $options['rowid']>0?' AND id='.$options['rowid']:'';
                            $query = 'SELECT `'.$iteme->name .'` FROM `'.$itemt->db_table_name.'` WHERE '.$where;
                            $this->_db->setQuery( $query );
                            $iteme->content = $this->_db->loadResult();
                        }
                        unset($iteme);

                        if ($itemg->group_id == 14) {

                            foreach($elements as $element) {
                                if(!empty($element->label) && $element->label!=' ') {
                                    if ($element->plugin=='date' && $element->content>0) {
                                        $date_params = json_decode($element->params);
                                        $elt = date($date_params->date_form_format, strtotime($element->content));
                                    } else $elt = $element->content;
                                    $forms .= '<p><b>'.JText::_($element->label).': </b>'.JText::_($elt).'</p>';
                                }
                            }

                            // TABLEAU DE PLUSIEURS LIGNES
                        } elseif ($itemg->repeated > 0 || $itemg->repeated_1 > 0){
                            $forms .= '<p><table class="adminlist">
                              <thead>
                              <tr> ';

                            //-- Entrée du tableau -- */
                            //$nb_lignes = 0;
                            $t_elt = array();
                            foreach($elements as $element) {
                                $t_elt[] = $element->name;
                                $forms .= '<th scope="col">'.JText::_($element->label).'</th>';
                            }
                            unset($element);
                            //$table = $itemt->db_table_name.'_'.$itemg->group_id.'_repeat';
                            $query = 'SELECT table_join FROM #__fabrik_joins WHERE group_id='.$itemg->group_id;
                            $this->_db->setQuery($query);
                            $table = $this->_db->loadResult();

                            if($itemg->group_id == 174)
                                $query = 'SELECT '.implode(",", $t_elt).', id FROM '.$table.'
                                        WHERE parent_id=(SELECT id FROM '.$itemt->db_table_name.' WHERE user='.$aid.') OR applicant_id='.$aid;
                            else
                                $query = 'SELECT '.implode(",", $t_elt).', id FROM '.$table.'
                                    WHERE parent_id=(SELECT id FROM '.$itemt->db_table_name.' WHERE user='.$aid.')';
                            $this->_db->setQuery($query);
                            $repeated_elements = $this->_db->loadObjectList();
                            unset($t_elt);
                            $forms .= '</tr></thead><tbody>';

                            // -- Ligne du tableau --
                            foreach ($repeated_elements as $r_element) {
                                $forms .= '<tr>';
                                $j = 0;
                                foreach ($r_element as $key => $r_elt) {
                                    if ($key != 'id' && $key != 'parent_id' && isset($elements[$j]) && $elements[$j]->plugin != 'display') {

                                        if ($elements[$j]->plugin=='date') {
                                            $date_params = json_decode($elements[$j]->params);
                                            $elt = date($date_params->date_form_format, strtotime($r_elt));
                                        }
                                        elseif (($elements[$j]->plugin=='birthday' || $elements[$j]->plugin=='birthday_remove_slashes') && $r_elt>0) {
                                            $format = json_decode($elements[$j]->params)->list_date_format;

                                            $d = DateTime::createFromFormat($format, $r_elt);
                                            if($d && $d->format($format) == $r_elt) {
                                                $elt = JHtml::_('date', $r_elt, JText::_('DATE_FORMAT_LC'));
                                            }
                                            else {
                                                $elt = JHtml::_('date', $r_elt, $format);
                                            }
                                        }
                                        elseif($elements[$j]->plugin=='databasejoin') {
                                            $params = json_decode($elements[$j]->params);
                                            $select = !empty($params->join_val_column_concat)?"CONCAT(".$params->join_val_column_concat.")":$params->join_val_column;
                                            $from = $params->join_db_name;
                                            $where = $params->join_key_column.'='.$this->_db->Quote($r_elt);
                                            $query = "SELECT ".$select." FROM ".$from." WHERE ".$where;
                                            $query = preg_replace('#{thistable}#', $from, $query);
                                            $query = preg_replace('#{my->id}#', $aid, $query);
                                            $query  = preg_replace('#{shortlang}#', $this->locales, $query);

                                            $this->_db->setQuery( $query );
                                            $elt = $this->_db->loadResult();
                                        }
                                        elseif($elements[$j]->plugin == 'checkbox') {
                                            $elt = implode(", ", json_decode (@$r_elt));
                                        }
                                        elseif($elements[$j]->plugin == 'dropdown' || $elements[$j]->plugin == 'radiobutton') {
                                            $params = json_decode($elements[$j]->params);
                                            $index = array_search($r_elt, $params->sub_options->sub_values);
                                            if (strlen($index) > 0) {
                                                $elt = JText::_($params->sub_options->sub_labels[$index]);
                                            } else {
                                                $elt = "";
                                            }
                                        }
                                        else
                                            $elt = $r_elt;

                                        $forms .= '<td><div id="em_training_'.$r_element->id.'" class="course '.$r_element->id.'">'.JText::_($elt).'</div></td>';
                                    }
                                    $j++;
                                }
                                $forms .= '</tr>';
                            }
                            $forms .= '</tbody></table></p>';

                            // AFFICHAGE EN LIGNE
                        } else {
                            foreach($elements as $element) {
                                if(!empty($element->label) && $element->label!=' '  && $elements->plugin != 'display') {

                                    if ($element->plugin=='date' && $element->content>0) {
                                        $date_params = json_decode($element->params);
                                        // $elt = strftime($date_params->date_form_format, strtotime($element->content));
                                        $elt = date($date_params->date_form_format, strtotime($element->content));

                                    }
                                    elseif (($element->plugin=='birthday' || $element->plugin=='birthday_remove_slashes') && $element->content>0) {
                                        $format = json_decode($element->params)->list_date_format;

                                        $d = DateTime::createFromFormat($format, $element->content);
                                        if($d && $d->format($format) == $element->content) {
                                            $elt = JHtml::_('date', $element->content, JText::_('DATE_FORMAT_LC'));
                                        }
                                        else {
                                            $elt = JHtml::_('date', $element->content, $format);
                                        }
                                    }
                                    elseif($element->plugin=='databasejoin') {
                                        $params = json_decode($element->params);
                                        $select = !empty($params->join_val_column_concat)?"CONCAT(".$params->join_val_column_concat.")":$params->join_val_column;
                                        $from = $params->join_db_name;
                                        $where = $params->join_key_column.'='.$this->_db->Quote($element->content);
                                        $query = "SELECT ".$select." FROM ".$from." WHERE ".$where;
                                        $query = preg_replace('#{thistable}#', $from, $query);
                                        $query = preg_replace('#{my->id}#', $aid, $query);
                                        $query  = preg_replace('#{shortlang}#', $this->locales, $query);

                                        $this->_db->setQuery( $query );
                                        $elt = $this->_db->loadResult();
                                    }
                                    elseif($element->plugin=='cascadingdropdown') {
                                        $params = json_decode($element->params);
                                        $cascadingdropdown_id = $params->cascadingdropdown_id;
                                        $r1 = explode('___', $cascadingdropdown_id);
                                        $cascadingdropdown_label = $params->cascadingdropdown_label;
                                        $r2 = explode('___', $cascadingdropdown_label);
                                        $select = !empty($params->cascadingdropdown_label_concat)?"CONCAT(".$params->cascadingdropdown_label_concat.")":$r2[1];
                                        $from = $r2[0];
                                        $where = $r1[1].'='.$this->_db->Quote($element->content);
                                        $query = "SELECT ".$select." FROM ".$from." WHERE ".$where;
                                        $query = preg_replace('#{thistable}#', $from, $query);
                                        $query = preg_replace('#{my->id}#', $aid, $query);
                                        $query  = preg_replace('#{shortlang}#', $this->locales, $query);

                                        $this->_db->setQuery( $query );
                                        $elt = $this->_db->loadResult();
                                    }
                                    elseif($element->plugin == 'checkbox') {
                                        $elt = implode(", ", json_decode (@$element->content));
                                    }
                                    elseif($element->plugin=='dropdown' || $element->plugin=='radiobutton') {
                                        $params = json_decode($element->params);
                                        $index = array_search($element->content, $params->sub_options->sub_values);
                                        if (strlen($index) > 0) {
                                            $elt = JText::_($params->sub_options->sub_labels[$index]);
                                        } else {
                                            $elt = "";
                                        }
                                    }
                                    else
                                        $elt = $element->content;
                                    $forms .= '<p><b>'.JText::_($element->label).': </b>'.JText::_($elt).'</p>';
                                }
                            }
                        }
                        //$forms .= '</fieldset>';
                    }
                }
            }
        }
        return $forms;
    }

    public function getEmail($user_id) {
        $query = 'SELECT *
        FROM #__messages as email
        LEFT JOIN #__users as user ON user.id=email.user_id_from
        LEFT JOIN #__emundus_users as eu ON eu.user_id=user.id
        WHERE email.user_id_to ='.$user_id.' ORDER BY `date_time` DESC';
        $this->_db->setQuery($query);
        $results['to'] = $this->_db->loadObjectList('message_id');

        $query = 'SELECT *
        FROM #__messages as email
        LEFT JOIN #__users as user ON user.id=email.user_id_to
        LEFT JOIN #__emundus_users as eu ON eu.user_id=user.id
        WHERE email.user_id_from ='.$user_id.' ORDER BY `date_time` DESC';
        $this->_db->setQuery($query);
        $results['from'] = $this->_db->loadObjectList('message_id');

        return $results;
    }

    public function getApplicationMenu() {
        $juser = JFactory::getUser();

        try {
            $db = $this->getDbo();
            $grUser = $juser->getAuthorisedViewLevels();

            $query = 'SELECT m.id, m.title, m.link, m.lft, m.rgt, m.note
                        FROM #__menu as m
                        WHERE m.published=1 AND m.menutype = "application" and m.access in ('.implode(',', $grUser).')
                        ORDER BY m.lft';

            $db->setQuery($query);
            return $db->loadAssocList();

        } catch (Exception $e) {
            return false;
        }
    }

    public function getProgramSynthesis($cid) {
        try {
            $db = $this->getDbo();
            $query = 'select p.synthesis, p.id, p.label from #__emundus_setup_programmes as p left join #__emundus_setup_campaigns as c on c.training = p.code where c.id='.$cid;
            $db->setQuery($query);
            return $db->loadObject();
        } catch (Exception $e) {
            return null;
        }
    }

    public function getAttachments($ids) {
        try {
            $query = "SELECT id, fnum, user_id, filename FROM #__emundus_uploads WHERE id in (".implode(',', $ids).")";
            $this->_db->setQuery($query);
            return $this->_db->loadObjectList();
        } catch (Exception $e) {
            error_log($e->getMessage(), 0);
            return false;
        }
    }

    public function getAttachmentsByFnum($fnum, $ids=null, $attachment_id=null) {
        try {

            $query = "SELECT eu.*, sa.value FROM #__emundus_uploads as eu
                        LEFT JOIN #__emundus_setup_attachments as sa on sa.id = eu.attachment_id
                        WHERE fnum like ".$this->_db->quote($fnum);

            if (isset($attachment_id) && !empty($attachment_id)){
                if (is_array($attachment_id) && $attachment_id[0] != "") {
	                $query .= " AND eu.attachment_id IN (".implode(',', $attachment_id).")";
                } else {
	                $query .= " AND eu.attachment_id = ".$attachment_id;
                }
            }

            if (!empty($ids) && $ids != "null") {
	            $query .= " AND eu.id in ($ids)";
            }

            $query .= " ORDER BY sa.value DESC";

            $this->_db->setQuery($query);
            $docs = $this->_db->loadObjectList();
        } catch(Exception $e) {
            error_log($e->getMessage(), 0);
            return false;
        }

	    require_once (JPATH_BASE.DS.'components'.DS.'com_emundus'.DS.'helpers'.DS.'access.php');
	    // Sort the docs out that are not allowed to be exported by the user.
	    $allowed_attachments = EmundusHelperAccess::getUserAllowedAttachmentIDs(JFactory::getUser()->id);
	    if ($allowed_attachments !== true) {
		    foreach ($docs as $key => $doc) {
			    if (!in_array($doc->attachment_id, $allowed_attachments)) {
				    unset($docs[$key]);
			    }
		    }
	    }
        return $docs;
    }

    public function getAccessFnum($fnum)
    {
        $query = "SELECT jecc.fnum, jesg.label as gname, jea.*, jesa.label as aname FROM #__emundus_campaign_candidature as jecc
                    LEFT JOIN #__emundus_setup_campaigns as jesc on jesc.id = jecc.campaign_id
                    LEFT JOIN #__emundus_setup_programmes as jesp on jesp.code = jesc.training
                    LEFT JOIN #__emundus_setup_groups_repeat_course as jesgrc on jesgrc.course = jesp.code
                    LEFT JOIN #__emundus_setup_groups as jesg on jesg.id = jesgrc.parent_id
                    LEFT JOIN #__emundus_acl as jea on jea.group_id = jesg.id
                    LEFT JOIN #__emundus_setup_actions as jesa on jesa.id = jea.action_id
                    WHERE jecc.fnum like '".$fnum."' and jesa.status = 1 order by jecc.fnum, jea.group_id, jea.action_id";

        try
        {
            $db = $this->getDbo();
            $db->setQuery($query);
            $res = $db->loadAssocList();
            $access = array();
            foreach($res as $r)
            {
                $access['groups'][$r['group_id']]['gname'] = $r['gname'];
                $access['groups'][$r['group_id']]['isAssoc'] = false;
                $access['groups'][$r['group_id']]['isACL'] = true;
                $access['groups'][$r['group_id']]['actions'][$r['action_id']]['aname'] = $r['aname'];
                $access['groups'][$r['group_id']]['actions'][$r['action_id']]['c'] = $r['c'];
                $access['groups'][$r['group_id']]['actions'][$r['action_id']]['r'] = $r['r'];
                $access['groups'][$r['group_id']]['actions'][$r['action_id']]['u'] = $r['u'];
                $access['groups'][$r['group_id']]['actions'][$r['action_id']]['d'] = $r['d'];
            }
            $query = "SELECT jeacl.group_id, jeacl.action_id as acl_action_id, jeacl.c as acl_c, jeacl.r as acl_r, jeacl.u as acl_u, jeacl.d as acl_d,
                        jega.fnum, jega.action_id, jega.c, jega.r, jega.u, jega.d, jesa.label as aname,
                        jesg.label as gname
                        FROM jos_emundus_acl as jeacl
                        LEFT JOIN jos_emundus_setup_actions as jesa ON jesa.id = jeacl.action_id
                        LEFT JOIN jos_emundus_setup_groups as jesg on jesg.id = jeacl.group_id
                        LEFT JOIN jos_emundus_group_assoc as jega on jega.group_id=jesg.id
                        WHERE  jega.fnum like ".$db->quote($fnum)." and jesa.status = 1
                        ORDER BY jega.fnum, jega.group_id, jega.action_id";
            $db->setQuery($query);
            $res = $db->loadAssocList();
            foreach($res as $r)
            {
                $ovverideAction = ($r['acl_action_id'] == $r['action_id']) ? true : false;
                if(isset($access['groups'][$r['group_id']]['actions'][$r['acl_action_id']]))
                {
                    $access['groups'][$r['group_id']]['isAssoc'] = true;
                    $access['groups'][$r['group_id']]['actions'][$r['acl_action_id']]['c'] += ($ovverideAction) ? (($r['acl_c']==-2 || $r['c']==-2) ? -2 : max($r['acl_c'], $r['c'])) : $r['acl_c'];
                    $access['groups'][$r['group_id']]['actions'][$r['acl_action_id']]['r'] += ($ovverideAction) ? (($r['acl_r']==-2 || $r['r']==-2) ? -2 : max($r['acl_r'], $r['r'])) : $r['acl_r'];
                    $access['groups'][$r['group_id']]['actions'][$r['acl_action_id']]['u'] += ($ovverideAction) ? (($r['acl_u']==-2 || $r['u']==-2) ? -2 : max($r['acl_u'], $r['u'])) : $r['acl_u'];
                    $access['groups'][$r['group_id']]['actions'][$r['acl_action_id']]['d'] += ($ovverideAction) ? (($r['acl_d']==-2 || $r['d']==-2) ? -2 : max($r['acl_d'], $r['d'])) : $r['acl_d'];
                }
                else
                {
                    $access['groups'][$r['group_id']]['gname'] = $r['gname'];
                    $access['groups'][$r['group_id']]['isAssoc'] = true;
                    $access['groups'][$r['group_id']]['isACL'] = false;
                    $access['groups'][$r['group_id']]['actions'][$r['acl_action_id']]['aname'] = $r['aname'];
                    $access['groups'][$r['group_id']]['actions'][$r['acl_action_id']]['c'] = ($ovverideAction) ? (($r['acl_c']==-2 || $r['c']==-2) ? -2 : max($r['acl_c'], $r['c'])) : $r['acl_c'];
                    $access['groups'][$r['group_id']]['actions'][$r['acl_action_id']]['r'] = ($ovverideAction) ? (($r['acl_r']==-2 || $r['r']==-2) ? -2 : max($r['acl_r'], $r['r'])) : $r['acl_r'];
                    $access['groups'][$r['group_id']]['actions'][$r['acl_action_id']]['u'] = ($ovverideAction) ? (($r['acl_u']==-2 || $r['u']==-2) ? -2 : max($r['acl_u'], $r['u'])) : $r['acl_u'];
                    $access['groups'][$r['group_id']]['actions'][$r['acl_action_id']]['d'] = ($ovverideAction) ? (($r['acl_d']==-2 || $r['d']==-2) ? -2 : max($r['acl_d'], $r['d'])) : $r['acl_d'];
                }
            }

            $query = "SELECT jeua.*, ju.name as uname, jesa.label as aname
                        FROM #__emundus_users_assoc as jeua
                        LEFT JOIN #__users as ju on ju.id = jeua.user_id
                        LEFT JOIN   #__emundus_setup_actions as jesa on jesa.id = jeua.action_id
                        where  jeua.fnum like '".$fnum."' and jesa.status = 1
                        ORDER BY jeua.fnum, jeua.user_id, jeua.action_id";
            $db->setQuery($query);
            $res = $db->loadAssocList();
            foreach($res as $r)
            {
                if(isset($access['groups'][$r['user_id']]['actions'][$r['action_id']]))
                {
                    $access['users'][$r['user_id']]['actions'][$r['action_id']]['c'] += $r['c'];
                    $access['users'][$r['user_id']]['actions'][$r['action_id']]['r'] += $r['r'];
                    $access['users'][$r['user_id']]['actions'][$r['action_id']]['u'] += $r['u'];
                    $access['users'][$r['user_id']]['actions'][$r['action_id']]['d'] += $r['d'];
                }
                else
                {
                    $access['users'][$r['user_id']]['uname'] = $r['uname'];
                    $access['users'][$r['user_id']]['actions'][$r['action_id']]['aname'] = $r['aname'];
                    $access['users'][$r['user_id']]['actions'][$r['action_id']]['c'] = $r['c'];
                    $access['users'][$r['user_id']]['actions'][$r['action_id']]['r'] = $r['r'];
                    $access['users'][$r['user_id']]['actions'][$r['action_id']]['u'] = $r['u'];
                    $access['users'][$r['user_id']]['actions'][$r['action_id']]['d'] = $r['d'];
                }

            }
            return $access;
        }
        catch(Exception $e)
        {
            error_log($e->getMessage(), 0);
            return false;
        }
    }

    public function getActions()
    {
        $dbo = $this->getDbo();
        try
        {
            $query = 'select * from #__emundus_setup_actions ';
            $dbo->setQuery($query);
            return $dbo->loadAssocList('id');
        }
        catch(Exception $e)
        {
            throw $e;
        }
    }

    public function checkGroupAssoc($fnum, $gid, $aid = null)
    {
        $dbo = $this->getDbo();
        try
        {
            if(!is_null($aid))
            {
                $query = "select * from #__emundus_group_assoc where `action_id` = $aid and  `group_id` = $gid and `fnum` like ".$dbo->quote($fnum);
            }
            else
            {
                $query = "select * from #__emundus_group_assoc where `group_id` = $gid and `fnum` like ".$dbo->quote($fnum);
            }
            $dbo->setQuery($query);
            return $dbo->loadObject();
        }
        catch(Exception $e)
        {
            throw $e;
        }
    }

    public function updateGroupAccess($fnum, $gid, $actionId, $crud, $value)
    {
        $dbo = $this->getDbo();
        try
        {
            if($this->checkGroupAssoc($fnum, $gid) !== null)
            {
                if($this->checkGroupAssoc($fnum, $gid, $actionId) !== null)
                {
                    $query = "update #__emundus_group_assoc set ".$dbo->quoteName($crud)." = ".$value.
                        " where `group_id` = $gid and `action_id` = $actionId and `fnum` like ".$dbo->quote($fnum);
                    $dbo->setQuery($query);
                    return $dbo->execute();
                }
                else
                {
                    return $this->_addGroupAssoc($fnum, $crud, $actionId, $gid, $value);
                }
            }
            else
            {
                return $this->_addGroupAssoc($fnum, $crud, $actionId, $gid, $value);
            }
        }
        catch(Exception $e)
        {
            throw $e;
        }
    }

    private function _addGroupAssoc($fnum, $crud, $aid, $gid, $value)
    {
        $dbo = $this->getDbo();
        $actionQuery = "select c, r, u, d from #__emundus_acl where action_id = $aid  and  group_id = $gid";
        $dbo->setQuery($actionQuery);
        $actions = $dbo->loadAssoc();
        $actions[$crud] = $value;
        $query = "INSERT INTO `#__emundus_group_assoc`(`group_id`, `action_id`, `fnum`, `c`, `r`, `u`, `d`) VALUES ($gid, $aid, ".$dbo->quote($fnum).",{$actions['c']}, {$actions['r']}, {$actions['u']}, {$actions['d']})";
        $dbo->setQuery($query);
        return $dbo->execute();
    }

    public function checkUserAssoc($fnum, $uid, $aid = null)
    {
        $dbo = $this->getDbo();
        try
        {
            if(!is_null($aid))
            {
                $query = "select * from #__emundus_users_assoc where `action_id` = $aid and  `user_id` = $uid and `fnum` like ".$dbo->quote($fnum);
            }
            else
            {
                $query = "select * from #__emundus_users_assoc where `user_id` = $uid and `fnum` like ".$dbo->quote($fnum);
            }
            $dbo->setQuery($query);
            return $dbo->loadObject();
        }
        catch(Exception $e)
        {
            throw $e;
        }
    }

    private function _addUserAssoc($fnum, $crud, $aid, $uid, $value)
    {
        $dbo = $this->getDbo();
        $actionQuery = "select jea.c, jea.r, jea.u, jea.d from #__emundus_acl as jea left join #__emundus_groups as jeg on jeg.group_id = jea.group_id
        where jea.action_id = {$aid}  and jeg.user_id  = {$uid}";
        $dbo->setQuery($actionQuery);
        $actions = $dbo->loadAssoc();
        $actionQuery = "select jega.c, jega.r, jega.u, jega.d from #__emundus_group_assoc as jega left join #__emundus_groups as jeg on jeg.group_id = jega.group_id
        where jega.action_id = {$aid} and jeg.user_id  = {$uid} and jega.fnum like {$dbo->quote($fnum)}";
        $dbo->setQuery($actionQuery);
        $actionAssoc = $dbo->loadAssoc();
        if(!empty($actionAssoc))
        {
            $actions['c'] += $actionAssoc['c'];
            $actions['r'] += $actionAssoc['r'];
            $actions['u'] += $actionAssoc['u'];
            $actions['d'] += $actionAssoc['d'];
        }
        $actions[$crud] = $value;
        $query = "INSERT INTO `#__emundus_group_assoc`(`user_id`, `action_id`, `fnum`, `c`, `r`, `u`, `d`) VALUES ($uid, $aid, ".$dbo->quote($fnum).",{$actions['c']}, {$actions['r']}, {$actions['u']}, {$actions['d']})";
        $dbo->setQuery($query);
        return $dbo->execute();
    }

    public function updateUserAccess($fnum, $uid, $actionId, $crud, $value)
    {
        $dbo = $this->getDbo();
        try
        {
            if($this->checkUserAssoc($fnum, $uid) !== null)
            {
                if($this->checkUserAssoc($fnum, $uid, $actionId) !== null)
                {
                    $query = "update #__emundus_users_assoc set ".$dbo->quoteName($crud)." = ".$value.
                        " where `user_id` = $uid and `action_id` = $actionId and `fnum` like ".$dbo->quote($fnum);
                    $dbo->setQuery($query);
                    return $dbo->execute();
                }
                else
                {
                    return $this->_addUserAssoc($fnum, $crud, $actionId, $uid, $value);
                }
            }
            else
            {
                return $this->_addUserAssoc($fnum, $crud, $actionId, $uid, $value);
            }
        }
        catch(Exception $e)
        {
            throw $e;
        }
    }

    public function deleteGroupAccess($fnum, $gid)
    {
        $dbo = $this->getDbo();
        try
        {
            $query = "delete from #__emundus_group_assoc  where `group_id` = $gid and `fnum` like ".$dbo->quote($fnum);
            $dbo->setQuery($query);
            return $dbo->execute();
        }
        catch(Exception $e)
        {
            throw $e;
        }
    }

    public function deleteUserAccess($fnum, $uid)
    {
        $dbo = $this->getDbo();
        try
        {
            $query = "delete from #__emundus_users_assoc where `user_id` = $uid and `fnum` like ".$dbo->quote($fnum);
            $dbo->setQuery($query);
            return $dbo->execute();
        }
        catch(Exception $e)
        {
            throw $e;
        }
    }

    public function getApplications($uid)
    {
        $db = $this->getDbo();
        try
        {
            $query = 'SELECT ecc.*, esc.*, ess.step, ess.value, ess.class
                        FROM #__emundus_campaign_candidature AS ecc
                        LEFT JOIN #__emundus_setup_campaigns AS esc ON esc.id=ecc.campaign_id
                        LEFT JOIN #__emundus_setup_status AS ess ON ess.step=ecc.status
                        WHERE ecc.applicant_id ='.$uid.'
                        ORDER BY esc.end_date DESC';
            $db->setQuery($query);
            $result = $db->loadObjectList('fnum');
            return (array) $result;
        }
        catch(Exception $e)
        {
            throw $e;
        }
    }

    public function getApplication($fnum)
    {
        $dbo = $this->getDbo();
        try
        {
            $query = 'SELECT ecc.*, esc.*, ess.step, ess.value, ess.class
                        FROM #__emundus_campaign_candidature AS ecc
                        LEFT JOIN #__emundus_setup_campaigns AS esc ON esc.id=ecc.campaign_id
                        LEFT JOIN #__emundus_setup_status AS ess ON ess.step=ecc.status
                        WHERE ecc.fnum like '.$dbo->Quote($fnum).'
                        ORDER BY esc.end_date DESC';
            $dbo->setQuery($query);
            $result = $dbo->loadObject();
            return $result;
        }
        catch(Exception $e)
        {
            throw $e;
        }
    }

    /**
     * Return the order for current fnum. If an order with confirmed status is found for fnum campaign period, then return the order
     * If $sent is sent to true, the function will search for orders with a status of 'created' and offline paiement methode
     * @param $fnumInfos $sent
     * @param bool $sent
     * @param bool $admission
     * @return bool|mixed
     */
    public function getHikashopOrder($fnumInfos, $sent = false, $admission = false) {
        $eMConfig = JComponentHelper::getParams('com_emundus');

        if ($admission) {
            $startDate = $fnumInfos['admission_start_date'];
            $endDate = $fnumInfos['admission_end_date'];
        } else {
            $startDate = $fnumInfos['start_date'];
            $endDate = $fnumInfos['end_date'];
        }

        $dbo = $this->getDbo();

        $em_application_payment = $eMConfig->get('application_payment', 'user');

        switch ($em_application_payment) {


            case 'user' :
                if ($sent) {

                    $query = 'SELECT ho.*, hu.user_cms_id
                                FROM #__hikashop_order ho
                                LEFT JOIN #__hikashop_user hu on hu.user_id=ho.order_user_id
                                WHERE hu.user_cms_id='.$fnumInfos['applicant_id'].'
                                AND (ho.order_status like "created" OR ho.order_status like "confirmed")
                                AND ho.order_created >= '.strtotime($startDate).'
                                AND ho.order_created <= '.strtotime($endDate).'
                                ORDER BY ho.order_created desc';

                }
                else {

                    $query = 'SELECT ho.*, hu.user_cms_id
                                FROM #__hikashop_order ho
                                LEFT JOIN #__hikashop_user hu on hu.user_id=ho.order_user_id
                                WHERE hu.user_cms_id='.$fnumInfos['applicant_id'].'
                                AND ho.order_status like "confirmed"
                                AND ho.order_created >= '.strtotime($startDate).'
                                AND ho.order_created <= '.strtotime($endDate).'
                                ORDER BY ho.order_created desc';

                }
            break;

            case 'fnum' :
                if ($sent) {
                    $query = 'SELECT ho.*, eh.user as user_cms_id
                                FROM #__emundus_hikashop eh
                                LEFT JOIN #__hikashop_order ho on ho.order_id = eh.order_id
                                WHERE eh.fnum LIKE "'.$fnumInfos['fnum'].'" 
                                AND (ho.order_status like "created" OR ho.order_status like "confirmed")
                                AND ho.order_created >= '.strtotime($startDate).'
                                AND ho.order_created <= '.strtotime($endDate).'
                                ORDER BY ho.order_created desc';
                }
                else {
                    $query = 'SELECT ho.*, eh.user as user_cms_id
                                FROM #__emundus_hikashop eh
                                LEFT JOIN #__hikashop_order ho on ho.order_id = eh.order_id
                                WHERE eh.fnum LIKE "'.$fnumInfos['fnum'].'" 
                                AND ho.order_status like "confirmed"
                                AND ho.order_created >= '.strtotime($startDate).'
                                AND ho.order_created <= '.strtotime($endDate).'
                                ORDER BY ho.order_created desc';
                }
            break;

            case 'campaign' :
                if ($sent) {
                    $query = 'SELECT ho.*, hu.user_cms_id
                                FROM #__emundus_hikashop eh
                                LEFT JOIN #__hikashop_order ho on ho.order_id = eh.order_id
                                LEFT JOIN #__hikashop_user hu on hu.user_id=ho.order_user_id
                                WHERE eh.campaign_id = '.$fnumInfos['id'].' 
                                AND hu.user_cms_id = '.$fnumInfos['applicant_id'].' 
                                AND (ho.order_status like "created" OR ho.order_status like "confirmed")
                                AND ho.order_created >= '.strtotime($startDate).'
                                AND ho.order_created <= '.strtotime($endDate).'
                                ORDER BY ho.order_created desc';
                }
                else {
                    $query = 'SELECT ho.*, hu.user_cms_id
                                FROM #__emundus_hikashop eh
                                LEFT JOIN #__hikashop_order ho on ho.order_id = eh.order_id
                                LEFT JOIN #__hikashop_user hu on hu.user_id=ho.order_user_id
                                WHERE eh.campaign_id= '.$fnumInfos['id'].' 
                                AND hu.user_cms_id = '.$fnumInfos['applicant_id'].' 
                                AND ho.order_status like "confirmed"
                                AND ho.order_created >= '.strtotime($startDate).'
                                AND ho.order_created <= '.strtotime($endDate).'
                                ORDER BY ho.order_created desc';
                }
                break;
        }


        try {

            $dbo->setQuery($query);
            return $dbo->loadObject();

        } catch (Exception $e) {
            echo $e->getMessage();
            JLog::add(JUri::getInstance().' :: USER ID : '.JFactory::getUser()->id.' -> '.$e->getMessage(), JLog::ERROR, 'com_emundus');
            return false;
        }
    }

    /**
     * Return any cancelled orders for the current fnum. If an order with cancelled status is found for fnum campaign period, then return the order
     * @param $fnumInfos
     * @return bool|mixed
     */
    public function getHikashopCancelledOrders($fnumInfos, $admission = false) {

        if($admission) {
            $startDate = $fnumInfos['admission_start_date'];
            $endDate = $fnumInfos['admission_end_date'];
        }
        else {
            $startDate = $fnumInfos['start_date'];
            $endDate = $fnumInfos['end_date'];
        }

        $db = $this->getDBo();

        try {

            $query = 'SELECT ho.*, hu.user_cms_id
                FROM #__hikashop_order ho
                LEFT JOIN #__hikashop_user hu on hu.user_id=ho.order_user_id
                WHERE hu.user_cms_id='.$fnumInfos['applicant_id'].'
                AND ho.order_status like "canceled"
                AND ho.order_created >= '.strtotime($startDate).'
                AND ho.order_created <= '.strtotime($endDate);

            $db->setQuery($query);
            return $db->loadObject();

        } catch (Exception $e) {
            JLog::add('Error in model/application at query : '.$query, JLog::ERROR, 'com_emundus');
            return false;
        }

    }


    /**
     * Return the checkout URL order for current fnum.
     * @param $pid      the applicant's profile_id
     * @return bool|mixed
     */
    public function getHikashopCheckoutUrl($pid)
    {
        $dbo = $this->getDbo();
        try
        {
            $query = 'SELECT CONCAT(link, "&Itemid=", id) as url
                        FROM #__menu
                        WHERE alias like "checkout'.$pid.'"';
            $dbo->setQuery($query);
            $url = $dbo->loadResult();
            return $url;
        }
        catch (Exception $e)
        {
            echo $e->getMessage();
            JLog::add(JUri::getInstance().' :: USER ID : '.JFactory::getUser()->id.' -> '.$e->getMessage(), JLog::ERROR, 'com_emundus');
            return false;
        }
    }


	/**
	 * Move an application file from one programme to another
	 *
	 * @param      $fnum_from String the fnum of the source
	 * @param      $fnum_to   String the fnum of the moved application
	 * @param      $campaign  String the programme id to move the file to
	 * @param null $status
	 *
	 * @return bool
	 */
    public function moveApplication($fnum_from, $fnum_to, $campaign, $status = null) {
        $db = JFactory::getDbo();

        try {

            $query = 'SELECT * FROM #__emundus_campaign_candidature cc WHERE fnum like ' . $db->Quote($fnum_from);
            $db->setQuery($query);
            $cc_line = $db->loadAssoc();

            if (!empty($cc_line)) {

                $query = 'UPDATE #__emundus_campaign_candidature SET `fnum` = '. $db->Quote($fnum_to) .', `campaign_id` = '. $db->Quote($campaign) .', `copied` = 2 WHERE `id` = ' . $db->Quote($cc_line['id']);
                if (!empty($status)) {
                	$query .= ' `status` = '.$db->Quote($status);
                }
                $db->setQuery($query);
                $db->execute();

            } else {
            	return false;
            }

        } catch (Exception $e) {

            echo $e->getMessage();
            JLog::add(JUri::getInstance().' :: USER ID : '.JFactory::getUser()->id.' -> '.$e->getMessage(), JLog::ERROR, 'com_emundus');
            return false;

        }

        return true;
    }

    /**
     * Duplicate an application file (form data)
     * @param $fnum_from String the fnum of the source
     * @param $fnum_to String the fnum of the duplicated application
     * @param $pid Int the profile_id to get list of forms
     * @return bool
     */
    public function copyApplication($fnum_from, $fnum_to, $pid = null) {
        $db = JFactory::getDbo();

        try {
            if (empty($pid)) {
                $m_profiles = new EmundusModelProfile();

                $fnumInfos = $m_profiles->getFnumDetails($fnum_from);
                $pid = (isset($fnumInfos['profile_id_form']) && !empty($fnumInfos['profile_id_form']))?$fnumInfos['profile_id_form']:$fnumInfos['profile_id'];
            }

            $forms = @EmundusHelperMenu::buildMenuQuery($pid);

            foreach ($forms as $form) {

                $query = 'SELECT * FROM '.$form->db_table_name.' WHERE fnum like '.$db->Quote($fnum_from);
                $db->setQuery($query);
                $stored = $db->loadAssoc();

                if (count($stored) > 0) {
                    // update form data
                    $parent_id = $stored['id'];
                    unset($stored['id']);
                    $stored['fnum'] = $fnum_to;
                    $q=1;

                    $query = 'INSERT INTO '.$form->db_table_name.' (`'.implode('`,`', array_keys($stored)).'`) VALUES('.implode(',', $db->Quote($stored)).')';
                    $db->setQuery($query);
                    $db->execute();
                    $id = $db->insertid();

                    // liste des groupes pour le formulaire d'une table
                    $query = 'SELECT ff.id, ff.group_id, fe.name, fg.id, fg.label, (IF( ISNULL(fj.table_join), fl.db_table_name, fj.table_join)) as `table`, fg.params as `gparams`
                                FROM #__fabrik_formgroup ff
                                LEFT JOIN #__fabrik_lists fl ON fl.form_id=ff.form_id
                                LEFT JOIN #__fabrik_groups fg ON fg.id=ff.group_id
                                LEFT JOIN #__fabrik_elements fe ON fe.group_id=fg.id
                                LEFT JOIN #__fabrik_joins AS fj ON (fj.group_id = fe.group_id AND fj.list_id != 0 AND fj.element_id = 0)
                                WHERE ff.form_id = "'.$form->form_id.'"
                                ORDER BY ff.ordering';
                    $q=2;
                    $db->setQuery($query);
                    $groups = $db->loadObjectList();

                    // get data and update current form
                    $data = array();
                    if (count($groups) > 0) {
                        foreach ($groups as $group) {
                            $group_params = json_decode($group->gparams);
                            if (@$group_params->repeat_group_button == 1) {
                                $data[$group->group_id]['repeat_group'] = $group_params->repeat_group_button;
                                $data[$group->group_id]['group_id'] = $group->group_id;
                                $data[$group->group_id]['element_name'][] = $group->name;
                                $data[$group->group_id]['table'] = $group->table;
                            }
                        }

                        if (count($data) > 0) {
                            foreach ($data as $d) {
                                $q=3;
                                $query = 'SELECT '.implode(',', $d['element_name']).' FROM '.$d['table'].' WHERE parent_id='.$parent_id;
                                $db->setQuery($query);
                                $stored = $db->loadAssocList();

                                if (count($stored) > 0) {
                                    $arrayValue = [];

                                    foreach($stored as $rowvalues) {
                                        unset($rowvalues['id']);
                                        $rowvalues['parent_id'] = $id;
                                        $arrayValue[] = '('.implode(',', $db->quote($rowvalues)).')';
                                        $keyValue[] = $rowvalues;
                                    }
                                    unset($stored[0]['id']);
                                    $q=4;

                                    // update form data
                                    $query = 'INSERT INTO '.$d['table'].' (`'.implode('`,`', array_keys($stored[0])).'`)'.' VALUES '.implode(',', $arrayValue);
                                    $db->setQuery($query);
                                    $db->execute();
                                }
                            }
                        }
                    }
                }
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            JLog::add(JUri::getInstance().' :: USER ID : '.JFactory::getUser()->id.' -> '.$q.' :: '.$query, JLog::ERROR, 'com_emundus');
            return false;
        }

        return true;
    }

    /**
     * Duplicate all documents (files)
     * @param $fnum_from String the fnum of the source
     * @param $fnum_to String the fnum of the duplicated application
     * @param $pid Int the profile_id to get list of forms
     * @param null $duplicated
     * @return bool
     */
    public function copyDocuments($fnum_from, $fnum_to, $pid = null, $can_delete = null) {
        $db = JFactory::getDbo();

        try {
            if (empty($pid)) {
                $m_profiles = new EmundusModelProfile();

                $fnumInfos = $m_profiles->getFnumDetails($fnum_from);
                $pid = (isset($fnumInfos['profile_id_form']) && !empty($fnumInfos['profile_id_form']))?$fnumInfos['profile_id_form']:$fnumInfos['profile_id'];
            }

            // 1. get list of uploaded documents for previous file defined as duplicated
            $query = 'SELECT eu.*
                        FROM #__emundus_uploads as eu
                        LEFT JOIN #__emundus_setup_attachment_profiles as esap on esap.attachment_id=eu.attachment_id AND esap.profile_id='.$pid.'
                        WHERE eu.fnum like '.$db->Quote($fnum_from);

            if (empty($pid)) {
            	$query .= ' AND esap.duplicate=1';
            }

            $db->setQuery($query);
            $stored = $db->loadAssocList();

            if (count($stored) > 0) {
                // 2. copy DB définition and duplicate files in applicant directory
                foreach ($stored as $row) {
                    $src = $row['filename'];
                    $ext = explode('.', $src);
                    $ext = $ext[count($ext)-1];
                    $cpt = 0-(int)(strlen($ext)+1);
                    $dest = substr($row['filename'], 0, $cpt).'-'.$row['id'].'.'.$ext;
                    $row['filename'] = $dest;
                    $row['fnum'] = $fnum_to;
                    $row['can_be_deleted'] = empty($can_delete) ? 0 : 1;
                    unset($row['id']);

                    try {
                        $query = 'INSERT INTO #__emundus_uploads (`'.implode('`,`', array_keys($row)).'`) VALUES('.implode(',', $db->Quote($row)).')';
                        $db->setQuery($query);
                        $db->execute();
                        $id = $db->insertid();
                        $path = EMUNDUS_PATH_ABS.$row['user_id'].DS;
                        if (!copy($path.$src, $path.$dest)) {
                            $query = 'UPDATE #__emundus_uploads SET filename='.$src.' WHERE id='.$id;
                            $db->setQuery($query);
                            $db->execute();
                        }
                    } catch (Exception $e) {
                        $error = JUri::getInstance().' :: USER ID : '.$row['user_id'].' -> '.$e->getMessage();
                        JLog::add($error, JLog::ERROR, 'com_emundus');
                    }
                }
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            JLog::add(JUri::getInstance().' :: USER ID : '.JFactory::getUser()->id.' -> '.$e->getMessage(), JLog::ERROR, 'com_emundus');
            return false;
        }

        return true;
    }

    /**
     * Duplicate all documents (files)
     *
     * @param       $fnum             String     the fnum of application file
     * @param       $applicant        Object     the applicant user ID
     * @param array $param
     * @param int   $status
     *
     * @return bool
     */
    public function sendApplication($fnum, $applicant, $param = array(), $status = 1) {
        include_once(JPATH_BASE.'/components/com_emundus/models/emails.php');

        if ($status == '-1') {
            $status = $applicant->status;
        }

        $db = JFactory::getDBO();
        try {
            // Vérification que le dossier à été entièrement complété par le candidat
            $query = 'SELECT id
                        FROM #__emundus_declaration
                        WHERE fnum  like '.$db->Quote($fnum);
            $db->setQuery($query);
            $db->execute();
            $id = $db->loadResult();

            $today = date('Y-m-d h:i:s');

            if ($id > 0) {
                $query = 'UPDATE #__emundus_declaration SET time_date='.$db->quote($today). ', user='.$applicant->id.' WHERE id='.$id;
            } else {
                $query = 'INSERT INTO #__emundus_declaration (time_date, user, fnum, type_mail)
                                VALUE ('.$db->quote($today). ', '.$applicant->id.', '.$db->Quote($fnum).', "paid_validation")';
            }

            $db->setQuery($query);
            $db->execute();

            // Insert data in #__emundus_campaign_candidature
            $query = 'UPDATE #__emundus_campaign_candidature SET submitted=1, date_submitted=NOW(), status='.$status.' WHERE applicant_id='.$applicant->id.' AND campaign_id='.$applicant->campaign_id. ' AND fnum like '.$db->Quote($applicant->fnum);
            $db->setQuery($query);
            $db->execute();

            // Send emails defined in trigger
            $m_emails = new EmundusModelEmails;
            $code = array($applicant->code);
            $to_applicant = '0,1';
            $m_emails->sendEmailTrigger($status, $code, $to_applicant, $applicant);

        } catch (Exception $e) {
            // catch any database errors.
            JLog::add(JUri::getInstance().' :: USER ID : '.JFactory::getUser()->id.' -> '.$e->getMessage(), JLog::ERROR, 'com_emundus');
        }

        return true;
    }

    /**
     * Check if iframe can be used
     * @param $url String url to check
     * @return bool
     */
    function allowEmbed($url) {
        $header = @get_headers($url, 1);

        // URL okay?
        if (!$header || stripos($header[0], '200 ok') === false)
            return false;

        // Check X-Frame-Option
        elseif (isset($header['X-Frame-Options']) && (stripos($header['X-Frame-Options'], 'SAMEORIGIN') !== false || stripos($header['X-Frame-Options'], 'deny') !== false))
            return false;

        // Everything passed? Return true!
        return true;
    }

	/**
	 * Gets the first page of the application form. Used for opening a file.
	 *
	 * @param string $redirect
	 * @param null   $fnums
	 *
	 * @return String The URL to the form.
	 * @since 3.8.8
	 */
    function getFirstPage($redirect = 'index.php', $fnums = null) {

        $user = JFactory::getSession()->get('emundusUser');
        $db = JFactory::getDBo();
	    $query = $db->getQuery(true);

        if (!empty($fnums)) {

            $fnums = is_array($fnums) ? implode(',', $fnums) : $fnums;

	        $query->select(['CONCAT(m.link,"&Itemid=", m.id) as link', $db->quoteName('cc.fnum')])
		        ->from($db->quoteName('#__emundus_campaign_candidature', 'cc'))
		        ->leftJoin($db->quoteName('#__emundus_setup_campaigns', 'esc').' ON '.$db->quoteName('esc.id').' = '.$db->quoteName('cc.campaign_id'))
		        ->leftJoin($db->quoteName('#__emundus_setup_profiles', 'esp').' ON '.$db->quoteName('esp.id').' = '.$db->quoteName('esc.profile_id'))
		        ->leftJoin($db->quoteName('#__menu', 'm').' ON '.$db->quoteName('m.menutype').' = '.$db->quoteName('esp.menutype').' AND '.$db->quoteName('m.published').'=1 AND '.$db->quoteName('link').' <> "" AND '.$db->quoteName('link').' <> "#"')
		        ->where($db->quoteName('cc.fnum').' IN('.$fnums.')')
		        ->order($db->quoteName('m.lft').' DESC');
	        $db->setQuery($query);

	        try {
		        return $db->loadAssocList('fnum');
	        } catch (Exception $e) {
		        JLog::add('Error getting first page of application at model/application in query : '.$query->__toString(), JLog::ERROR, 'com_emundus');
		        return $redirect;
	        }

        } else {

	        if (empty($user->menutype)) {
		        return $redirect;
	        }

	        $query->select(['id','link'])
		        ->from($db->quoteName('#__menu'))
		        ->where($db->quoteName('published').'=1 AND '.$db->quoteName('menutype').' LIKE '.$db->quote($user->menutype).' AND '.$db->quoteName('link').' <> "" AND '.$db->quoteName('link').' <> "#"')
		        ->order($db->quoteName('lft').' ASC');

	        try {
		        $db->setQuery($query);
		        $res = $db->loadObject();
		        return $res->link.'&Itemid='.$res->id;
	        } catch (Exception $e) {
		        JLog::add('Error getting first page of application at model/application in query : '.$query->__toString(), JLog::ERROR, 'com_emundus');
		        return $redirect;
	        }

        }
    }

    public function attachment_validation($attachment_id, $state) {
        $dbo = $this->getDbo();
        try {
            $query = 'UPDATE #__emundus_uploads  SET `is_validated` = '.(int) $state.' WHERE `id` = '.(int) $attachment_id;
            $dbo->setQuery($query);
            return $dbo->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }



    /** Gets the URL of the final form in the application in order to submit.
     * @param $fnums
     *
     * @return Mixed
     */
    function getConfirmUrl($fnums = null) {

        $user = JFactory::getSession()->get('emundusUser');
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        if (!empty($fnums)) {
            $query->select(['CONCAT(m.link,"&Itemid=", m.id) as link', $db->quoteName('cc.fnum')])
                ->from($db->quoteName('#__emundus_campaign_candidature', 'cc'))
                ->leftJoin($db->quoteName('#__emundus_setup_campaigns', 'esc') . ' ON ' . $db->quoteName('esc.id') . ' = ' . $db->quoteName('cc.campaign_id'))
                ->leftJoin($db->quoteName('#__emundus_setup_profiles', 'esp') . ' ON ' . $db->quoteName('esp.id') . ' = ' . $db->quoteName('esc.profile_id'))
                ->leftJoin($db->quoteName('#__menu', 'm') . ' ON ' . $db->quoteName('m.menutype') . ' = ' . $db->quoteName('esp.menutype') . ' AND ' . $db->quoteName('m.published') . '>=0 AND ' . $db->quoteName('m.level') . '=1 AND ' . $db->quoteName('m.link') . ' <> "" AND ' . $db->quoteName('m.link') . ' <> "#"')
                ->where($db->quoteName('cc.fnum') . ' IN(' . implode(',', $fnums) . ')')
                ->order($db->quoteName('m.lft') . ' ASC');

            $db->setQuery($query);
            try {
                return $db->loadAssocList('fnum');
            } catch (Exception $e) {
                JLog::add('Error getting confirm URLs in model/application at query -> ' . $query->__toString(), JLog::ERROR, 'com_emundus');
                return false;
            }
        } else {

            if (empty($user->menutype)) {
                return false;
            }

            $query->select(['id','link'])
                ->from($db->quoteName('#__menu'))
                ->where($db->quoteName('published').'=1 AND '.$db->quoteName('menutype').' LIKE '.$db->quote($user->menutype).' AND '.$db->quoteName('link').' <> "" AND '.$db->quoteName('link').' <> "#"')
                ->order($db->quoteName('lft') . ' DESC');
            try {
                $db->setQuery($query);

                $res = $db->loadObject();
                return $res->link.'&Itemid='.$res->id;
            } catch (Exception $e) {
                JLog::add('Error getting first page of application at model/application in query : '.$query->__toString(), JLog::ERROR, 'com_emundus');
                return false;
            }
        }

    }


    public function searchFilesByKeywords($fnum){
        $db = $this->getDbo();
        $jinput = JFactory::getApplication()->input;
        $search = $jinput->get('search');

        $query = 'SELECT eu.id AS aid, esa.*, eu.attachment_id, eu.filename, eu.description, eu.timedate, eu.can_be_deleted, eu.can_be_viewed, eu.is_validated, esc.label as campaign_label, esc.year, esc.training
            FROM #__emundus_uploads AS eu
            LEFT JOIN #__emundus_setup_attachments AS esa ON  eu.attachment_id=esa.id
            WHERE eu.fnum like '.$this->_db->Quote($fnum).'
            AND $where LIKE '.$search;

        $db->setQuery($query);
        return $db->execute();
    }

	/**
	 * @param $elements
	 * @param $table
	 * @param $parent_table
	 * @param $fnum
	 *
	 * @return bool
	 *
	 */
    public function checkEmptyRepeatGroups($elements, $table, $parent_table, $fnum) {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $subQuery = $db->getQuery(true);

        $elements = array_map(function($obj) {return 't.'.$obj->name;}, $elements);

        $subQuery
            ->select($db->quoteName('id'))
            ->from($db->quoteName($parent_table))
            ->where($db->quoteName('fnum') . ' LIKE ' . $db->quote($fnum));

        $query
            ->select(implode(',', $elements))
            ->from($db->quoteName($table, 't'))
            ->leftJoin($db->quoteName($parent_table, 'j') . ' ON ' . $db->quoteName('j.id') . ' = '. $db->quoteName('t.parent_id'))
            ->where($db->quoteName('t.parent_id') . " = (" . $subQuery . ")");

        try {
            $db->setQuery($query);
            $db->execute();
            if ($db->getNumRows() == 1) {
                $res = $db->loadAssoc();

                $elements = array_map(function($arr) {
                    if (is_numeric($arr)) {
                        return (empty(floatval($arr)));
                    } else {
                        if ($arr == "0000-00-00 00:00:00") {
                            return true;
                        }
                        return empty($arr);
                    }
                }, $res);

                $elements = array_filter($elements, function($el) {return $el === false;});
                return !empty($elements);
            }

            return true;

        } catch (Exception $e ) {
	        JLog::add('Error checking if repeat group is empty at model/application in query : '.$query->__toString(), JLog::ERROR, 'com_emundus');
	        return false;
        }
    }

}
