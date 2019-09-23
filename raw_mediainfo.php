<?php
$file = escapeshellarg($_GET['file']))
echo nl2br(shell_exec('mediainfo "'.$file.'"'));
