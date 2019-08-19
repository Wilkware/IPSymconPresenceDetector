[![Version](https://img.shields.io/badge/Symcon-PHP--Modul-red.svg)](https://www.symcon.de/service/dokumentation/entwicklerbereich/sdk-tools/sdk-php/)
[![Product](https://img.shields.io/badge/Symcon%20Version-4.1%20%3E-blue.svg)](https://www.symcon.de/produkt/)
[![Version](https://img.shields.io/badge/Modul%20Version-1.2.20190818-orange.svg)](https://github.com/Wilkware/IPSymconPresenceDetector)
[![License](https://img.shields.io/badge/License-CC%20BY--NC--SA%204.0-green.svg)](https://creativecommons.org/licenses/by-nc-sa/4.0/)
[![StyleCI](https://github.styleci.io/repos/203022044/shield?style=flat)](https://github.styleci.io/repos/203022044)

# Toolmatic Presence Detector (Präsenzmelder)

Die **Toolmatic** Bibliothek ist eine kleine Tool-Sammlung im Zusammenhang mit HomeMatic/IP Geräten.  
Hauptsächlich beinhaltet sie kleine Erweiterung zur Automatisierung von Aktoren oder erleichtert das Steuern von Geäten bzw. bietet mehr Komfort bei der Bedienung.  
  
Der *Präsenzmelder* schaltet in Abhängigkeit von Bewegung und Helligkeit eine hinterlegte Variable bzw. führt ein Script aus.  

## Inhaltverzeichnis

1. [Funktionsumfang](#1-funktionsumfang)
2. [Voraussetzungen](#2-voraussetzungen)
3. [Installation](#3-installation)
4. [Einrichten der Instanzen in IP-Symcon](#4-einrichten-der-instanzen-in-ip-symcon)
5. [Statusvariablen und Profile](#5-statusvariablen-und-profile)
6. [WebFront](#6-webfront)
7. [PHP-Befehlsreferenz](#7-php-befehlsreferenz)
8. [Versionshistorie](#8-versionshistorie)

### 1. Funktionsumfang

* Übernimmt die Bewewgungsdaten vom Melder und verarbeitet bzw. reicht sie weiter.
* Einstellung eines Helligkeitswertes, ab welchem weiterverarbeitet werden soll.
* Zusätzlich bzw. ausschließlich kann ein Script ausgeführt werden.
* Über die Funktion _TPD_SetThreshold(id, wert)_ kann der Schwellwert der Helligkeit gesetzt werden.

### 2. Voraussetzungen

* IP-Symcon ab Version 4.1

### 3. Installation

* Über den Modul Store das Modul 'Toolmatic Presence Detector' installieren.
* Alternativ Über das Modul-Control folgende URL hinzufügen.  
`https://github.com/Wilkware/IPSymconPresenceDetector` oder `git://github.com/Wilkware/IPSymconPresenceDetector.git`

### 4. Einrichten der Instanzen in IP-Symcon

* Unter "Instanz hinzufügen" ist das 'Präsensmelder'-Modul (Alias: Bewegungsmelder) unter dem Hersteller '(Geräte)' aufgeführt.

__Konfigurationsseite__:

Name                | Beschreibung
------------------- | ---------------------------------
Bewegungsvariable   | Statusvariable eines Bewegungsmelders (true = Anwesend; false = Abwesend).
Helligkeitsvariable | Quellvariable, über welche die Helligkeit abgefragt werden kann, bei HmIP-SMI ist es ILLUMINATION.
Schwellwert         | Schwellwert, von 0 bis 100 Lux.
Schaltvariable      | Zielvariable, die bei hinreichender Bedingung geschalten wird (true).
Skript              | Script(auswahl), welches zum Einsatz kommen soll.
Statusvariable      | Schalter, ob die Statusvariable über HM-Befehl geschaltet werden soll oder einfach ein nur einfacher boolscher Switch gemacht werden soll.

### 5. Statusvariablen und Profile

Es werden keine zusätzlichen Profile benötigt.

### 6. WebFront

Es ist keine weitere Steuerung oder gesonderte Darstellung integriert.

### 7. PHP-Befehlsreferenz

`void TPD_SwitchState(int $InstanzID);`  
Schaltet bei hinreichender Bedingung die Schaltervariable an.  
Die Funktion liefert keinerlei Rückgabewert.  
Direkter Aufruf macht aber eigentlich kein Sinn.  

Beispiel:  
`TPD_SwitchState(12345);`  

`void TPD_SetThreshold(int $InstanzID, int wert);`  
Setzt den Helligkeits-Schwellwert auf den neuen Lux-'wert'.  
Die Funktion liefert true im Erfolgsfall.  

Beispiel:  
`TPD_SetThreshold(12345, 50);`  
Setzt den Schwellwert auf 50 Lux.

### 8. Versionshistorie

v1.2.20190818

* _NEU_: Umstellung für Module Store
* _FIX_: Dokumentation überarbeitet

v1.1.20170322

* _FIX_: Anpassungen für IPS Version 5

v1.0.20170125

* _NEU_: Initialversion

## Entwickler

* Heiko Wilknitz ([@wilkware](https://github.com/wilkware))

## Spenden

Die Software ist für die nicht kommzerielle Nutzung kostenlos, Schenkungen als Unterstützung für den Entwickler bitte hier:  
<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=8816166" target="_blank"><img src="https://www.paypalobjects.com/de_DE/DE/i/btn/btn_donate_LG.gif" border="0" /></a>

## Lizenz

[![Licence](https://licensebuttons.net/i/l/by-nc-sa/transparent/00/00/00/88x31-e.png)](https://creativecommons.org/licenses/by-nc-sa/4.0/)
