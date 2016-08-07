<script id="table-actions" type="text/html">
  <div class="action-buttons">
    <?php $event->trigger('mediaAction', ['file']); ?>

    <a href="<%= url %>" title="下载">
      <i class="fa fa-cloud-download bigger-130"></i>
    </a>

    <a href="<%= $.url('admin/files/edit', {id: id}) %>" title="编辑">
      <i class="fa fa-edit bigger-130"></i>
    </a>

    <a class="text-danger delete-record" data-href="<%= $.url('admin/files/destroy', {id: id}) %>" href="javascript:;" title="删除">
      <i class="fa fa-trash-o bigger-130"></i>
    </a>
  </div>
</script>
