<?
set_time_limit(0);

$files = scandir('data13');

foreach ($files as $file)
{
	if ($file == '.' || $file == '..') continue;
	
	print "Stripping data13/$file\n"; flush();
	
	$ged = file_get_contents('data13/'.$file);
	$ged = preg_replace('/<input.*?type="hidden".*?>/', '', $ged);
	file_put_contents('data13/'.$file, $ged);
}

print "Done!\n"; flush();
?>