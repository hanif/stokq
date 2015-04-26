<script type="text/template" id="edit-type-template">
    <td colspan="2">
        <form action="/type/update" method="post">
            <input type="text" name="name" class="form-control" value="<%= name %>" placeholder="Tipe" autofocus="yes" />
            <input name="id" type="hidden" value="<%= id %>"/>
            <small class="meta block mt10 text-right">
                <a class="save" href="#">simpan</a> |
                <a class="cancel" href="#">batal</a>
            </small>
        </form>
    </td>
</script>