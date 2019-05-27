<?php
/**
 * @version		$Id: data.php 14401 2014-09-16 14:10:00Z brivalland $
 * @package		Joomla
 * @subpackage	Emundus
 * @copyright	Copyright (C) 2005 - 2015 eMundus SAS. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
?>

<input type="hidden" id="view" name="view" value="files">
<div class="panel panel-default">
	<?php if (is_array($this->datas)):?>
        <div class="container-result">
            <div>
                <?= $this->pagination->getResultsCounter(); ?>
            </div>
            <div id="countCheckedCheckbox" class="countCheckedCheckbox"></div>

        </div>
		<div class="em-data-container">
			<table class="table table-striped table-hover" id="em-data">
				<thead>
				<tr>
					<?php foreach ($this->datas[0] as $kl => $v): ?>
						<th title="<?= JText::_(strip_tags($v)); ?>" id="<?= $kl; ?>" >
							<div class="em-cell">
								<?php if($kl == 'check'): ?>

                                    <div class="selectContainer" id="selectContainer">
                                        <div class="selectPage">
                                            <input type="checkbox" value="-1" id="em-check-all" class="em-hide em-check">
                                            <label for="em-check-all" class="check-box"></label>
                                        </div>
                                        <div class="selectDropdown" id="selectDropdown">
                                            <i class="fas fa-sort-down"></i>
                                        </div>
                                        <div id="tooltipSelect">
                                            <p><?= JText::_('COM_EMUNDUS_SELECT'); ?></p>
                                        </div>
                                    </div>
                                    <div class="selectAll" id="selectAll">
                                        <label for="em-check-all">
                                            <input value="-1" id="em-check-all" type="checkbox" class="em-check" />
                                            <span id="span-check-all"><?= JText::_('COM_EMUNDUS_CHECK_ALL');?></span>
                                        </label>
                                        <label class="em-check-all-all" for="em-check-all-all">
                                            <input value="all" id="em-check-all-all" type="checkbox" class="em-check-all-all" />
                                            <span id="span-check-all-all"><?= JText::_('COM_EMUNDUS_CHECK_ALL_ALL'); ?></span>
                                        </label>
                                        <label class="em-check-none" for="em-check-none">
                                            <span id="span-check-none"><?= JText::_('COM_EMUNDUS_CHECK_NONE'); ?></span>
                                        </label>
                                    </div>

									<!--<label for="em-check-all">
										<input type="checkbox" value="-1" id="em-check-all" class="em-check" style="width:20px !important;"/>
										<span><?= JText::_('COM_EMUNDUS_CHECK_ALL');?></span>
									</label>

									<label class="em-hide em-check-all-all" for="em-check-all-all">
										<input class="em-check-all-all em-hide" type="checkbox" name="check-all-all" value="all" id="em-check-all-all" style="width:20px !important;"/>
										<span class="em-hide em-check-all-all"><?= JText::_('COM_EMUNDUS_CHECK_ALL_ALL');?></span>
									</label>-->
								<?php elseif ($this->lists['order'] == $kl): ?>
									<?php if ($this->lists['order_dir'] == 'desc'): ?>
										<span class="glyphicon glyphicon-sort-by-attributes-alt"></span>
									<?php else: ?>
										<span class="glyphicon glyphicon-sort-by-attributes"></span>
									<?php endif;?>
									<strong>
										<?= JText::_($v)?>
									</strong>
								<?php else: ?>
									<?= JText::_($v)?>
								<?php endif;?>

							</div>
						</th>
					<?php endforeach; ?>
				</tr>
				</thead>

				<tbody>
				<?php foreach ($this->datas as $key => $line):?>
					<?php if ($key != 0): ?>
						<tr>
							<?php foreach ($line as $k => $value):?>
								<td <?php if ($k == 'check'&& $value->class != null) { echo 'class="'.$value->class.'"'; } ?>>
									<div class="em-cell" >
										<?php if ($k == 'check'): ?>
											<label for = "<?= $line['fnum']->val ?>_check">
												<input type="checkbox" name="<?= $line['fnum']->val ?>_check" id="<?= $line['fnum']->val ?>_check" class='em-check' style="width:20px !important;"/>
												<?php
													$tab = explode('-', $key);
													echo ($tab[1] + $this->pagination->limitstart);
												?>
											</label>
										<?php elseif ($k == 'status'):?>
											<span class="label label-<?= $value->status_class ?>" title="<?= $value->val ?>"><?= $value->val ?></span>
										<?php elseif ($k == 'fnum'):?>
											<a href="#<?= $value->val ?>|open" id="<?= $value->val ?>" class="em_file_open">
												<?php if (isset($value->photo)) :?>
													<div class="em_list_photo"><?= $value->photo; ?></div>
												<?php endif; ?>
												<div class="em_list_text">
													<span class="em_list_text" title="<?= $value->val ?>"> <strong> <?= $value->user->name; ?></strong></span>
													<div class="em_list_email"><?= $value->user->email; ?></div>
													<div class="em_list_email"><?= $value->user->id; ?></div>
												</div>
											</a>
									<?php elseif ($k == "access"):?>
										<?= $this->accessObj[$line['fnum']->val]?>
									<?php elseif ($k == "id_tag"):?>
										<?= @$this->colsSup['id_tag'][$line['fnum']->val]?>
                                    <?php elseif (array_key_exists($k, $this->colsSup)) :?>
                                        <?= @$this->colsSup[$k][$line['fnum']->val] ?>
									<?php else :?>
										<?php if ($value->type == 'text' ) :?>
											<?= strip_tags($value->val); ?>
										<?php elseif ($value->type == "date")  :?>
										<strong>
											<?php if (!isset($value->val) || $value->val == "0000-00-00 00:00:00") :?>
													<span class="em-radio" id="<?= $value->id.'-'.$value->val; ?>" aria-hidden="true"></span>
											<?php else: ?>
												<?php
													$formatted_date = DateTime::createFromFormat('Y-m-d H:i:s', $value->val);
													//echo $formatted_date->format("M j, Y, H:i");
													echo JFactory::getDate($value->val)->format("M j, Y, H:i");
												?>
											<?php endif; ?>
										</strong>
										<?php else: ?>
											<?= $value->val; ?>
										<?php endif; ?>
									<?php endif; ?>
									</div>
								</td>
							<?php endforeach; ?>
						</tr>
					<?php endif;?>
				<?php  endforeach;?>
				</tbody>
			</table>
		</div>
		<div class="well">
			<label for="pager-select" class="em-paginate-label"><?= JText::_('DISPLAY') ?></label>
			<select name="pager-select" class="chzn-select" id="pager-select">
				<option value="0" <?php if ($this->pagination->limit == 0) { echo "selected=true"; } ?>><?= JText::_('ALL')?></option>
				<option value="5" <?php if ($this->pagination->limit == 5) { echo "selected=true"; } ?>>5</option>
				<option value="10" <?php if ($this->pagination->limit == 10) { echo "selected=true"; } ?>>10</option>
				<option value="15" <?php if ($this->pagination->limit == 15) { echo "selected=true"; } ?>>15</option>
				<option value="20" <?php if ($this->pagination->limit == 20) { echo "selected=true"; } ?>>20</option>
				<option value="25" <?php if ($this->pagination->limit == 25) { echo "selected=true"; } ?>>25</option>
				<option value="30" <?php if ($this->pagination->limit == 30) { echo "selected=true"; } ?>>30</option>
				<option value="50" <?php if ($this->pagination->limit == 50) { echo "selected=true"; } ?>>50</option>
				<option value="100" <?php if ($this->pagination->limit == 100) { echo "selected=true"; } ?>>100</option>
			</select>
			<div>
				<ul class="pagination pagination-sm">
					<li><a href="#em-data" id="<?= $this->pagination->{'pagesStart'} ?>"><<</a></li>
					<?php if ($this->pagination->{'pagesTotal'} > 15):?>

						<?php for ($i = 1; $i <= 5; $i++ ):?>
							<li <?php if ($this->pagination->{'pagesCurrent'} == $i) { echo 'class="active"'; } ?>><a id="<?= $i ?>" href="#em-data"><?= $i ?></a></li>
						<?php endfor;?>
						<li class="disabled"><span>...</span></li>
						<?php if ($this->pagination->{'pagesCurrent'} <= 5):?>
							<?php for ($i = 6; $i <= 10; $i++ ):?>
								<li <?php if ($this->pagination->{'pagesCurrent'} == $i) { echo 'class="active"'; } ?>><a id="<?= $i ?>" href="#em-data"><?= $i ?></a></li>
							<?php endfor;?>
						<?php else:?>
							<?php for ($i = ($this->pagination->{'pagesCurrent'} - 2); $i <= ($this->pagination->{'pagesCurrent'} + 2); $i++ ):?>
								<li <?php if ($this->pagination->{'pagesCurrent'} == $i) { echo 'class="active"'; } ?>><a id="<?= $i ?>" href="#em-data"><?= $i ?></a></li>
							<?php endfor;?>
						<?php endif;?>
						<li class="disabled"><span>...</span></li>
						<?php for ($i = ($this->pagination->{'pagesTotal'} - 4); $i <= $this->pagination->{'pagesTotal'}; $i++ ):?>
							<li <?php if ($this->pagination->{'pagesCurrent'} == $i) { echo 'class="active"'; } ?>><a id="<?= $i ?>" href="#em-data"><?= $i ?></a></li>
						<?php endfor;?>
					<?php else:?>
						<?php for ($i = 1; $i <= $this->pagination->{'pagesStop'}; $i++ ):?>
							<li <?php if ($this->pagination->{'pagesCurrent'} == $i) { echo 'class="active"'; } ?>><a id="<?= $i ?>" href="#em-data"><?= $i ?></a></li>
						<?php endfor;?>
					<?php endif;?>
					<li><a href="#em-data" id="<?= $this->pagination->{'pagesTotal'} ?>">>></a></li>
				</ul>
			</div>
		</div>
	<?php else:?>
		<?= $this->datas?>
	<?php endif;?>
</div>

<script type="text/javascript">
    function checkurl() {

        var url = $(location).attr('href');
        url = url.split("#");
        $('.alert.alert-warning').remove();

        if (url[1] != null && url[1].length >= 20) {
            url = url[1].split("|");
            var fnum = {};
            fnum.fnum = url[0];

            if (fnum.fnum != null && fnum.fnum !== "close") {
                addDimmer();
                $.ajax({
                    type:'get',
                    url:'index.php?option=com_emundus&controller=files&task=getfnuminfos',
                    dataType:"json",
                    data:({fnum: fnum.fnum}),
                    success: function(result) {
                        if (result.status && result.fnumInfos != null) {
                            var fnumInfos = result.fnumInfos;
                            fnum.name = fnumInfos.name;
                            fnum.label = fnumInfos.label;
                            openFiles(fnum);
                        } else {
                            $('.em-dimmer').remove();
                            $(".panel.panel-default").prepend("<div class=\"alert alert-warning\"><?= JText::_('CANNOT_OPEN_FILE') ?></div>");
                        }
                    },
                    error: function (jqXHR) {
                        $('.em-dimmer').remove();
                        $("<div class=\"alert alert-warning\"><?= JText::_('CANNOT_OPEN_FILE') ?></div>").prepend($(".panel.panel-default"));
                        console.log(jqXHR.responseText);
                    }
                })
            }
        }

    }
	$(document).ready(function() {
        checkurl();
        $('#rt-mainbody-surround').children().addClass('mainemundus');
        $('#rt-main').children().addClass('mainemundus');
        $('#rt-main').children().children().addClass('mainemundus');
		$('.em-data-container').doubleScroll();
	});
    window.parent.$("html, body").animate({scrollTop : 0}, 300);
</script>

<script>

    $('#selectContainer').on("mouseenter", function() {

        $('#tooltipSelect').css({
            'height':'30px',
            'width':'70px',
            'display':'flex',
            'opacity':'1',
            'transiition':'display,500ms',
            'background':'#33332E',
            'border-radius':'10px'
        });
        $('#tooltipSelect p').css({
            'color':'white',
            'font-size':'0.6rem',
        });

    }).on("mouseleave", function() {
        $('#tooltipSelect').css({
            'display':'none',
            'transiition':'display,500ms'
        })
    });
</script>
<script>
    $('#selectAll').css('display','none');
    $('#selectDropdown').click(function() {

        $('#selectContainer').removeClass('borderSelect');
        $('#selectAll').slideToggle(function() {

            if ($(this).is(':visible')) {

                $('#selectContainer').addClass('borderSelect');
                $(document).click(function (e) {

                    var container = $("#selectDropdown");

                    if (!container.is(e.target) && container.has(e.target).length === 0){
                        $('#selectAll').slideUp();
                        $('#selectContainer').removeClass('borderSelect');
                    }
                });
            }
        });
    });

    $('#selectAll>span').click(function() {
        $('#selectAll').slideUp();
    });

    $('#span-check-all-all').click(function() {
        $('.selectAll.em-check-all-all#em-check-all-all').prop('checked',true);// all
        //$('.em-check#em-check-all').prop('checked',true);//.selectPage Page
        //$('.em-check-all#em-check-all').prop('checked',true);//.selectAll Page
        $('.em-check').prop('checked',true);
        reloadActions('files', undefined, true);
    });

    $('#span-check-none').click(function(){
        $('#em-check-all-all').prop('checked',false);
        $('.em-check#em-check-all').prop('checked',false);
        $('.em-check-all#em-check-all').prop('checked',false);
        $('.em-check').prop('checked',false);
        $('#countCheckedCheckbox').html('');
        reloadActions('files', undefined, false);
    });

    $(document).on('change', '.em-check, .em-check-all-all', function() {

        let countCheckedCheckbox = $('.em-check').not('#em-check-all.em-check,#em-check-all-all.em-check ').filter(':checked').length;
        let allCheck = $('.em-check-all-all#em-check-all-all').is(':checked');
        let nbChecked = allCheck == true ? Joomla.JText._('COM_EMUNDUS_SELECT_ALL') : countCheckedCheckbox;

        let files = countCheckedCheckbox === 1 ? Joomla.JText._('COM_EMUNDUS_FILE') : Joomla.JText._('COM_EMUNDUS_FILES');
        if (countCheckedCheckbox !== 0) {
            $('#countCheckedCheckbox').html('<p>'+Joomla.JText._('COM_EMUNDUS_YOU_HAVE_SELECT') + nbChecked + ' ' + files+'</p>');
        } else {
            $('#countCheckedCheckbox').html('');
        }
    });
</script>