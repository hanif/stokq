<script type="text/template" id="edit-template">
    <td colspan="5" class="bg-grey">
        <form action="/menu/update" method="post" role="form" class="pl10 pr10">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label class="control-label">Nama Menu</label>
                        <input type="text" name="name" value="<%= name %>" class="form-control" placeholder="Nama Menu">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-6">
                    <div class="form-group">
                        <label class="control-label">Varian</label>
                        <select name="parent" class="form-control" title="Varian">
                            <option value=""></option>
                            <%= parentSelectOption() %>
                        </select>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6">
                    <div class="form-group">
                        <label class="control-label">Jenis</label>
                        <select name="types[]" class="form-control" title="Jenis" multiple="multiple" data-ui="select2">
                            <%= typeSelectOption() %>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-6">
                    <div class="form-group">
                        <label class="control-label">Unit Saji</label>
                        <input type="text" name="serving_unit" value="<%= servingUnit %>" class="form-control" placeholder="Unit Saji">
                    </div>
                </div>
                <div class="col-sm-12 col-md-6">
                    <div class="form-group">
                        <label class="control-label">Harga Default</label>
                        <input type="text" name="default_price" value="<%= defaultPrice %>" class="form-control" placeholder="Harga Default">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-6">
                    <div class="form-group">
                        <label class="control-label">Deskripsi</label>
                        <textarea name="description" rows="3" class="form-control" placeholder="Deskripsi"><%= description %></textarea>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6">
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