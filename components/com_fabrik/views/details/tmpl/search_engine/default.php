<?php
/**
 * Form details template used for the HESAM search engine pages.
 *
 * @package     Joomla
 * @subpackage  Fabrik
 * @copyright   Copyright (C) 2018  eMundus - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @since       3.1
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

// If we are not logged in: we cannot access this page and so we are redirected to the login page.
$user = JFactory::getUser();
if ($user->guest) {
	JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode(JFactory::getURI())), JText::_('JGLOBAL_YOU_MUST_LOGIN_FIRST'), 'warning');
	return;
}
$user = JFactory::getSession()->get('emundusUser');

// This is currently the only way of getting the fnum.
$fnum = $this->data["jos_emundus_recherche___fnum_raw"];
$author = JFactory::getUser((int)substr($fnum,-7));

require_once (JPATH_BASE.DS.'components'.DS.'com_emundus'.DS.'models'.DS.'files.php');
$m_files = new EmundusModelFiles();
$fnumInfos = $m_files->getFnumInfos($fnum);

$form = $this->form;
$model = $this->getModel();
$groupTmpl = $model->editable ? 'group' : 'group_details';
$active = ($form->error != '') ? '' : ' fabrikHide';

if ($this->params->get('show_page_heading', 1)) : ?>
	<div class="componentheading<?php echo $this->params->get('pageclass_sfx')?>">
	    <?php echo $this->escape($this->params->get('page_heading')); ?>
	</div>
<?php endif;


echo $this->plugintop;
echo $this->loadTemplate('buttons');
echo $this->loadTemplate('relateddata');


$region=""; $department=""; $chercheur=""; $cherches=""; $themes="";
$regions 	= $this->data['data_regions___name_raw'];
$departments= $this->data['data_departements___departement_nom_raw'];
$chercheur 	= strtolower($this->data['jos_emundus_setup_profiles___label_raw'][0]);
$profile    = $this->data['jos_emundus_setup_profiles___id_raw'][0];


?>
<!-- Title -->
<p class="em-offre-title">
    <?php echo $this->data['jos_emundus_projet___titre_raw'][0]; ?>
</p>

<div class="em-offre-meta">
    <p>Sujet déposé le <strong class="em-highlight"><?php echo date('d/m/Y', strtotime($fnumInfos['date_submitted'])); ?></strong></p>
    <p>Par un <strong class="em-highlight"><?php echo $chercheur; ?></strong></p>
</div>

<!-- Author -->
<!-- TODO: Add more information, not just the author's name but also something more like 'la communauté de communes de :' -->
<div class="em-offre-author">
    <h1 class="em-offre-title"> Le déposant </h1>
    <div class="em-offre-subtitle">Profil du déposant</div>
    <div class="em-offre-author">
        <strong>Nom : </strong><div class="em-offre-author-name"><?php echo $author->name; ?></div>
    </div>

    <?php
    // We need to change up the page based on if the person is viewing an offer from a lab, a future PHd, or a municiplaity.
    //// Profile 1006 : Futur doctorant = display no special information.
    //// Profile 1007 : Researcher = display information about his lab.
    //// Profile 1008 : Municipality = display information about the organization.
    if ($profile == '1007') :?>
        <?php
        require_once (JPATH_BASE.DS.'components'.DS.'com_emundus'.DS.'models'.DS.'cifre.php');
        $m_cifre = new EmundusModelCifre();
        $laboratoire = $m_cifre->getUserLaboratory($author->id);
        ?>
        <div class="em-offre-inst">
            <strong>Laboratoire :</strong>
            <div class="em-offre-institution">
                <?php
                if (!empty($laboratoire->website)) {
                    $parse = parse_url($laboratoire->website, PHP_URL_SCHEME) === null ? 'http://' . $laboratoire->website: $laboratoire->website;
                    echo '<a target="_blank " href="'.$parse.'">';
                }

                echo $laboratoire->name;
                if (!empty($laboratoire->website))
                    echo '</a>';
                ?>
            </div>
        </div>
        <a class="btn btn-default" href="/index.php?option=com_fabrik&task=details.view&formid=308&listid=318&rowid=<?php echo $laboratoire->id; ?>">Cliquez ici pour plus d'information</a>
    <?php elseif ($profile == '1008') :?>
        <?php
        require_once (JPATH_BASE.DS.'components'.DS.'com_emundus'.DS.'models'.DS.'cifre.php');
        $m_cifre = new EmundusModelCifre();
        $institution = $m_cifre->getUserInstitution($author->id);
        ?>
        <div class="em-offre-inst">
            <strong>Structure :</strong>
            <div class="em-offre-institution">
                <?php
                if (!empty($institution->website)) {
                    $parse = parse_url($institution->website, PHP_URL_SCHEME) === null ? 'http://' . $institution->website: $institution->website;
                    echo '<a target="_blank " href="'.$parse.'">';
                }
                echo $institution->nom_de_structure;
                if (!empty($institution->website))
                    echo '</a>';
                ?>
            </div>
        </div>
        <a class="btn btn-default" href="/index.php?option=com_fabrik&task=details.view&formid=307&listid=317&rowid=<?php echo $institution->id; ?>">Cliquez ici pour plus d'information</a>
    <?php endif; ?>
</div>

<div class="em-offre">
    <h1 class="em-offre-title"> Le Sujet </h1>

    <!-- THEMES -->
    <div class="em-offre-themes">
        <div class="em-offre-subtitle">Thématiques identifiées :</div>
        <strong class="em-highlight"> <?php echo is_array($this->data['data_thematics___thematic_raw'])?implode('</strong>; <strong class="em-highlight">', $this->data['data_thematics___thematic_raw']):$this->data['data_thematics___thematic_raw']; ?></strong>
    </div>

	<?php if ($profile == '1006') :?>
        <!-- Project context -->
        <p class="em-offre-contexte">
            <div class="em-offre-subtitle">Contexte : </div><?php echo $this->data['jos_emundus_projet___contexte_raw'][0]; ?>
        </p>
    <?php endif; ?>

    <!-- Project question -->
    <?php
        if ($profile == '1006')
            $questionText = 'Grande question posée :';
        elseif ($profile == '1007')
            $questionText = 'Problématique de recherche :';
        elseif ($profile == '1008')
            $questionText = 'Grand défi :';
    ?>
    <p class="em-offre-question">
        <div class="em-offre-subtitle"><?php echo $questionText; ?></div><?php echo $this->data['jos_emundus_projet___question_raw'][0]; ?>
    </p>

	<?php if ($profile == '1006') :?>
        <!-- Project methodology -->
        <p class="em-offre-methodologie">
            <div class="em-offre-subtitle">Méthodologie proposée : </div><?php echo $this->data['jos_emundus_projet___methodologie_raw'][0]; ?>
        </p>
	<?php endif; ?>

</div>

<!-- Contact information -->
<div class="em-offre-contact">
    <h1 class="em-contact-title"> Contact </h1>
    <p class="em-contact-item"><strong>Nom : </strong><?php echo !empty($this->data['jos_emundus_projet___contact_nom_raw'][0])?$this->data['jos_emundus_projet___contact_nom_raw'][0]:'Aucun nom renseigné'; ?></p>
    <p class="em-contact-item"><strong>Mail : </strong><?php echo !empty($this->data['jos_emundus_projet___contact_mail_raw'][0])?$this->data['jos_emundus_projet___contact_mail_raw'][0]:'Aucun mail renseingé'; ?></p>
    <p class="em-contact-item"><strong>Tel : </strong><?php echo !empty($this->data['jos_emundus_projet___contact_tel_raw'][0])?$this->data['jos_emundus_projet___contact_tel_raw'][0]:'Aucun numéro renseigné'; ?></p>

    <?php
    // Log the action of opening the persons form.
    require_once (JPATH_BASE.DS.'components'.DS.'com_emundus'.DS.'models'.DS.'logs.php');
    EmundusModelLogs::log($user->id, $author->id, $fnum, 33, 'r', 'COM_EMUNDUS_LOGS_OPEN_OFFER');

    // Action button types:
    // // NO BUTTON : if the offer belongs to the user.
    // // ENTREZ EN CONTACT : If the user has not already contacted.
    // // REPONDRE : If the user has already been contacted for this offer but has not answered.
    // // RELANCE : If the user has contacted but not been answered yet.
    // // BREAK UP : If the user is collaborating with the other.
    require_once (JPATH_BASE.DS.'components'.DS.'com_emundus'.DS.'controllers'.DS.'cifre.php');
    $c_ciffe = new EmundusControllerCifre();
    $action_button = $c_ciffe->getActionButton($fnum);
    ?>

    <!-- Button used for matching with the offer -->
    <div class="em-search-item-action">

        <span class="alert alert-danger hidden" id="em-action-text"></span>

        <div id="em-search-item-action-button">

        <?php if ($action_button == 'contact') :?>

            <?php $offers = $c_ciffe->getOwnOffers($fnum); ?>

            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#contactModal">
                Entrer en contact
            </button>

            <div class="modal fade" id="contactModal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Demande de contact</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>Veuillez confirmer que vous souhaitez contacter le créateur de cette offre.</p>
                            <?php if (!empty($offers)) :?>
                                <p>Si vous le souhaitez: vous pouvez joindre une de vos offres.</p>
                                <select id="em-join-offer">
                                    <option value="">Je ne souhaite pas joindre mes offres</option>
                                    <?php foreach ($offers as $offer) :?>
                                        <option value="<?php echo $offer->fnum; ?>"><?php echo $offer->titre; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>
                            <textarea id="em-contact-message" placeholder="Ajouter un méssage (facultatif)"></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="actionButton('contact')">Envoyer la demande de contact</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                        </div>
                    </div>
                </div>
            </div>

        <?php elseif ($action_button == 'reply') :?>
            <button type="button" class="btn btn-primary" onclick="actionButton('reply')">
                Répondre
            </button>
            <button type="button" class="btn btn-primary" onclick="breakUp('ignore')">
                Ignorer
            </button>

        <?php elseif ($action_button == 'retry') :?>
            <button type="button" class="btn btn-primary" onclick="actionButton('retry')">
                Relancer
            </button>
            <button type="button" class="btn btn-primary" onclick="breakUp('cancel')">
                Annuler la demande
            </button>

        <?php elseif ($action_button == 'breakup') :?>
            <button type="button" class="btn btn-primary" onclick="breakUp('breakup')">
                Couper contact
            </button>
        <?php endif; ?>

        </div>

    </div>
</div>

<script>

    function actionButton(action) {

        var fnum = '<?php echo $fnum; ?>';
        var data = {
            fnum : fnum
        };

        if (action == 'contact') {
            if (document.getElementById('em-join-offer') != null) {
                // Get the offer selected from the dropdown by the user.
                var linkedOffer = document.getElementById('em-join-offer').value;
                if (linkedOffer != null && linkedOffer != '' && typeof linkedOffer != 'undefined')
                    data.linkedOffer = linkedOffer;

            }

            // Get the attached message if there is one.
            var message = document.getElementById('em-contact-message').value;
            if (message != null && message != '' && typeof message != 'undefined')
                data.message = message;

        }

        jQuery.ajax({
            type: 'POST',
            dataType: 'json',
            url: 'index.php?option=com_emundus&controller=cifre&task='+action,
            data: data,
            beforeSend: function () {
                jQuery('#em-search-item-action-button').html('<button type="button" class="btn btn-default" disabled> ... </button>');

                if (action == 'contact') {
                    jQuery('#contactModal').modal('hide');
                    jQuery('body').removeClass('modal-open');
                    jQuery('.modal-backdrop').remove();
                }


            },
            success: function(result) {
                if (result.status) {

                    // When we successfully change the status, we simply dynamically change the button.

                    if (action == 'contact')
                        jQuery('#em-search-item-action-button').html('<button type="button" class="btn btn-primary" onclick="actionButton(\'retry\')">Relancer</button><button type="button" class="btn btn-primary" onclick="breakUp(\'cancel\')">Annuler la demande</button>');
                    else if (action == 'retry')
                        jQuery('#em-search-item-action-button').html('<button type="button" class="btn btn-default" disabled > Message envoyé </button>');
                    else if (action == 'reply')
                        jQuery('#em-search-item-action-button').html('<button type="button" class="btn btn-danger" onclick="breakUp()"> Couper contact </button>');

                } else {
                    var actionText = document.getElementById('em-action-text');
                    actionText.classList.remove('hidden');
                    actionText.innerHTML = result.msg;
                }
            },
            error: function(jqXHR) {
                console.log(jqXHR.responseText);
            }
        });
    }

    function breakUp(action) {
        var data = {
            fnum : '<?php echo $fnum; ?>'
        };

        jQuery.ajax({
            type: 'POST',
            dataType: 'json',
            url: 'index.php?option=com_emundus&controller=cifre&task=breakup&action='+action,
            data: data,
            beforeSend: function () {
                jQuery('#em-search-item-action-button').html('<button type="button" class="btn btn-default" disabled> ... </button>');
            },
            success: function(result) {
                if (result.status) {

                    // Dynamically change the button back to the state of not having a link.
                    jQuery('#em-search-item-action-button').html('' +
                        '<button type="button" class="btn btn-success" data-toggle="modal" data-target="#contactModal">' +
                        '        Entrer en contact' +
                        '        </button>' +
                        '        <div class="modal fade" id="contactModal" tabindex="-1" role="dialog">' +
                        '            <div class="modal-dialog" role="document">' +
                        '                <div class="modal-content">' +
                        '                    <div class="modal-header">' +
                        '                        <h5 class="modal-title">Demande de contact</h5>' +
                        '                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">' +
                        '                            <span aria-hidden="true">&times;</span>' +
                        '                        </button>' +
                        '                    </div>' +
                        '                    <div class="modal-body">' +
                        '                        <p>Veuillez confirmer que vous souhaitez contacter le créateur de cette offre.</p>' +
		                                            <?php if (!empty($offers)) :?>
                        '                            <p>Si vous le souhaitez: vous pouvez joindre une de vos offres.</p>' +
                        '                            <select id="em-join-offer">' +
		                                                <?php foreach ($offers as $offer) :?>
                        '                                    <option value="<?php echo $offer->fnum; ?>"><?php echo $offer->titre; ?></option>' +
		                                                <?php endforeach; ?>
                        '                            </select>' +
		                                            <?php endif; ?>
                        '                       <textarea class="em-contact-message" placeholder="Ajouter un méssage (facultatif)"></textarea>'+
                        '                    </div>' +
                        '                    <div class="modal-footer">' +
                        '                        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="actionButton(\'contact\')">Evoyer la demande de contact</button>' +
                        '                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>' +
                        '                    </div>' +
                        '                </div>' +
                        '            </div>');

                } else {
                    var actionText = document.getElementById('em-action-text');
                    actionText.classList.remove('hidden');
                    actionText.innerHTML = result.msg;
                }
            },
            error: function(jqXHR) {
                console.log(jqXHR.responseText);
            }
        });
    }

</script>

<?php
echo $this->pluginbottom;
echo $this->loadTemplate('actions');
echo '</div>';
echo $form->outro;
echo $this->pluginend;
