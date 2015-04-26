<script type="text/template" id="item-row">
    <tr>
        <td>
            <a class="btn btn-delete btn-xs" href="#" onclick="return deleteItem(this);"><i class="fa fa-times"></i></a>
        </td>
        <td>
            <input data-item-name name="raw_items[<%= num %>][item_name]" type="text" class="alt form-control" value="" placeholder="Nama Item"/>
            <input data-item-id name="raw_items[<%= num %>][stock]" type="hidden" value=""/>
        </td>
        <td>
            <input data-code type="text" class="alt form-control" placeholder="#" value="" readonly>
        </td>
        <td>
            <input data-quantity type="text" name="raw_items[<%= num %>][quantity]" class="alt form-control"
                   placeholder="Quantity" value="1">
        </td>
        <td>
            <input data-unit name="raw_items[<%= num %>][unit]" class="alt form-control" type="text"
                   placeholder="Unit"/>
        </td>
        <td>
            <input data-unit-price type="text" name="raw_items[<%= num %>][unit_price]"
                   class="alt form-control text-right" placeholder="Harga" value="0"/>
        </td>
        <td>
            <input data-subtotal type="text" name="raw_items[<%= num %>][subtotal]"
                   class="alt form-control text-right" placeholder="Subtotal" value="0" readonly/>
        </td>
        <td>
            <input data-total type="text" name="raw_items[<%= num %>][total]"
                   class="alt form-control text-right" placeholder="Total" value="0"/>
        </td>
    </tr>
</script>