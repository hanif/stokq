var app = app || {},
    menuParents = menuParents || [];

app.Menu = {};
app.Type = app.Type || { data: {} };
app.Menu.Model = Backbone.Model.extend({
    defaults: {
        id: 0,
        name: "",
        hasParent: "",
        parent: { id:0, name:"" },
        types: [],
        menuTypes: [],
        createdAt: "",
        description: "",
        servingUnit: "",
        defaultPrice: "",
        status: "",
        note: "",
        updated: 0,
        parentSelectOption: function() {
            var options = [];
            menuParents.forEach(function(p) {
                if (p.id == this.parent.id) {
                    options.push('<option value="' + p.id + '" selected>' + p.name + '</option>');
                } else {
                    options.push('<option value="' + p.id + '">' + p.name + '</option>');
                }
            }.bind(this));
            return options.join("");
        },
        typeSelectOption: function() {
            var options = [];
            app.Type.data.forEach(function(model) {
                var k = model.get('id'),
                    v = model.get('name');
                if (this.types.indexOf(k) >= 0) {
                    options.push('<option value="' + k + '" selected>' + v + '</option>');
                } else {
                    options.push('<option value="' + k + '">' + v + '</option>');
                }
            }.bind(this));
            return options.join("");
        }
    }
});
app.Menu.Collection = Backbone.Collection.extend({ model: app.Menu.Model });
app.Menu.data = new app.Menu.Collection([]);
app.Menu.ItemView = Backbone.View.extend({
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
        var view = new app.Menu.EditView({ model: this.model });
        this.$el.replaceWith(view.render().el);
        $('select[data-ui=select2]').select2();
    },
    del: function(e) {
        e.preventDefault();
        if (confirm("Yakin ingin menghapus item yang dipilih?")) {
            var id = this.model.get('id');
            $.ajax({
                url: $(e.target).attr('href'),
                data: {id:id},
                method: 'delete',
                success: function() {
                    app.Menu.data.remove(id);
                },
                error: function() {
                    alert('Item tidak dapat dihapus saat ini, cobalah beberapa saat lagi.');
                }
            });
        }
    }
});
app.Menu.EditView = Backbone.View.extend({
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
        var view = new app.Menu.ItemView({ model: this.model });
        this.$el.replaceWith(view.render().el);
        this.model.set({updated: false});
    }
});
app.Menu.ListView = Backbone.View.extend({
    target: $('#main-list'),
    emptyTemplate: _.template($('#no-item').html()),
    initialize: function() {
        this.listenTo(app.Menu.data, 'change remove', this.render);
        this.listenTo(app.Menu.data, 'add', this.append);
        this.render();
    },
    append: function(model) {
        $('.blank-state', this.target).remove();
        var view = new app.Menu.ItemView({ model: model });
        this.target.append(view.render().el);
    },
    render: function() {
        this.target.html('');
        if (app.Menu.data.size() > 0) {
            app.Menu.data.each(function(model) {
                var view = new app.Menu.ItemView({ model: model });
                this.target.append(view.render().el);
            }, this);
        } else {
            this.target.append(this.emptyTemplate());
        }
        return this;
    }
});

app.Menu.listView = null;

$.getJSON('/menu/list', function(data) {
    data.forEach(function(item) {
        app.Menu.data.add(item);
    });
    app.Menu.listView = new app.Menu.ListView();
});

ajaxForm($('#add-menu-form'), {
    busy: formBusy(),
    done: formFinish(),
    ok: function(data) {
        app.Menu.data.add(data);
        var form = $(this);
        form.trigger('reset');
        $('.cancel', form).trigger('click');
    }
});