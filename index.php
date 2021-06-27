<?php

function genThumb($img)
{
    $name = substr($img, 0, strrpos($img, "."));
    print("\t\t<li>");
    print("<img src='{$img}' alt='{$name}' loading='lazy'><br>");
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