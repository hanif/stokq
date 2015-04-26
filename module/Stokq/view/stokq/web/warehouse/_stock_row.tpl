<script type="text/template" id="stock-row-template">
    <td class="middle center">
        <a href="/stock/delete?id=<%= id %>" class="btn btn-delete btn-xs" data-delete>
            <i class="fa fa-times"></i>
        </a>
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
            <div class="input-group">
                <input class="form-control" type="number" name="current_level" data-id="<%= id %>"
                       min="1" step="0.0001" value="<%= currentLevel %>" placeholder="Current Level"
                       data-value="<%= currentLevel %>" data-current-level/>
                <span class="input-group-addon"><%= stockItem.storageUnit.name %></span>
            </div>
        </fieldset>
    </td>
    <td>
        <fieldset>
            <div class="input-group">
                <input name="id" type="hidden" value="<%= id %>"/>
                <input class="form-control" type="number" name="reorder_level" min="1" step="0.0001"
                       value="<%= reorderLevel %>" placeholder="Reorder Level"
                       data-value="<%= reorderLevel %>" data-reorder-level/>
                <span class="input-group-addon"><%= stockItem.storageUnit.name %></span>
            </div>
        </fieldset>
    </td>
    <td>

    </td>
    <td>

    </td>
</script>