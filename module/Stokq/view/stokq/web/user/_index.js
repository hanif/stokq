var app = app || { User: {} };
app.User.Model = Backbone.Model.extend({
    defaults: {
        id: 0,
        name: "",
        address: "",
        email: "",
        status: "",
        contactNo: ""
    }
});
app.User.Collection = Backbone.Collection.extend({ model: app.User.Model });
app.User.data = new app.User.Collection([]);
app.User.ItemView = Backbone.View.extend({
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
        var view = new app.User.EditView({ model: this.model });
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
                    app.User.data.remove(id);
                },
                error: function() {
                    alert('Item tidak dapat dihapus saat ini, cobalah beberapa saat lagi.');
                }
            });
        }
    }
});
app.User.EditView = Backbone.View.extend({
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
        var view = new app.User.ItemView({ model: this.model });
        this.$el.replaceWith(view.render().el);
        this.model.set({updated: false});
    }
});
app.User.ListView = Backbone.View.extend({
    target: $('#main-list'),
    initialize: function() {
        this.listenTo(app.User.data, 'change remove filter reset', this.render);
        this.listenTo(app.User.data, 'add', this.append);
        this.render();
    },
    append: function(model) {
        $('.blank-state', this.target).remove();
        var view = new app.User.ItemView({ model: model });
        this.target.append(view.render().el);
    },
    render: function() {
        this.target.html('');
        app.User.data.each(function(model) {
            var view = new app.User.ItemView({ model: model });
            this.target.append(view.render().el);
        }, this);
        return this;
    }
});

app.User.listView = null;

$.getJSON('/user/list', function(data) {
    data.forEach(function(item) {
        app.User.data.add(item);
    });
    app.User.listView = new app.User.ListView();
});

ajaxForm($('#add-user-form'), {
    busy: formBusy(),
    done: formFinish(),
    ok: function(data) {
        app.User.data.add(data);
        var form = $(this);
        form.trigger('reset');
        $('input:first', form).focus();
    }
});