<?php
// просто рисует тестовую страницу
$file = "view.html";
if(file_exists($file)) {
    readfile($file);
}

?>