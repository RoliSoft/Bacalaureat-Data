<?
set_time_limit(0);

file_put_contents('13-09.js', "[\n");
file_put_contents('13-09.csv', chr(239).chr(187).chr(191)."Nume,Poziția în ierarhie pe județ,Poziția în ierarhie pe țară,Unitatea de învățământ,Județul,Promotie anterioară,Forma învățământ,Specializare,\"Limba și literatura română\r\nCompetențe\",\"Limba și literatura română\r\nScris\",\"Limba și literatura română\r\nContestație\",\"Limba și literatura română\r\nNota finală\",Limba și literatura maternă,\"Limba și literatura maternă\r\nCompetențe\",\"Limba și literatura maternă\r\nScris\",\"Limba și literatura maternă\r\nContestație\",\"Limba și literatura maternă\r\nNota finală\",Limba modernă studiată,\"Limba modernă studiată\r\nNota\",Disciplina obligatorie a profilului,\"Disciplina obligatorie a profilului\r\nNota\",\"Disciplina obligatorie a profilului\r\nContestație\",\"Disciplina obligatorie a profilului\r\nNota finală\",Disciplina la alegere,\"Disciplina la alegere\r\nNota\",\"Disciplina la alegere\r\nContestație\",\"Disciplina la alegere\r\nNota finală\",Competențe digitale,Media,Rezultatul final\r\n");
file_put_contents('13-09.xml', "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<results>\n");

$files = scandir('data13-09');

print "Sorting files...\n"; flush();

usort($files, function($a, $b)
	{
		$a = ext($a, '/(\d+)/');
		$b = ext($b, '/(\d+)/');
		
		if ($a == $b)
		{
			return 0;
		}
		
		return ($a < $b) ? -1 : 1;
	});

foreach ($files as $file)
{
	if ($file == '.' || $file == '..') continue;
	
	print "Parsing data13-09/$file\n"; flush();
	
	$ged = file_get_contents('data13-09/'.$file);
	$ged = s3(ext($ged, '/ged\(\){return "([^"]+)/'));
	preg_match_all('/<tr class=\"tr\d\".*?>(.*?)<\/tr>/', $ged, $mc);
	
	for ($i = 0; $i < count($mc[1]); $i += 2)
	{
		preg_match_all('/"\]="([^"]+)/', $mc[1][$i], $mk);
		preg_match_all('/<td.*?class=td.*?>(?:&nbsp;)?(.*?)<\/td>/', $mc[1][$i], $td);
		preg_match_all('/<td.*?class=td.*?>(?:&nbsp;)?(.*?)<\/td>/', $mc[1][$i + 1], $tf);
		
		$s = new stdClass;
		$s->name     = trim2(preg_replace('/(?:^|\s)([A-Z])(?:\s|$)/', ' \1. ', str_replace(' - ', '-', mb_convert_case(preg_replace('/\.([a-z])[\.\s]\s*/i', '. \1. ', str_replace('<br>', '', mb_strtolower(preg_replace('/([A-Z])\s*\-\s*([A-Z])/', '\1 - \2', $mk[1][0]), 'UTF-8'))), MB_CASE_TITLE, 'UTF-8'))));
		$s->posCn    = (int)trim2(ext($td[1][2], '/>(\d+)</'));
		$s->posRo    = (int)trim2(ext($td[1][3], '/>(\d+)</'));
		$s->school   = trim2(ucwords2(mb_strtolower(ext($td[1][4], '/>(?:&nbsp;)?(.+)</'), 'UTF-8')));
		$s->county   = trim2(ext($td[1][5], '/>(?:&nbsp;)?(.+)</'));
		$s->retry    = $td[1][6] == 'DA';
		$s->eduType  = trim2($td[1][7]);
		$s->class    = trim2($td[1][8]);
		$s->romOral  = trim2(str_replace('Nivel ', '', str_replace('Utilizator ', '', ucwords(str_replace('&nbsp', '', $td[1][9]))))); if ($s->romOral == 'Neprezentat') $s->romOral = null;
		$s->romExam  = max(0, (float)$td[1][10]);
		$s->romCont  = trim2($td[1][11]); $s->romCont = $s->romCont > 0 ? (float)$s->romCont : null;
		$s->romGrade = max(0, (float)$td[1][12]);
		$s->tngLang  = trim2(ucwords(str_replace('Limba si literatura ', '', $td[1][13])));
		$s->tngOral  = trim2(str_replace('Nivel ', '', str_replace('Utilizator ', '', ucwords(str_replace('&nbsp', '', $tf[1][0]))))); if ($s->tngOral == 'Neprezentat') $s->tngOral = null;
		$s->tngExam  = $s->tngLang != '' ? max(0, (float)$tf[1][1]) : null;
		$s->tngCont  = $s->tngLang != '' ? ((float)$tf[1][2] > 0 ? (float)$tf[1][2] : null) : null;
		$s->tngGrade = $s->tngLang != '' ? max(0, (float)$tf[1][3]) : null;
		$s->intLang  = trim2(str_replace('Limba ', '', ucwords($td[1][14])));
		$s->intGrade = trim2(str_replace('Neprezentat', '', str_replace('&nbsp', '', $td[1][15]))); if(empty($s->intGrade)) $s->intGrade = null; if(empty($s->intGrade)) $s->intGrade = null;
		$s->reqName  = trim2($td[1][16]);
		$s->reqExam  = max(0, (float)$tf[1][4]);
		$s->reqCont  = trim2($tf[1][5]); $s->reqCont = $s->reqCont > 0 ? (float)$s->reqCont : null;
		$s->reqGrade = max(0, (float)$tf[1][6]);
		$s->selName  = trim2($td[1][17]);
		$s->selExam  = max(0, (float)$tf[1][7]);
		$s->selCont  = trim2($tf[1][8]); $s->selCont = $s->selCont > 0 ? (float)$s->selCont : null;
		$s->selGrade = max(0, (float)$tf[1][9]);
		$s->itSkillz = trim2(str_replace('Nivel ', '', str_replace('Utilizator ', '', ucwords(str_replace('&nbsp', '', $td[1][18]))))); if ($s->itSkillz == 'Neprezentat') $s->itSkillz = null;
		$s->grade    = (float)trim2($mk[1][1]);
		$s->passed   = $mk[1][2] == 'Reuşit';
		
		$g = array($s->romGrade, $s->reqGrade, $s->selGrade);
		if ($s->tngLang != null)
		{
			$g[] = $s->tngGrade;
		}
		
		$g2 = array_sum($g) / count($g);
		if ($s->grade != null && abs($s->grade - $g2) > 0.011) // rounding error up to this point
		{
			$s->computedGrade = (float)number_format($g2, 2);
			$s->wtfIsThisShit = $s->grade - $g2; // interesting at this point
			print 'Grade difference detected for '.$s->name.': '.$s->grade.' should be '.$s->computedGrade."\n";
		}
		else
		{
			$s->grade = (float)number_format($g2, 2);
		}
		
		file_put_contents("13-09.js", json_encode($s).",\n", FILE_APPEND);
		file_put_contents("13-09.csv", join(',', array(esc($s->name), esc($s->posCn), esc($s->posRo), esc($s->school), esc($s->county), esc($s->retry ? 'Da' : 'Nu'), esc($s->eduType), esc($s->class), esc($s->romOral), esc($s->romExam), esc($s->romCont), esc($s->romGrade), esc($s->tngLang), esc($s->tngOral), esc($s->tngExam), esc($s->tngCont), esc($s->tngGrade), esc($s->intLang), esc($s->intGrade), esc($s->reqName), esc($s->reqExam), esc($s->reqCont), esc($s->reqGrade), esc($s->selName), esc($s->selExam), esc($s->selCont), esc($s->selGrade), esc($s->itSkillz), esc($s->grade), esc($s->passed ? 'Reușit' : 'Respins')))."\r\n", FILE_APPEND);
		file_put_contents("13-09.xml", "<result>".genxml($s)."</result>\n", FILE_APPEND);
	}
}

file_put_contents('13-09.js', "]\n", FILE_APPEND);
file_put_contents('13-09.xml', "</results>\n", FILE_APPEND);

print "Done!\n"; flush();

function ext($data, $regex)
{
	preg_match($regex, $data, $match);
	return @$match[1];
}

function s0($a1, $a2, $a3)
{
	$a1 = str_replace($a2, '_', $a1);
	$a1 = str_replace($a3, $a2, $a1);
	$a1 = str_replace('_', $a3, $a1);
	
	return $a1;
}

function s1($a1, $a2)
{
	return s0($a1, strtolower($a2), strtoupper($a2));
}

function s2($a1)
{
	foreach (["a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z"] as $l)
	{
		$a1 = s1($a1, $l);
	}
	
	return $a1;
}

function s3($a1)
{
	$a1 = s0($a1, "0", "O");
	$a1 = s0($a1, "1", "l");
	$a1 = s0($a1, "5", "S");
	$a1 = s0($a1, "m", "s");
	$a1 = s2($a1);
	
	return mb_convert_encoding($a1, 'UTF-8', 'BASE64');
}

function ucwords2($str)
{
	$chlst = '"\'- ';
	$next = true;
	$max = mb_strlen($str, 'UTF-8');
	
	for ($i = 0; $i < $max; $i++)
	{
		if (mb_strpos($chlst, mb_substr($str, $i, 1, 'UTF-8'), 0, 'UTF-8') !== false)
		{
			$next = true;
		}
		else if ($next)
		{
			$next = false;
			$str = mb_substr($str, 0, $i, 'UTF-8').mb_strtoupper(mb_substr($str, $i, 1, 'UTF-8'), 'UTF-8').mb_substr($str, $i + 1, null, 'UTF-8');
		}
	}
	
	return $str;
}

function trim2($str)
{
	$str = trim($str);
	
	return $str == '' ? null : $str;
}

function esc($str)
{
	if (strpos($str, ',') !== false)
	{
		return '"'.str_replace('"', '""', $str).'"';
	}
	else
	{
		return $str;
	}
}

function genxml($array)
{
	$xml = '';
	
	if (is_object($array) && get_class($array) == 'stdClass')
	{
		$array = get_object_vars($array);
	}
	
	if (is_array($array) || is_object($array)) 
	{
		foreach ($array as $key => $value)
		{
			if (empty($value))
			{
				$xml .= '<'.$key.'/>';
			}
			else
			{
				$xml .= '<'.$key.'>'.genxml($value).'</'.$key.'>';
			}
		}
	}
	else
	{
		if (is_bool($array))
		{
			$array = $array ? 'true' : 'false';
		}
		
		$xml = htmlspecialchars($array, ENT_QUOTES);
	}
	
	return $xml;
}
?>