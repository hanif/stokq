var app = app || {},
    types = types || [];

app.Type = {};
app.Type.Model = Backbone.Model.extend({
    defaults: {
        id: 0,
        name: "",
        updated: 0
    }
});
app.Type.Collection = Backbone.Collection.extend({ model: app.Type.Model });
app.Type.data = new app.Type.Collection(types);
app.Type.ItemView = Backbone.View.extend({
    tagName: 'tr',
    template: _.template($('#type-item-template').html()),
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
        var view = new app.Type.EditView({ model: this.model });
        this.$el.replaceWith(view.render().el);
    },
    del: function(e) {
        e.stopPropagation();
        e.preventDefault();
        if (confirm("Yakin ingin menghapus item yang dipilih?.")) {
            var id = this.model.get('id');
            $.ajax({
                url: $(e.target).attr('href'),
                data: {id:id},
                method: 'delete',
                success: function() {
                    app.Type.data.remove(id);
                },
                error: function() {
                    alert('Item tidak dapat dihapus saat ini, cobalah beberapa saat lagi.');
                }
            });
        }
    }
});
app.Type.EditView = Backbone.View.extend({
    tagName: 'tr',
    template: _.template($('#edit-type-template').html()),
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
        var view = new app.Type.ItemView({ model: this.model });
        this.$el.replaceWith(view.render().el);
        this.model.set({updated: false});
    },
    save: function(e) {
        e.preventDefault();
        $('form', this.$el).trigger('submit');
        return this;
    }
});
app.Type.ListView = Backbone.View.extend({
    target: $('#type-list'),
    emptyTemplate: _.template($('#no-type').html()),
    initialize: function() {
        this.listenTo(app.Type.data, 'change remove', this.render);
        this.listenTo(app.Type.data, 'add', this.append);
        this.render();
    },
    append: function(model) {
        $('.blank-state', this.target).remove();
        var view = new app.Type.ItemView({ model: model });
        this.target.append(view.render().el);
    },
    render: function() {
        this.target.html('');
        if (app.Type.data.size() > 0) {
            app.Type.data.each(function(model) {
                var view = new app.Type.ItemView({ model: model });
                this.target.append(view.render().el);
            }, this);
        } else {
            this.target.append(this.emptyTemplate());
        }
        return this;
    }
});

app.Type.listView = new app.Type.ListView();

ajaxForm($('#add-type-form'), {
    busy: formBusy(),
    done: formFinish(),
    ok: function(data) {
        app.Type.data.add(data);
        var form = $(this);
        form.trigger('reset');
        $('.cancel', form).trigger('click');
    }
});
