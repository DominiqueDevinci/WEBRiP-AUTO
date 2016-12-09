<?php
echo nl2br(shell_exec('mediainfo "'.urldecode(urldecode($_GET['file'])).'"'));
