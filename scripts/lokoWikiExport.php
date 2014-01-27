<?php
//Run this Script on time per Day!
require '../vendor/autoload.php';
require '../config/config.php';

$mediaWiki = new SSP\MediaWiki\MediaWikiConnect();
$mediaWiki->setAPIUrl("https://wiki.junge-piraten.de/w/api.php");
$mediaWiki->setLoginData("sspssp", "gelnhausen");
$mediaWiki->login();

$pdo = new Easy\PDOW\PDOW();
$pdo->connect($mysql["host"], $mysql["user"], $mysql["pw"], $mysql["db"], true);

$loko = new Jupis\LoKo();
$loko->setPDO($pdo);

$groups = $loko->listGroups(0);

#var_dump($groups);

$text = "<!-- Diese Seite wird durch einen Bot erstellt, manuelle änderungen werden überschrieben! !-->\r\n\r\n\r\n";
$text .= '{| class="wikitable sortable" style="width:100%"
! Typ !! Name !! Aktiv !! Bundesland !! Bemerkung
';
foreach($groups as $group)
{
	$bemerkung = "''Keine''";
	if($group["aktiv"]==1)
	{
		$people = $loko->searchPeople("group:".$group["id"]);
		if(count($people)==0)
		{
			$bemerkung = "'''Keine [[Loko/Ansprechpartner|Ansprechpartner]], bitte an loko@junge-piraten.de wenden!'''";
		}
	}
	$aktiv="Nein";
	if($group["aktiv"])
	{
		$aktiv = "Ja";
	}
	$text.='|-
| '.$group["typ"].'
| [['.$group["wiki"].'|'.$group["groupName"].']]
| '.$aktiv.'
| '.$group["bundesland"].'
| '.$bemerkung.'
|-';
}
$text .= '
|}';

#$mediaWiki->getEditToken("Benutzer:Sspssp/Test");
$mediaWiki->setPageText("Benutzer:Sspssp/Test", $text);
#$text = $mediaWiki->getPageText("Benutzer:Sspssp/Test");
#echo $text;