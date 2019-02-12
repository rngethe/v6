<?php
/**
 * Bootstrap Details Template
 *
 * @package     Joomla
 * @subpackage  Fabrik
 * @copyright   Copyright (C) 2005-2016  Media A-Team, Inc. - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @since       3.1
 */
// PDF EXPORT
// No direct access
defined('_JEXEC') or die('Restricted access');

$form = $this->form;
$model = $this->getModel();

$lang = JFactory::getLanguage();
$extension = 'com_emundus';
$base_dir = JPATH_SITE . '/components/com_emundus';
$language_tag =& JFactory::getLanguage()->getTag();
$reload = true;
$lang->load($extension, $base_dir, $language_tag, $reload);

require_once(JPATH_BASE . DS . 'components' . DS . 'com_emundus' . DS . 'models' . DS . 'users.php');
$m_users = new EmundusModelUsers();

require_once(JPATH_BASE . DS . 'components' . DS . 'com_emundus' . DS . 'models' . DS . 'cifre.php');
$m_cifre = new EmundusModelCifre();


if ($this->params->get('show_page_heading', 1)) : ?>
    <div class="componentheading<?php echo $this->params->get('pageclass_sfx') ?>">
        <?php echo $this->escape($this->params->get('page_heading')); ?>
    </div>
<?php
endif;
?>

<?php
echo $form->intro;
if ($this->isMambot) :
    echo '<div class="fabrikForm fabrikDetails fabrikIsMambot" id="' . $form->formid . '">';
else :
    echo '<div class="fabrikForm fabrikDetails" id="' . $form->formid . '">';
endif;
echo $this->plugintop;
echo $this->loadTemplate('buttons');
echo $this->loadTemplate('relateddata');

$regions = $this->data['data_regions___name_raw'];

$db = JFactory::getDBO();

$query = $db->getquery('true');
$user_to = $m_users->getUserById($this->data["jos_emundus_campaign_candidature___applicant_id_raw"][0]);

// Get all uploaded files
$query->select($db->quoteName(array('eup.filename', 'sa.value')))
    ->from($db->quoteName('#__emundus_uploads', 'eup'))
    ->join('LEFT', $db->quoteName('#__emundus_setup_attachments', 'sa') . ' ON (' . $db->quoteName('sa.id') . ' = ' . $db->quoteName('eup.attachment_id') . ')')
    ->where($db->quoteName('fnum') . ' LIKE "' . $this->data['jos_emundus_recherche___fnum_raw'] . '" AND eup.can_be_viewed = 1');

$db->setQuery($query);

try {
    $files = $db->loadAssocList();
    $query->clear();
} catch (Exception $e) {
    echo "<pre>";
    var_dump($query->__toString());
    echo "</pre>";
    die();
}



function getDepartment($dept) {
    $db = JFactory::getDBO();

    $query = $db->getquery('true');

    $query->select($db->quoteName('departement_nom'))
        ->from($db->quoteName('data_departements'))
        ->where($db->quoteName('departement_id') . ' = ' . $dept);

    $db->setQuery($query);
    try {
        return $db->loadResult();
    } catch (Exception $e) {
        echo "<pre>";
        var_dump($query->__toString());
        echo "</pre>";
        die();
    }
}
?>


<div class="em-pdf-group">
    <img src="images/custom/Hesam/Logo_1000doctorants.JPG" alt="Logo 1000doctorants" style="vertical-align: top;"
         width="252" height="90">
    <div class="em-pdf-title-div">
        <h3>Récapitulatif de l'annonce déposé sur <a href="<?php echo JURI::root(); ?>"><?php echo JURI::root(); ?></a></h3>
    </div>

    <div class="em-pdf-element">

        <div class="em-pdf-element-label">
            <p>Numéro de dossier</p>
        </div>

        <div class="em-pdf-element-value">
            <p><?php echo $this->data["jos_emundus_campaign_candidature___fnum_raw"][0]; ?></p>
        </div>

    </div>

    <div class="em-pdf-element">

        <div class="em-pdf-element-label">
            <p>Date de dépôt du dossier</p>
        </div>

        <div class="em-pdf-element-value">
            <p><?php echo date("d/m/Y", strtotime($this->data["jos_emundus_campaign_candidature___date_submitted_raw"][0])); ?></p>
        </div>

    </div>
</div>


<div class="em-pdf-group">
    <div class="em-pdf-title-div">
        <h3>Auteur de l'annonce</h3>
    </div>

    <div class="em-pdf-element">

        <div class="em-pdf-element-label">
            <p>Type</p>
        </div>

        <div class="em-pdf-element-value">
            <p><?php echo $this->data["jos_emundus_setup_profiles___label_raw"][0]; ?></p>
        </div>

    </div>


    <?php if ($this->data["jos_emundus_setup_profiles___id_raw"][0] != '1008') : ?>
        <div class="em-pdf-element">

            <div class="em-pdf-element-label">
                <p>Civlité</p>
            </div>

            <div class="em-pdf-element-value">
                <p><?php echo ($user_to[0]->gender == "M") ? "Monsieur" : "Madame"; ?></p>
            </div>

        </div>

        <div class="em-pdf-element">

            <div class="em-pdf-element-label">
                <p>Nom</p>
            </div>

            <div class="em-pdf-element-value">
                <p><?php echo ucfirst($user_to[0]->lastname); ?></p>
            </div>

        </div>

        <div class="em-pdf-element">

            <div class="em-pdf-element-label">
                <p>Prénom</p>
            </div>

            <div class="em-pdf-element-value">
                <p><?php echo ucfirst($user_to[0]->firstname); ?></p>
            </div>

        </div>


    <?php else: ?>

        <?php $institution = $m_cifre->getUserInstitution($user_to[0]->user_id);?>
        <div class="em-pdf-element">

            <div class="em-pdf-element-label">
                <p>Nom</p>
            </div>

            <div class="em-pdf-element-value">
                <p><?php echo $institution->nom_de_structure; ?></p>
            </div>

        </div>
    <?php endif; ?>

    <div class="em-pdf-element">

        <div class="em-pdf-element-label">
            <p>Email</p>
        </div>

        <div class="em-pdf-element-value">
            <p><a href="mailto:<?php echo $user_to[0]->email; ?>"><?php echo $user_to[0]->email; ?></a></p>
        </div>

    </div>

    <?php if (!empty($this->data['jos_emundus_projet___contact_tel_raw'][0])) : ?>
        <div class="em-pdf-element">

            <div class="em-pdf-element-label">
                <p>Tél</p>
            </div>
            <div class="em-pdf-element-value">
                <p><?php echo $this->data['jos_emundus_projet___contact_tel_raw'][0]; ?></p>
            </div>

        </div>
    <?php endif; ?>

    <?php if ($this->data["jos_emundus_setup_profiles___id_raw"][0] == '1007') : ?>

        <?php $laboratoire = $m_cifre->getUserLaboratory($author->id);
            if (!empty($laboratoire)) :?>
                <div class="em-pdf-element">

                    <div class="em-pdf-element-label">
                        <p>Nom de l'unité de recherche</p>
                    </div>

                    <div class="em-pdf-element-value">
                        <p><?php echo $laboratoire->name; ?></p>
                    </div>

                </div>
        <?php endif; ?>

        <?php $ecoleDoctorale = $m_cifre->getDoctorale($laboratoire->id); ?>

        <?php if (!empty($ecoleDoctorale)) :?>
            <div class="em-pdf-element">

                <div class="em-pdf-element-label">
                    <p>École doctorale</p>
                </div>

                <div class="em-pdf-element-value">
                    <p><?php echo $ecoleDoctorale; ?></p>
                </div>

            </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if (!empty($this->data['jos_emundus_projet___limit_date_raw'][0])) : ?>
        <div class="em-pdf-element">

            <div class="em-pdf-element-label">
                <p>Date de disponibilité</p>
            </div>

            <div class="em-pdf-element-value">
                <p><?php echo $this->data["jos_emundus_projet___limit_date_raw"][0]; ?></p>
            </div>

        </div>
    <?php endif; ?>

</div>

<div class="em-pdf-group breaker">
    <div class="em-pdf-title-div">
        <h3>Le projet</h3>
    </div>

    <div class="em-pdf-element">

        <div class="em-pdf-element-label">
            <p>Titre</p>
        </div>

        <div class="em-pdf-element-value">
            <p><?php echo $this->data["jos_emundus_projet___titre_raw"][0]; ?></p>
        </div>

    </div>

    <?php if ($this->data["jos_emundus_setup_profiles___id_raw"][0] != '1008') :?>
        <?php if (!empty($this->data['jos_emundus_projet___contexte_raw'][0])) :?>
            <div class="em-pdf-element">

                <div class="em-pdf-element-label">
                    <p>Enjeu et actualité du sujet</p>
                </div>

                <div class="em-pdf-element-value">
                    <p><?php echo $this->data["jos_emundus_projet___contexte_raw"][0]; ?></p>
                </div>

            </div>
        <?php endif; ?>
    <?php else :?>

        <?php if (!empty($this->data['jos_emundus_projet___contexte_raw'][0])) :?>
            <div class="em-pdf-element">

                <div class="em-pdf-element-label">
                    <p>Territoire</p>
                </div>

                <div class="em-pdf-element-value">
                    <p><?php echo $this->data["jos_emundus_projet___contexte_raw"][0]; ?></p>
                </div>

            </div>
        <?php endif; ?>
    <?php endif; ?>


    <?php if (!empty($this->data['jos_emundus_projet___question_raw'][0])) :?>
        <?php
            if ($this->data["jos_emundus_setup_profiles___id_raw"][0] == '1006')
                $questionText = 'Problématique :';
            elseif ($this->data["jos_emundus_setup_profiles___id_raw"][0] == '1007')
                $questionText = 'Problématique :';
            elseif ($this->data["jos_emundus_setup_profiles___id_raw"][0] == '1008')
                $questionText = 'Grand défi :';
        ?>
        <div class="em-pdf-element">

            <div class="em-pdf-element-label">
                <p><?php echo $questionText; ?></p>
            </div>

            <div class="em-pdf-element-value">
                <p><?php echo $this->data["jos_emundus_projet___question_raw"][0]; ?></p>
            </div>

        </div>
    <?php endif; ?>


    <?php if (!empty($this->data['jos_emundus_projet___methodologie_raw'][0])) : ?>
        <div class="em-pdf-element">

            <div class="em-pdf-element-label">
                <p>Méthodologie proposée</p>
            </div>

            <div class="em-pdf-element-value">
                <p><?php echo $this->data["jos_emundus_projet___methodologie_raw"][0]; ?></p>
            </div>

        </div>

    <?php endif; ?>



    <?php if (!empty($this->data['data_thematics___thematic_raw'][0])) : ?>

        <div class="em-pdf-element">

            <div class="em-pdf-element-label">
                <p>Thématiques associées</p>
            </div>

            <div class="em-pdf-element-value">
                <p><?php echo implode(", ", $this->data["data_thematics___thematic_raw"]); ?></p>
            </div>

        </div>

    <?php endif; ?>

    <?php if (!empty($this->data["em_discipline___disciplines_raw"])) : ?>
        <div class="em-pdf-element">

            <div class="em-pdf-element-label">
                <p>Disciplines sollicitées</p>
            </div>

            <div class="em-pdf-element-value">
                <p><?php echo is_array($this->data["em_discipline___disciplines_raw"]) ? implode(', ', $this->data["em_discipline___disciplines_raw"]) : $this->data["em_discipline___disciplines_raw"]; ?></p>
            </div>

        </div>
    <?php endif; ?>


    <div class="em-pdf-element">

        <div class="em-pdf-element-label">
            <p>Régions</p>
        </div>

        <div class="em-pdf-element-value">
            <p><?php echo !empty($regions) ? implode(', ', $regions) : JText::_('COM_EMUNDUS_FABRIK_NO_REGIONS'); ?></p>
        </div>

    </div>

    <div class="em-pdf-element">

        <div class="em-pdf-element-label">
            <p>Départements</p>
        </div>

        <div class="em-pdf-element-value">
            <p><?php
                if (!empty($this->data["jos_emundus_recherche_630_repeat_repeat_department___department"])) {
                $departmentArray = array();
                foreach ($this->data["jos_emundus_recherche_630_repeat_repeat_department___department"] as $dep) {
                    $departmentArray[] = getDepartment($dep);
                }
                echo implode(', ', $departmentArray);
                }
                else {
                    echo JText::_('COM_EMUNDUS_FABRIK_NO_DEPARTMENTS');
                }
                ?>
            </p>
        </div>

    </div>

</div>

<div class="em-pdf-group breaker">

    <div class="em-pdf-title-div">
        <h3>Les partenaires recherchés</h3>
    </div>

    <?php if ($this->data["jos_emundus_setup_profiles___id_raw"][0] != '1006') : ?>

        <div class="em-pdf-element">

            <div class="em-pdf-element-label">
                <p>Un futur doctorant</p>
            </div>

            <div class="em-pdf-element-value">
                <p><?php echo $this->data["jos_emundus_recherche___futur_doctorant_yesno"]; ?></p>
            </div>

        </div>


        <?php if ($this->data["jos_emundus_recherche___futur_doctorant_yesno"] == 0 && !empty($this->data["jos_emundus_recherche___futur_doctorant_nom_raw"]) && !empty($this->data["jos_emundus_recherche___futur_doctorant_prenom_raw"])) : ?>
            <div class="em-pdf-element">

                <div class="em-pdf-element-label">
                    <p>Nom et prénom du futur doctorant</p>
                </div>

                <div class="em-pdf-element-value">
                    <p><?php echo strtoupper($this->data["jos_emundus_recherche___futur_doctorant_nom"]) . " " . $this->data["jos_emundus_recherche___futur_doctorant_prenom"]; ?></p>
                </div>

            </div>
        <?php endif; ?>


    <?php endif; ?>

    <?php if ($this->data["jos_emundus_setup_profiles___id_raw"][0] != '1007') : ?>
        <div class="em-pdf-element">

            <div class="em-pdf-element-label">
                <p>Une équipe de recherche pour codirection</p>
            </div>

            <div class="em-pdf-element-value">
                <p><?php echo $this->data["jos_emundus_recherche___equipe_de_recherche_codirection_yesno"]; ?></p>
            </div>

        </div>

        <?php if ($this->data["jos_emundus_recherche___equipe_de_recherche_codirection_yesno_raw"] == 0 && !empty($this->data["jos_emundus_recherche___equipe_codirection_nom_du_laboratoire_raw"])) : ?>
            <div class="em-pdf-element">

                <div class="em-pdf-element-label">
                    <p>Nom de l'équipe partenaire</p>
                </div>

                <div class="em-pdf-element-value">
                    <p><?php echo $this->data["jos_emundus_recherche___equipe_codirection_nom_du_laboratoire_raw"]; ?></p>
                </div>

            </div>
        <?php endif; ?>

    <?php else : ?>
        <div class="em-pdf-element">

            <div class="em-pdf-element-label">
                <p>Une équipe de recherche pour direction</p>
            </div>

            <div class="em-pdf-element-value">
                <p><?php echo $this->data["jos_emundus_recherche___equipe_de_recherche_direction_yesno"]; ?></p>
            </div>

        </div>

        <?php if ($this->data["jos_emundus_recherche___equipe_de_recherche_direction_yesno"] == 0 && !empty($this->data["jos_emundus_recherche___equipe_direction_nom_du_laboratoire_raw"])) : ?>
            <div class="em-pdf-element">

                <div class="em-pdf-element-label">
                    <p>Nom de l'équipe partenaire</p>
                </div>

                <div class="em-pdf-element-value">
                    <p><?php echo $this->data["jos_emundus_recherche___equipe_direction_nom_du_laboratoire_raw"]; ?></p>
                </div>

            </div>
        <?php endif; ?>

    <?php endif; ?>



    <?php if ($this->data["jos_emundus_setup_profiles___id_raw"][0] != '1008') : ?>
        <div class="em-pdf-element">

            <div class="em-pdf-element-label">
                <p>Un acteur public ou associatif</p>
            </div>

            <div class="em-pdf-element-value">
                <p><?php echo $this->data["jos_emundus_recherche___acteur_public_yesno"]; ?></p>
            </div>

        </div>

        <?php if ($this->data["jos_emundus_recherche___acteur_public_yesno_raw"] == 0) : ?>

            <?php if (!empty($this->data["jos_emundus_recherche___acteur_public_type_raw"])) : ?>
                <div class="em-pdf-element">

                    <div class="em-pdf-element-label">
                        <p>Type</p>
                    </div>

                    <div class="em-pdf-element-value">
                        <p><?php echo $this->data["jos_emundus_recherche___acteur_public_type_raw"]; ?></p>
                    </div>

                </div>
            <?php endif; ?>

            <?php if (!empty($this->data["jos_emundus_recherche___acteur_public_nom_de_structure_raw"])) : ?>

                <div class="em-pdf-element">

                    <div class="em-pdf-element-label">
                        <p>Nom du partenaire</p>
                    </div>

                    <div class="em-pdf-element-value">
                        <p><?php echo $this->data["jos_emundus_recherche___acteur_public_nom_de_structure_raw"]; ?></p>
                    </div>

                </div>
            <?php endif; ?>

        <?php endif; ?>

    <?php endif; ?>
</div>

<div class="em-pdf-group breaker">
    <?php if (!empty($files)) : ?>
        <div class="em-pdf-title-div">
            <h3>Pièces jointes à l'annonce</h3>
        </div>

        <?php foreach ($files as $file) : ?>
            <div class="em-pdf-element">

                <div class="em-pdf-element-label">
                    <p><?php echo $file["value"]; ?></p>
                </div>

                <div class="em-pdf-element-value">
                    <p>
                        <a target="_blank" href="<?php echo JURI::root().'images'.DS.'emundus'.DS.'files'.DS.$this->data["jos_emundus_campaign_candidature___applicant_id_raw"][0].DS.$this->data["jos_emundus_cifre_links___document_raw"]; ?>"><?php echo $file["filename"]; ?></a>
                    </p>
                </div>

            </div>
        <?php endforeach; ?>

    <?php endif; ?>

</div>
