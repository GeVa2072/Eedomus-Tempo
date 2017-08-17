<?php
/*************************************************************************************
**												
** Script PHP pour eedomus (toutes versions)
**
** Script qui permet d'afficher au format XML les données Tempo d'une zone prédéfinie :
**  - état Tempo du jour
**  - état Tempo du lendemain (Calculé par EDF entre 11h et 12h)
**  - décompte des jours Tempo
**
**************************************************************************************/
// URL des pages à parser
$URL_etat = "https://particulier.edf.fr/bin/edf_rc/servlets/ejptemponew?TypeAlerte=TEMPO&Date_a_remonter=";
$URL_histo = "https://particulier.edf.fr/bin/edf_rc/servlets/ejptempodaysnew?TypeAlerte=TEMPO"
// Date du jour au format demandé par l'API du site
$aujourdhui = date("Y-m-d");
// Période de conservation des données en cache (en mn)
$validite_cache = 60;
$validite_cache = $validite_cache * 60;
$time_TEMPO = loadVariable("TEMPO_time");
$heure=date("H")
if ( ($heure => 11 && $heure < 12)) || ((time() - $time_TEMPO) > $validite_cache)
{
	$json_etat_TEMPO = jsonToXML(httpQuery($URL_etat.$aujourdhui));
	$str_TEMPO_auj = xpath($json_etat_TEMPO,"/root/JourJ/Tempo");
	$str_TEMPO_dem = xpath($json_etat_TEMPO,"/root/JourJ1/Tempo");
	
	$json_histo_TEMPO = jsonToXML(httpQuery($URL_histo));
	$str_TEMPO_bleu = xpath($json_histo_TEMPO,"/root/PARAM_NB_J_BLEU");
	$str_TEMPO_blanc = xpath($json_histo_TEMPO,"/root/PARAM_NB_J_BLANC");
	$str_TEMPO_rouge = xpath($json_histo_TEMPO,"/root/PARAM_NB_J_ROUGE");

	$xml = '<?xml version="1.0" encoding="UTF-8"?>';
	$xml .= '<tempo>';
	$xml .= '<aujourdhui>'.$str_TEMPO_auj.'</aujourdhui>';
	$xml .= '<demain>'.$str_TEMPO_dem.'</demain>';
	$xml .= '<decompte>';
	$xml .= '<bleu>'.$str_TEMPO_bleu.'</bleu>';
	$xml .= '<blanc>'.$str_TEMPO_blanc.'</blanc>';
	$xml .= '<rouge>'.$str_TEMPO_rouge.'</rouge>';
	$xml .= '</decompte>';
	$xml .= '</tempo>';
	saveVariable("TEMPO_XML",$xml);
}
else
{
	// Rappel des valeurs précédemment sauvegardées
	$xml = loadVariable("TEMPO_XML");
}

echo $xml;
?>