<?php
/**
 * Bootstrap List Template - Default
 *
 * @package     Joomla
 * @subpackage  Fabrik
 * @copyright   Copyright (C) 2005-2016  Media A-Team, Inc. - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @since       3.1
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

$pageClass = $this->params->get('pageclass_sfx', '');

if ($pageClass !== '') :
    echo '<div class="' . $pageClass . '">';
endif;

if ($this->tablePicker != '') : ?>
    <div style="text-align:right"><?= FText::_('COM_FABRIK_LIST') ?>: <?= $this->tablePicker; ?></div>
<?php
endif;

if ($this->params->get('show_page_heading')) :
    echo '<h1>' . $this->params->get('page_heading') . '</h1>';
endif;




// Intro outside of form to allow for other lists/forms to be injected.
echo $this->table->intro;
?>
<form class="fabrikForm form-search" action="<?= $this->table->action; ?>" method="post" id="<?= $this->formid; ?>" name="fabrikList">


    <div class="fabrikDataContainer">

        <?php foreach ($this->pluginBeforeList as $c) :
            echo $c;

        endforeach;

        $data = array();
        $i = 0;
        $rows = $this->rows[0];
        if (!empty($rows)) {
            foreach ($rows as $k => $v) {
                foreach ($this->headings as $key => $val) {
                    $raw = $key.'_raw';
                    if (property_exists($v->data, $raw)) {
                        if ($raw =="jos_emundus_users___birthday_raw") {
                            $v->data->$raw = !empty($v->data->$raw) ? date('d/m/Y', strtotime($v->data->$raw)) : JText::_('NO_DATE');
                        }
                        $data[$i][$val] = $v->data->$raw;
                    }
                }

                if (property_exists($v->data, '__pk_val')) {
                    $data[$i]['id'] = $v->data->__pk_val;
                }
                if (property_exists($v->data, 'fabrik_edit') && !empty($v->data->fabrik_edit)) {
                    $data[$i]['fabrik_edit_url'] = $v->data->fabrik_edit_url;
                }
                if (property_exists($v->data, 'id')) {
                    $data[$i]['row_id'] = $v->id;
                }
                if (property_exists($v->data, 'jos_emundus_users___user_id_raw')) {
                    $data[$i]['id'] = $v->data->jos_emundus_users___user_id_raw;
                }

                if (property_exists($v->data, 'fabrik_view_url') && !empty($v->data->fabrik_view_url)) {
                    $data[$i]['fabrik_view_url'] = $v->data->fabrik_view_url;
                }

                $i = $i + 1;
            }
        }
        
        ?>


        <div class="g-block size-100">
            <?php if ($this->navigation->total < 1) :?>
                <?php if($this->table->db_table_name == 'jos_emundus_entreprise') :?>
                    <?= JText::_("COM_EMUNDUS_NO_COMPANIES"); ?>
                <?php elseif ($this->table->db_table_name == 'jos_emundus_users') :?>
                    <?= JText::_("COM_EMUNDUS_NO_ASSOCIATES"); ?>
                <?php endif; ?>
            <?php else: ?>
                <?php
                    $gCounter = 0;
                    foreach ($data as $d) :?>
                        <div class="accordion-container accordion-container-<?= $this->table->renderid; ?>">
                            <div class="article-title article-title-<?= $this->table->renderid; ?>">
                                <div class="article-name">
                                <i class="fas fa-caret-right"></i>
                                <?php if ($this->table->db_table_name == 'jos_emundus_entreprise') :?>
                                    <?php if (!empty($d["Raison sociale"])) :?>
                                    <h4><?= $d["Raison sociale"]; ?></h4></div>
                                    <?php endif; ?>
                                <?php elseif ($this->table->db_table_name == 'jos_emundus_users') :?>

                                    <?php if (!empty($d["lastname"]) && !empty($d["firstname"])) :?>
                                        <h4><?= $d["lastname"]. " " .$d["firstname"]; ?></h4></div>
                                    <?php elseif (!empty($d["Nom"]) && !empty($d["Prénom"])) :?>
                                        <h4><?= $d["Nom"]. " " .$d["Prénom"]; ?></h4></div>
                                    <?php endif; ?>

	                                <?php if (!empty($d['user_id'])) :?>
                                        <div class="em-inscrire-col"><a href="/inscription?user=<?= $d['user_id']; ?>"><?= JText::_("COM_EMUNDUS_SIGNUP_FORMATION"); ?></a></div>
	                                <?php endif; ?>
                                <?php endif; ?>
                                <div class="accordion-icons">
                                    <?php if ($d['fabrik_edit_url']) :?>
	                                    <?= ($this->table->db_table_name == 'jos_emundus_users') ? "<a class='em-consult-col' href='".$d['fabrik_edit_url']."'><span>".JText::_("COM_EMUNDUS_RESET_PASSWORD")."</span></a>" : "<a href='".$d['fabrik_edit_url']."'><i class='fa fa-pen'></i></a>"; ?>
                                    <?php endif; ?>
                                    <?php if ($d['fabrik_view_url']) :?>
                                        <?= ($this->table->db_table_name == 'jos_emundus_users') ? "<a class='em-consult-col' href='".$d['fabrik_view_url']."'><span>".JText::_("COM_EMUNDUS_CONSULT_FORMATION")."</span></a>" : "<a href='".$d['fabrik_view_url']."'><i class='fa fa-eye'></i></a>"; ?>
                                    <?php endif; ?>
                                    <div style="display: inline" id="delete-row-<?= $d['row_id']; ?>" class="delete-row-<?= $this->table->db_table_name; ?>" data-id="<?= $d['id']; ?>" <?= (!empty($d['user_id']))?'data-cid= "'.$d['cid'].'"':""; ?>>
                                        <i class="fas fa-times"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-content">
                                <?php foreach ($d as $k => $v) :?>
                                    <?php if ($k != 'fabrik_edit_url' && $k != 'fabrik_view_url' && $k != 'id' && $k != 'row_id' && $k != '__pk_val' && $k != 'user_id' && $k != 'cid') :?>
                                        <?php if (strpos($k, 'Title')) :?>
                                            <div class="em-group-title">
                                                <span><?= str_replace('Title-', '',$k); ?></span>
                                            </div>
                                        <?php else: ?>
                                            <div class="em-element <?= strtolower(str_replace(' ', '-', str_replace(['è', 'é'],'e', str_replace(['.', '(', ')','°'], '', $k)))); ?>">
                                                <div class="em-element-label"><?= $k; ?> : </div>
                                            <div class="em-element-value <?= empty($v)?"em-empty-value":""; ?>"><?= $v; ?></div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <ul id="list-pagin-<?= $this->table->renderid; ?>" class="list-pagin"></ul>
</form>

<?php
if ($this->hasButtons) {
	echo $this->loadTemplate('buttons');
}
echo $this->table->outro;
if ($pageClass !== '') {
	echo '</div>';
}
?>


<script>

    var data = <?= sizeof($data); ?>;

    //Pagination
    pageSize = 3;

    var pageCount =  data / pageSize;

    if (pageCount > 1) {
        for (var i = 0 ; i<pageCount;i++) {
            jQuery("#list-pagin-<?= $this->table->renderid; ?>").append('<li><p>'+(i+1)+'</p></li> ');
        }
    }

    jQuery("#list-pagin-<?= $this->table->renderid; ?> li").first().find("p").addClass("current");
    showPage<?= $this->table->renderid; ?>  = function(page) {
        jQuery(".accordion-container-<?= $this->table->renderid; ?>").hide();
        jQuery(".accordion-container-<?= $this->table->renderid; ?>").each(function(n) {
            if (n >= pageSize * (page - 1) && n < pageSize * page)
                jQuery(this).show();
        });
    };

    showPage<?= $this->table->renderid; ?> (1);

    jQuery("#list-pagin-<?= $this->table->renderid; ?> li p").click(function() {
        jQuery("#list-pagin-<?= $this->table->renderid; ?> li p").removeClass("current");
        jQuery(this).addClass("current");
        showPage<?= $this->table->renderid; ?> (parseInt(jQuery(this).text()))
    });


// accordion
    jQuery(function() {
        var Accordion = function(el, multiple) {
            this.el = el || {};
            this.multiple = multiple || false;

            var links = this.el.find('.article-title-<?= $this->table->renderid; ?>');
            links.on('click', {
                el: this.el,
                multiple: this.multiple
            }, this.dropdown)
        };

        Accordion.prototype.dropdown = function(e) {
            var $el = e.data.el;

            $this = jQuery(this),
            $next = $this.next();

            $next.slideToggle();
            $this.parent().toggleClass('open');


            $this.find('.fa-caret-right').toggleClass("down");

            if (!e.data.multiple) {
                $el.find('.accordion-content').not($next).slideUp().parent().removeClass('open');

                $el.find('.accordion-content').not($next).parent().find('.fa-caret-right').removeClass("down");
            }
        };
        var accordion = new Accordion(jQuery('.accordion-container-<?= $this->table->renderid; ?>'), false);
    });

    jQuery(document).ready(function(){
        if(jQuery(this).find('.accordion-container-<?= $this->table->renderid; ?>').size() > 0 ) {
            var first = document.querySelectorAll('.accordion-container-<?= $this->table->renderid; ?>')[0];
            jQuery(first.getElementsByClassName('accordion-content')[0]).slideToggle();
            first.classList.add('open');
            jQuery(first).find('.fa-caret-right').addClass("down");
        }
    });

    jQuery(".delete-row-<?= $this->table->db_table_name; ?>").on('click', function (e) {
        var that = jQuery(this);
        var row = jQuery(this).closest('.accordion-container')[0];

        e.stopPropagation();

        Swal.fire({
                title: "<?= ($this->table->db_table_name == 'jos_emundus_users') ? JText::_('REMOVE_ASSOCIATE_CONFIRM') : JText::_('REMOVE_COMPANY_CONFIRM'); ?>",
                type: "question",
                showCancelButton: true,
                confirmButtonColor: "#28a745",
                cancelButtonColor: "#dc3545",
                confirmButtonText: "<?= JText::_('JYES'); ?>",
                cancelButtonText: "<?= JText::_('JNO'); ?>"
            }
        ).then(
            function (isConfirm) {
                if (isConfirm.value == true) {
                    jQuery.ajax({
                        type: "post",
                        url: "<?= ($this->table->db_table_name == 'jos_emundus_users') ? 'index.php?option=com_emundus&controller=formations&task=deleteassociate' : 'index.php?option=com_emundus&controller=formations&task=deletecompany'; ?>",
                        dataType: 'json',
                        data : ({
                            id: jQuery(that).data("id"),
                            <?php if (!empty($d['user_id'])) :?>
                                cid: jQuery(that).data("cid"),
                            <?php endif; ?>
                        }),
                        success: function(result) {
                            if (result.status) {
                                jQuery(row).hide();
                                Swal.fire({
                                    type: 'success',
                                    title: "<?= ($this->table->db_table_name == 'jos_emundus_users') ? JText::_('REMOVE_ASSOCIATE_REMOVED') : JText::_('REMOVE_COMPANY_REMOVED'); ?>"
                                });
                            }
                            else {
                                Swal.fire({
                                    type: 'error',
                                    text: "<?= ($this->table->db_table_name == 'jos_emundus_users') ? JText::_('REMOVE_ASSOCIATE__NOT_REMOVED') : JText::_('REMOVE_COMPANY_NOT_REMOVED'); ?>"
                                });
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.log(jqXHR.responseText);
                            Swal.fire({
                                type: 'error',
                                text: "<?= ($this->table->db_table_name == 'jos_emundus_users') ? JText::_('REMOVE_ASSOCIATE_NOT_REMOVED') : JText::_('REMOVE_COMPANY_NOT_REMOVED'); ?>"
                            });
                        }
                    });
                }
            }
        );
    });

</script>

