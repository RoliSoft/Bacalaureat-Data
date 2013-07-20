<?
genlist('12-07', 'http://static.bacalaureat.edu.ro/2012/rapoarte/rezultate/alfabetic/page_%d.html', 20019);
genlist('12-09', 'http://static.bacalaureat.edu.ro/2012/rapoarte_sept/rezultate/alfabetic/page_%d.html', 9945);

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
