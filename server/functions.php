<?php
function subs($text) {
	$imageFolder = "Images";
	$text = preg_replace('/img:(.*):/', '<img src="'.$imageFolder.'/$1" />', $text);
	$text = preg_replace('/img:(.*)/', '<img src="'.$imageFolder.'/$1" />', $text);
	# $imgTag = 'img:';
	# $tagStart = strpos($text, $imgTag);
	# if ($tagStart !== false) {
		# $tagEnd = strpos(substr($text, strlen($imgTag) + $tagStart), ":");
		# if ($tagEnd !== false) {
			# return preg_replace('/img:(.*):/', '<img src="$1"', $text);
		# }
	# }
	return $text;
}
