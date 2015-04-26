<script type="text/template" id="edit-unit-template">
    <td colspan="3">
        <form action="/stock-unit/update" method="post">
            <div class="row">
                <div class="col-sm-4">
                    <label class="control-label">Nama</label>
                    <input type="text" name="name" class="form-control" value="<%= name %>" placeholder="Nama" autofocus="yes" />
                </div>
                <div class="col-sm-4">
                    <label class="control-label">Deskripsi</label>
                    <input type="text" name="description" class="form-control" value="<%= description %>" placeholder="Deskripsi" />
                </div>
                <div class="col-sm-4">
                    <label class="control-label">Ratio</label>
                    <input type="text" name="ratio" class="form-control" value="<%= ratio %>" placeholder="Ratio" />
                </div>
            </div>
            <small class="meta block mt10 text-right">
                <a class="save" href="#">save</a> |
                <a class="cancel" href="#">cancel</a>
            </small>
            <input name="id" type="hidden" value="<%= id %>"/>
            <input name="type" type="hidden" value="<%= type.id %>"/>
        </form>
    </td>
</script>