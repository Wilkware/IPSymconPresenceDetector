# Toolmatic Presence Detector (Präsenzmelder)

[![Version](https://img.shields.io/badge/Symcon-PHP--Modul-red.svg)](https://www.symcon.de/service/dokumentation/entwicklerbereich/sdk-tools/sdk-php/)
[![Product](https://img.shields.io/badge/Symcon%20Version-5.2-blue.svg)](https://www.symcon.de/produkt/)
[![Version](https://img.shields.io/badge/Modul%20Version-3.0.20220320-orange.svg)](https://github.com/Wilkware/IPSymconPresenceDetector)
[![License](https://img.shields.io/badge/License-CC%20BY--NC--SA%204.0-green.svg)](https://creativecommons.org/licenses/by-nc-sa/4.0/)
[![Actions](https://github.com/Wilkware/IPSymconPresenceDetector/workflows/Check%20Style/badge.svg)](https://github.com/Wilkware/IPSymconPresenceDetector/actions)

Der *Präsenzmelder* schaltet in Abhängigkeit von Bewegung und Helligkeit eine Variable bzw. führt ein Skript aus.  

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

* Einfaches oder logisches Verknüpfen von Bewegungsdaten (ein oder zwei Bewegungsmelder).
* Einstellung eines Helligkeitswertes, ab welchem weiterverarbeitet werden soll.
* Hinterlegung eines Wochenplans zum gezielten Aktivieren bzw. Deaktivierung des Melders
* Ein oder mehrere Geräte einschalten
* Zusätzlich bzw. ausschließlich kann ein Skript ausgeführt werden.
* Der Helligkeitsschwellwert kann bei Bedarf auch über das Webfront gesetzt werden.

### 2. Voraussetzungen

* IP-Symcon ab Version 6.0

### 3. Installation

* Über den Modul Store das Modul *Presence Detector* installieren.
* Alternativ Über das Modul-Control folgende URL hinzufügen.  
`https://github.com/Wilkware/IPSymconPresenceDetector` oder `git://github.com/Wilkware/IPSymconPresenceDetector.git`

### 4. Einrichten der Instanzen in IP-Symcon

* Unter 'Instanz hinzufügen' ist das *Präsensmelder*-Modul (Alias: *Bewegungsmelder*) unter dem Hersteller '(Geräte)' aufgeführt.

__Konfigurationsseite__:

> Sensoren ...

Name                 | Beschreibung
-------------------- | ---------------------------------
1' Bewegungsvariable | Statusvariable des primären Bewegungsmelders (true = Anwesend; false = Abwesend).
Verknüpfung          | Logische Verknüpfung der Bewegungsdaten (UND, ODER, NICHT).
2' Bewegungsvariable | Statusvariable des sekundären Bewegungsmelders (true = Anwesend; false = Abwesend).
Helligkeitsvariable  | Quellvariable, über welche die Helligkeit abgefragt werden kann, bei HmIP-SMI ist es ILLUMINATION.
Schwellwert          | Schwellwert, von Immer(0) bis 500 Lux.

> Zeitsteuerung ...

Name                 | Beschreibung
-------------------- | ---------------------------------
Zeitplan             | Wochenprogram, welches den Bewegungsmelder zeitgesteuert aktiviert bzw. deaktiviert.
ZEITPLAN HINZUFÜGEN  | Button zum Erzeugen und Hinzufügen eines Wochenprogrammes.

> Geräte ...

Name                 | Beschreibung
-------------------- | ---------------------------------
Geräteauswahl        | 'Ein Gerät' oder 'Mehrere Geräte' - Umschalten zwischen Variablenauswahl und Variablenliste.
Schaltvariable*      | Zielvariable, die bei hinreichender Bedingung geschalten wird (true). *[Ein Gerät]
Schaltvariablen*     | Zielvariablen, welche alle bei hinreichender Bedingung geschalten werden (true). *[Mehrere Geräte]
Skript               | Skript(auswahl), welches gleichzeitig ausgeführt werden soll.

> Erweiterte Einstellungen ...

Name                 | Beschreibung
---------------------| ---------------------------------
Checkbox Schwellwert | Schalter, ob eine Zustandsvariable für den Helligkeitsschwellwert angelegt werden soll.

Einem hinterlegtem Skript werden folgende Konfigurationswerte mitgegeben:  
  
Parameter            | Beschreibung
-------------------- | ---------------------------------
MotionVariable       | ID(s) der Bewegungsvariable(n), Zugriff im Skript via *$_IPS['MotionVariable']*
BrightnessVariable   | ID der Helligkeitsvariable, Zugriff im Skript via *$_IPS['BrightnessVariable']*
SwitchVariable       | ID(s) der Schaltvariable(s), Zugriff im Skript via *$_IPS['SwitchVariable']*
ThresholdValue       | Wert von Schwellwert, Zugriff im Skript via *$_IPS['ThresholdValue']*

### 5. Statusvariablen und Profile

Die Statusvariablen/Timer werden automatisch angelegt. Das Löschen einzelner kann zu Fehlfunktionen führen.

Name                   | Typ       | Beschreibung
---------------------- | --------- | ----------------
Helligkeitsschwellwert | Integer   | Helligkeitswert ab welchem geschalten werden soll

Folgende Profile werden angelegt:

Name                   | Typ       | Beschreibung
---------------------- | --------- | ----------------
TPD.Threshold          | Integer   | Werte: Immer, 5, 10, 15, 20, 25, 30, 40, 45, 50, 75, 100, 150, 200, 250 und 500 Lux

### 6. WebFront

Die erzeugten Variablen können direkt ins Webfront verlinkt werden.  

### 7. PHP-Befehlsreferenz

Ein direkter Aufruf von öffentlichen Funktionen ist nicht notwendig!

### 8. Versionshistorie

v3.0.20220320

* *NEU*: Zweiten Bewegungsmelder hinzugefügt
* *NEU*: Logische Verknüpfung der Bewegungsmelder eingeführt
* *NEU*: Kompatibilität auf IPS 6.0 hoch gesetzt
* *NEU*: Bibliotheks- bzw. Modulinfos vereinheitlicht
* *NEU*: Konfigurationsdialog überarbeitet (v6 Möglichkeiten genutzt)
* *NEU*: Auswahl des Helligkeitsschwellwertes erweitert
* *NEU*: Konfiguration der Zeitsteuerung überarbeitet
* *NEU*: Umschalten zwischen einem oder mehreren schaltbaren Geräten
* *NEU*: Eine reine boolesche Schaltvariable (ein Gerät) wird automatisch erkannt
* *NEU*: Referenzieren der Gerätevariablen hinzugefügt (sicheres Löschen)
* *FIX*: Funktion `TPD_SwitchState` wegen neuer Prozessverarbeitung entfernt
* *FIX*: Interne Bibliotheken erweitert und vereinheitlicht
* *FIX*: Markdown der Dokumentation überarbeitet

v2.1.20210723

* *NEU*: Konfigurationsformular überarbeitet und vereinheitlicht
* *NEU*: Schwellwert(Helligkeit) kann über Webfront(RequestAction) gesetzt bzw. manipuliert werden
* *FIX*: Funktion `TPD_Threshold` wegen Verwendung von IPS_SetProperty entfernt
* *FIX*: Übersetzungen nachgezogen
* *FIX*: Interne Bibliotheken überarbeitet und vereinheitlicht
* *FIX*: Debug Meldungen überarbeitet
* *FIX*: Dokumentation überarbeitet

v2.0.20200422

* *NEU*: Zeitplan hinzugefügt
* *NEU*: Unterstützung für die Erstellung eines Wochenplans
* *FIX*: Interne Bibliotheken überarbeitet
* *FIX*: Dokumentation überarbeitet

v1.3.20191105

* *NEU*: Skriptaufruf auf IPS_RunScriptEx umgestellt (Variablenübergabe)
* *FIX*: Debugausgabe war fehlerhaft bei hinterlegtem Skript
* *FIX*: Dokumentation überarbeitet

v1.2.20190818

* *NEU*: Umstellung für Module Store
* *FIX*: Dokumentation überarbeitet

v1.1.20170322

* *FIX*: Anpassungen für IPS Version 5

v1.0.20170125

* *NEU*: Initialversion

## Entwickler

Seit nunmehr über 10 Jahren fasziniert mich das Thema Haussteuerung. In den letzten Jahren betätige ich mich auch intensiv in der IP-Symcon Community und steuere dort verschiedenste Skript und Module bei. Ihr findet mich dort unter dem Namen @pitti ;-)

[![GitHub](https://img.shields.io/badge/GitHub-@wilkware-181717.svg?style=for-the-badge&logo=github)](https://wilkware.github.io/)

## Spenden

Die Software ist für die nicht kommerzielle Nutzung kostenlos, über eine Spende bei Gefallen des Moduls würde ich mich freuen.

[![PayPal](https://img.shields.io/badge/PayPal-spenden-00457C.svg?style=for-the-badge&logo=paypal)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=8816166)

## Lizenz

Namensnennung - Nicht-kommerziell - Weitergabe unter gleichen Bedingungen 4.0 International

[![Licence](https://img.shields.io/badge/License-CC_BY--NC--SA_4.0-EF9421.svg?style=for-the-badge&logo=creativecommons)](https://creativecommons.org/licenses/by-nc-sa/4.0/)
