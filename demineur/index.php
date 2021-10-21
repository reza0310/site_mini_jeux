<?php
set_include_path($_SERVER['DOCUMENT_ROOT']."/jeux");
echo(str_replace("uno", "active", str_replace("%php%", file_get_contents("page.html"), file_get_contents("header.html", true))));
?>