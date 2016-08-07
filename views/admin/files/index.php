<?php $view->layout() ?>

<?= $block('css') ?>
<link rel="stylesheet" href="<?= $asset('plugins/admin/assets/filter.css') ?>"/>
<?= $block->end() ?>

<?php require $view->getFile('file:admin/files/page-header.php'); ?>

<div class="row">
  <div class="col-xs-12">
    <!-- PAGE CONTENT BEGINS -->
    <div class="table-responsive">
      <form class="form-horizontal filter-form" id="search-form" role="form">

        <div class="well form-well m-b">
          <div class="form-group form-group-sm">

            <label class="col-md-1 control-label" for="categoryId">栏目：</label>

            <div class="col-md-3">
              <select class="form-control" name="categoryId" id="categoryId">
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
          <th style="width: 120px;">栏目名称</th>
          <th>文件名</th>
          <th style="width: 200px;">有效时间</th>
          <th style="width: 50px">类型</th>
          <th style="width: 100px">大小(KB)</th>
          <th style="width: 200px">修改时间</th>
          <?php wei()->event->trigger('tableCol', ['file']); ?>
          <th style="width: 180px">操作</th>
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
<?php require $view->getFile('file:admin/files/actions.php'); ?>

<?= $block('js') ?>
<script>
  require(['form', 'dataTable', 'jquery-deparam', 'template'], function (form) {
    form.toOptions($('#categoryId'), <?= json_encode(wei()->category()->notDeleted()->withParent('file')->getTreeToArray()) ?>, 'id', 'name');

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
          render: function(data, type, full) {
            return full.startTime.replace(/-/g, '.').substr(0, 10) + '~' + full.endTime.replace(/-/g, '.').substr(0, 10);
          }
        },
        {
          data: 'ext'
        },
        {
          data: 'size',
          render: function(data, type, full) {
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
