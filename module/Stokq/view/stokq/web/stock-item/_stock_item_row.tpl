<script type="text/template" id="item-template">
    <td>
        <a class="btn btn-xs btn-edit edit" href="#"><i class="fa fa-pencil"></i></a>
        <a class="btn btn-xs btn-delete delete" href="/stock-item/delete"><i class="fa fa-times"></i></a>
    </td>
    <td>
        <strong>#<%= id %>: <%= name %></strong>
    </td>
    <td><%= code %></td>
    <td><%= storageType.name %></td>
    <td><%= storageUnit.name %></td>
    <td><%= usageUnit.name %></td>
    <td data-stock-level data-stock-item="<%= id %>">

    </td>
</script>