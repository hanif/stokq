<script type="text/template" id="edit-template">
    <td colspan="5" class="bg-grey">
        <form action="/user/update" method="post" role="form" class="pl10 pr10">
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label class="control-label">Nama</label>
                        <input type="text" name="name" value="<%= name %>" class="form-control" placeholder="Nama">
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label class="control-label">Email</label>
                        <input type="email" name="email" value="<%= email %>" class="form-control" placeholder="Login Email">
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label class="control-label">Konatak/Telp.</label>
                        <input type="text" name="contact_no" value="<%= contactNo %>" class="form-control" placeholder="Konatak/Telp.">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label class="control-label">Alamat</label>
                        <textarea name="address" rows="3" class="form-control" placeholder="Alamat"><%= address %></textarea>
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