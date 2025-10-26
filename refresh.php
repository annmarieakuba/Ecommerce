<?php
// Force refresh and redirect to main index
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
header("Location: index.php?" . time());
exit();
?>
