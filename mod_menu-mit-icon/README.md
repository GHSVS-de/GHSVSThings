# Override mit sauberem, separatem SPAN icon innerhalb des A-Links.

Auf das derzeitig komische Joomla 4 RC2 Verhalten, dass Titel nicht immer verschwinden, obwohl so im Menüeintrag gewählt, gehe ich hier nicht per Code ein.

## Bedingungen/Ablauf
- Eine Bild-CSS-Klasse [menu_image_css] ist im Menüpunkt eingetragen, also eine Icon-Klasse wie z.B. `icon-home`,
- aber KEIN Bild ausgewählt.
- Dann wird eine eigene, neue Item-Eigenschaft `$item->svg` mit einem Icon-SPAN gefüllt. Siehe `default.php`. https://github.com/GHSVS-de/GHSVSThings/blob/master/mod_menu-mit-icon/default.php#L14-L41
- Der dann bei der Link-Generierung mit übergebem wird. Siehe `default_component.php`.https://github.com/GHSVS-de/GHSVSThings/blob/master/mod_menu-mit-icon/default_component.php#L73-L80

Siehe auch Kommentare `#################### Override` in den beiden Codes.

Siehe auch https://forum.joomla.de/thread/13903-awesome-icon-als-men%C3%BC-titel-ohne-text/

Da die `$item->svg` in der `default.php` generiert werden, stehen sie für alle Menü-Items zur Verfügung und können in allen Unter-Layouts der Art `default_xyz.php` verwendet werden.
