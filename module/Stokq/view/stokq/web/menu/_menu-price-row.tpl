<script type="text/template" id="menu-price-row-template">
    <td class="middle center">
        <a href="/menu-price/delete?id=<%= id %>" class="btn btn-delete btn-xs" data-delete>
            <i class="fa fa-times"></i>
        </a>
    </td>
    <td>
        <%= outlet.name %>
    </td>
    <td>
        <small class="grey left"><% if (menu.servingUnit) { %><%= menu.servingUnit %><% } else { %>:<% } %> </small>
        <strong class="grey">0</strong>
    </td>
    <td>
        <span class="grey left"><%= currency %></span>
        <strong class="grey">0.00</strong>
    </td>
    <td>
        <fieldset>
            <div class="input-group">
                <input name="id" type="hidden" value="<%= id %>"/>
                <span class="input-group-addon"><%= currency %></span>
                <input class="form-control" type="number" name="price" min="0" step="0.01"
                       value="<%= price %>" placeholder="Price"
                       data-value="<%= price %>" data-price/>
                <span class="input-group-addon"> / <%= menu.servingUnit %></span>
            </div>
        </fieldset>
    </td>
</script>