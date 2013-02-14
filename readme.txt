=== Nav2Me ===
Contributors: Stephan Gaertner
Donate link: http://www.stegasoft.de/wordpress-plugins/nav2me/
Tags: google,maps,navigation,route,routing,geo,routenplaner
Requires at least: 3.3
Tested up to: 3.5.1
Stable tag: 1.0


Einfaches, Google Maps (v.3) basiertes Routenplaner-Plugin

== Description ==
Nav2Me arbeitet nach der Google-Maps Version 3. Es ist kein API-Key notwendig, um dieses Plugin zu nutzen.
Bitte beachten Sie die Google Maps Nutzungs-Richtlinien.

= Funktionen: =
* Adresse oder Koordinaten (optional) als Standort-Angabe
* Kartengroesse, Zoom, Karten-Typ anpassbar
* Control-Elemente beliebig auswaehlbar
* Anzeigetext fuer Standort anpassbar
* Anordnung von Karte, Button etc. ueber Template definierbar
* Routenplanung zum definierten Standort
* Sprache: Deutsch, Englisch

== Installation ==
Entpacken Sie die ZIP-Datei und laden Sie den Ordner nav2me in das
Plugin-Verzeichnis von WordPress hoch: *wp-content/plugins/*.


Loggen Sie sich dann als Admin unter Wordpress ein.
Unter dem Menuepunkt "Plugins" koennen Sie Nav2Me
nun aktivieren. Sie finden dort auch den Untermenuepunkt "Nav2Me".
Durch Klick auf diesen Link gelangen Sie zur Administration des
Plugins.

Bei Nutzung im Multisite-Betrieb bitte beachten:
Das Plugin ueber das Netzwerk installieren aber nicht(!) fuer
alle Netzwerke aktivieren!
Das Plugin fuer jeden Blog separat aktivieren!

== Frequently Asked Questions ==
Zur Zeit keine Angaben.

== Changelog ==
= Version 1.0 (14.02.2013) =
* erste Version

== Upgrade Notice ==
Zur Zeit keine Angaben.

== Screenshots ==
Screenshots unter [Nav2Me SteGaSoft](http://www.stegasoft.de/wordpress-plugins/nav2me/nav2me-screenshots/)


== Other Notes ==

= Copyright =
Wordpress - Plugin "Nav2Me"
(c) 2013 by SteGaSoft, Stephan Gaertner
Www: <http://www.stegasoft.de/>
eMail: s. website


= Hinweis =
Ich versuche, Nav2Me fuer moeglichst viele Browser-Varianten zu entwickeln.
Bitte haben Sie aber Verstaendnis dafuer, dass aufgrund der teils kurzen Update-Intervalle
der Browser leider manchmal vorallem aeltere Versionen aus der Kompatibilitaetsliste rausfallen.


= Administration =
Deinstallieren:
Wenn Sie dieses Feld markieren, werden alle Daten und Tabellen nach Deaktivierung des Plugins geloescht.

Karte hinzufuegen:
Nach Klick auf diesen Link oeffnet sich ein Formular, in dem alle noetigen Karten-Daten eingetragen werden koennen.

Edit-Button (Stift):
Nach Klick auf diesen Button oeffnet sich ein Formular, in dem alle entspr. Karten-Daten ergaenzt oder geaendert werden koennen.

Delete-Button (Kreuz):
Nach Klick auf diesen Button wird die entspr. Karte geloescht.


= Karte einbinden =
Wenn Sie eine Karte erstellt haben, binden Sie diese einfach über den Shortcode [nav2me id=ID] in einen Beitrag oder eine Seite ein.
Ersetzen Sie dabei "ID" durch die entspr. Karten-ID.

Beachten Sie bitte, dass nicht mehrere Karten mit gleicher ID auf der selben Seite angezeigt werden!


= Style anpassen =
Ueber folgende Klassen bzw. IDs haben Sie Zugriff auf die einzelnen Plugin-Elemente:

.n2m_addr { ...}     => Klasse fuer Input-Textfeld (Startadresse) bei Routenplanung
#n2m_start_addr_ID   => ID des Input-Textfeldes (Startadresse) bei Routenplanung, ID entspricht der Karten-ID

.n2m_button          => Klasse fuer Input-Button (Startadresse) bei Routenplanung
#n2m_button_ID       => ID des Input-Buttons (Startadresse) bei Routenplanung, ID entspricht der Karten-ID
Sie koennen die Standardbeschriftung "OK" des Buttons aendern, indem Sie dem Shortcode einen Wert fuer "value" zuweisen.
Beispiel: [button value="start"] zeigt den Button mit der Beschriftung "start".

.nav2me_canvas       => Klasse fuer Div fuer Kartendarstellung
#nav2me_canvas_ID    => ID des Divs fuer Kartendarstellung, ID entspricht der Karten-ID

.n2m_txtbox          => Klasse fuer P-Absatzes fuer Info-Box (wird bei Nadel angezeigt)
#n2m_txtbox_ID       => ID des P-Absatzes fuer Info-Box, ID entspricht der Karten-ID

.nav2me_dirpanel     => Klasse fuer Div fuer Routen-Beschreibung
#nav2me_dirpanel_ID  => ID des Divs fuer Routen-Beschreibung, ID entspricht der Karten-ID
Die Hoehe und/oder/Breite des Divs fuer Routen-Beschreibung koennen Sie aendern, indem Sie dem Shortcode entspr. Werte
fuer "height" bzw. "width" zuweisen.
Beispiel: [dir width="350px" height="400px"] aendert die Groesse auf 350x400 Pixel.


= Gewaehrleistung =
Es gibt keine Gewaehrleistung fuer die Funktionalitaet von Nav2Me. Ausserdem uebernimmt der Autor/Programmierer
von Nav2Me keine Garantie fuer evtl. Datenverluste oder sonstige Beeintraechtigungen, die evtl. durch die
Nutzung von Nav2Me entstanden sind.
Die Nuzung von Nav2Me geschieht auf eigenes Risiko des jeweiligen Nutzers.
Beachten Sie ausserdem die aktuellen Google Maps Nutzungs-Richtlinien.



Viel Spass mit dem Plugin wuenscht
SteGaSoft, Stephan Gaertner