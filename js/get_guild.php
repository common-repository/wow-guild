<?php
  $realm = $_GET["realm"];
  $gname = $_GET["guild"];
  $id = $_GET["id"];
  $realmType = $_GET["realmType"] ? $_GET["realmType"] : "US";
	$rt = ($realmType == "EU" ? 'eu' : 'www');
	//$realm = str_replace(" ","+",$realm);
	$url = curl_init("http://$rt.wowarmory.com/guild-info.xml?r=".stripslashes(str_replace(" ","+",$realm))."&n=".stripslashes(str_replace(" ","+",$gname)));
	$showNo = $_GET['showNo'];
	$baseURL = $_GET['baseURL'] . '/wp-content/plugins/wow-guild/';
	$linkback = isset($_GET['linkback']) ? $_GET['linkback'] : 0;
	
	$img_url = "http://$rt.wowarmory.com/images/icons/";
	
	$start = isset($_GET["start"]) ? $_GET["start"] : 1 ;
	$end = $start+$showNo-1;
	curl_setopt($url, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");
	curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
	$output = curl_exec($url);
	curl_close($url);
	$parser = xml_parser_create();
	xml_parse_into_struct($parser, $output,$vals,$index);
	xml_parser_free($parser);
	$rowa = true;
	if(!$index) {
		echo '<p>Armory unavailable</p>';
	} else {
    echo '<div><span class="guild_name"><a href="'."http://$rt.wowarmory.com/guild-info.xml?r=".stripslashes(str_replace(" ","+",$realm))."&n=".stripslashes(str_replace(" ","+",$gname )).'" >'.stripslashes($gname).'</a></span><br/>';
    echo '<span class="guild_realm">of '.stripslashes($realm).'-'.stripslashes($realmType).'</span></div>';
    echo '<table class="wowguild_char_list" style="width:100%;" cellspacing="0" cellpadding="0">';
    //echo '<tr><th>Name</th><th></th><th>Level</th><th>Race</th><th>Class</th></tr>';
    $i = 0;
    $more = false;
    foreach($index["CHARACTER"] as $chars) {
      $i++;
      if ($i>=$start && $i<=$end) {      
        $char = $vals[$chars]["attributes"];
        echo '<tr class="'.($char['RANK']==0 ? 'lead' : ($rowa ? 'a' : 'b')).'"><td style="text-align:center;width:20px;">'.($char['RANK']==0?'<img src="http://www.wowarmory.com/images/icons/icon-guildmaster.gif" style="display:inline;" />' : '').'</td><td><a href="http://'.$rt.'.wowarmory.com/character-sheet.xml?r='.str_replace(" ","+",$realm).'&n='.$char["NAME"].'">'.$char["NAME"].'</a></td><td style="text-align:right;width:20px;">'.$char["LEVEL"].'</td><td style="text-align:center;width:20px;"><img src="'.$img_url.'race/'.$char["RACEID"].'-'.$char["GENDERID"].'.gif" /></td><td style="text-align:center;width:20px;"><img src="'.$img_url.'class/'.$char["CLASSID"].'.gif" /></td></tr>';
        $rowa = !$rowa;
      }
      if ($i > $end) {
        $more = true;
      }
    }
    echo '<tr class="'.($rowa ? 'a' : 'b').'"><td>';
    if($start!=1) { 
      ?>
        <a href="" class="guild_back">
          <input type="hidden" value="<?php echo $id; ?>" class="guild_no" />
          <input type="hidden" value="<?php echo $gname; ?>" class="guild_gname" />
          <input type="hidden" value="<?php echo stripslashes($realm); ?>" class="guild_realm" />
          <input type="hidden" value="<?php echo $realmType; ?>" class="guild_realmtype" />
          <input type="hidden" value="<?php echo $showNo; ?>" class="guild_showno" />
          <input type="hidden" value="<?php echo $start-$showNo; ?>" class="guild_start" />
          <input type="hidden" value="<?php echo $linkback; ?>" class="guild_link" />
          Back
        </a>
      <?php
    }
    echo '</td><td colspan="3" style="text-align:center;"></td><td style="text-align:right;">';
    if ($more) {
      ?>
        <a href="" class="guild_next">
          <input type="hidden" value="<?php echo $id; ?>" class="guild_no" />
          <input type="hidden" value="<?php echo $gname; ?>" class="guild_gname" />
          <input type="hidden" value="<?php echo stripslashes($realm); ?>" class="guild_realm" />
          <input type="hidden" value="<?php echo $realmType; ?>" class="guild_realmtype" />
          <input type="hidden" value="<?php echo $showNo; ?>" class="guild_showno" />
          <input type="hidden" value="<?php echo $start+$showNo; ?>" class="guild_start" />
          <input type="hidden" value="<?php echo $linkback; ?>" class="guild_link" />
          Next
        </a>       
      <?php
    }
    echo '</td></tr></table>';
    if ($linkback == 1) {
				echo '<br/><p style="text-align:right;"><a href="http://timsworld.nfshost.com"><img src="'.$baseURL.'powered_by.png" style="border:0;"/></a></p>';
			}
	}
	
	
?>