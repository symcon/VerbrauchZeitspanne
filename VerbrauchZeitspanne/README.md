# VerbrauchZeitspanne

### Inhaltsverzeichnis

1. [Funktionsumfang](#1-funktionsumfang)
2. [Voraussetzungen](#2-voraussetzungen)
3. [Software-Installation](#3-software-installation)
4. [Einrichten der Instanzen in IP-Symcon](#4-einrichten-der-instanzen-in-ip-symcon)
5. [Statusvariablen und Profile](#5-statusvariablen-und-profile)
6. [WebFront](#6-webfront)
7. [PHP-Befehlsreferenz](#7-php-befehlsreferenz)

### 1. Funktionsumfang

* Berechnet für eine Zeitspanne den Verbrauch anhand der Aggregation der ausgewählten Quellvariable.
* Bei Änderung der Zeitspanne wird der Verbrauch neu ermittelt
* Verschiedene Detailgerade für Start- und End-Datum
* Einstellbares Intervall für die Aktualisierung der Berechnung

### 2. Voraussetzungen

- IP-Symcon ab Version 4.2

### 3. Software-Installation

* Über den Module Store das Modul Verbrauch in Zeitspanne installieren.
* Alternativ über das Module Control folgende URL hinzufügen:
`https://github.com/symcon/VerbrauchZeitspanne`  

### 4. Einrichten der Instanzen in IP-Symcon

- Unter "Instanz hinzufügen" ist das 'Verbrauch in Zeitspanne'-Modul unter dem Hersteller '(Gerät)' aufgeführt.  

__Konfigurationsseite__:

Name                               | Beschreibung
---------------------------------- | ---------------------------------
Quelle                             | Quellvariable, die für Verbrauch genutzt werden soll
Detailgrad                         | Legt fest wie genau der Start- und Endzeitpunkt festgelegt werden kann (Datum, Uhrzeit, Datum/Uhrzeit)
Intervall zum Aktualisieren nutzen | Wenn aktiv, wird ein Intervall zum Aktualisieren der Berechnung genutzt
Intervall                          | Das Intervall in Minuten, in dem die Berechnung aktualisiert wird

### 5. Statusvariablen und Profile

Die Statusvariablen/Kategorien werden automatisch angelegt. Das Löschen einzelner kann zu Fehlfunktionen führen.

##### Statusvariablen

Name        | Typ     | Beschreibung
----------- | ------- | ----------------
Start       | Integer | Start-Datum/Start-Zeit für den Verbrauch (Sekunden werden nicht beachtet)
Ende        | Integer | End-Datum/End-Zeit für den Verbrauch (Sekunden werden nicht beachtet)
Verbrauch   | Float   | Verbrauch zwischen Start- und End-Datum

##### Profile

Es werden keine zusätzlichen Profile hinzugefügt.

### 6. WebFront

Über das WebFront werden die Variablen angezeigt. Es ist keine weitere Steuerung oder gesonderte Darstellung integriert.

### 7. PHP-Befehlsreferenz

`boolean VIZ_Calculate(integer $InstanzID);`  
Berechnet den Verbrauch zwischen Start- und End-Datum neu.
Die Funktion liefert keinerlei Rückgabewert.  
Beispiel:  
`VIZ_Calculate(12345);`
