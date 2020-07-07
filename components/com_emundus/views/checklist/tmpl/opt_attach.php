<?php
defined('_JEXEC') or die('Restricted access');
$user = JFactory::getSession()->get('emundusUser');
$_db = JFactory::getDBO();

$document = JFactory::getDocument();
$document->addStyleSheet("media/com_emundus/css/emundus_checklist.css" );

$query='SELECT id, link FROM #__menu WHERE alias like "checklist%" AND menutype like "%'.$user->menutype.'"';
$_db->setQuery( $query );
$itemid = $_db->loadAssoc();
?>
</ul>
<?php

// Check if column campaign_id exist in emundus_setup_attachment_profiles
$config = new JConfig();
$db_name = $config->db;

$query = $_db->getQuery(true);
$query
    ->select('COUNT(*)')
    ->from($_db->quoteName('information_schema.columns'))
    ->where($_db->quoteName('table_schema') . ' = ' . $_db->quote($db_name))
    ->andWhere($_db->quoteName('table_name') . ' = ' . $_db->quote('jos_emundus_setup_attachment_profiles'))
    ->andWhere($_db->quoteName('column_name') . ' = ' . $_db->quote('campaign_id'));
$_db->setQuery($query);
$exist = $_db->loadResult();
//
if(intval($exist) > 0){
    $query='SELECT esa.value, esap.id, esa.id as _id
	FROM #__emundus_setup_attachment_profiles esap
	JOIN #__emundus_setup_attachments esa ON esa.id = esap.attachment_id
    WHERE esap.displayed = 1 AND esap.campaign_id ='.$user->campaign_id.'
	ORDER BY esap.ordering';

    $_db->setQuery( $query );
    $all_forms = $_db->loadObjectList();

    $query='SELECT esa.value, esap.id, esa.id as _id
	FROM #__emundus_setup_attachment_profiles esap
	JOIN #__emundus_setup_attachments esa ON esa.id = esap.attachment_id
    WHERE esap.displayed = 1 AND esap.mandatory = 0 AND esap.campaign_id ='.$user->campaign_id.'
	ORDER BY esap.ordering';

    $_db->setQuery( $query );
    $forms = $_db->loadObjectList();
}

if (intval($exist) == 0 || empty($all_forms)) {
    $query='SELECT esa.value, esap.id, esa.id as _id
	FROM #__emundus_setup_attachment_profiles esap
	JOIN #__emundus_setup_attachments esa ON esa.id = esap.attachment_id
	WHERE esap.displayed = 1 AND esap.mandatory = 0 AND esap.profile_id ='.$user->profile.' AND esap.campaign_id IS NULL  
	ORDER BY esa.ordering';

    $_db->setQuery( $query );
    $forms = $_db->loadObjectList();
}
		foreach ($forms as $form) {
			$query = 'SELECT count(id) FROM #__emundus_uploads up
						WHERE up.user_id = '.$user->id.' AND up.attachment_id = '.$form->_id.' AND fnum like '.$_db->Quote($user->fnum);
						//echo $query;
			$_db->setQuery( $query );
			$cpt = $_db->loadResult();
			$link 	= '<a id="'.$form->_id.'" class="document" href="'.$itemid['link'].'&Itemid='.$itemid['id'].'#a'.$form->_id.'">';
			if ($cpt==0)
				$class	= 'need_missing_fac';
			else
				$class	= 'need_ok';
			$endlink= '</a>';
?>
    <li class="em_module <?php echo $class; ?>"><div class="em_form em-checklist"><?php echo $link.$form->value.$endlink; ?></div></li>
<?php } ?>
</ul>
<?php
unset($link);
unset($endlink);
?>
