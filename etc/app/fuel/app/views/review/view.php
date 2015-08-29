<?php if (!empty($errmsg)): ?>
  <div class='alert alert-danger'><?php echo $errmsg ?></div>
<?php else: ?>
  <?php echo render('review/_view', array('review' => $review)); ?>
<?php endif; ?>

