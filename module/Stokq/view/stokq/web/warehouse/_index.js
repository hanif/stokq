var app = app || { Warehouse: {} };
app.Warehouse.Model = Backbone.Model.extend({
    defaults: {
        id: 0,
        name: "",
        address: "",
        description: "",
        latitude: "",
        longitude: "",
        updated: 0,
        stock_count: null,
        empty_stocks: null,
        low_stocks: null
    }
});
app.Warehouse.Collection = Backbone.Collection.extend({ model: app.Warehouse.Model });
app.Warehouse.data = new app.Warehouse.Collection([]);
app.Warehouse.ItemView = Backbone.View.extend({
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
        var view = new app.Warehouse.EditView({ model: this.model });
        this.$el.replaceWith(view.render().el);
    },
    del: function(e) {
        e.preventDefault();
        if (confirm("Yakin ingin menghapus item yang dipilih?")) {
            var id = this.model.get('id');
            $.ajax({
                url: $(e.currentTarget).attr('href'),
                data: {id:id},
                method: 'post',
                success: function() {
                    app.Warehouse.data.remove(id);
                },
                error: function() {
                    alert('Item tidak dapat dihapus saat ini, cobalah beberapa saat lagi.');
                }
            });
        }
    }
});
app.Warehouse.EditView = Backbone.View.extend({
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
        var view = new app.Warehouse.ItemView({ model: this.model });
        this.$el.replaceWith(view.render().el);
        this.model.set({updated: false});
    }
});
app.Warehouse.ListView = Backbone.View.extend({
    target: $('#main-list'),
    emptyTemplate: _.template($('#no-item').html()),
    initialize: function() {
        this.listenTo(app.Warehouse.data, 'change remove filter reset', this.render);
        this.listenTo(app.Warehouse.data, 'add', this.append);
        this.render();
    },
    append: function(model) {
        $('.blank-state', this.target).remove();
        var view = new app.Warehouse.ItemView({ model: model });
        this.target.append(view.render().el);
    },
    render: function() {
        this.target.html('');
        if (app.Warehouse.data.size() > 0) {
            app.Warehouse.data.each(function(model) {
                var view = new app.Warehouse.ItemView({ model: model });
                this.target.append(view.render().el);
            }, this);
        } else {
            this.target.append(this.emptyTemplate());
        }
        return this;
    }
});

app.Warehouse.listView = null;

$.getJSON('/warehouse/list-with-indicator', function(data) {
    data.forEach(function(item) {
        app.Warehouse.data.add(item);
    });
    app.Warehouse.listView = new app.Warehouse.ListView();
});

ajaxForm($('#add-warehouse-form'), {
    busy: formBusy(),
    done: formFinish(),
    ok: function(data) {
        app.Warehouse.data.add(data);
        var form = $(this);
        form.trigger('reset');
        $('input:first', form).focus();
    }
});