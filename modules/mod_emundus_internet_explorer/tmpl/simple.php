<?php
defined('_JEXEC') or die;
header('Content-Type: text/html; charset=utf-8');
$document = JFactory::getDocument();
//Chart.js is the libary used for this module's graphs
$document->addScript('media'.DS.'com_emundus'.DS.'lib'.DS.'Chart.min.js');
//moment.js is a Date libary, using to retrieve missing dates
$document->addScript('media'.DS.'com_emundus'.DS.'lib'.DS.'moment.min.js');
$document->addStyleSheet('media'.DS.'com_emundus'.DS.'lib'.DS.'bootstrap-336'.DS.'css'.DS.'bootstrap.min.css');
$document->addStyleSheet('media'.DS.'com_emundus'.DS.'lib'.DS.'Semantic-UI-CSS-master'.DS.'semantic.min.css');
?>

<?php if($show) { ?>
<center>
	<div class="container">
		<div class="bandeau-internet-explorer">
			<a class="closeButton" onclick="deleteMessage()">&#10006;</a>
			<b><?php echo stripslashes(JText::_($message)) ?></b>
		</div>
	</div>
</center>

<script>
	function deleteMessage() {
		jQuery.ajax({
			url : "index.php?option=com_ajax&module=emundus_internet_explorer&method=closeMessage&format=json",
			async: true,
			cache: false,
			success : function(data) {
				document.getElementsByClassName('bandeau-internet-explorer')[0].style.display = "none";
			}
		});
	}
</script>
<?php } ?>