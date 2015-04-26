<script type="text/template" id="ingredient-item-template">
    <tr class="new">
        <td class="middle center">
            <a href="/ingredient/delete?id=<%= id %>" data-delete class="btn btn-delete btn-xs"><i class="fa fa-times"></i></a>
        </td>
        <td>
            <%= stockItem.name %>
            <br/>
            <small class="grey">
                <%= stockItem.code %>
            </small>
        </td>
        <td>
            <fieldset>
                <input name="id" type="hidden" value="<%= id %>"/>
                <div class="input-group">
                    <input data-value="<%= quantity %>" type="number" name="quantity" class="form-control"
                           placeholder="Quantity" value="<%= quantity %>" min="1" step="0.001">
                    <span class="input-group-addon"><%= stockItem.usageUnit.name %></span>
                </div>
            </fieldset>
        </td>
        <td>
            <fieldset>
                <input name="id" type="hidden" value="<%= id %>"/>
                <div class="input-group">
                    <span class="input-group-addon"><%= currency %></span>
                    <input class="form-control" type="number" name="qty_price" min="0" step="1"
                           value="<%= qty_price %>" placeholder="Harga/Qty."
                           data-value="<%= qty_price %>" data-qty-price/>
                </div>
            </fieldset>
        </td>
    </tr>
</script>