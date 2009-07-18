=== Nav2Me ===
Contributors: Stephan Gaertner
Donate link: http://www.stegasoft.de
Tags: geo, route, planer, google ,maps ,googlemaps , routeplaner, routenplaner, routing
Requires at least: 2.3
Tested up to: 2.7
Stable tag: 0.4


== Description ==
Mit Nav2Me koennen Sie ganz einfach einen auf Google Maps basierenden Routenplaner in Ihren Blog einbauen.
Sie benoetigen dazu nur einen API-Key, den Sie hier erhalten:
http://code.google.com/intl/de-DE/apis/maps/signup.html


== Copyright ==
Wordpress - Plugin "Nav2Me"
Ver. 0.4 (05/2009)
(c) 2009 by SteGaSoft, Stephan Gärtner
Www: http://www.stegasoft.de
eMail: s. website

Das Plugin darf kostenlos genutzt werden. Es besteht
jedoch kein Anspruch auf Funktionalitaet. Auch wird
jegliche Haftung bei Problemen ausgeschlossen.
Die Nutzung geschieht auf eigene Verantwortung.

Bitte beachten Sie das Copyright von Google Maps.


== Historie ==
Version 0.4 (26.05.2009)
  Das eigene Icon kann beliebig skaliert werden. Somit kann es einfach an
  die Kartengroesse angepasst werden.
  Kartentyp (Strassenkarte, Satellit, Hybrid) kann eingestellt werden. 

Version 0.3 (24.05.2009)
  Es koennen optional auch die Laenge und Breite eingegeben werden.
  Diese haben eine hoehere Prioritaet als die Adresse, d.h. die Nadel
  wird ggf. auf die Long/Lat - Koordinaten gesetzt.
  Ein eigenes Icon kann statt der Standard-Nadel benutzt werden.
  Map-/Type-Controls koennen ein-/ausgeblendet werden.
  MapControl-Typ kann ausgewaehlt werden.
  Zoom-Level kann eingestellt werden.

Version 0.2 (21.05.2009)
  die Load-Funktion wird vom Plugin an die entsprechende Stelle
  eingebaut, die manuelle Aenderung der Datei "header.php" ist
  somit nicht mehr noetig.

Version 0.1
  Erste Version fuer Wordpress bis V2.7



== Installation ==
Laden Sie den Ordner "nav2me" einfach in das Pluginverzeichnis
von Wordpress hoch. 

Aendern Sie die Zugriffs-/Schreibrechte folgender Dateien auf 777 (chmod):
 - vars.js
 - template_form.htm
 - template_page.htm


Loggen Sie sich dann als Admin unter Wordpress ein. 
Unter dem Menuepunkt "Plugins" koennen Sie Nav2Me
nun aktivieren. Sie finden dort auch den Untermenuepunkt "Nav2Me".
Durch Klick auf diesen Link gelangen Sie zur Administration des
Plugins.

Tragen Sie auf der Seite/im Artikel in der/dem die Karte erscheinen
soll folgenden Tag ein: [nav2me].
Dieser Tag wird spaeter durch die Karte ersetzt.


== Administration ==
Die Administration sollte eigentlich selbsterklaerend sein.
Fuellen Sie einfach die entsprechenden Felder aus.

In der Spalte "zeigen" koennen Sie festlegen, welche Daten auf
der Karte angezeigt werden sollen. Fuellen Sie die Felder aber auf 
jeden Fall aus, damit spaeter die Route berechnet werden kann.

Sie koennen optional die Laenge und Breite angeben. Beide muessen
vorhanden sein, damit diese Parameter wirksam werden. Die Adresse
hat dann keinen Einfluss mehr auf die Positionsbestimmung.
Geben Sie die Koordinaten in dezimaler Form oder in Grad, Minute und
Sekunde ein. In letzterem Fall trennen Sie die Einheiten bitte jeweils
mit einem Leerzeichen. Die Werte werden dann automatisch in dezimale 
Werte umgerechnet.

Template "Form":
hier koennen Sie bestimmen, wie das Adressfeld und der Button
eingebaut werden sollen. Sie koenne dazu HTML-Code verwenden.
Setzen Sie die Platzhalter an die entsprechende Stelle ein.
Den Button-Text koennen Sie wie folgt erzeugen:
Bsp.:
[btn=Route berechnen] erzeugt einen Button mit der Aufschrift
"Route Berechnen".

Template "Seite":
hier gilt das gleiche wie für "Form". Sie koenne [map] oder [dir]
auch weglassen. Dann wird nur die Karte oder nur die Wegbeschreibung
angezeigt. Das [form]-Tag sollten Sie aber nicht vergessen, sonst fehlt
die Moeglichkeit zur Routenberechnung!

Sie koenne auch ein eigenes Icon statt der Standard-Nadel nutzen. Kopieren
Sie dazu einfach ein entspr. Bild (JPG, PNG oder GIF) in den Ordner "Icons".
Sie koennen dann das Bild in der Adminebene auswaehlen.
Die untere Bildmitte wird spaeter automatisch auf die entspr. Koordinate gesetzt.
Mit "skalieren auf" koennen Sie Ihr Icon skalieren. 100% entspricht der
Originalgroesse des Icons. 


Ueber die Datei "styles.css" kann das Aussehen angepasst werden.
Im Einzelnen sind das folgende Elemente:
 - form1
 - fromAddress
 - route_btn
 - directions
 - map

Die restlichen Formatierungen beziehen sich auf die Plugin-Administration
und sollten nicht geaendert werden.


Vergessen Sie zum Schluss das Speichern nicht!


Viel Spass mit dem Plugin wuenscht
SteGaSoft, Stephan Gaertner