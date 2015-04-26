<?php

namespace DoctrineORMModule\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150424190424 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE accounts (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, billing_user_id INT NOT NULL, name VARCHAR(255) NOT NULL, address LONGTEXT NOT NULL, phone VARCHAR(255) NOT NULL, fax VARCHAR(255) NOT NULL, website VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, facebook VARCHAR(255) NOT NULL, twitter VARCHAR(255) NOT NULL, default_timezone VARCHAR(255) NOT NULL, default_currency VARCHAR(255) NOT NULL, default_locale VARCHAR(255) NOT NULL, max_users INT DEFAULT NULL, max_outlets INT DEFAULT NULL, max_warehouses INT DEFAULT NULL, max_menus INT DEFAULT NULL, max_stock_items INT DEFAULT NULL, next_due_date DATE DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_CAC89EAC7E3C61F9 (owner_id), INDEX IDX_CAC89EACCC405842 (billing_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, color VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_3AF346685E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE classifications (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_4A95A4E35E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ingredients (id INT AUTO_INCREMENT NOT NULL, type_id INT NOT NULL, menu_id INT NOT NULL, stock_item_id INT NOT NULL, position INT NOT NULL, quantity DOUBLE PRECISION NOT NULL, qty_price NUMERIC(19, 2) NOT NULL, note VARCHAR(255) NOT NULL, INDEX IDX_4B60114FC54C8C93 (type_id), INDEX IDX_4B60114FCCD7E912 (menu_id), INDEX IDX_4B60114FBC942FD (stock_item_id), UNIQUE INDEX ingredient_unique_menu_id_and_stock_item_id (menu_id, stock_item_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ingredient_types (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, color VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_4002EEF15E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE level_changes (id INT AUTO_INCREMENT NOT NULL, corrector_id INT DEFAULT NULL, stock_id INT NOT NULL, current_level DOUBLE PRECISION NOT NULL, delta DOUBLE PRECISION NOT NULL, auto TINYINT(1) NOT NULL, type VARCHAR(255) NOT NULL, note VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_81889D223A6E8746 (corrector_id), INDEX IDX_81889D22DCD6110 (stock_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE logs (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, user_name VARCHAR(255) NOT NULL, resource_id INT NOT NULL, resource_type VARCHAR(255) NOT NULL, action VARCHAR(255) NOT NULL, params VARCHAR(255) NOT NULL, message LONGTEXT NOT NULL, data LONGTEXT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_F08FC65CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE menus (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, note LONGTEXT NOT NULL, serving_unit VARCHAR(255) NOT NULL, default_price NUMERIC(19, 2) NOT NULL, status VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_727508CF5E237E06 (name), INDEX IDX_727508CF727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE menu_prices (id INT AUTO_INCREMENT NOT NULL, outlet_id INT NOT NULL, menu_id INT NOT NULL, price NUMERIC(19, 2) NOT NULL, INDEX IDX_378C3581CAFD2DE2 (outlet_id), INDEX IDX_378C3581CCD7E912 (menu_id), UNIQUE INDEX menu_price_unique_outlet_id_and_menu_id (outlet_id, menu_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE menu_sales (id INT AUTO_INCREMENT NOT NULL, menu_price_id INT NOT NULL, sale_item_id INT NOT NULL, INDEX IDX_FA2D5323FA458271 (menu_price_id), UNIQUE INDEX UNIQ_FA2D5323677190CC (sale_item_id), UNIQUE INDEX menu_sale_unique_menu_price_id_and_sale_item_id (menu_price_id, sale_item_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE menu_types (id INT AUTO_INCREMENT NOT NULL, menu_id INT NOT NULL, type_id INT NOT NULL, INDEX IDX_C89CAA57CCD7E912 (menu_id), INDEX IDX_C89CAA57C54C8C93 (type_id), UNIQUE INDEX menu_type_unique_menu_id_and_type_id (menu_id, type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notifications (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, message LONGTEXT NOT NULL, status VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, notify_at DATETIME DEFAULT NULL, expired_at DATETIME DEFAULT NULL, INDEX IDX_6000B0D3A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE outlets (id INT AUTO_INCREMENT NOT NULL, warehouse_id INT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, latitude VARCHAR(255) DEFAULT NULL, longitude VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_A9CE40B5E237E06 (name), INDEX IDX_A9CE40B5080ECDE (warehouse_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE outlet_managers (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, outlet_id INT NOT NULL, INDEX IDX_471DC17AA76ED395 (user_id), INDEX IDX_471DC17ACAFD2DE2 (outlet_id), UNIQUE INDEX outlet_manager_unique_user_id_and_outlet_id (user_id, outlet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE outlet_sales (id INT AUTO_INCREMENT NOT NULL, outlet_id INT NOT NULL, sale_id INT NOT NULL, INDEX IDX_3BC4FB69CAFD2DE2 (outlet_id), UNIQUE INDEX UNIQ_3BC4FB694A7E4868 (sale_id), UNIQUE INDEX outlet_sale_unique_outlet_id_and_sale_id (outlet_id, sale_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE price_histories (id INT AUTO_INCREMENT NOT NULL, menu_price_id INT NOT NULL, price NUMERIC(19, 2) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_4C40FD5FFA458271 (menu_price_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE purchases (id INT AUTO_INCREMENT NOT NULL, creator_id INT DEFAULT NULL, supplier_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, po_number VARCHAR(255) DEFAULT NULL, currency VARCHAR(255) NOT NULL, note LONGTEXT NOT NULL, status VARCHAR(255) NOT NULL, ordered_at DATETIME DEFAULT NULL, delivered_at DATETIME DEFAULT NULL, canceled_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, last_update DATETIME DEFAULT NULL, INDEX IDX_AA6431FE61220EA6 (creator_id), INDEX IDX_AA6431FE2ADD6D8C (supplier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE purchase_items (id INT AUTO_INCREMENT NOT NULL, purchase_id INT NOT NULL, item_name VARCHAR(255) NOT NULL, note LONGTEXT NOT NULL, unit_price NUMERIC(19, 2) NOT NULL, quantity DOUBLE PRECISION NOT NULL, unit VARCHAR(255) NOT NULL, subtotal NUMERIC(19, 2) NOT NULL, total NUMERIC(19, 2) NOT NULL, INDEX IDX_35D56A67558FBEB9 (purchase_id), UNIQUE INDEX purchase_item_unique_purchase_id_and_item_name (purchase_id, item_name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sales (id INT AUTO_INCREMENT NOT NULL, creator_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, currency VARCHAR(255) NOT NULL, note LONGTEXT NOT NULL, type VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, ordered_at DATETIME NOT NULL, paid_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, last_update DATETIME DEFAULT NULL, INDEX IDX_6B81704461220EA6 (creator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sale_items (id INT AUTO_INCREMENT NOT NULL, sale_id INT NOT NULL, item_name VARCHAR(255) NOT NULL, quantity DOUBLE PRECISION NOT NULL, unit_price NUMERIC(19, 2) NOT NULL, subtotal NUMERIC(19, 2) NOT NULL, total NUMERIC(19, 2) NOT NULL, note LONGTEXT NOT NULL, INDEX IDX_31C2B1CE4A7E4868 (sale_id), UNIQUE INDEX sale_item_unique_sale_id_and_item_name (sale_id, item_name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stocks (id INT AUTO_INCREMENT NOT NULL, warehouse_id INT NOT NULL, stock_item_id INT NOT NULL, current_unit_price NUMERIC(19, 2) DEFAULT NULL, current_level DOUBLE PRECISION NOT NULL, reorder_level DOUBLE PRECISION DEFAULT NULL, last_change DATETIME DEFAULT NULL, last_purchase DATETIME DEFAULT NULL, INDEX IDX_56F798055080ECDE (warehouse_id), INDEX IDX_56F79805BC942FD (stock_item_id), UNIQUE INDEX stock_unique_warehouse_id_and_stock_item_id (warehouse_id, stock_item_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stock_categories (id INT AUTO_INCREMENT NOT NULL, stock_item_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_94BAB1C4BC942FD (stock_item_id), INDEX IDX_94BAB1C412469DE2 (category_id), UNIQUE INDEX stock_category_unique_stock_item_id_and_category_id (stock_item_id, category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stock_classifications (id INT AUTO_INCREMENT NOT NULL, stock_item_id INT NOT NULL, classification_id INT NOT NULL, INDEX IDX_AB3CDF42BC942FD (stock_item_id), INDEX IDX_AB3CDF422A86559F (classification_id), UNIQUE INDEX stock_classification_unique_stock_item_id_and_classification_id (stock_item_id, classification_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stock_items (id INT AUTO_INCREMENT NOT NULL, type_id INT DEFAULT NULL, storage_unit_id INT NOT NULL, usage_unit_id INT NOT NULL, code VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, note LONGTEXT NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_7D0EF5BC77153098 (code), INDEX IDX_7D0EF5BCC54C8C93 (type_id), INDEX IDX_7D0EF5BC8F81436F (storage_unit_id), INDEX IDX_7D0EF5BCA6390612 (usage_unit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stock_purchases (id INT AUTO_INCREMENT NOT NULL, stock_id INT NOT NULL, purchase_item_id INT NOT NULL, INDEX IDX_32F9D716DCD6110 (stock_id), UNIQUE INDEX UNIQ_32F9D7169B59827 (purchase_item_id), UNIQUE INDEX stock_purchase_unique_stock_id_and_purchase_item_id (stock_id, purchase_item_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stock_units (id INT AUTO_INCREMENT NOT NULL, type_id INT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, ratio DOUBLE PRECISION NOT NULL, UNIQUE INDEX UNIQ_75A068B85E237E06 (name), INDEX IDX_75A068B8C54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE storage_types (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_34E440105E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE suppliers (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, contact VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, website VARCHAR(255) NOT NULL, note LONGTEXT NOT NULL, UNIQUE INDEX UNIQ_AC28B95C5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tokens (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, type VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, expired_at DATETIME DEFAULT NULL, INDEX IDX_AA5A118EA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE types (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_593089305E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE unit_types (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_D202F73B5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, password_changed TINYINT(1) NOT NULL, contact_no VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, bio LONGTEXT NOT NULL, status VARCHAR(255) NOT NULL, timezone VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE warehouses (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, latitude VARCHAR(255) DEFAULT NULL, longitude VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_AFE9C2B75E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE warehouse_managers (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, warehouse_id INT NOT NULL, INDEX IDX_890182A0A76ED395 (user_id), INDEX IDX_890182A05080ECDE (warehouse_id), UNIQUE INDEX warehouse_manager_unique_user_id_and_warehouse_id (user_id, warehouse_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE warehouse_purchases (id INT AUTO_INCREMENT NOT NULL, warehouse_id INT NOT NULL, purchase_id INT NOT NULL, INDEX IDX_95F17F415080ECDE (warehouse_id), UNIQUE INDEX UNIQ_95F17F41558FBEB9 (purchase_id), UNIQUE INDEX warehouse_purchase_unique_warehouse_id_and_purchase_id (warehouse_id, purchase_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE accounts ADD CONSTRAINT FK_CAC89EAC7E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id) ON DELETE RESTRICT');
        $this->addSql('ALTER TABLE accounts ADD CONSTRAINT FK_CAC89EACCC405842 FOREIGN KEY (billing_user_id) REFERENCES users (id) ON DELETE RESTRICT');
        $this->addSql('ALTER TABLE ingredients ADD CONSTRAINT FK_4B60114FC54C8C93 FOREIGN KEY (type_id) REFERENCES ingredient_types (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ingredients ADD CONSTRAINT FK_4B60114FCCD7E912 FOREIGN KEY (menu_id) REFERENCES menus (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ingredients ADD CONSTRAINT FK_4B60114FBC942FD FOREIGN KEY (stock_item_id) REFERENCES stock_items (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE level_changes ADD CONSTRAINT FK_81889D223A6E8746 FOREIGN KEY (corrector_id) REFERENCES users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE level_changes ADD CONSTRAINT FK_81889D22DCD6110 FOREIGN KEY (stock_id) REFERENCES stocks (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE logs ADD CONSTRAINT FK_F08FC65CA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE menus ADD CONSTRAINT FK_727508CF727ACA70 FOREIGN KEY (parent_id) REFERENCES menus (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE menu_prices ADD CONSTRAINT FK_378C3581CAFD2DE2 FOREIGN KEY (outlet_id) REFERENCES outlets (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE menu_prices ADD CONSTRAINT FK_378C3581CCD7E912 FOREIGN KEY (menu_id) REFERENCES menus (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE menu_sales ADD CONSTRAINT FK_FA2D5323FA458271 FOREIGN KEY (menu_price_id) REFERENCES menu_prices (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE menu_sales ADD CONSTRAINT FK_FA2D5323677190CC FOREIGN KEY (sale_item_id) REFERENCES sale_items (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE menu_types ADD CONSTRAINT FK_C89CAA57CCD7E912 FOREIGN KEY (menu_id) REFERENCES menus (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE menu_types ADD CONSTRAINT FK_C89CAA57C54C8C93 FOREIGN KEY (type_id) REFERENCES types (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D3A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE outlets ADD CONSTRAINT FK_A9CE40B5080ECDE FOREIGN KEY (warehouse_id) REFERENCES warehouses (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE outlet_managers ADD CONSTRAINT FK_471DC17AA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE outlet_managers ADD CONSTRAINT FK_471DC17ACAFD2DE2 FOREIGN KEY (outlet_id) REFERENCES outlets (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE outlet_sales ADD CONSTRAINT FK_3BC4FB69CAFD2DE2 FOREIGN KEY (outlet_id) REFERENCES outlets (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE outlet_sales ADD CONSTRAINT FK_3BC4FB694A7E4868 FOREIGN KEY (sale_id) REFERENCES sales (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE price_histories ADD CONSTRAINT FK_4C40FD5FFA458271 FOREIGN KEY (menu_price_id) REFERENCES menu_prices (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE purchases ADD CONSTRAINT FK_AA6431FE61220EA6 FOREIGN KEY (creator_id) REFERENCES users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE purchases ADD CONSTRAINT FK_AA6431FE2ADD6D8C FOREIGN KEY (supplier_id) REFERENCES suppliers (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE purchase_items ADD CONSTRAINT FK_35D56A67558FBEB9 FOREIGN KEY (purchase_id) REFERENCES purchases (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sales ADD CONSTRAINT FK_6B81704461220EA6 FOREIGN KEY (creator_id) REFERENCES users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE sale_items ADD CONSTRAINT FK_31C2B1CE4A7E4868 FOREIGN KEY (sale_id) REFERENCES sales (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE stocks ADD CONSTRAINT FK_56F798055080ECDE FOREIGN KEY (warehouse_id) REFERENCES warehouses (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE stocks ADD CONSTRAINT FK_56F79805BC942FD FOREIGN KEY (stock_item_id) REFERENCES stock_items (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE stock_categories ADD CONSTRAINT FK_94BAB1C4BC942FD FOREIGN KEY (stock_item_id) REFERENCES stock_items (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE stock_categories ADD CONSTRAINT FK_94BAB1C412469DE2 FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE stock_classifications ADD CONSTRAINT FK_AB3CDF42BC942FD FOREIGN KEY (stock_item_id) REFERENCES stock_items (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE stock_classifications ADD CONSTRAINT FK_AB3CDF422A86559F FOREIGN KEY (classification_id) REFERENCES classifications (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE stock_items ADD CONSTRAINT FK_7D0EF5BCC54C8C93 FOREIGN KEY (type_id) REFERENCES storage_types (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE stock_items ADD CONSTRAINT FK_7D0EF5BC8F81436F FOREIGN KEY (storage_unit_id) REFERENCES stock_units (id) ON DELETE RESTRICT');
        $this->addSql('ALTER TABLE stock_items ADD CONSTRAINT FK_7D0EF5BCA6390612 FOREIGN KEY (usage_unit_id) REFERENCES stock_units (id) ON DELETE RESTRICT');
        $this->addSql('ALTER TABLE stock_purchases ADD CONSTRAINT FK_32F9D716DCD6110 FOREIGN KEY (stock_id) REFERENCES stocks (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE stock_purchases ADD CONSTRAINT FK_32F9D7169B59827 FOREIGN KEY (purchase_item_id) REFERENCES purchase_items (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE stock_units ADD CONSTRAINT FK_75A068B8C54C8C93 FOREIGN KEY (type_id) REFERENCES unit_types (id) ON DELETE RESTRICT');
        $this->addSql('ALTER TABLE tokens ADD CONSTRAINT FK_AA5A118EA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE warehouse_managers ADD CONSTRAINT FK_890182A0A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE warehouse_managers ADD CONSTRAINT FK_890182A05080ECDE FOREIGN KEY (warehouse_id) REFERENCES warehouses (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE warehouse_purchases ADD CONSTRAINT FK_95F17F415080ECDE FOREIGN KEY (warehouse_id) REFERENCES warehouses (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE warehouse_purchases ADD CONSTRAINT FK_95F17F41558FBEB9 FOREIGN KEY (purchase_id) REFERENCES purchases (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE stock_categories DROP FOREIGN KEY FK_94BAB1C412469DE2');
        $this->addSql('ALTER TABLE stock_classifications DROP FOREIGN KEY FK_AB3CDF422A86559F');
        $this->addSql('ALTER TABLE ingredients DROP FOREIGN KEY FK_4B60114FC54C8C93');
        $this->addSql('ALTER TABLE ingredients DROP FOREIGN KEY FK_4B60114FCCD7E912');
        $this->addSql('ALTER TABLE menus DROP FOREIGN KEY FK_727508CF727ACA70');
        $this->addSql('ALTER TABLE menu_prices DROP FOREIGN KEY FK_378C3581CCD7E912');
        $this->addSql('ALTER TABLE menu_types DROP FOREIGN KEY FK_C89CAA57CCD7E912');
        $this->addSql('ALTER TABLE menu_sales DROP FOREIGN KEY FK_FA2D5323FA458271');
        $this->addSql('ALTER TABLE price_histories DROP FOREIGN KEY FK_4C40FD5FFA458271');
        $this->addSql('ALTER TABLE menu_prices DROP FOREIGN KEY FK_378C3581CAFD2DE2');
        $this->addSql('ALTER TABLE outlet_managers DROP FOREIGN KEY FK_471DC17ACAFD2DE2');
        $this->addSql('ALTER TABLE outlet_sales DROP FOREIGN KEY FK_3BC4FB69CAFD2DE2');
        $this->addSql('ALTER TABLE purchase_items DROP FOREIGN KEY FK_35D56A67558FBEB9');
        $this->addSql('ALTER TABLE warehouse_purchases DROP FOREIGN KEY FK_95F17F41558FBEB9');
        $this->addSql('ALTER TABLE stock_purchases DROP FOREIGN KEY FK_32F9D7169B59827');
        $this->addSql('ALTER TABLE outlet_sales DROP FOREIGN KEY FK_3BC4FB694A7E4868');
        $this->addSql('ALTER TABLE sale_items DROP FOREIGN KEY FK_31C2B1CE4A7E4868');
        $this->addSql('ALTER TABLE menu_sales DROP FOREIGN KEY FK_FA2D5323677190CC');
        $this->addSql('ALTER TABLE level_changes DROP FOREIGN KEY FK_81889D22DCD6110');
        $this->addSql('ALTER TABLE stock_purchases DROP FOREIGN KEY FK_32F9D716DCD6110');
        $this->addSql('ALTER TABLE ingredients DROP FOREIGN KEY FK_4B60114FBC942FD');
        $this->addSql('ALTER TABLE stocks DROP FOREIGN KEY FK_56F79805BC942FD');
        $this->addSql('ALTER TABLE stock_categories DROP FOREIGN KEY FK_94BAB1C4BC942FD');
        $this->addSql('ALTER TABLE stock_classifications DROP FOREIGN KEY FK_AB3CDF42BC942FD');
        $this->addSql('ALTER TABLE stock_items DROP FOREIGN KEY FK_7D0EF5BC8F81436F');
        $this->addSql('ALTER TABLE stock_items DROP FOREIGN KEY FK_7D0EF5BCA6390612');
        $this->addSql('ALTER TABLE stock_items DROP FOREIGN KEY FK_7D0EF5BCC54C8C93');
        $this->addSql('ALTER TABLE purchases DROP FOREIGN KEY FK_AA6431FE2ADD6D8C');
        $this->addSql('ALTER TABLE menu_types DROP FOREIGN KEY FK_C89CAA57C54C8C93');
        $this->addSql('ALTER TABLE stock_units DROP FOREIGN KEY FK_75A068B8C54C8C93');
        $this->addSql('ALTER TABLE accounts DROP FOREIGN KEY FK_CAC89EAC7E3C61F9');
        $this->addSql('ALTER TABLE accounts DROP FOREIGN KEY FK_CAC89EACCC405842');
        $this->addSql('ALTER TABLE level_changes DROP FOREIGN KEY FK_81889D223A6E8746');
        $this->addSql('ALTER TABLE logs DROP FOREIGN KEY FK_F08FC65CA76ED395');
        $this->addSql('ALTER TABLE notifications DROP FOREIGN KEY FK_6000B0D3A76ED395');
        $this->addSql('ALTER TABLE outlet_managers DROP FOREIGN KEY FK_471DC17AA76ED395');
        $this->addSql('ALTER TABLE purchases DROP FOREIGN KEY FK_AA6431FE61220EA6');
        $this->addSql('ALTER TABLE sales DROP FOREIGN KEY FK_6B81704461220EA6');
        $this->addSql('ALTER TABLE tokens DROP FOREIGN KEY FK_AA5A118EA76ED395');
        $this->addSql('ALTER TABLE warehouse_managers DROP FOREIGN KEY FK_890182A0A76ED395');
        $this->addSql('ALTER TABLE outlets DROP FOREIGN KEY FK_A9CE40B5080ECDE');
        $this->addSql('ALTER TABLE stocks DROP FOREIGN KEY FK_56F798055080ECDE');
        $this->addSql('ALTER TABLE warehouse_managers DROP FOREIGN KEY FK_890182A05080ECDE');
        $this->addSql('ALTER TABLE warehouse_purchases DROP FOREIGN KEY FK_95F17F415080ECDE');
        $this->addSql('DROP TABLE accounts');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE classifications');
        $this->addSql('DROP TABLE ingredients');
        $this->addSql('DROP TABLE ingredient_types');
        $this->addSql('DROP TABLE level_changes');
        $this->addSql('DROP TABLE logs');
        $this->addSql('DROP TABLE menus');
        $this->addSql('DROP TABLE menu_prices');
        $this->addSql('DROP TABLE menu_sales');
        $this->addSql('DROP TABLE menu_types');
        $this->addSql('DROP TABLE notifications');
        $this->addSql('DROP TABLE outlets');
        $this->addSql('DROP TABLE outlet_managers');
        $this->addSql('DROP TABLE outlet_sales');
        $this->addSql('DROP TABLE price_histories');
        $this->addSql('DROP TABLE purchases');
        $this->addSql('DROP TABLE purchase_items');
        $this->addSql('DROP TABLE sales');
        $this->addSql('DROP TABLE sale_items');
        $this->addSql('DROP TABLE stocks');
        $this->addSql('DROP TABLE stock_categories');
        $this->addSql('DROP TABLE stock_classifications');
        $this->addSql('DROP TABLE stock_items');
        $this->addSql('DROP TABLE stock_purchases');
        $this->addSql('DROP TABLE stock_units');
        $this->addSql('DROP TABLE storage_types');
        $this->addSql('DROP TABLE suppliers');
        $this->addSql('DROP TABLE tokens');
        $this->addSql('DROP TABLE types');
        $this->addSql('DROP TABLE unit_types');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE warehouses');
        $this->addSql('DROP TABLE warehouse_managers');
        $this->addSql('DROP TABLE warehouse_purchases');
    }
}
