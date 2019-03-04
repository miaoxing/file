<?php $view->layout() ?>

<ul class="list">
  <?php foreach ($files as $file) : ?>
    <li>
      <a href="<?= $file['url'] ?>" class="list-item list-has-arrow">
        <h4 class="list-title">
          <?= $file['originalName'] ?>
        </h4>
        <i class="bm-angle-right list-arrow"></i>
      </a>
    </li>
  <?php endforeach ?>
</ul>
