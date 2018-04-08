<?php
function subs($text) {
	$imageFolder = "Images";
	$soundFolder = "Sounds";
	$text = preg_replace('/img:(.*):/', '<img src="'.$imageFolder.'/$1" />', $text);
	$text = preg_replace('/sound:(.*):/', '<audio controls><source type="audio/wav" src="'.$soundFolder.'/$1" /></audio>', $text);
	$text = preg_replace('/img:(.*)/', '<img src="'.$imageFolder.'/$1" />', $text);
	$text = preg_replace('/sound:(.*)/', '<audio controls><source type="audio/wav" src="'.$soundFolder.'/$1" /></audio>', $text);
	return $text;
}
