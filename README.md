# Magento Cash-Back-Plugin

## Ablauf

Gibt der Nutzer eine Bestellung auf, wird ein "pending cashback"-Eintrag angelegt
Dieser beinhaltet unter anderem den Zeitpunkt der Bestellung sowie den gutzuschreibenden Betrag

Wird diese Bestellung als "complete" abgeschlossen, wird dieser Zeitpunkt
als "completed_at" in den Eintrag eingefügt

Täglich führt ein Cron-Job das "CashbackUpdate" durch, in dem alle Einträge ermittelt werden, die älter als die Storno-Frist sind (z.B. älter als 2 Wochen) und die über einen "completed_at"-Zeitstempel verfügen

Alternativ können auch jene Einträge ermittelt werden, deren "completed_at"-Zeitstempel länger als die Storno-Frist zurückliegt

Die entsprechenden Guthaben werden den jeweiligen Nutzern gutgeschrieben, anschließend wird der Eintrag gelöscht

Bei Stornierung des Auftrags wird der "pending cashback"-Eintrag ebenfalls gelöscht