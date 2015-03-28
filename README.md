# ZIS
ZIS ist das zentrale Informationssystem bei der ZKK


Dieses System umfasst den automatischen Import von den Konferenz-Wikis, Datenexport (Text-Dateien) und mehr. Die Integration zur ZKK-App ist hier ebenfalls optimiert.

## Anpassungen für eigene Konferenz
Um dieses System für eine Konferenz an zu passen bzw. alleine zu nutzen, sind mehrere Anpassungen nötig. Diese umfassen:

* Setzen der Infos in der Config.php. (insbesondere Kennwort, Twitter-Tokens, etc.)
* Anpassung der UI (Z.B. alles mit ZaPF/KoMa/gemeinsam entfernen um nur die KIF zu behalten)
* Links zu den Datenquellen anpassen (beispielsweise der Link zum Tagungsheft und der Link zu der Wiki-Seite der Konferenz für den Import)
* Kleinkram, wie Impressum und Server-Konfiguration

## Server-Anforderungen
Die Anforderungen sind überschaubar. Ein einfacher Webserver mit Dateizugriff und PHP reicht aus. Entwickelt und getestet wurde das System mit enem Apache mit PHP, produktiv läuft es mit Anpassungen auf nginx. Diese Anpassungen umfassen die Übersetzung der .htaccess-Dateien in nginx-Config. (TOOD: nginx-Config hier erklären)

## Hinweise
* Dieses System wurde in kurzer Zeit "mal eben so" geschrieben. Daher ist die Dokumentation ausbaufähig. Dafür möchte ich mich entschuldigen.
* Die Twitter-Integration läuft über Codebird (ebenfalls auf github). Dieses ist ein GPL-Projekt und sollte im Auge behalten werden für eventuelle Updates.
* Das System ist sehr schnell gewachsen. Anfangs war es lediglich ein Systemzum Anzeigen von News. Daher ist die Programmstruktur nicht optimal. Insbesondere gibt es mit der data.php eine riesige Sammlung von Funktionen in einer Datei.
* Das ZIS nutzt zur Darstellung Bootstrap 2. Eine Konvertierung in Bootstrap 3 ist wünschenswert, aber derzeit nciht in Planung für die ZKK
* Die Dateien, die zur Datenverwaltung erstellt werden können von der ZKK App direkt genutzt werden. 

Bei Fragen gerne auch Arno (auX) bei der ZKK ansprechen.
