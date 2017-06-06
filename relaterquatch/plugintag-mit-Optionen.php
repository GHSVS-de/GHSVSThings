<?php
/*
Wie man bspw. aus einem Plugin-Tag

{fotorama: cat=888,title=3,nav=pips}

die Optionen auslesen kann, egal in welcher Reihenfolge eingegeben oder auch gar nicht eingegeben.

2017-06-02 Re:Later für https://forum.joomla.de/index.php/Thread/3978-Joomla-plugin-get-parameters-from-syntax/
*/

// Testtext erstellen mit gültigen und 1 ungültigem Tag (cat fehlt):
$article = new stdClass;
$article->text ='
Nöcke pöcke 

{fotorama:cat=95,nav=dots, title=1} 

blah blubber. 

{fotorama:    nav=linse  ,  nav=purpsps , title = 4556} 

Elle pelle haus verpalle.

{fotorama: cat=888,title=3,nav=pips  } 

rense pemse 

{fotorama:nav=linse, cat   =  963} 

Ende.
';

// Schnelle Prüfung erspart ggf. preg_-Methoden:
if (strpos($article->text, '{fotorama:') !== false)
{

	// Muster, das den Optionen-Block extrahiert:
	$regex_cat = '#{fotorama:\s*([^}]+)[^}]*}#';

	// Relevante Musterfunde in Array $matches:
	if (preg_match_all($regex_cat, $article->text, $matches, PREG_SET_ORDER))
	{

		// Für jedes gefundene, korrekte Muster Schleife:
		// $matchArray[0]: Der gesamte gefundene Block {fotorama: usw.}. String.
		// $matchArray[1]: Die extrahierten Optionen. String.
		foreach($matches as $i => $matchArray)
		{
			$html = '';

			// Grobe Prüfung: Optionen grundlegend OK:
   			if (
				($Optionen = trim($matchArray[1]))
				&& strpos($Optionen, '=') !== false
			){

				// Init Ablage-Array für Optionen und Werte des aktuellen fotorama:
				$OptionenUndWerte = array();

				// Trenne Optionen-Paare an Kommas:
				$Optionen = explode(',', $Optionen);

				// Schleife über alle so gefundenen Option=Wert-Paare: 
				foreach ($Optionen as $Option)
				{

					// Trenne Option=Wert-Paar an =. Trenne nur am ersten =.:
					$Option = explode('=', $Option, 2);

					// Generelle Prüfung des so gewonnenen Option=Wert-Paares.
					// Zugleich Säuberung von Leerzeichen.
					if (
						count($Option) == 2 &&
						($opt = trim($Option[0])) &&
						($wert = trim($Option[1]))
					){
						
						// Alles gut? Dann Option=Wert-Paar festhalten in Ablage-Array.
						$OptionenUndWerte[$opt] = $wert;
					}
				}

				// Etwas gesammelt?
				if ($OptionenUndWerte)
				{
					
					// Joomla-Registry-Objekt erspart ggf. viele isset-Abfragen:
					$OptionenUndWerte = new Joomla\Registry\Registry($OptionenUndWerte);

					// catid auslesen.
					$catid = (int) $OptionenUndWerte->get('cat');

					// Wenn catid brauchbar (numerisch ungleich 0):
					if ($catid)
					{

						// Wenn nav=irgendwas eingetragen wurde nimm das für $navigation.
						// Ansonsten den Default-Wert 'dots'
						$navigation = $OptionenUndWerte->get('nav', 'dots');

						// Wenn title=irgendwas eingetragen wurde nimm das für $title.
						// Ansonsten den Default-Wert '1'
						$title = $OptionenUndWerte->get('title', '1');

						// Generiere $html:
						$html = " [[Fotorama $i::" . 'Catid des Fotorama ist: ' . $catid . "! ";
						$html .= 'Navigation des Fotorama ist: ' . $navigation . "! ";
						$html .= 'Title des Fotorama ist: ' . $title . "! ]] ";

					}

				}

			}
			
			// Ersetzt auch Muster mit fehlender cat, wo $html = ''.
			// Verwende ressourcenschonendes str_replace statt preg_replace.
			// Das können wir, weil $matchArray[0] der exakte String des Fundes ist, also z.B.
			//  {fotorama:    cat   =  963,    nav=linse} mit Leerzeichen und Kram.
			$article->text = str_replace($matchArray[0], $html, $article->text);

		} 

	}

	// Abschließend noch mal ressourcenschonender testen, ob noch unvollständige Tags enthalten.
	// Wobei im obigen Kontext wohl unnütig, weil das Muster oben zu hart formuliert ist, so, dass
	// hier gar nichts gefunden werden kann. Also: Ggf. neues Putzmuster kreieren.
	if (strpos($article->text, '{fotorama:') !== false)
	{
		// Dann erst diese mit preg_ entfernen. 
		$article->text = preg_replace($regex_cat, '', $article->text);
	}

	// Probeausgabe
 	echo $article->text;exit;
}
