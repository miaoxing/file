<?php $view->layout() ?>

<?= $block->css() ?>
<link rel="stylesheet" href="<?= $asset('plugins/admin/css/filter.css') ?>"/>
<?= $block->end() ?>

<?php require $view->getFile('@file/admin/files/page-header.php'); ?>

<div class="row">
  <div class="col-12">
    <!-- PAGE CONTENT BEGINS -->
    <div class="table-responsive">
      <form class="form-horizontal filter-form" id="search-form" role="form">

        <div class="well">
          <div class="form-group">

            <label class="col-md-1 control-label" for="category-id">栏目：</label>

            <div class="col-md-3">
              <select class="form-control" name="categoryId" id="category-id">
                <option value="">全部栏目</option>
              </select>
            </div>

            <?php wei()->event->trigger('searchForm', ['file']); ?>

            <label class="col-md-1 control-label" for="search">文件名：</label>

            <div class="col-md-3">
              <input type="text" class="form-control" id="search" name="search">
            </div>

          </div>
        </div>
      </form>

      <table class="js-record-table record-table table table-bordered table-hover">
        <thead>
        <tr>
          <th class="t-8">栏目名称</th>
          <th>文件名</th>
          <th class="t-10">有效时间</th>
          <th class="t-4">类型</th>
          <th class="t-6">大小(KB)</th>
          <th class="t-9">修改时间</th>
          <?php wei()->event->trigger('tableCol', ['file']); ?>
          <th class="t-8">操作</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
    <!-- /.table-responsive -->
    <!-- PAGE CONTENT ENDS -->
  </div>
  <!-- /col -->
</div>
<!-- /row -->
<?php require $view->getFile('@file/admin/files/actions.php'); ?>

<?= $block->js() ?>
<script>
  require(['form', 'plugins/admin/js/data-table', plugins/app/libs/artTemplate/template.min], function (form) {
    var categoryJson = <?= json_encode(wei()->category()->notDeleted()->withParent('file')->getTreeToArray()) ?>;
    form.toOptions($('#category-id'), categoryJson, 'id', 'name');

    $('#search-form').loadParams().update(function () {
      recordTable.reload($(this).serialize(), false);
    });

    var recordTable = $('.js-record-table').dataTable({
      ajax: {
        url: $.queryUrl('admin/files.json')
      },
      columns: [
        {
          data: 'categoryName'
        },
        {
          data: 'name',
          sClass: 'text-left'
        },
        {
          data: 'ext',
          render: function (data, type, full) {
            var timeRange = full.startTime.replace(/-/g, '.').substr(0, 10);
            timeRange += '~' + full.endTime.replace(/-/g, '.').substr(0, 10);
            return timeRange;
          }
        },
        {
          data: 'ext'
        },
        {
          data: 'size',
          render: function (data, type, full) {
            return (data / 1024).toFixed(2);
          }
        },
        {
          data: 'updateTime',
          render: function (data, type, full) {
            return data;
          }
        },
        <?php wei()->event->trigger('tableData', ['file']); ?>
        {
          data: 'type',
          render: function (data, type, full) {
            return template.render('table-actions', full)
          }
        }
      ]
    });

    recordTable.deletable();
  });
</script>
<?= $block->end() ?>
