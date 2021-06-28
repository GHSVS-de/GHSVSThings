# Override mit sauberem SPAN icon.
## Bedingungen/Ablauf
- Eine Bild-CSS-Klasse [menu_image_css] ist im Menüpunkt eingetragen, also eine Icon-Klasse wie z.B. `icon-home`,
- aber KEIN Bild ausgewählt.
- Dann wird eine Variable `$item->svg` mit einem Icon-SPAN gefüllt. Siehe `default.php`
- Der dann bei der Link-Generierung mit übergebem wird. Siehe `default_component.php`

Siehe auch Kommentare `#################### Override` in den beiden Codes.

Siehe auch https://forum.joomla.de/thread/13903-awesome-icon-als-men%C3%BC-titel-ohne-text/
