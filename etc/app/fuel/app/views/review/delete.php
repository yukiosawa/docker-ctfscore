<?php if (!empty($errmsg)): ?>
  <div class='alert alert-danger'><?php echo $errmsg ?></div>
<?php endif; ?>

<?php if (isset($review)): ?>
  <div class='alert alert-success'><?php echo $msg ?></div>
  <?php echo render('review/_view', array('review' => $review)); ?>
<?php endif; ?>



