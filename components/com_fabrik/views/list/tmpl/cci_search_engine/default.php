<?php
/**
 * Fabrik List Template: Div
 *
 * @package     Joomla
 * @subpackage  Fabrik
 * @copyright   Copyright (C) 2005-2016  Media A-Team, Inc. - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

$doc = JFactory::getDocument();
$doc->addStyleSheet('media/com_emundus/lib/bootstrap-232/css/bootstrap.min.css');
$doc->addScript('media/com_emundus/lib/chosen/chosen.jquery.js');
$doc->addStyleSheet('media/com_emundus/lib/chosen/chosen.css');

// The number of columns to split the list rows into
$pageClass = $this->params->get('pageclass_sfx', '');

if ($pageClass !== '') :
	echo '<div class="' . $pageClass . '">';
endif;

if ($this->tablePicker != '') : ?>
    <div style="text-align:right"><?php echo FText::_('COM_FABRIK_LIST') ?>: <?php echo $this->tablePicker; ?></div>
<?php
endif;

if ($this->params->get('show_page_heading')) :
	echo '<h1>' . $this->params->get('page_heading') . '</h1>';
endif;

if ($this->showTitle == 1) : ?>
    <div class="page-header">
        <h1><?php echo $this->table->label;?></h1>
    </div>
<?php
endif;

// Intro outside of form to allow for other lists/forms to be injected.
echo $this->table->intro;

?>

<div class="main">
    <div class="form">
        <form class="fabrikForm form-search" action="<?php echo $this->table->action;?>" method="post" id="<?php echo $this->formid;?>" name="fabrikList">

			<?php
			if ($this->hasButtons)
				echo $this->loadTemplate('buttons');
			?>

            <div class="fabrikDataContainer">

				<?php foreach ($this->pluginBeforeList as $c) {
					echo $c;
				}


				$data = array();
				$i = 0;
				if (!empty($this->rows[0])) {
					foreach ($this->rows[0] as $k => $v) {
						foreach ($this->headings as $key => $val) {
							if (array_key_exists($key, $v->data)) {
								if (strcasecmp($v->data->$key, "1") == 0)
									$data[$i][$val] = $v->data->$key;
								else
									$data[$i][$key] = $v->data->$key;
							}
						}
						if (array_key_exists('fabrik_view_url', $v->data)) {
							$data[$i]['fabrik_view_url'] = $v->data->fabrik_view_url;
						}
						$i = $i + 1;
					}
				} ?>

                <div class="em-search-engine-filters">
					<?php if ($this->showFilters && $this->bootShowFilters)
						echo $this->layoutFilters();
					?>
                </div>

                <div class="em-search-engine-data">
                    <table>
                        <thead>
                        <tr>
                            <td><h3>RESULTAT DE LA RECHERCHE</h3></td>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr class="fabrik___heading">
                            <td colspan="<?php echo count($this->headings);?>">
								<?php echo $this->nav;?>
                            </td>
                        </tr>
                        </tfoot>

                        <tbody>

						<?php
						$region=""; $department=""; $chercheur=""; $cherches=""; $themes="";
						$gCounter = 0;
						//var_dump($data);die;
						foreach ($data as $d) {
							$region 	= $d['data_regions___name'];
							$department = $d['data_departements___departement_nom'];
							$chercheur 	= strtolower($d['jos_emundus_setup_profiles___label']);

							$cherches = [];
							if ($d['jos_emundus_recherche___futur_doctorant_yesno'] == 'oui')
								$cherches[] = $this->headings['jos_emundus_recherche___futur_doctorant_yesno'];
							if ($d['jos_emundus_recherche___acteur_public_yesno'] == 'oui')
								$cherches[] = $this->headings['jos_emundus_recherche___acteur_public_yesno'];
							if ($d['jos_emundus_recherche___equipe_de_recherche_direction_yesno'] == 'oui')
								$cherches[] = $this->headings['jos_emundus_recherche___equipe_de_recherche_direction_yesno'];
							if ($d['jos_emundus_recherche___equipe_de_recherche_codirection_yesno'] == 'oui')
								$cherches[] = $this->headings['jos_emundus_recherche___equipe_de_recherche_codirection_yesno'];
							$cherches = strtolower(implode('</b> et <b>', $cherches));

							$themes = $d['data_thematics___thematic'];
							?>
                            <tr>
                                <td>
                                    <div class="em-search-engine-div-data">
                                        <div><?php if (!empty(strip_tags($region))) :?>En région <i><b><?php echo $region; ?></b></i>,<?php endif; if (!empty(strip_tags($department))) :?> <?php if (empty(strip_tags($region))) :?> Dans <?php else :?> dans <?php endif; ?> le(s) département(s) <i><b><?php echo $department; ?></b></i>, <?php endif; if (empty(strip_tags($region)) && empty(strip_tags($department))) :?> Un <?php else :?> un <?php endif; ?><i><b><?php echo $chercheur; ?></b></i> cherche <i><b><?php echo $cherches; ?></b></i><?php if (!empty(strip_tags($themes))) :?> sur le(s) thème(s) <i><b><?php echo $themes; ?></b></i><?php endif; ?></div>
                                        <hr>
                                        <ul>
                                            <li>id : <?php echo strip_tags($d['jos_emundus_projet___id']); ?></li>
                                            <li>date : <?php echo $d['jos_emundus_campaign_candidature___date_submitted']; ?></li>
                                            <li>déposé part : <?php echo $d['jos_emundus_setup_profiles___label']; ?></li>
                                            <li>thematiques : <?php echo $d['data_thematics___thematic']; ?></li>
                                            <li>région : <?php echo $d['data_regions___name']; ?></li>
                                            <li>département : <?php echo $d['data_departements___departement_nom']; ?></li>
                                            <li>recherche futur doctorant : <?php echo $d['jos_emundus_recherche___futur_doctorant_yesno']; ?></li>
                                            <li>recherche acteur publique : <?php echo $d['jos_emundus_recherche___acteur_public_yesno']; ?></li>
                                            <li>recherche équipe de recherche direction : <?php echo $d['jos_emundus_recherche___equipe_de_recherche_codirection_yesno']; ?></li>
                                            <li>recherche équipe de recherche codirection : <?php echo $d['jos_emundus_recherche___equipe_de_recherche_direction_yesno']; ?></li>
                                        </ul>
                                        <hr>
                                        <?php if (JFactory::getUser()->guest) :?>
                                            <div class="em-search-engine-learn-more"><a href="<?php echo 'index.php?option=com_users&view=login&return=' . base64_encode(JFactory::getURI())?>"> Connectez-vous pour en savoir plus </a></div>
                                        <?php else :?>
                                            <div class='em-search-engine-details'><a href="<?php echo $d['fabrik_view_url']; ?>">Consultez l'offre</a></div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php
                            unset($cherches);
                            unset($themes);
                            $gCounter++;
                        }

						?>
                        </tbody>
						<?php if ($this->hasCalculations) : ?>
                            <tfoot>
                            <tr class="fabrik_calculations">

								<?php foreach ($this->headings as $key => $heading) :
									$h = $this->headingClass[$key];
									$style = empty($h['style']) ? '' : 'style="' . $h['style'] . '"'; ?>
                                    <td class="<?php echo $h['class']?>" <?php echo $style?>>
										<?php
										$cal = $this->calculations[$key];
										echo array_key_exists($groupedBy, $cal->grouped) ? $cal->grouped[$groupedBy] : $cal->calc;
										?>
                                    </td>
								<?php
								endforeach;
								?>

                            </tr>
                            </tfoot>
						<?php endif ?>
                    </table>
                </div>

				<?php print_r($this->hiddenFields);?>
            </div>
        </form>
    </div>

    <script>
        jQuery(document).ready(function(){
            jQuery('select.fabrik_filter[multiple]').chosen({
                placeholder_text_single: "<?php echo JText::_('CHOSEN_SELECT_ONE'); ?>",
                placeholder_text_multiple: "<?php echo JText::_('CHOSEN_SELECT_MANY'); ?>",
                no_results_text: "<?php echo JText::_('CHOSEN_NO_RESULTS'); ?>"
            });
        });
    </script>

</div>