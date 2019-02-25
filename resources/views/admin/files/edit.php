<?php $view->layout() ?>

<div class="page-header">
  <a class="btn float-right" href="<?= $url('admin/files/index') ?>">返回列表</a>

  <h1>
    微官网
    <small>
      <i class="fa fa-angle-double-right"></i>
      文件管理
    </small>
  </h1>
</div>
<!-- /.page-header -->

<div class="row">
  <div class="col-12">
    <!-- PAGE CONTENT BEGINS -->
    <form id="file-form" class="js-file-form form-horizontal" method="post" role="form">
      <div class="form-group">
        <label class="col-lg-2 control-label" for="category-id">
          栏目
        </label>

        <div class="col-lg-4">
          <select id="category-id" name="categoryId" class="form-control">
            <option value="">选择栏目</option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label class="col-lg-2 control-label" for="file">
          <span class="text-warning">*</span>
          选择文件
        </label>

        <div class="col-lg-4">
          <input type="file" class="file" name="file" id="file" data-rule-required="true" data-show-upload="false">
        </div>

        <label class="col-lg-6 help-text" for="file">
          支持txt, xml, pdf, zip, doc, ppt, xls, docx, pptx, xlsx格式
        </label>
      </div>

      <div class="form-group">
        <label class="col-lg-2 control-label" for="start-time">
          开始时间
        </label>

        <div class="col-lg-4">
          <div>
            <input type="text" class="form-control js-start-time" name="startTime" id="start-time">
          </div>
        </div>
      </div>

      <div class="form-group">
        <label class="col-lg-2 control-label" for="end-time">
          结束时间
        </label>

        <div class="col-lg-4">
          <div>
            <input type="text" class="form-control js-end-time" name="endTime" id="end-time">
          </div>
        </div>
      </div>

      <input type="hidden" name="id" id="id"/>

      <div class="clearfix form-actions form-group">
        <div class="offset-lg-2">
          <button class="btn btn-primary" type="submit">
            <i class="fa fa-check bigger-110"></i>
            保存
          </button>

          &nbsp; &nbsp; &nbsp;
          <a class="btn btn-default" href="<?= $url('admin/files') ?>">
            <i class="fa fa-undo bigger-110"></i>
            返回列表
          </a>
        </div>
      </div>
    </form>
  </div>
  <!-- PAGE CONTENT ENDS -->
</div><!-- /.col -->
<!-- /.row -->

<?= $block->js() ?>
<script>
  require([
    'form', 'plugins/app/js/validation',
    'plugins/admin/js/range-date-time-picker',
    'css!comps/bootstrap-fileinput/css/fileinput.min',
    'comps/bootstrap-fileinput/js/fileinput.min'
  ], function (form) {
    var categoryJson = <?= json_encode(wei()->category()->notDeleted()->withParent('file')->getTreeToArray()) ?>;
    form.toOptions($('#category-id'), categoryJson, 'id', 'name');

    var file = <?= $file->toJson(); ?>;

    $('.js-file-form')
      .loadJSON(file)
      .ajaxForm({
        url: $.url('admin/files/' + '<?= $file['id'] ? 'update' : 'create' ?>'),
        dataType: 'json',
        type: 'post',
        loading: true,
        success: function (result) {
          $.msg(result, function () {
            if (result.code > 0) {
              window.location = $.url('admin/files');
            }
          });
        }
      }).validate();

    // 开始结束时间使用日期时间范围选择器
    $('.js-start-time, .js-end-time').rangeDateTimePicker({
      dateFormat: 'yy-mm-dd'
    });

    var $caption = $('.js-file-form').find('.file-caption-name');
    $caption.html('<?= $file['id'] ? $file['path'] : '' ?>');

    // 不可编辑已有的文件路径
    <?php if (!$file->isNew()) : ?>
      $('#file').attr('disabled', true);
    <?php endif ?>
  });
</script>
<?= $block->end() ?>
