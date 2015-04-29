var app = app || {},
    groupedUnits = groupedUnits || [];

app.StockItem = {};
app.Category = app.Category || { data: {} };
app.StorageType = app.StorageType || { data: {} };
app.StockItem.Model = Backbone.Model.extend({
    defaults: {
        id: 0,
        name: "",
        code: "",
        description: "",
        note: "",
        createdAt: "",
        usageUnit: { id:0, name:"", type:0 },
        storageUnit: { id:0, name:"", type:0 },
        storageType: { id:0, name:"" },
        stockCategories: [],
        categories: [],
        storageTypeSelectOption: function() {
            var options = [];
            app.StorageType.data.forEach(function(model) {
                var k = model.get('id'),
                    v = model.get('name');
                if (this.storageType.id == k) {
                    options.push('<option value="' + k + '" selected>' + v + '</option>');
                } else {
                    options.push('<option value="' + k + '">' + v + '</option>');
                }
            }.bind(this));
            return options.join("");
        },
        categorySelectOption: function() {
            var options = [];
            app.Category.data.forEach(function(model) {
                var k = model.get('id'),
                    v = model.get('name');
                if (this.categories.indexOf(k) >= 0) {
                    options.push('<option value="' + k + '" selected>' + v + '</option>');
                } else {
                    options.push('<option value="' + k + '">' + v + '</option>');
                }
            }.bind(this));
            return options.join("");
        },
        storageUnitSelectOption: function() {
            var options = [];
            if (groupedUnits[this.storageUnit.type]) {
                groupedUnits[this.storageUnit.type]['units'].forEach(function(unit) {
                    if (this.storageUnit.id == unit.id) {
                        options.push('<option value="' + unit.id + '" selected>' + unit.name + '</option>');
                    } else {
                        options.push('<option value="' + unit.id + '">' + unit.name + '</option>');
                    }
                }.bind(this));
                return options.join("");
            }
            return "";
        },
        usageUnitSelectOption: function() {
            var options = [];
            if (groupedUnits[this.usageUnit.type]) {
                groupedUnits[this.usageUnit.type]['units'].forEach(function(unit) {
                    if (this.usageUnit.id == unit.id) {
                        options.push('<option value="' + unit.id + '" selected>' + unit.name + '</option>');
                    } else {
                        options.push('<option value="' + unit.id + '">' + unit.name + '</option>');
                    }
                }.bind(this));
                return options.join("");
            }
            return "";
        },
        updated: 0
    }
});
app.StockItem.Collection = Backbone.Collection.extend({ model: app.StockItem.Model });
app.StockItem.data = new app.StockItem.Collection([]);
app.StockItem.ItemView = Backbone.View.extend({
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
        var view = new app.StockItem.EditView({ model: this.model });
        this.$el.replaceWith(view.render().el);
        $('select[data-ui=select2]').select2();
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
                    app.StockItem.data.remove(id);
                },
                error: function() {
                    alert('Item tidak dapat dihapus saat ini, cobalah beberapa saat lagi.');
                }
            });
        }
    }
});
app.StockItem.EditView = Backbone.View.extend({
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
        var view = new app.StockItem.ItemView({ model: this.model });
        this.$el.replaceWith(view.render().el);
        this.model.set({updated: false});
    }
});
app.StockItem.ListView = Backbone.View.extend({
    target: $('#main-list'),
    emptyTemplate: _.template($('#no-item').html()),
    initialize: function() {
        this.listenTo(app.StockItem.data, 'change remove', this.render);
        this.listenTo(app.StockItem.data, 'add', this.append);
        this.render();
    },
    append: function(model) {
        $('.blank-state', this.target).remove();
        var view = new app.StockItem.ItemView({ model: model });
        this.target.append(view.render().el);
    },
    render: function() {
        this.target.html('');
        if (app.StockItem.data.size() > 0) {
            app.StockItem.data.each(function(model) {
                var view = new app.StockItem.ItemView({ model: model });
                this.target.append(view.render().el);
            }, this);
        } else {
            this.target.append(this.emptyTemplate());
        }
        return this;
    }
});

app.StockItem.listView = null;

$.getJSON('/stock-item/list', function(data) {
    data.forEach(function(item) {
        app.StockItem.data.add(item);
    });
    app.StockItem.listView = new app.StockItem.ListView();
});

var storageUnit = $('select[name=storage_unit]'),
    usageUnit = $('select[name=usage_unit]');

ajaxForm($('#add-stock-item-form'), {
    busy: formBusy(),
    done: formFinish(),
    ok: function(data) {
        app.StockItem.data.add(data);
        var form = $(this);
        form.trigger('reset');
        storageUnit.attr('disabled', 'disabled').html('');
        usageUnit.attr('disabled', 'disabled').html('');
        $('input:first', form).focus();
    }
});

$(document).ready(function() {
    $('select[name=unit_type]').on('change', function(e) {
        var node = $(this);

        if (node.val()) {
            storageUnit.removeAttr('disabled').html('');
            usageUnit.removeAttr('disabled').html('');

            var items = groupedUnits[node.val()]['units'];
            if (items) {
                $(items).each(function(i, v) {
                    var optionTpl = $('<option value="' + v['id'] + '">' + v['description'] + ' (' + v['name'] + ')</option>');
                    storageUnit.append(optionTpl.clone());
                    usageUnit.append(optionTpl.clone());
                });
            }
        } else {
            storageUnit.attr('disabled', 'disabled').html('');
            usageUnit.attr('disabled', 'disabled').html('');
        }
    });
});
