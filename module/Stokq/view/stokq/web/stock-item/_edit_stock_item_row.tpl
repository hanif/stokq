<script type="text/template" id="edit-template">
    <td colspan="7" class="bg-grey">
        <form action="/stock-item/update" method="post" role="form" class="pl10 pr10">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label">Nama</label>
                        <input type="text" name="name" value="<%= name %>" class="form-control" placeholder="Nama">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label">Kode</label>
                        <input type="text" name="code" value="<%= code %>" class="form-control" placeholder="Kode">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label">Unit Penyimpanan</label>
                        <select name="storage_unit" class="form-control" title="Unit Penyimpanan">
                            <%= storageUnitSelectOption() %>
                        </select>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label">Unit Penggunaan</label>
                        <select name="usage_unit" class="form-control" title="Unit Penggunaan">
                            <%= usageUnitSelectOption() %>
                        </select>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label">Jenis</label>
                        <select name="type" class="form-control" title="Jenis">
                            <%= storageTypeSelectOption() %>
                        </select>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label">Kategori</label>
                        <select name="categories[]" class="form-control" title="Kategori" multiple="multiple" data-ui="select2">
                            <%= categorySelectOption() %>
                        </select>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label">Deskripsi</label>
                        <textarea name="description" rows="3" class="form-control" placeholder="Deskripsi"><%= description %></textarea>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label">Catatan</label>
                        <textarea name="note" rows="3" class="form-control" placeholder="Catatan"><%= note %></textarea>
                    </div>
                </div>
            </div>

            <hr class="mt10 mb20"/>

            <div class="form-group mb0">
                <button type="submit" class="btn alt green"><i class="fa fa-save"></i> Simpan</button>
                <a href="javascript:void(0);" class="ml20 cancel"><i class="fa fa-times"></i> tutup</a>
            </div>

            <br class="mb20"/>

        </form>
    </td>
</script>