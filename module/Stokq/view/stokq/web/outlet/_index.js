var app = app || { Outlet: {}},
    currency = currency || '';

app.Outlet.Model = Backbone.Model.extend({
    defaults: {
        id: 0,
        name: "",
        address: "",
        description: "",
        latitude: "",
        longitude: "",
        warehouse: { id:0, name: "" },
        updated: 0,
        quantity_sold_in_7d: 0,
        income_in_7d: 0,
        quantity_sold_in_30d: 0,
        income_in_30d: 0,
        currency: currency,
        warehouseSelectOption: function() {
            var options = [];
            app.warehouses.forEach(function(w) {
                if (w.id == this.warehouse.id) {
                    options.push('<option value="' + w.id + '" selected>' + w.name + '</option>');
                } else {
                    options.push('<option value="' + w.id + '">' + w.name + '</option>');
                }
            }.bind(this));
            return options.join("");
        },
        formatCurrency: function(input, prefixCurrency) {
            if (!input) return;
            return accounting.formatMoney(input, prefixCurrency ? currency : '', 2);
        }
    }
});
app.Outlet.Collection = Backbone.Collection.extend({ model: app.Outlet.Model });
app.Outlet.data = new app.Outlet.Collection([]);
app.Outlet.ItemView = Backbone.View.extend({
    tagName: 'tr',
    template: _.template($('#item-template').html()),
    events: {
        'click a.edit': 'edit',
        'click a.delete': 'del'
    },
    initialize: function() {
        this.listenTo(this.model, 'change', this.render);
    },
    render: function() {
        this.$el.html(this.template(this.model.toJSON()));
        return this;
    },
    edit: function(e) {
        e.preventDefault();
        var view = new app.Outlet.EditView({ model: this.model });
        this.$el.replaceWith(view.render().el);
    },
    del: function(e) {
        e.stopPropagation();
        e.preventDefault();
        if (confirm("Yakin ingin menghapus item yang dipilih?")) {
            var id = this.model.get('id');
            $.ajax({
                url: $(e.currentTarget).attr('href'),
                data: {id:id},
                method: 'post',
                success: function() {
                    app.Outlet.data.remove(id);
                },
                error: function() {
                    alert('Item tidak dapat dihapus saat ini, cobalah beberapa saat lagi.');
                }
            });
        }
    }
});
app.Outlet.EditView = Backbone.View.extend({
    tagName: 'tr',
    template: _.template($('#edit-template').html()),
    events: {
        'click .cancel': 'cancel'
    },
    initialize: function() {
        this.listenTo(this.model, 'change', this.render);
    },
    ok: function(data) {
        this.model.set(data);
        this.model.set({updated: true});
        this.$el.html(this.template(this.model.toJSON()));
        $('.cancel', this.$el).trigger('click');
    },
    error: function() {
        alert('Item tidak dapat meng-update data saat ini, cobalah beberapa saat lagi.');
    },
    render: function() {
        this.$el.html(this.template(this.model.toJSON()));
        var form = $('form', this.$el);
        form.find('[name]:first').select();
        ajaxForm(form, {
            busy: formBusy(),
            done: formFinish(),
            ok: this.ok.bind(this),
            error: this.error,
            data: {id:this.model.get('id')}
        });
        return this;
    },
    cancel: function(e) {
        e.preventDefault();
        var view = new app.Outlet.ItemView({ model: this.model });
        this.$el.replaceWith(view.render().el);
        this.model.set({updated: false});
    }
});
app.Outlet.ListView = Backbone.View.extend({
    target: $('#main-list'),
    emptyTemplate: _.template($('#no-item').html()),
    initialize: function() {
        this.listenTo(app.Outlet.data, 'change remove', this.render);
        this.listenTo(app.Outlet.data, 'add', this.append);
        this.render();
    },
    append: function(model) {
        $('.blank-state', this.target).remove();
        var view = new app.Outlet.ItemView({ model: model });
        this.target.append(view.render().el);
    },
    render: function() {
        this.target.html('');
        if (app.Outlet.data.size() > 0) {
            app.Outlet.data.each(function(model) {
                var view = new app.Outlet.ItemView({ model: model });
                this.target.append(view.render().el);
            }, this);
        } else {
            this.target.append(this.emptyTemplate());
        }
        return this;
    }
});

app.Outlet.listView = null;

$.getJSON('/outlet/list-with-sales', function(data) {
    data.forEach(function(item) {
        app.Outlet.data.add(item);
    });
    app.Outlet.listView = new app.Outlet.ListView();
});

ajaxForm($('#add-outlet-form'), {
    busy: formBusy(),
    done: formFinish(),
    ok: function(data) {
        app.Outlet.data.add(data);
        var form = $(this);
        form.trigger('reset');
        $('input:first', form).focus();
    }
});