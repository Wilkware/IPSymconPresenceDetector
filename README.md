# Toolmatic Presence Detector (Präsenzmelder)

[![Version](https://img.shields.io/badge/Symcon-PHP--Modul-red.svg)](https://www.symcon.de/service/dokumentation/entwicklerbereich/sdk-tools/sdk-php/)
[![Product](https://img.shields.io/badge/Symcon%20Version-5.1%20%3E-blue.svg)](https://www.symcon.de/produkt/)
[![Version](https://img.shields.io/badge/Modul%20Version-2.0.20200422-orange.svg)](https://github.com/Wilkware/IPSymconPresenceDetector)
[![License](https://img.shields.io/badge/License-CC%20BY--NC--SA%204.0-green.svg)](https://creativecommons.org/licenses/by-nc-sa/4.0/)
[![Actions](https://github.com/Wilkware/IPSymconPresenceDetector/workflows/Check%20Style/badge.svg)](https://github.com/Wilkware/IPSymconPresenceDetector/actions)

Die *Toolmatic Bibliothek* ist eine kleine Tool-Sammlung im Zusammenhang mit HomeMatic/IP Geräten.  
Hauptsächlich beinhaltet sie kleine Erweiterung zur Automatisierung von Aktoren oder erleichtert das Steuern von Geräten bzw. bietet mehr Komfort bei der Bedienung.  
  
Der *Präsenzmelder* schaltet in Abhängigkeit von Bewegung und Helligkeit eine Variable bzw. führt ein Script aus.  

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
* Hinterlegung eines Wochenplans zum gezielten Aktivieren bzw. Deaktivierung des Melders
* Zusätzlich bzw. ausschließlich kann ein Script ausgeführt werden.
* Über die Funktion _TPD_SetThreshold(id, wert)_ kann der Schwellwert der Helligkeit gesetzt werden (Hinweis beachten).

### 2. Voraussetzungen

* IP-Symcon ab Version 5.1

### 3. Installation

* Über den Modul Store das Modul *Toolmatic Presence Detector* installieren.
* Alternativ Über das Modul-Control folgende URL hinzufügen.  
`https://github.com/Wilkware/IPSymconPresenceDetector` oder `git://github.com/Wilkware/IPSymconPresenceDetector.git`

### 4. Einrichten der Instanzen in IP-Symcon

* Unter 'Instanz hinzufügen' ist das *Präsensmelder*-Modul (Alias: *Bewegungsmelder*) unter dem Hersteller '(Geräte)' aufgeführt.

__Konfigurationsseite__:

Name                | Beschreibung
------------------- | ---------------------------------
Bewegungsvariable   | Statusvariable eines Bewegungsmelders (true = Anwesend; false = Abwesend).
Helligkeitsvariable | Quellvariable, über welche die Helligkeit abgefragt werden kann, bei HmIP-SMI ist es ILLUMINATION.
Schwellwert         | Schwellwert, von 0 bis 100 Lux.
Schaltvariable      | Zielvariable, die bei hinreichender Bedingung geschalten wird (true).
Skript              | Script(auswahl), welches zum Einsatz kommen soll.
Zeitplan            | Wochenprogram, welches den Bewegungsmelder zeitgesteuert aktiviert bzw. deaktiviert.
Statusvariable      | Schalter, ob die Statusvariable über HM-Befehl geschaltet werden soll oder einfach ein nur einfacher boolscher Switch gemacht werden soll.

Einem hinterlegtem Script werden folgende Konfigurationswerte mitgegeben:  
  
Parameter           | Beschreibung
------------------- | ---------------------------------
MotionVariable      | ID der Bewegungsvariable, Zugriff im Skript via *$_IPS['MotionVariable']*
BrightnessVariable  | ID der Helligkeitsvariable, Zugriff im Skript via *$_IPS['BrightnessVariable']*
SwitchVariable      | ID der Schaltvariable, Zugriff im Skript via *$_IPS['SwitchVariable']*
ThresholdValue      | Wert von Schwellwert, Zugriff im Skript via *$_IPS['ThresholdValue']*

### 5. Statusvariablen und Profile

Es werden keine zusätzlichen Profile benötigt.

### 6. WebFront

Es ist keine weitere Steuerung oder gesonderte Darstellung integriert.

### 7. PHP-Befehlsreferenz

```php
void TPD_SwitchState(int $InstanzID);
```

Schaltet bei hinreichender Bedingung die Schaltervariable an.  
Die Funktion liefert keinerlei Rückgabewert.  
Direkter Aufruf macht aber eigentlich kein Sinn.  

__Beispiel__: `TPD_SwitchState(12345);` Schaltet die in der Instanz hinterlegte Schaltvariable.

```php
void TPD_SetThreshold(int $InstanzID, int wert);
```

Setzt den Helligkeits-Schwellwert auf den neuen Lux-'wert'.  
Die Funktion liefert true im Erfolgsfall.  
  
**_HINWEIS_**: **Durch das Aufrufen der Funktion wird die Konfiguration neu geschrieben, dieses kann bei gleichzeitig geöffneter Konfiguration (Konfigurationsformular) zu Verlust noch nicht gespeicherter Veränderungen führen.**

__Beispiel__: `TPD_SetThreshold(12345, 50);` Setzt den Schwellwert auf 50 Lux.

### 8. Versionshistorie

v2.0.20200422

* _NEU_: Zeitplan hinzugefügt
* _NEU_: Unterstützung für die Erstellung eines Wochenplans
* _FIX_: Interne Bibliotheken überarbeitet
* _FIX_: Dokumentation überarbeitet

v1.3.20191105

* _NEU_: Scriptaufruf auf IPS_RunScriptEX umgestellt (Variablenübergabe)
* _FIX_: Debugausgabe war fehlerhaft bei hinterlegtem Script
* _FIX_: Dokumentation überarbeitet

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

[![License](https://img.shields.io/badge/Einfach%20spenden%20mit-PayPal-blue.svg)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=8816166)

## Lizenz

[![Licence](https://licensebuttons.net/i/l/by-nc-sa/transparent/00/00/00/88x31-e.png)](https://creativecommons.org/licenses/by-nc-sa/4.0/)
