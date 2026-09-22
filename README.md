# Cocktail Bar — Coding Challenge

Platzierung von Gästegruppen an einer ringförmigen Cocktailbar mit `n` Barhockern.

Betritt eine Gruppe von `k` Gästen die Lounge, sucht das Programm `k` aufeinanderfolgende freie Plätze. Verlässt die 
Gruppe die Bar, werden alle `k` Plätze gleichzeitig frei. Findet sich keine ausreichend große Lücke, wird die Gruppe 
abgewiesen und vermerkt.

## Anforderungen

- PHP >= 8.4
- Composer

## Installation

```bash
composer install
```

## Starten

```bash
php bin/lounge.php
```

Das Programm fragt zunächst die Anzahl der Barhocker ab und startet dann eine interaktive
Eingabe für die Lounge-Managerin.

### Befehle

| Befehl | Wirkung |
|---|---|
| `enter <size>` | Gruppe mit `<size>` Gästen platzieren |
| `leave <groupId>` | Gruppe mit der ID `<groupId>` gehen lassen |
| `status` | Aktuelle Belegung, platzierte und abgewiesene Gruppen |
| `help` | Befehlsübersicht |
| `exit` | Programm beenden |

### Beispielsitzung

```
Enter bar size: 8

> enter 3
Group 1 seated
> enter 2
Group 2 seated
> status

Current seating:
-> [0: 1] [1: 1] [2: 1] [3: 2] [4: 2] [5: _] [6: _] [7: _] ->
Seated groups:
  Group-ID |       Size
         1 |          3
         2 |          2
> leave 1
Group 1 left
> enter 6
Group 3 seated
> status

Current seating:
-> [0: 3] [1: 3] [2: 3] [3: 2] [4: 2] [5: 3] [6: 3] [7: 3] ->
Seated groups:
  Group-ID |       Size
         2 |          2
         3 |          6
> exit
```

Die letzte Platzierung zeigt den Ringcharakter der Bar: Gruppe 3 sitzt auf den Plätzen
5, 6, 7, 0, 1, 2, also über die definierte Länge der Bar hinaus.

## Tests

```bash
vendor/bin/phpunit
```

## Lösungsansatz

**Datenstruktur.** `CircularArray` kapselt die gesamte Ring-Logik: ein Index wird per Modulo auf den gültigen Bereich 
abgebildet, auch negativ. Dadurch kann der Rest des Codes naiv `$index + $i` rechnen und muss sich um den Übergang 
nicht kümmern.

**Platzierung: Best-Fit.** Bei jeder Ankunft werden alle zusammenhängenden freien
Bereiche ermittelt (inkl. Übergang "Barende" zu "Baranfang" / Ringstruktur). Nach dieser Ermittlung wird der *kleinste*
passenste Bereich für die Gruppe zur Platzierung gewählt. Große Lücken bleiben damit für große Gruppen erhalten, statt 
von kleinen Gruppen zerschnitten zu werden.

Die Gruppe wird an den **Anfang** der Lücke gesetzt, sodass der verbleibende Rest der Lücke zusammenhängend bleibt und 
nicht in zwei unbrauchbare Teile zerfällt.

**Nicht umgesetzt/Features für die Zukunft:** 
- Umplatzieren von Gruppen; algorithmisch Hilfreich aber nicht unbedingt realitätsnah
- Warteschlange für abgewiesene Gruppen; werden aktuell nur gezählt und in `status` zum monitoring ausgewiesen

## Projektstruktur

```
bin/lounge.php              Entrypoint
src/Domain/Bar.php          Belegung, Lückensuche, Platzierung
src/Domain/Group.php        Gruppe (ID + Größe)
src/Application/            LoungeService: vergibt Gruppen-IDs, führt Buch über
                            anwesende und abgewiesene Gruppen
src/Command/                LoungeCommand: interaktive Eingabe
src/Support/CircularArray   Ring-Datenstruktur
tests/                      PHPUnit-Tests, gespiegelte Struktur
```
