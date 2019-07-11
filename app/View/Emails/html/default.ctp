<?php
foreach ($vars as $key => $line):
	echo '<p> ' .$key.': '. $line . "</p>\n";
endforeach;
?>
<br/>
<?= 'From domain: '.$domain; ?>
