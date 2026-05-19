<?php
$old = $_SESSION['form_old'] ?? [];
unset($_SESSION['form_old']);
?>
// Then in the input: value="<?= $old['name'] ?? '' ?>"
