var app = app || {},
    stockUnits = stockUnits || [];

app.StockUnit = {};
app.StockUnit.Model = Backbone.Model.extend({
    defaults: {
        id: 0,
        name: "",
        description: "",
        type: { id: 0, name: "" },
        ratio: 1,
        updated: 0
    }
});
app.StockUnit.Collection = Backbone.Collection.extend({ model: app.StockUnit.Model });
app.StockUnit.data = new app.StockUnit.Collection(stockUnits);
app.StockUnit.ItemView = Backbone.View.extend({
    tagName: 'tr',
    template: _.template($('#unit-item-template').html()),
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
        var view = new app.StockUnit.EditView({ model: this.model });
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
                    app.StockUnit.data.remove(id);
                },
                error: function() {
                    alert('Item tidak dapat dihapus saat ini, cobalah beberapa saat lagi.');
                }
            });
        }
    }
});
app.StockUnit.EditView = Backbone.View.extend({
    tagName: 'tr',
    template: _.template($('#edit-unit-template').html()),
    events: {
        'click .cancel': 'cancel',
        'click .save': 'save'
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
        $('input:first', this.$el).select();
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
        var view = new app.StockUnit.ItemView({ model: this.model });
        this.$el.replaceWith(view.render().el);
        this.model.set({updated: false});
    },
    save: function(e) {
        e.preventDefault();
        $('form', this.$el).trigger('submit');
        return this;
    }
});
app.StockUnit.ListView = Backbone.View.extend({
    target: $('#unit-list'),
    emptyTemplate: _.template($('#no-unit').html()),
    initialize: function() {
        this.listenTo(app.StockUnit.data, 'change remove', this.render);
        this.listenTo(app.StockUnit.data, 'add', this.append);
        this.render();
    },
    append: function(model) {
        $('.blank-state', this.target).remove();
        var view = new app.StockUnit.ItemView({ model: model });
        this.target.append(view.render().el);
    },
    render: function() {
        this.target.html('');
        if (app.StockUnit.data.size() > 0) {
            app.StockUnit.data.each(function(model) {
                var view = new app.StockUnit.ItemView({ model: model });
                this.target.append(view.render().el);
            }, this);
        } else {
            this.target.append(this.emptyTemplate());
        }
        return this;
    }
});

app.StockUnit.listView = new app.StockUnit.ListView();

ajaxForm($('#add-unit-form'), {
    busy: formBusy(),
    done: formFinish(),
    ok: function(data) {
        app.StockUnit.data.add(data);
        var form = $(this);
        form.trigger('reset');
        $('input:first', form).focus();
    }
});
