var app = app || {},
    storageTypes = storageTypes || [];

app.StorageType = {};
app.StorageType.Model = Backbone.Model.extend({
    defaults: {
        id: 0,
        name: "",
        updated: 0
    }
});
app.StorageType.Collection = Backbone.Collection.extend({ model: app.StorageType.Model });
app.StorageType.data = new app.StorageType.Collection(storageTypes);
app.StorageType.ItemView = Backbone.View.extend({
    tagName: 'tr',
    template: _.template($('#storage-type-item-template').html()),
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
        var view = new app.StorageType.EditView({ model: this.model });
        this.$el.replaceWith(view.render().el);
    },
    del: function(e) {
        e.preventDefault();
        if (confirm("Yakin ingin menghapus item yang dipilih?")) {
            var id = this.model.get('id');
            $.ajax({
                url: $(e.currentTarget).attr('href'),
                data: {id:id},
                method: 'delete',
                success: function() {
                    app.StorageType.data.remove(id);
                },
                error: function() {
                    alert('Item tidak dapat dihapus saat ini, cobalah beberapa saat lagi.');
                }
            });
        }
    }
});
app.StorageType.EditView = Backbone.View.extend({
    tagName: 'tr',
    template: _.template($('#edit-storage-type-template').html()),
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
        var view = new app.StorageType.ItemView({ model: this.model });
        this.$el.replaceWith(view.render().el);
        this.model.set({updated: false});
    },
    save: function(e) {
        e.preventDefault();
        $('form', this.$el).trigger('submit');
        return this;
    }
});
app.StorageType.ListView = Backbone.View.extend({
    target: $('#storage-type-list'),
    emptyTemplate: _.template($('#no-storage-type').html()),
    initialize: function() {
        this.listenTo(app.StorageType.data, 'change remove', this.render);
        this.listenTo(app.StorageType.data, 'add', this.append);
        this.render();
    },
    append: function(model) {
        $('.blank-state', this.target).remove();
        var view = new app.StorageType.ItemView({ model: model });
        this.target.append(view.render().el);
    },
    render: function() {
        this.target.html('');
        if (app.StorageType.data.size() > 0) {
            app.StorageType.data.each(function(model) {
                var view = new app.StorageType.ItemView({ model: model });
                this.target.append(view.render().el);
            }, this);
        } else {
            this.target.append(this.emptyTemplate());
        }
        return this;
    }
});

app.StorageType.listView = new app.StorageType.ListView();

ajaxForm($('#add-storage-type-form'), {
    busy: formBusy(),
    done: formFinish(),
    ok: function(data) {
        app.StorageType.data.add(data);
        var form = $(this);
        form.trigger('reset');
        $('input:first', form).focus();
    }
});
