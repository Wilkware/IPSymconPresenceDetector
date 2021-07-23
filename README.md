# Toolmatic Presence Detector (Präsenzmelder)

[![Version](https://img.shields.io/badge/Symcon-PHP--Modul-red.svg)](https://www.symcon.de/service/dokumentation/entwicklerbereich/sdk-tools/sdk-php/)
[![Product](https://img.shields.io/badge/Symcon%20Version-5.2-blue.svg)](https://www.symcon.de/produkt/)
[![Version](https://img.shields.io/badge/Modul%20Version-2.1.20210723-orange.svg)](https://github.com/Wilkware/IPSymconPresenceDetector)
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
* Der Helligkeitsschwellwert kann bei Bedarf auch über das Webfront gesetzt werden.

### 2. Voraussetzungen

* IP-Symcon ab Version 5.2

### 3. Installation

* Über den Modul Store das Modul *Toolmatic Presence Detector* installieren.
* Alternativ Über das Modul-Control folgende URL hinzufügen.  
`https://github.com/Wilkware/IPSymconPresenceDetector` oder `git://github.com/Wilkware/IPSymconPresenceDetector.git`

### 4. Einrichten der Instanzen in IP-Symcon

* Unter 'Instanz hinzufügen' ist das *Präsensmelder*-Modul (Alias: *Bewegungsmelder*) unter dem Hersteller _'(Geräte)'_ aufgeführt.

__Konfigurationsseite__:

> Geräte ...

Name                | Beschreibung
------------------- | ---------------------------------
Schaltvariable      | Zielvariable, die bei hinreichender Bedingung geschalten wird (true).
Bewegungsvariable   | Statusvariable eines Bewegungsmelders (true = Anwesend; false = Abwesend).
Helligkeitsvariable | Quellvariable, über welche die Helligkeit abgefragt werden kann, bei HmIP-SMI ist es ILLUMINATION.
Schwellwert         | Schwellwert, von 0 bis 100 Lux.

> Zeitsteuerung ...

Name                | Beschreibung
------------------- | ---------------------------------
Zeitplan            | Wochenprogram, welches den Bewegungsmelder zeitgesteuert aktiviert bzw. deaktiviert.

> Erweiterte Einstellungen ...

Name                         | Beschreibung
-----------------------------| ---------------------------------
Skript                       | Script(auswahl), welches zum Einsatz kommen soll.
Checkpox Statusvariable      | Schalter, ob die Statusvariable über HM-Befehl geschaltet werden soll oder einfach ein nur einfacher boolscher Switch gemacht werden soll.
Checkbox Schwellwert         | Schalter, ob eine Zustandsvariable für den Helligkeitsschwellwert angelegt werden soll.

Einem hinterlegtem Script werden folgende Konfigurationswerte mitgegeben:  
  
Parameter           | Beschreibung
------------------- | ---------------------------------
MotionVariable      | ID der Bewegungsvariable, Zugriff im Skript via *$_IPS['MotionVariable']*
BrightnessVariable  | ID der Helligkeitsvariable, Zugriff im Skript via *$_IPS['BrightnessVariable']*
SwitchVariable      | ID der Schaltvariable, Zugriff im Skript via *$_IPS['SwitchVariable']*
ThresholdValue      | Wert von Schwellwert, Zugriff im Skript via *$_IPS['ThresholdValue']*

### 5. Statusvariablen und Profile

Die Statusvariablen/Timer werden automatisch angelegt. Das Löschen einzelner kann zu Fehlfunktionen führen.

Name                   | Typ       | Beschreibung
---------------------- | --------- | ----------------
Helligkeitsschwellwert | Integer   | Helligkeitswert ab welchem geschalten werden soll

Folgende Profile werden angelegt:

Name                   | Typ       | Beschreibung
---------------------- | --------- | ----------------
TPD.Threshold          | Integer   | Werte: Immer, 5, 10, 15, 20, 25, 30, 40, 45, 50, 75 und 100 Lux

### 6. WebFront

Die erzeugten Variablen können direkt ins Webfront verlingt werden.  

### 7. PHP-Befehlsreferenz

```php
void TPD_SwitchState(int $InstanzID);
```

Schaltet bei hinreichender Bedingung die Schaltervariable an.  
Die Funktion liefert keinerlei Rückgabewert.  
Direkter Aufruf macht aber eigentlich kein Sinn.  

__Beispiel__: `TPD_SwitchState(12345);` Schaltet die in der Instanz hinterlegte Schaltvariable.

### 8. Versionshistorie

v2.1.20210723

* _NEU_: Konfigurationsformular überarbeitet und vereinheitlicht
* _NEU_: Schwellwert(Helligkeit) kann über Webfront(RequestAction) gesetzt bzw. manipuliert werden
* _FIX_: Funktionen `TPD_Threshold` wegen Verwendung von IPS_SetProperty entfernt
* _FIX_: Übersetzungen nachgezogen
* _FIX_: Interne Bibliotheken überarbeitet und vereinheitlicht
* _FIX_: Debug Meldungen überarbeitet
* _FIX_: Dokumentation überarbeitet

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

Seit nunmehr über 10 Jahren fasziniert mich das Thema Haussteuerung. In den letzten Jahren betätige ich mich auch intensiv in der IP-Symcon Community und steuere dort verschiedenste Skript und Module bei. Ihr findet mich dort unter dem Namen @pitti ;-)

[![GitHub](https://img.shields.io/badge/GitHub-@wilkware-blueviolet.svg?logo=github)](https://wilkware.github.io/)

## Spenden

Die Software ist für die nicht kommzerielle Nutzung kostenlos, über eine Spende bei Gefallen des Moduls würde ich mich freuen.

[![PayPal](https://img.shields.io/badge/PayPal-spenden-blue.svg?logo=paypal)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=8816166)

## Lizenz

[![Licence](https://licensebuttons.net/i/l/by-nc-sa/transparent/00/00/00/88x31-e.png)](https://creativecommons.org/licenses/by-nc-sa/4.0/)
