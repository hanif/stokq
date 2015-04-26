<script type="text/template" id="item-template">
    <td>
        <a class="btn btn-xs btn-edit edit" href="#"><i class="fa fa-pencil"></i></a>
        <a class="btn btn-xs btn-delete delete" href="/outlet/delete"><i class="fa fa-times"></i></a>
    </td>
    <td>
        <strong>#<%= id %> : <%= name %></strong>
        <br/>
        <small>
            <a href="/outlet/detail?id=<%= id %>" class="grey">harga menu &rarr;</a>
        </small>
    </td>
    <td><%= warehouse.name %></td>
    <td><%= quantity_sold_in_7d %></td>
    <td><%= quantity_sold_in_30d %></td>
    <td>
        <strong class="light-grey block"><%= currency %></strong>
        <span class="grey block text-right">
            <%= formatCurrency(income_in_7d) %>
        </span>
    </td>
    <td>
        <strong class="light-grey block"><%= currency %></strong>
        <span class="grey block text-right">
            <%= formatCurrency(income_in_30d) %>
        </span>
    </td>
</script>