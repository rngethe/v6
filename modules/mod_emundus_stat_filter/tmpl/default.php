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

<center>
	<div class="filter">
		<form action="" method="" onsubmit="return false;">
			<label>Programme</label>
			<select name="prog" id="progFilter" onchange="progAction()">
				<option value="-1"></option>
				<?php
				foreach ($tabProg as $prog) { 
					echo "<option value=\"".$prog['code']."\">".$prog['label']."</option>";
				} ?>
			</select>
			<label>Année</label>
			<select name="years" id="yearsFilter" onchange="yearAction()">
				<option value="-1"></option>
				<?php
				foreach ($tabYear as $year) { 
					echo "<option value=\"".$year['year']."\">".$year['year']."</option>";
				} ?>
			</select>
			<label>Campagne</label>
			<select name="campaign" id="campaignFilter" onchange="campaignAction()">
				<option value="-1"></option>
				<?php
				foreach ($tabCampaign as $campaign) { 
					echo "<option value=\"".$campaign['id']."\">".$campaign['label']."</option>";
				} ?>
			</select>
		</form>
	</div>
</center>
<script>
	function progAction() {
		jQuery.ajax({
			url: "index.php?option=com_ajax&module=emundus_stat_filter&format=json", 
			type: "POST",
			async: true,
			cache: false,
			data: {
				prog : document.getElementById("progFilter").value,
				year : -2,
				campaign : -2
			},
			success: function(response){
				document.getElementById("yearsFilter").innerHTML = response["data"].split("////")[1];
				document.getElementById("campaignFilter").innerHTML = response["data"].split("////")[2];
				fusioncharts = new Array();
				refreshModuleGraph();
			}
		});
	}
	
	function yearAction() {
		jQuery.ajax({
			url: "index.php?option=com_ajax&module=emundus_stat_filter&format=json", 
			type: "POST",
			async: true,
			cache: false,
			data: {
				prog : -2,
				year : document.getElementById("yearsFilter").value,
				campaign : -2
			},
			success: function(response){
				document.getElementById("progFilter").innerHTML = response["data"].split("////")[0];
				document.getElementById("campaignFilter").innerHTML = response["data"].split("////")[2];
				fusioncharts = new Array();
				refreshModuleGraph();
			}
		});
	}
	function campaignAction() {
		jQuery.ajax({
			url: "index.php?option=com_ajax&module=emundus_stat_filter&format=json", 
			type: "POST",
         cache:false,
         async: false,
			data: {
				prog : -2,
				year : -2,
				campaign : document.getElementById("campaignFilter").value
			},
			success: function(response){
				document.getElementById("progFilter").innerHTML = response["data"].split("////")[0];
				document.getElementById("yearsFilter").innerHTML = response["data"].split("////")[1];
				fusioncharts = new Array();
				refreshModuleGraph();
			}
		});
	}
	
	function refreshModuleGraph() {
		jQuery.ajax({
			type: 'POST',
			url: 'index.php?option=com_ajax&module=emundus_stat_filter&method=reloadModule&format=json',
			dataType: 'html',
			data: {module_type: 'mod_emundus_stat', module_name: 'Nationality Statistic'},
			success: function(response) {
				if(fusioncharts != undefined) {
					for(var cpt = 0 ; cpt < fusioncharts.length ; cpt++)
						fusioncharts[cpt].dispose();
				}
				// console.log(JSON.parse(response).data);
				var modulesString = JSON.parse(response).data.split("////");
				// console.log(document.getElementsByClassName('moduletable').length);
				var cpt0 = 0;
				for(var cpt = 1 ; cpt < modulesString.length ; cpt++) {
					for(var i = 0 ; cpt0 < document.getElementsByClassName('moduletable').length &&
					document.getElementsByClassName('moduletable')[cpt0].getElementsByClassName(modulesString[cpt]).length <= 0 ; i++) {
						console.log(document.getElementsByClassName('moduletable')[cpt0]);
						cpt0++;
					}
					cpt++;
					// console.log(cpt + " : " + document.getElementsByClassName('moduletable').length);
					document.getElementsByClassName('moduletable')[cpt0].innerHTML = modulesString[cpt];
					var scripts = document.getElementsByClassName('moduletable')[cpt0].getElementsByTagName('script');
					for(var i=0; i < scripts.length;i++)
					{
						if (window.execScript)
						{
							window.execScript(scripts[i].text.replace('<!--',''));
						}
						else
						{
							window.eval(scripts[i].text);
						}
					}
					
					cpt0++;
				}
				
				// console.log(fusioncharts);
				if(fusioncharts != undefined) {
					for(var cpt = 0 ; cpt < fusioncharts.length ; cpt++)
						fusioncharts[cpt].render();
				}
			}
		});
	}
</script>