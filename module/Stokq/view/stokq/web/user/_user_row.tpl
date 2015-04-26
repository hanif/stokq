<script type="text/template" id="item-template">

    <td>
        <a class="btn btn-xs btn-edit edit" href="#"><i class="fa fa-pencil"></i></a>
        <a class="btn btn-xs btn-delete delete" href="/user/delete"><i class="fa fa-times"></i></a>
    </td>

    <td>
        <strong>#<%= id %> : <%= name %></strong>
    </td>

    <td><%= email %></td>

    <td><%= contactNo %></td>

    <td>
        <% if (status == "active") { %>
            <span class="small square green"></span>
        <% } else if (status == "new") { %>
            <span class="small square yellow"></span>
        <% } else { %>
            <span class="small square red"></span>
        <% } %>
        <%= status %>
    </td>

</script>