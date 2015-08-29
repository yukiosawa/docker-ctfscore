<?php
$path = '/review/list';
if (Controller_Auth::is_admin_url()) $path = '/admin' . $path;
?>
<a href="<?php echo $path; ?>" class="btn btn-primary">戻る</a>

