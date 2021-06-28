<?php

define("THUMBDIR", getcwd()."/.selfgallery-cache");
define("LOWEND", 128);
define("MAXQUALITY", 100);
define("MINQUALITY", 5);

# If you can process images AND no thumbnail folder exists, create it.
!extension_loaded('gd') ?: is_dir(THUMBDIR) ?: mkdir(THUMBDIR, 0777, true);

function makePreview($img) {
	# If it's a .GIF, don't bother.
	if (substr($img, -4) === ".gif") return $img;

	$fname = substr($img, 0, strrpos($img, "."));
	$fhash = md5_file($img);

	# If the thumbnail already exists, deliver it.
	if (file_exists(THUMBDIR."/{$fhash}.webp")) return ".selfgallery-cache/{$fhash}.webp";

	# Preview synthesis
	$isize = getimagesize($img);
	$ifile = imagecreatefromstring(file_get_contents($img));

	# QUALITY FORMULA
	$quality = max(ceil(MAXQUALITY + -(max($isize[0], $isize[1]) ** 2) / 140 ** 2), MINQUALITY);
	(max($isize[0], $isize[1]) > LOWEND) ?: $quality = MAXQUALITY;

	imagewebp($ifile, THUMBDIR."/{$fhash}.webp", $quality);
	# Free up memory
	imagedestroy($ifile);
	return ".selfgallery-cache/{$fhash}.webp";
}

function genThumb($nimg)
{
	$name = substr($nimg, 0, strrpos($nimg, "."));
	# If you cannot process images, don't bother trying to make previews
	$img = extension_loaded('gd') ? makePreview($nimg) : $nimg;
	$size = getimagesize($img)[3];
    print("\t\t<li>");
    print("<img src='{$img}' alt='{$name}' onclick='window.open(\"{$nimg}\")' loading='lazy'><br>");
    print("<p>{$name}</p>");
    print("</li>\n");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Gallery of /<?=basename(__DIR__)?></title>
	<style>
		:root {
			--labelHeight: 1.5em;
			--imgArea: calc(100% - var(--labelHeight));
		}
		html {
			font-family: sans-serif;
			text-align: center;
			background: #bbb;
		}
		ul {
 			display: flex;
  			flex-wrap: wrap;
			list-style-type: none;
			padding: 0;
		}

		li {
  			height: 40vmin;
  			flex-grow: 1;
			margin: 0.5em;
			background: #ddd;
			padding-top: calc(var(--labelHeight) / 2);
			border-radius: calc(var(--labelHeight) / 4);
		}

		li:last-child {
			flex-grow: 10;
			background: #0000
		}

		li > img {
  			max-height: var(--imgArea);
  			min-width: var(--imgArea);
			max-width: 90vmin;
  			object-fit: contain;
  			vertical-align: bottom;
			cursor: pointer;
		}

		li > p {
			display: inline;
			font-size: 1.25em;
		}
	</style>
	<?=!file_exists("SelfGallery.css") ? "\n" : "<link rel='stylesheet' href='SelfGallery.css'>\n"?>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
	<h1>Gallery of /<?=basename(__DIR__)?></h1>
	<ul class='sgallery'>
<?php

$dir = array_diff(scandir("./"), array('..', '.'));
natcasesort($dir);

foreach ($dir as $file) {
    !exif_imagetype($file) ?: genThumb($file);
}

?>
		<li></li>
	</ul>
</body>
</html>