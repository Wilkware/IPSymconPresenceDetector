# Präsenzmelder (Presence Detector)

[![Version](https://img.shields.io/badge/Symcon-PHP--Modul-red.svg)](https://www.symcon.de/service/dokumentation/entwicklerbereich/sdk-tools/sdk-php/)
[![Product](https://img.shields.io/badge/Symcon%20Version-6.0-blue.svg)](https://www.symcon.de/produkt/)
[![Version](https://img.shields.io/badge/Modul%20Version-3.1.20220909-orange.svg)](https://github.com/Wilkware/IPSymconPresenceDetector)
[![License](https://img.shields.io/badge/License-CC%20BY--NC--SA%204.0-green.svg)](https://creativecommons.org/licenses/by-nc-sa/4.0/)
[![Actions](https://github.com/Wilkware/IPSymconPresenceDetector/workflows/Check%20Style/badge.svg)](https://github.com/Wilkware/IPSymconPresenceDetector/actions)

Das Modul Präsenzmelder (Presence Detector) schaltet in Abhängigkeit von Bewegung(en) und Helligkeit ein oder mehrere Geräte ein bzw. führt ein Skript aus. Die Bewegungsdaten können dabei logisch verknüpft werden. Zusätzlich kann ein Schwellwert für die Helligkeit hinterlegt werden.  

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

* Über den Modul Store das Modul _Präsenzmelder_ installieren.
* Alternativ Über das Modul-Control folgende URL hinzufügen.  
`https://github.com/Wilkware/IPSymconPresenceDetector` oder `git://github.com/Wilkware/IPSymconPresenceDetector.git`

### 4. Einrichten der Instanzen in IP-Symcon

* Unter 'Instanz hinzufügen' ist das _Präsensmelder_-Modul (Alias: _Bewegungsmelder_) unter dem Hersteller '(Geräte)' aufgeführt.

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
MotionVariable       | ID(s) der Bewegungsvariable(n), Zugriff im Skript via _$_IPS['MotionVariable']_
BrightnessVariable   | ID der Helligkeitsvariable, Zugriff im Skript via _$_IPS['BrightnessVariable']_
SwitchVariable       | ID(s) der Schaltvariable(s), Zugriff im Skript via _$_IPS['SwitchVariable']_
ThresholdValue       | Wert von Schwellwert, Zugriff im Skript via _$_IPS['ThresholdValue']_

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

v3.1.20220909

* _NEU_: Zeitfenster wird jetzt bei allen Verknüpfungen berücksichtigt
* _NEU_: Zeitfenster jetzt bis 2 Min einstellbar
* _FIX_: Doppelte Ausführung bei OR gefixt
* _FIX_: Bibliotheken nachgezogen

v3.0.20220320

* _NEU_: Zweiten Bewegungsmelder hinzugefügt
* _NEU_: Logische Verknüpfung der Bewegungsmelder eingeführt
* _NEU_: Kompatibilität auf IPS 6.0 hoch gesetzt
* _NEU_: Bibliotheks- bzw. Modulinfos vereinheitlicht
* _NEU_: Konfigurationsdialog überarbeitet (v6 Möglichkeiten genutzt)
* _NEU_: Auswahl des Helligkeitsschwellwertes erweitert
* _NEU_: Konfiguration der Zeitsteuerung überarbeitet
* _NEU_: Umschalten zwischen einem oder mehreren schaltbaren Geräten
* _NEU_: Eine reine boolesche Schaltvariable (ein Gerät) wird automatisch erkannt
* _NEU_: Referenzieren der Gerätevariablen hinzugefügt (sicheres Löschen)
* _FIX_: Funktion `TPD_SwitchState` wegen neuer Prozessverarbeitung entfernt
* _FIX_: Interne Bibliotheken erweitert und vereinheitlicht
* _FIX_: Markdown der Dokumentation überarbeitet

v2.1.20210723

* _NEU_: Konfigurationsformular überarbeitet und vereinheitlicht
* _NEU_: Schwellwert(Helligkeit) kann über Webfront(RequestAction) gesetzt bzw. manipuliert werden
* _FIX_: Funktion `TPD_Threshold` wegen Verwendung von IPS_SetProperty entfernt
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

* _NEU_: Skriptaufruf auf IPS_RunScriptEx umgestellt (Variablenübergabe)
* _FIX_: Debugausgabe war fehlerhaft bei hinterlegtem Skript
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

[![GitHub](https://img.shields.io/badge/GitHub-@wilkware-181717.svg?style=for-the-badge&logo=github)](https://wilkware.github.io/)

## Spenden

Die Software ist für die nicht kommerzielle Nutzung kostenlos, über eine Spende bei Gefallen des Moduls würde ich mich freuen.

[![PayPal](https://img.shields.io/badge/PayPal-spenden-00457C.svg?style=for-the-badge&logo=paypal)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=8816166)

## Lizenz

Namensnennung - Nicht-kommerziell - Weitergabe unter gleichen Bedingungen 4.0 International

[![Licence](https://img.shields.io/badge/License-CC_BY--NC--SA_4.0-EF9421.svg?style=for-the-badge&logo=creativecommons)](https://creativecommons.org/licenses/by-nc-sa/4.0/)
