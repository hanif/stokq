<script type="text/template" id="item-template">
    <td>
        <a class="btn btn-xs btn-edit edit" href="#"><i class="fa fa-pencil"></i></a>
        <a class="btn btn-xs btn-delete delete" href="/menu/delete"><i class="fa fa-times"></i></a>
    </td>
    <td>
        <strong>#<%= id %> : <%= name %></strong>
        <br/>
        <small>
            <a href="/menu/detail?id=<%= id %>">bahan pembuatan &rarr;</a>
        </small>
    </td>
    <td>
        <% if (hasParent) { %>
        <%= parent.name %>
        <% } else { %>
        <em class="grey">&mdash;</em>
        <% } %>
    </td>
    <td><%= servingUnit %></td>
    <td>
        <%= defaultPrice %>
    </td>
</script>