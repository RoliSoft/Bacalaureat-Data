<?
genlist('13-09', 'http://bacalaureat.edu.ro/2013/rapoarte_sept/rezultate/alfabetic/page_%d.html', 8809);

function genlist($fn, $url, $cnt)
{
	$lst = '';

	for ($i = 1; $i <= $cnt; $i++)
	{
		$lst .= sprintf($url, $i)."\n";
	}

	file_put_contents($fn.'.lst', $lst);
}
?>
