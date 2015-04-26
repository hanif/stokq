<script type="text/template" id="item-template">
    <td>
        <a class="btn btn-xs btn-edit edit" href="#"><i class="fa fa-pencil"></i></a>
        <a class="btn btn-xs btn-delete delete" href="/warehouse/delete"><i class="fa fa-times"></i></a>
    </td>
    <td>
        <strong>#<%= id %> : <%= name %></strong>
        <br/>
        <small>
            <a href="/warehouse/detail?id=<%= id %>" class="grey">lihat stok &rarr;</a>
        </small>
    </td>
    <td>
        <% if (address) { %>
            <%= address %>
        <% } else { %>
            <em class="grey">Tidak ada info alamat.</em>
        <% } %>
    </td>

    <% if (stock_count == null) { %>
        <td></td>
    <% } else { %>
        <td class="stock"><%= stock_count %></td>
    <% } %>

    <% if (empty_stocks == null) { %>
        <td></td>
    <% } else if (parseFloat(empty_stocks) > 0) { %>
        <td class="stock empty"><%= empty_stocks %></td>
    <% } else { %>
        <td class="stock normal">0</td>
    <% } %>

    <% if (low_stocks == null) { %>
        <td></td>
    <% } else if (parseFloat(low_stocks) > 0) { %>
        <td class="stock low"><%= low_stocks %></td>
    <% } else { %>
        <td class="stock normal">0</td>
    <% } %>
</script>