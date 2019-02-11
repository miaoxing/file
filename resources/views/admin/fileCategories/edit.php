<?php $view->layout() ?>

<div class="page-header">
  <a class="btn pull-right btn-default" href="<?= $url('admin/file-categories') ?>">返回列表</a>

  <h1>
    微官网
    <small>
      <i class="fa fa-angle-double-right"></i>
      文件栏目管理
    </small>
  </h1>
</div>
<!-- /.page-header -->

<div class="row">
  <div class="col-12">
    <!-- PAGE CONTENT BEGINS -->
    <form id="category-form" class="form-horizontal" method="post" role="form">

      <div class="form-group">
        <label class="col-lg-2 control-label" for="parent-id">
          所属栏目
        </label>

        <div class="col-lg-4">
          <select name="parentId" id="parent-id" class="form-control">
            <option value="file">根栏目</option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label class="col-lg-2 control-label" for="name">
          <span class="text-warning">*</span>
          名称
        </label>

        <div class="col-lg-4">
          <input type="text" class="form-control" name="name" id="name" data-rule-required="true">
        </div>
      </div>

      <div class="form-group">
        <label class="col-lg-2 control-label" for="sort">
          顺序
        </label>

        <div class="col-lg-4">
          <input type="text" class="form-control" name="sort" id="sort">
        </div>

        <label class="col-lg-6 help-text" for="sort">
          大的显示在前面,按从大到小排列.
        </label>
      </div>

      <div class="form-group">
        <label class="col-lg-2 control-label" for="description">
          简介
        </label>

        <div class="col-lg-4">
          <textarea class="form-control" id="description" name="description"></textarea>
        </div>
      </div>

      <input type="hidden" name="type" id="type" value="file">
      <input type="hidden" name="id" id="id">

      <div class="clearfix form-actions form-group">
        <div class="offset-lg-2">
          <button class="btn btn-primary" type="submit">
            <i class="fa fa-check bigger-110"></i>
            提交
          </button>
          &nbsp; &nbsp; &nbsp;
          <a class="btn btn-default" href="<?= $url('admin/file-categories') ?>">
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
  require(['form', 'ueditor', 'jquery-deparam', 'dataTable', 'validator'], function (form) {
    var parentJson = <?= json_encode(wei()->category()->notDeleted()->withParent('file')->getTreeToArray()) ?>;
    form.toOptions($('#parent-id'), parentJson, 'id', 'name');

    var category = <?= $category->toJson() ?>;

    $('#category-form')
      .loadJSON(category)
      .loadParams()
      .ajaxForm({
        url: '<?= $url('admin/file-categories/' . ($category->isNew() ? 'create' : 'update')) ?>',
        dataType: 'json',
        beforeSubmit: function (arr, $form, options) {
          return $form.valid();
        },
        success: function (result) {
          $.msg(result, function () {
            if (result.code > 0) {
              window.location = $.url('admin/file-categories');
            }
          });
        }
      })
      .validate();
  });
</script>
<?= $block->end() ?>
