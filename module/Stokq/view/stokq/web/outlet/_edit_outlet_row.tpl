<script type="text/template" id="edit-template">
    <td colspan="7" class="bg-grey">
        <form action="/outlet/update" method="post" role="form" class="pl10 pr10">
            <div class="row">
                <div class="col-sm-12 col-md-6">
                    <div class="form-group">
                        <label class="control-label">Outlet</label>
                        <input type="text" name="name" value="<%= name %>" class="form-control" placeholder="Outlet">
                    </div>
                </div>
                <div class="col-sm-12 col-md-6">
                    <div class="form-group">
                        <label class="control-label">Gudang</label>
                        <select name="warehouse" class="form-control" title="Gudang">
                            <%= warehouseSelectOption() %>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-6">
                    <div class="form-group">
                        <label class="control-label">Latitude</label>
                        <input type="text" name="latitude" value="<%= latitude %>" class="form-control" placeholder="Latitude">
                    </div>
                </div>
                <div class="col-sm-12 col-md-6">
                    <div class="form-group">
                        <label class="control-label">Longitude</label>
                        <input type="text" name="longitude" value="<%= longitude %>" class="form-control" placeholder="Longitude">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-6">
                    <div class="form-group">
                        <label class="control-label">Alamat</label>
                        <textarea name="address" rows="3" class="form-control" placeholder="Alamat"><%= address %></textarea>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6">
                    <div class="form-group">
                        <label class="control-label">Deskripsi/Catatan</label>
                        <textarea name="description" rows="3" class="form-control" placeholder="Deskripsi/Catatan"><%= description %></textarea>
                    </div>
                </div>
            </div>

            <hr class="mt10 mb15"/>

            <div class="form-group mb0">
                <button type="submit" class="btn alt green"><i class="fa fa-save"></i> Simpan</button>
                <a href="javascript:void(0);" class="ml20 cancel"><i class="fa fa-times"></i> tutup</a>
            </div>

            <br class="mt5 mb0"/>

        </form>
    </td>
</script>