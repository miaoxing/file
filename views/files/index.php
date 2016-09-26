<?php $view->layout() ?>

<ul class="list list-link">
    <?php foreach ($files as $file) : ?>
    <li>
      <a href="<?= $file['url'] ?>" class="list-item has-feedback">
        <h4 class="list-heading">
            <?= $file['originalName'] ?>
        </h4>
        <i class="bm-angle-right list-feedback"></i>
      </a>
    </li>
    <?php endforeach ?>
</ul>
