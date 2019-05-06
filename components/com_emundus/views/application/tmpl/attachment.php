<?php
/**
 * @package       Joomla
 * @subpackage    eMundus
 * @link          http://www.emundus.fr
 * @copyright     Copyright (C) 2018 eMundus SAS. All rights reserved.
 * @license       GNU/GPL
 * @author        eMundus SAS
 */

// No direct access

defined('_JEXEC') or die('Restricted access');

$offset = JFactory::getConfig()->get('offset');
JFactory::getSession()->set('application_layout', 'attachment');

$can_export = EmundusHelperAccess::asAccessAction(8,'c', $this->_user->id, $this->fnum)?true:false;
?>

<!--<div class="title" id="em_application_attachments">
    <i class="dropdown icon"></i> <?php echo JText::_('ATTACHMENTS').' - '.$this->attachmentsProgress." % ".JText::_("SENT"); ?>
</div>-->
<div class="row">
    <div class="panel panel-default widget">
        <div class="panel-heading">
            <h3 class="panel-title">
            <span class="glyphicon glyphicon-paperclip"></span>
                <?php echo JText::_('ATTACHMENTS').' - '.$this->attachmentsProgress." % ".JText::_("SENT"); ?>

                <?php if($can_export && count($this->userAttachments) > 0)
                    echo '<button class="btn btn-default" id="em_export_pdf"  target="_blank" type="button" data-toggle="tooltip" data-placement="right" title="'.JText::_('EXPORT_FILE_ATTACHMENT').'">
                        <span class="glyphicon glyphicon-save" ></span>
                    </button>';
                ?>
            </h3>
        </div>

        <?php $j = 1; $i = 1;
        $new_cat_id = $this->userAttachments[0]->category;
        //var_dump($new_cat_id);
        $nameCategory = $this->nameCategory[$new_cat_id]; ?>
        <div class="em-collapse-container">
            <div class="em-utilitize">
                <div class="em-select-all-files">

                    <input type="checkbox" name="em_application_attachments_all" id="em_application_attachments_all" />
                    <p><?= JText::_('SELECT_ALL_FILES') ?></p>
                </div>

                <div class="input-group em-searchbar">
                        <input type="text" id="em-searchbar" class="form-control" placeholder="<?= JText::_('KEYWORDS') ?>">
                        <span class="input-group-btn">
                            <button id="btn-em-searchbar" class="btn btn-default" type="button"><?= JText::_('SEARCH') ?></button>
                        </span>
                </div>
            </div>
                <div class="em-collapse">
                    <div class="panel-heading fileCollapse" role="tab" id="heading<?= $new_cat_id ?>">
                        <div class="panel-title em-title-collapse">
                            <p><strong><?= JText::_($nameCategory); ?></strong></p>
                            <a id="em-button" class="em-button<?= $new_cat_id ?>" role="button">
                                <p id="em-arrow-down<?= $new_cat_id ?>" class='em-arrow-up'></p>
                            </a>
                        </div>
                    </div>
                    <div id="collapse<?= $new_cat_id ?>" class="borderContentCollapse">
                        <div class="panel-body">
                        <?php
                        if (count($this->userAttachments) > 0) {
                            if ($can_export)
                                $checkbox = '<input type="checkbox" name="em_application_attachments_all" id="em-checkbox-collapse" class="em-checkbox-collapse'. $new_cat_id.'"/>';?>

                                <table class="table table-hover attachments_table">
                                    <thead>
                                        <tr id="em-tr-collapse<?= $new_cat_id; ?>" class="em-tr-collapse">
                                            <th><?= $checkbox; ?> #
                                            </th>
                                            <div class="tooltipSelectAttachments selector<?= $new_cat_id; ?>"><p><?= JText::_('COM_EMUNDUS_SELECT_FILES_OF_CATEGORY'); ?></p></div>

                                            <th><?= JText::_('ATTACHMENT_FILENAME'); ?></th>
                                            <th><?= JText::_('ATTACHMENT_DATE'); ?></th>
                                            <th><?= JText::_('ATTACHMENT_DESCRIPTION'); ?></th>
                                            <th><?= JText::_('CAMPAIGN'); ?></th>
                                            <th><?= JText::_('ACADEMIC_YEAR'); ?></th>
                                            <th><?= JText::_('VALIDATION_STATE'); ?></th>
                                        </tr>
                                     </thead>
                                    <tbody>
                            <?php
                        } else echo JText::_('NO_ATTACHMENT');


        foreach ($this->userAttachments as $cat => $attachment){
            //var_dump($attachment->category);
            if($new_cat_id != $attachment->category){
                $i = 1;
                ?>
                                    </tbody>
                                </table>
                            <?php
                                if (count($this->userAttachments) > 0) {
                                    if (EmundusHelperAccess::asAccessAction(4, 'd', $this->_user->id, $this->fnum)) {
                                    echo '<div style="width:40px;  margin-top: -15px; text-align: center"><span class="glyphicon glyphicon-chevron-down"></span><br /><button class="btn btn-danger btn-xs btn-attach" data-title="' . JText::_('DELETE_SELECTED_ATTACHMENTS') . '" id="em_delete_attachments" name="em_delete_attachments" link="/index.php?option=com_emundus&controller=application&task=deleteattachement&fnum=' . $this->fnum . '&student_id=' . $this->student_id . '">
                                            <span class="glyphicon glyphicon-trash"></span></button></div> ';
                                    }
                                } ?>
                        </div>
                    </div>
                </div>

                <div class="em-collapse">
                    <div class="panel-heading fileCollapse">
                        <div class="em-title-collapse">
                            <p><strong><?= JText::_($this->nameCategory[$attachment->category]); ?></strong></p>
                            <a id="em-button" class="em-button<?= $attachment->category ?>" role="button">
                                <p id="em-arrow-down<?= $attachment->category ?>" class='em-arrow-up'></p>
                            </a>
                        </div>
                    </div>
                    <div id="collapse<?= $attachment->category ?>" class="borderContentCollapse">
                        <div class="panel-body">
                            <?php
                            if (count($this->userAttachments) > 0) {
                            if ($can_export)
                            $checkbox = '<input type="checkbox" name="em_application_attachments_all" id="em-checkbox-collapse" class="em-checkbox-collapse'.$attachment->category.'"/>';

                            echo '<table class="table table-hover attachments_table">
                                    <thead>
                                        <tr id="em-tr-collapse'.$attachment->category.'" class="em-tr-collapse">
                                            <th>' . $checkbox . ' #</th>
                                            <div class="tooltipSelectAttachments selector'.$attachment->category.'"><p>' . JText::_('COM_EMUNDUS_SELECT_FILES_OF_CATEGORY'). '</p></div>

                                            <th>' . JText::_('ATTACHMENT_FILENAME') . '</th>
                                            <th>' . JText::_('ATTACHMENT_DATE') . '</th>
                                            <th>' . JText::_('ATTACHMENT_DESCRIPTION') . '</th>
                                            <th>' . JText::_('CAMPAIGN') . '</th>
                                            <th>' . JText::_('ACADEMIC_YEAR') . '</th>
                                            <th>' . JText::_('VALIDATION_STATE') . '</th>
        
                                        </tr>
                                    </thead>
                                    <tbody>';
                            }
                }
                $new_cat_id = $attachment->category;

        ?>

                        <?php

                        if (count($this->userAttachments) > 0) {


                            //var_dump($this->userAttachments);

                                $path = $attachment->lbl == "_archive" ? EMUNDUS_PATH_REL . "archives/" . $attachment->filename : EMUNDUS_PATH_REL . $this->student_id . '/' . $attachment->filename;
                                $img_missing = (!file_exists($path)) ? '<img style="border:0;" src="media/com_emundus/images/icones/agt_update_critical.png" width=20 height=20 title="' . JText::_('FILE_NOT_FOUND') . '"/> ' : "";
                                $img_dossier = (is_dir($path)) ? '<img style="border:0;" src="media/com_emundus/images/icones/dossier.png" width=20 height=20 title="' . JText::_('FILE_NOT_FOUND') . '"/> ' : "";
                                $img_locked = (strpos($attachment->filename, "_locked") > 0) ? '<img src="media/com_emundus/images/icones/encrypted.png" />' : "";

                                if ($can_export)
                                    $checkbox = '<input type="checkbox" name="attachments[]" class="em_application_attachments" id="aid' . $attachment->aid . '" value="' . $attachment->aid . '" />';

                                $class = "";
                                $color = "";
                                $meaning = "";
                                if ($attachment->is_validated == -2) {
                                    $class = "glyphicon-unchecked";
                                    $color = "gray";
                                    $meaning = JText::_('UNCHECKED');
                                } elseif ($attachment->is_validated == null) {
                                    $class = "glyphicon-unchecked";
                                    $color = "gray";
                                    $meaning = JText::_('UNCHECKED');
                                } elseif ($attachment->is_validated == 1) {
                                    $class = "glyphicon-ok";
                                    $color = "green";
                                    $meaning = JText::_('VALID');
                                } else {
                                    $class = "glyphicon-warning-sign";
                                    $color = "orange";
                                    $meaning = JText::_('INVALID');
                                }
                                echo '<tr class="em-tr-collapse">
                                          <td>' . $checkbox . ' ' . $i . '</td>
                                          <td><a href="' . JURI::base() . $path . '" target="_blank">' . $img_dossier . ' ' . $img_locked . ' ' . $img_missing . ' ' . $attachment->value . '</a></td>
                                          <td>' . date('l, d F Y H:i', strtotime($attachment->timedate)) . '</td>
                                          <td>' . $attachment->description . '</td>
                                          <td>' . $attachment->campaign_label . '</td>
                                          <td>' . $attachment->year . '</td>
                                          <td><p class="is-validated" id="' . $attachment->aid . '" title="' . $meaning . '"><span class="glyphicon ' . $class . '" style="color:' . $color . '"></span></p></td>
                                      </tr>';


                            $i++;
                            }  else echo JText::_('NO_ATTACHMENT');

                            $j++;
                            //var_dump($new_cat_id);
                        }
                        echo '</tbody></table>';
                        if (count($this->userAttachments) > 0) {
                            if (EmundusHelperAccess::asAccessAction(4, 'd', $this->_user->id, $this->fnum)) {
                                echo '<div style="width:40px;  margin-top: -15px; text-align: center"><span class="glyphicon glyphicon-chevron-down"></span><br /><button class="btn btn-danger btn-xs btn-attach" data-title="' . JText::_('DELETE_SELECTED_ATTACHMENTS') . '" id="em_delete_attachments" name="em_delete_attachments" link="/index.php?option=com_emundus&controller=application&task=deleteattachement&fnum=' . $this->fnum . '&student_id=' . $this->student_id . '">
                                <span class="glyphicon glyphicon-trash"></span></button></div> ';
                            }
                        } ?>
                        </div>
                    </div>
                </div>

            <div class="modal fade" id="em-modal-actions" style="z-index:99999" tabindex="-1" role="dialog" aria-labelledby="em-modal-actions" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title" id="em-modal-actions-title"><?php echo JText::_('TITLE');?></h4>
                        </div>
                        <div class="modal-body">

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal"><?php echo JText::_('CANCEL')?></button>
                            <button type="button" class="btn btn-success"><?php echo JText::_('OK');?></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="em-modal-form" style="z-index:99999" tabindex="-1" role="dialog" aria-labelledby="em-modal-actions" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title" id="em-modal-actions-title"><?php echo JText::_('LOADING');?></h4>
                        </div>
                        <div class="modal-body">
                            <img src="<?php echo JURI::base(); ?>media/com_emundus/images/icones/loader-line.gif">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal"><?php echo JText::_('CANCEL')?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
   function getChecked() {
        var checkedInput = new Array();
        $('.em_application_attachments:checked').each(function() {
                checkedInput.push($(this).val());
            });
        return checkedInput;
    }

    function getJsonChecked() {
        var i=0;
        var myJSONObject = '{';
        $('.em_application_attachments:checked').each(function() {
                myJSONObject += '\"'+i+'\":\"'+$(this).val()+'\",';
                i=i+1;
            });
        myJSONObject = myJSONObject.substr(0, myJSONObject.length-1);
        myJSONObject += '}';
        if (myJSONObject.length === 2) {
            alert('SELECT_FILES');
            return false;
        } else {
            checkedInput = myJSONObject;
        }
        return checkedInput;
    }

    $(document).ready(function() {
        $('td').css('vertical-align', 'inherit');
    });

    /*$(document).on('click', '#em-modal-actions', function(e)
    {
        var url = "index.php?option=com_emundus&view=application&format=raw&layout=attachment&fnum=<?php echo $this->fnum; ?>";
        $.ajax({
            type:'get',
            url:url,
            dataType:'html',
            success: function(result)
            {
                $('#em-appli-block').empty();
                $('#em-appli-block').append(result);
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                console.log(jqXHR.responseText);
            }
        });
    });*/
    $(document).off('click', '#em_application_attachments_all');
    $(document).on('click', '#em_application_attachments_all', function() {
        if ($(this).is(':checked')) {
            $('.em_application_attachments').prop('checked', true);
        } else {
            $('.em_application_attachments').prop('checked', false);
        }
    });

    let j = 0;
    $('.fileCollapse').each(function(j){

       $(document).off('click', '#collapse'+j+' #em-checkbox-collapse');
       $(document).on('click', '#collapse'+j+' #em-checkbox-collapse', function(e)
       {
           console.log(j);
           if($(this).is(':checked'))
           {


               $('#collapse'+j+' .em_application_attachments').prop('checked', true);
           }
           else
           {
               $('#collapse'+j+' .em_application_attachments').prop('checked', false);
           }
       });

    });

    $(document).off('click', '#em_delete_attachments');
    $(document).on('click', '#em_delete_attachments', function(e) {
        if (e.handle === true) {
            e.handle = false;
            var checked = getChecked();

            if (checked.length > 0) {
                var res = confirm("<?php echo JText::_('CONFIRM_DELETE_SELETED_ATTACHMENTS')?>");
                if (res) {
                    var url = $(this).attr('link');
                    $('#em-modal-actions .modal-body').empty();
                    $('#em-modal-actions .modal-body').append('<div><img src="' + loadingLine + '" alt="' +
                    Joomla.JText._('LOADING') + '"/></div>');
                    $('#em-modal-actions .modal-footer').hide();
                    $('#em-modal-actions .modal-dialog').addClass('modal-lg');
                    $('#em-modal-actions .modal').show();
                    $('#em-modal-actions').modal({backdrop: false, keyboard: true}, 'toggle');

                    $.ajax(
                        {
                            type: 'post',
                            url: url,
                            dataType: 'json',
                            data: {ids: JSON.stringify(checked)},
                            success: function (result) {
                                $('#em-modal-actions').modal('hide');

                                var url = "index.php?option=com_emundus&view=application&format=raw&layout=attachment&fnum=<?php echo $this->fnum; ?>";
                                $.ajax({
                                    type:'get',
                                    url:url,
                                    dataType:'html',
                                    success: function(result)
                                    {
                                        $('#em-appli-block').empty();
                                        $('#em-appli-block').append(result);
                                    },
                                    error: function (jqXHR, textStatus, errorThrown)
                                    {
                                        console.log(jqXHR.responseText);
                                    }

                                });

                            var url = "index.php?option=com_emundus&view=application&format=raw&layout=attachment&fnum=<?php echo $this->fnum; ?>";
                            $.ajax({
                                type:'get',
                                url:url,
                                dataType:'html',
                                success: function(result) {
                                    $('#em-appli-block').empty();
                                    $('#em-appli-block').append(result);
                                },
                                error: function (jqXHR) {
                                    console.log(jqXHR.responseText);
                                }

                            });
                            //$('.list-group-item#1318').click();
                        },
                        error: function (jqXHR) {
                            console.log(jqXHR.responseText);
                        }
                    });
                }
            } else {
                alert("<?php echo JText::_('YOU_MUST_SELECT_ATTACHMENT')?>");
            }
        }

    });

    $(document).off('click', '#em_export_pdf');
    $(document).on('click', '#em_export_pdf', function() {
        var checkedInput = getJsonChecked();
        var checked = getChecked();
        console.log(checked);
        /*String.prototype.fmt = function (hash) {
            var string = this, key;
            for (key in hash) string = string.replace(new RegExp('\\{' + key + '\\}', 'gm'), hash[key]); return string;
        }*/

        //var url = $(this).attr('link')+'&ids='+encodeURIComponent(JSON.stringify(checkedInput));
        if (Array.isArray(checked) && checked.length){


        var url = "index.php?option=com_emundus&controller=application&task=exportpdf&fnum=<?php echo $this->fnum; ?>&student_id=<?php echo $this->student_id; ?>&ids="+checked;
        //url = url.fmt({ids: checkedInput});
        var link = window.open('', '_blank');
        $.ajax({
            type:'get',
            url: url,
            dataType:'json',

            success: function(result) {

                if(result.link){
                    link.location.href = result.link;
                }
            },
            error: function (jqXHR)
            {
                console.log(jqXHR.responseText);
            }
        });
        }
        else{
            Swal.fire({
                title: Joomla.JText._('INFORMATION'),
                text: Joomla.JText._('SELECT_AT_LEAST_ONE_FILE'),
                type: 'warning'
            })
        }
       /* if(checked.length > 0)
        {
            $('#em-modal-actions .modal-body').empty();
            $('#em-modal-actions-title').empty();
            $('#em-modal-actions-title').append('<?php echo JText::_('GENERATE_PDF') ?>');
            $('#em-modal-actions .modal-body').append('<div class="well">' +
            '<input class="em-ex-check" type="checkbox" value="forms" name="forms" id="em-ex-forms" checked/>' +
            '<label for="em-ex-forms">'+Joomla.JText._('FORMS_PDF')+'</label> <br/>' +
            '<input class="em-ex-check" type="checkbox" value="attachment" name="attachment" id="em-ex-attachment" checked/>' +
            '<label for="em-ex-attachment">'+Joomla.JText._('ATTACHMENT_PDF')+'</label> <br/>' +
            '</div>' +
            '<a class="btn btn-default btn-attach" id="em_generate" href=\''+url+'\' target="_blank"><?php echo JText::_('GENERATE_PDF') ?></a><div id="attachement_res"></div></div>');
            $('#em-modal-actions .modal-footer').hide();
            $('#em-modal-actions .modal-dialog').addClass('modal-lg');
            $('#em-modal-actions .modal').show();
            $('#em-modal-actions').modal({backdrop:false, keyboard:true},'toggle');
        }
        else
        {
            $('.em_application_attachments').prop('checked', false);
            alert("<?php //echo JText::_('YOU_MUST_SELECT_ATTACHMENT')?>");
        }*/
    });


    $(".is-validated").click(function () {
        var id = $(this).attr("id");
        var state = -2;
        if ($("#"+id+" span").hasClass("glyphicon-unchecked")) {
            $("#"+id+" span").removeClass("glyphicon-unchecked").addClass("glyphicon-ok").css("color", "green");
            $("#"+id).attr('title',Joomla.JText._('VALID'));
            state = 1

        }else{
            if($("#"+id+" span").hasClass("glyphicon-ok")){
                $("#"+id+" span").removeClass("glyphicon-ok").addClass("glyphicon-warning-sign").css("color", "orange");
                $("#"+id).attr('title',Joomla.JText._('INVALID'));
                state = 0
            }else{
                if($("#"+id+" span").hasClass("glyphicon-warning-sign")){
                    $("#"+id+" span").removeClass("glyphicon-warning-sign").addClass("glyphicon-unchecked").css("color", "gray");
                    $("#"+id).attr('title',Joomla.JText._('UNCHECKED'));
                    state = -2
                }
            }
        }
        $.ajax({
            type:'post',
            url:"index.php?option=com_emundus&controller=application&task=attachment_validation&fnum=<?php echo $this->fnum; ?>",
            dataType:'json',
            data:({state: state, att_id: id}),
            success: function(result) {
                if (result.res) {
                    console.log(res)
                }
            },
            error: function (jqXHR) {
                console.log(jqXHR.responseText);
            }
        });


});
    //disabled the enter key for the searchbar
   $('#em-searchbar').on('keyup keypress',function(event){

       // Compatibilité IE / Firefox
       if(!event && window.event) {
           event = window.event;
       }
       // IE
       if(event.keyCode == 13) {
           event.returnValue = false;
           event.cancelBubble = true;
       }
       // DOM
       if(event.which == 13) {
           event.preventDefault();
           event.stopPropagation();
       }

   });
   // function which update the files with keyword
   $('#btn-em-searchbar').click(function(){

        let search = $('#em-searchbar').val();
        console.log(search);
        $.ajax({
            type:'post',
            url:"index.php?option=com_emundus&view=application&format=raw&layout=attachment&fnum=<?php echo $this->fnum; ?>",
            data:{search: search},

            beforeSend: function(){
                $('#em-appli-block').empty();
                $('#em-appli-block').append('<div class="em-container-loader"><img src="http://emundus.local/media/com_emundus/images/icones/loader.gif"></div>');
            },
            success: function(result) {
                    $('#em-appli-block').empty();
                    $('#em-appli-block').append(result);
                    $('#em-searchbar').val(search);
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                console.log(jqXHR.responseText);
            }
        });
    })


</script>

<script>
    // Function which change the rotation of the arrow in the collapse
    $('.fileCollapse').each(function (i) {
        $('.em-button'+i).click(function () {

            $('#collapse'+i).animate({
                height: 'toggle',
                display: 'none'
            },500);

            if($('#em-arrow-down'+i).hasClass('em-arrow-down')){

                $('#em-arrow-down'+i).removeClass('em-arrow-down').addClass('em-arrow-up');
            }
            else{
                $('#em-arrow-down'+i).removeClass('em-arrow-up').addClass('em-arrow-down');
            }
        });
    });


</script>
<script>
    $('.fileCollapse').each(function (i) {

    $('#em-tr-collapse'+i+' th:nth-child(1)')
        .on("mouseenter",function(){

            $('.selector'+i).css({
                'height':'30px',
                'width':'auto',
                'display':'flex',
                'opacity':'1',
                'transition':'display,500ms',
                'background':'#33332E',
                'border-radius':'10px'
            });
            console.log(i);
            $('.selector'+i+' p').css({
                'color':'white',
                'font-size':'0.6rem',
            })

        })
        .on("mouseleave",function(){
            $('.selector'+i).css({
                'display':'none',
                'transition':'display,500ms'
            })
        });
    });

</script>
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
