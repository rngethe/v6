<?php


function application_form_pdf($user_id, $rowid, $output = true) {
	jimport( 'joomla.html.parameter' );

	//$rowid = JRequest::getVar('rowid', null, 'GET', 'none',0);

	$db = JFactory::getDBO();

	require_once(JPATH_COMPONENT.DS.'helpers'.DS.'filters.php');
	require_once(JPATH_COMPONENT.DS.'helpers'.DS.'list.php');
	require_once(JPATH_COMPONENT.DS.'helpers'.DS.'menu.php');
	require_once(JPATH_COMPONENT.DS.'models'.DS.'users.php');
	include_once(JPATH_BASE.DS.'components'.DS.'com_emundus'.DS.'models'.DS.'application.php');


	$menu = new EmundusHelperMenu;

	$current_user =  JFactory::getUser();
	$user =  JFactory::getUser($user_id); 

	$application = new @EmundusModelApplication;

	// Element Fabrik ID list to display in PDF
	$elts = array(2111, 2112, 2114, 2117, 2133, 2119, 2120, 2121, 2123, 2124, 2125, 2126, 2127);
	
	$options = array('show_list_label' => 0, 'show_form_label' => 0, 'show_group_label' => 0, 'rowid' => $rowid, 'profile_id' => '13');

	$forms = $application->getFormsPDFElts($user_id, $elts, $options);

	// Set title for PDF
	$title = @emunduShelpeRlist::getElementsDetailsByID(2113); 

	$where = 'user='.$user_id;
	$where .= $options['rowid']>0?' AND id='.$options['rowid']:'';
	$query = 'SELECT '.$title[0]->element_name. ' FROM '.$title[0]->tab_name.' WHERE '.$where;
	$db->setQuery($query);
	$intitule_poste = $db->loadResult();

	$htmldata = '';
	$htmldata .= '<h1>'.$intitule_poste.'</h1>';
	$htmldata .= $forms;
	// --------------------- //
	set_time_limit(0);
	require_once(JPATH_LIBRARIES.DS.'emundus'.DS.'tcpdf'.DS.'config'.DS.'lang'.DS.'eng.php');
	require_once(JPATH_LIBRARIES.DS.'emundus'.DS.'tcpdf'.DS.'tcpdf.php');
	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('www.emundus.fr');
	$pdf->SetTitle('Fiche emploi étudiant');
	
	//get logo
	$app 		= JFactory::getApplication();
	$template 	= $app->getTemplate(true);
	$params     = $template->params;
	$image   	= $params->get('logo')->custom->image; 
	$logo 		= preg_match_all("/'([^']*)'/", $image, $matches);
	$logo 		= !empty($matches[1][1]) ? JPATH_ROOT.DS.$matches[1][1] : preg_match_all('/"([^"]*)"/', $image, $matches);
	$logo 		= !empty($logo) ? JPATH_ROOT.DS.$matches[1][1] : "";
	
	//get title
	//$config = JFactory::getConfig(); 
	//$title = $config->getValue('config.sitename');
	$title = 'Emplois étudiants Sorbonne Universités';
	$pdf->SetHeaderData($logo, PDF_HEADER_LOGO_WIDTH, $title, PDF_HEADER_STRING);
	unset($logo);
	unset($title);

	$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
	$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	$pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
	$pdf->SetFont('helvetica', '', 8);
	$pdf->AddPage();
	$dimensions = $pdf->getPageDimensions();

	$html_table = '';
	$nb_groupes = 0;
	$nb_lignes = 0;
	$current_group_repeated = 0;

	if (!empty($htmldata)) {
		$pdf->startTransaction();
		$start_y = $pdf->GetY();
		$start_page = $pdf->getPage();
		//$pdf->Bookmark($itemt->label, 0);
		$pdf->writeHTMLCell(0,'','',$start_y, preg_replace('/(<[^>]+) style=".*?"/i', '$1', $htmldata),'B', 1);
		// $pdf->Ln(1);
		$end_page = $pdf->getPage();
		/*if ($end_page != $start_page) {
			$pdf = $pdf->rollbackTransaction();
			$pdf->addPage(); 
			$pdf->Bookmark($itemt->label, 0);
			$pdf->writeHTMLCell(0,'','',$pdf->GetY(),$htmldata,'B', 1);
			// $pdf->Ln(1);
		}*/
		//$htmldata = '';
	}

	@chdir('tmp');
	/*if(!file_exists(EMUNDUS_PATH_ABS.$item->id)) {	
		if (!mkdir(EMUNDUS_PATH_ABS.$item->id, 0777, true) || !copy(EMUNDUS_PATH_ABS.'index.html', EMUNDUS_PATH_ABS.$item->id.DS.'index.html')) 
			return JError::raiseWarning(500, 'Unable to create user file');
	}
*/
	if($output){
		$pdf->Output(EMUNDUS_PATH_ABS.$item->user_id.DS.'fiche_'.$rowid.'.pdf', 'FI');
	}else{
		$pdf->Output(EMUNDUS_PATH_ABS.$item->user_id.DS.'fiche_'.$rowid.'.pdf', 'FI');
	}

}
?>