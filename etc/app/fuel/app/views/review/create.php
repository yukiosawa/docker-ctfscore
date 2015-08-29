<?php if (!empty($errmsg)): ?>
  <div class='alert alert-danger'><?php echo $errmsg ?></div>
<?php endif; ?>

<?php
$action = $_SERVER['REQUEST_URI'];
echo render('review/_form', array('action' => $action, 'review' => $review, 'puzzles' => $puzzles));
?>


