<?php
	/**********************************************************************
	 * 
	 * 					Other functions ...
	 * 
	 **********************************************************************/

	function get_attr($attr, $c){
		if(is_array($c)){
			foreach($c as $val){
				if(preg_match("#^\s*\+\s*duration\s*:\s*([0-9]{2}:[0-9]{2}:[0-9]{2})(.+)*$#iUs", $val)){
					return preg_replace("#^\s*\+\s*duration\s*:\s*([0-9]{2}:[0-9]{2}:[0-9]{2})(.+)*$#iUs", "$1", $val);
				}
			}
		}else{
			echo "error #r0002\n";
		}
	}
	function get_lang($lang){
		$langs=array('fr', 'en', 'es', 'de', 'es', 'nl', 'ru', 'fin', 'swe', 'por', 'ita', 'dan', 'nor');
		if(in_array($lang, $langs)){
			return $lang;
		}else{
			switch(strtolower($lang)){
				case 'norsk':
					return 'nor';
					break;
				case 'dansk':
					return "dan";
					break;
				case 'suomi':
					return "fin";
					break;
				case 'svenska':
					return "swe";
					break;
				case 'portugues':
					return "por";
					break;
				case 'italiano':
					return 'ita';
					break;
				case 'français':
					return 'fr';
					break;
				case 'francais':
					return 'fr';
					break;
				case 'french':
					return 'fr';
					break;
				case 'english':
					return 'en';
					break;
				case 'spanish':
					return 'es';
					break;
				case 'espagnol':
					return 'es';
					break;
				case 'espanol':
					return 'es';
					break;
				case 'allemand':
					return 'de';
					break;
				case 'deutsch':
					return 'de';
					break;
				case 'german':
					return 'de';
					break;
				case 'dutch':
					return 'nl';
					break;
				case 'nederlands':
					return 'nl';
					break;
				case 'russian':
					return 'ru';
					break;
				case 'japan':
					return 'jp';
					break;
				case 'japanese':
					return 'jp';
					break;
				default:
					echo "<b style='color:red' >Warning, cannot recognize lang \"$lang\" ! error : #r0005 </b><br />\n";
					return 'Unknown';
					break;	
			}
		}
	}
	function convert_lang_mkv($lang){
		switch($lang){
			case 'fr':
				return "fre";
				break;
			case 'en':
				return "eng";
				break;
			case 'de':
				return "ger";
				break;
			case 'es':
				return "spa";
				break;
			case 'nl':
				return "dut";
				break;
			case 'ru':
				return "rus";
				break;
			case 'jp':
				return 'jpn';
				break;
			default:
				return $lang;
				break;
		}
	}
	function get_lang_3($lang){
		$langs=array('fre', 'eng', 'ger', 'es', 'dut', 'rus', 'jpn');
		if(in_array($lang, $langs)){
			return $lang;
		}else{
			switch(strtolower($lang)){
				case 'français':
					return 'fre';
					break;
				case 'francais':
					return 'fre';
					break;
				case 'french':
					return 'fre';
					break;
				case 'english':
					return 'eng';
					break;
				case 'spanish':
					return 'spa';
					break;
				case 'espagnol':
					return 'spa';
					break;
				case 'allemand':
					return 'ger';
					break;
				case 'deutsch':
					return 'ger';
					break;
				case 'german':
					return 'ger';
					break;
				case 'dutch':
					return 'dut';
					break;
				case 'nederlands':
					return 'dut';
					break;
				case 'russian':
					return 'rus';
					break;
				case 'dan':
					return 'Danish';
					break;
				case 'nor':
					return 'Norwegian';
					break;
				case 'japan':
					return 'jpn';
					break;
				case 'japanese':
					return 'jpn';
					break;
				default:
					echo "<b style='color:red' >Warning, cannot recognize lang \"$lang\" ! error : #r0005 </b><br />\n";
					return $lang;
					break;	
			}
		}
	}
	function get_human_lang($lang){
		switch($lang){
			case 'fr':
				return "French";
				break;
			case 'en':
				return "English";
				break;
			case 'de':
				return "German";
				break;
			case 'es':
				return "Spanish";
				break;
			case 'nl':
				return "Nederlands";
				break;
			case 'ru':
				return "Russian";
				break;
			case 'fin':
				return "Finnish";
				break;
			case 'swe':
				return "Swedish";
				break;
			case 'por':
				return "Portuguese";
				break;
			case 'ita':
				return "Italian";
				break;
			case 'jp':
				return 'Japanese';
				break;
			default:
				return $lang;
				break;
		}
	}

	function round_ar($ar){
		$ar=round(floatval($ar), 2);
		if($ar<=1.38&&$ar>=1.32){
			return "1.33";
		}
		if($ar<=1.67&&$ar>=1.65){
			return "1.66";
		}
		if($ar<=1.79&&$ar>=1.77){
			return "1.78";
		}
		if($ar<=1.87&&$ar>=1.84){
			return "1.85";
		}
		if($ar<=2.36&&$ar>=2.34){
			return "2.35";
		}
		if($ar<=2.41&&$ar>=2.39){
			return "2.40";
		}
		return $ar;
	}
	function get_vb($time, $ab, $size){
		global $dir_this;
		$ab=$ab/1000;
		return intval(shell_exec($dir_this."getvb.py -s $size -a $ab $time"));
	}
function get_hb_infos($tabs){
	$rs=array();
	$titles=array();
	$last=0;

	/**********************************************************************
	 * 
	 * 					Parsing Handbrake Results ...
	 * 
	 **********************************************************************/

	foreach($tabs as $v){
		if(preg_match("#^\s*\+(.+)$#iUs", $v)){
			if(preg_match("#^\s*\+\s*title\s[0-9]{1,2}:$#iUs", $v)){
				$t=preg_replace("#^\s*\+\s*title\s([0-9]{1,2}):$#iUs", "$1", $v);
				$last=$t;
				$titles[$t]=array();
			}else{
				if($last>0){
					$titles[$last][]=$v;
				}else{
					return "Error #r0001\n<br />";
					exit(0);
				}
			}
		}
	}
	
	/**********************************************************************
	 * 
	 * 			Parsing infos of precedents titles ...
	 * 
	 **********************************************************************/
	 
	
	foreach($titles as $title => $title_content){
		$audio=array();
		$subs=array();
		$video=array();

		$cat=null;
		foreach($titles[$title] as $v){
			if(preg_match("#^\s*\+\s*size:\s*([0-9]{3,4}x[0-9]{3,4})\s*,\s*pixel aspect:\s*([0-9]+/[0-9]+),\s*display aspect:\s*([0-9]+(\.[0-9]+)?),\s*([0-9]+(\.[0-9]+)?)\s*fps(.+)*$#iUs", $v)){
				$tabs=preg_split("#\s*+\s*size:\s*([0-9]{3,4}x[0-9]{3,4})\s*,\s*pixel aspect:\s*([0-9]+/[0-9]+),\s*display aspect:\s*([0-9]+(\.[0-9]+)?),\s*([0-9]+(\.[0-9]+)?)\s*fps(.+)*$#iUs", $v, -1, PREG_SPLIT_DELIM_CAPTURE);
				$video['ar']=$tabs[3];
				$video['reso']=$tabs[1];
				$video['aspect']=$tabs[2];
				$video['fps']=$tabs[5];
			}else if(preg_match("#^\s*\+\s*autocrop\s*:\s*([0-9]{1,3})/([0-9]{1,3})/([0-9]{1,3})/([0-9]{1,3})\s*$#iUs", $v)){
				$video['crop_top']=0;
				$video['crop_bottom']=0;
				$video['crop_left']=0;
				$video['crop_right']=0;
				$tabs=preg_split("#^\s*\+\s*autocrop\s*:\s*([0-9]{1,3})/([0-9]{1,3})/([0-9]{1,3})/([0-9]{1,3})\s*$#iUs", $v, -1, PREG_SPLIT_DELIM_CAPTURE);
				$video['crop_top']=$tabs[1];
				$video['crop_bottom']=$tabs[2];
				$video['crop_left']=$tabs[3];
				$video['crop_right']=$tabs[4];
			}else if(preg_match("#^\s*\+\s*audio tracks\s*:(.+)?$#iUs", $v)){
				$cat="audio";
			}else if(preg_match("#^\s*\+\s*subtitle tracks\s*:(.+)?$#iUs", $v)){
				$cat="subs";
			}else if(preg_match("#^\s*\+\s*duration\s*:\s*([0-9]{2}:[0-9]{2}:[0-9]{2})(.+)*$#iUs", $v)){
				$tmp=preg_replace("#^\s*\+\s*duration\s*:\s*([0-9]{2}:[0-9]{2}:[0-9]{2})(.+)*$#iUs", "$1", $v);
				$tabs=explode(':', $tmp);
				if(count($tabs)==3){
					$video['duration']=convert_to_time(intval($tabs[0]), intval($tabs[1]), intval($tabs[2]));
					$video['human_duration']=$tmp;
				}else{
					echo "error #r0003\n";									
				}
			}else if($cat=="audio"){
				// checking audio infos ...
				if(preg_match("#^\s*\+\s*[0-9]{1,2},\s*(\S+)\s*\((.+)\)\s*\((.+)\)\s*\((.+)\)\s*,\s*([0-9]{5})Hz,\s*([0-9]{6,7})bps(.+)?$#iUs", $v)){
					$tabs=preg_split("#^\s*\+\s*([0-9]{1,2}),\s*(\S+)\s*\((.+)\)\s*\((.+)\)\s*\((.+)\)\s*,\s*([0-9]{5})Hz,\s*([0-9]{6,7})bps(.+)?$#iUs", $v, -1, PREG_SPLIT_DELIM_CAPTURE);
					$audio[$tabs[1]]=array(
							'lang' => get_lang($tabs[2]),
							'codec' => $tabs[3],
							'ch' => $tabs[4],
							'encoding' => $tabs[5],
							'hz' => $tabs[6],
							'bps' => $tabs[7]
						);
				}elseif(preg_match("#^\s*\+\s*[0-9]{1,2},\s*(\S+)\s*\((.+)\)\s*\((.+)\)\s*\((.+)\)\s*(,\s*([0-9]{5})Hz,\s*([0-9]{6,7})bps(.+)?)?$#iUs", $v)){
					$tabs=preg_split("#^\s*\+\s*([0-9]{1,2}),\s*(\S+)\s*\((.+)\)\s*\((.+)\)\s*\((.+)\)\s*(,\s*([0-9]{5})Hz,\s*([0-9]{6,7})bps(.+)?)?$#iUs", $v, -1, PREG_SPLIT_DELIM_CAPTURE);
					if($tabs.length>=8){
						$audio[$tabs[1]]=array(
							'lang' => get_lang($tabs[2]),
							'codec' => $tabs[3],
							'ch' => $tabs[4],
							'encoding' => $tabs[5],
							'hz' => $tabs[7],
							'bps' => $tabs[8]
						);
					}else{
						$audio[$tabs[1]]=array(
							'lang' => get_lang($tabs[2]),
							'codec' => $tabs[3],
							'ch' => $tabs[4],
							'encoding' => $tabs[5],
							'hz' => 0,
							'bps' => 0
						);
					}
				}
			}else if($cat=="subs"){
				// checking subtitles infos ...
				if(preg_match("#^\s*\+\s*[0-9]{1,2},\s*(\S+)\s*\((.+)\)\s*\((.+)\)\s*\((.+)\)\s*(.+)?$#iUs", $v)){
					$tabs=preg_split("#^\s*\+\s*([0-9]{1,2}),\s*(\S+)\s*\((.+)\)\s*\((.+)\)\s*\((.+)\)\s*(.+)?$#iUs", $v, -1, PREG_SPLIT_DELIM_CAPTURE);
					$subs[$tabs[1]]=array(
						'lang' => get_lang($tabs[2]),
						'encoding' => $tabs[5],
						'format' => $tabs[3],
						'codec' => $tabs[4]
					);
				}
			}
		}
		$rs[$title]['video']=$video;
		$rs[$title]['audio']=$audio;
		$rs[$title]['subs']=$subs;
	}
	
	// autoselect justify title ..
		
		$selected_title=0;
		$last=0;
		foreach($rs as $t => $c){
			if($c['video']['duration']>$last){
				$selected_title=$t;
				$last=$c['video']['duration'];
			}
		}
		$rs['selected']=$selected_title;
	return $rs;
}
function get_suggest_output($f){
	$f=basename($f);
	$ext=explode('.', $f);
	$ext=$ext[count($ext)-1];
	if(preg_match("#^(.+)\.([0-9]{4})\.([^\.]+)?\.#iUs", $f)){
		$tabs=preg_split("#^(.+)[\. ]([0-9]{4})[\. ]([^\.]+)?\.#iUs", $f, -1, PREG_SPLIT_DELIM_CAPTURE);
		$name=$tabs[1];
		$year=$tabs[2];
		$lang=$tabs[3];
		if(strtolower($lang)!="french"||strtolower($lang)!="truefrench"||strtolower($lang)!="vostfr"){
			$lang="FRENCH";
		}
		$quality="BRRiP"; // xxxRiP ?!
		if(stripos($f, "bluray")){
			$quality="BRRiP";
		}else if(stripos($f, "dvd")||stripos($f, ".iso")){
			$quality="DVDRiP";
		}
		return $name.'.'.$year.'.'.$lang.'.'.$quality.'.x264.AC3-CiRAR.mkv';
	}else{
		return "The title is not valid for a suggest ...";
	}
}
function get_rlz_src($f){
	$f=basename($f);
	$quality="";
	if(stripos($f, "bluray")){
		$quality="BluRay";
		if(stripos($f, "1080p")){
			$quality.=" 1080p";
		}else if(stripos($f, "720p")){
			$quality.=" 720p";
		}
	}else if(stripos($f, "dvd")||stripos($f, ".iso")){
		$quality="FULL DVD";
	}
	// team
	$team="";
	if(strpos($f, '-')>0){
		$tmp=explode('-', $f);
		$tmp=$tmp[count($tmp)-1];
		if(strpos($tmp, '.')>0){
			$tmp=explode('.', $tmp);
			$team=$tmp[0];
		}else{
			$team=$tmp;
		}
		$team=" ".$team;
	}
	return trim($quality.$team);
}
