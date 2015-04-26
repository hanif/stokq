<script type="text/template" id="edit-storage-type-template">
    <td colspan="2">
        <form action="/storage-type/update" method="post">
            <input type="text" name="name" class="form-control" value="<%= name %>" placeholder="Type" autofocus="yes" />
            <input name="id" type="hidden" value="<%= id %>"/>
            <small class="meta block mt10 text-right">
                <a class="save" href="#">save</a> |
                <a class="cancel" href="#">cancel</a>
            </small>
        </form>
    </td>
</script>