INSERT INTO `core_user` (`id`, `email`, `last_logged_at`, `created_at`, `updated_at`, `guid`) VALUES
(1, 'jsem@tomaskulhanek.cz', NULL, '2021-10-04 09:24:43', NULL, '8de36e61-28f8-4e10-bfe9-a0438b821d1b'),
(2, 'kulhanek@shoptet.cz', NULL, '2021-10-04 09:21:16', NULL, 'e8e5289f-0d87-444b-a5ce-ba09234c4810'),
(3, 'veravrsecka@seznam.cz', NULL, '2021-10-04 09:21:16', NULL, 'f3ca0509-9aec-44bf-8d8a-132830ed59a9');

INSERT INTO `sf_projects` (`id`, `owner_id`, `access_token`, `token_type`, `scope`, `eshop_id`, `eshop_url`, `contact_email`, `revoked`, `revoked_at`, `guid`, `created_at`, `updated_at`) VALUES
(1, 1, 'emnkdkkguxcupkp56phxr3wl5haps1svfi0o8t5zo0b2hl4ad73njhq6pwu9kntrpds4mh37d6qgo0gfbfetw9l4fa1xaljvrmbuto5unx2206lia6w1bjsp4hzmxx0mkybro78v06gcoyf3gffhomy1zoou9ehgs0g4vkyl3hlgqfi81940ix76cigpjfew4n2fyi8tyhvlda9mhbm9hqttcim5ba1t5cnswe0f6vb8kmqiw9unazpqrej94ul', 'bearer', 'api', 470424, 'https://shoptet.helppc.cz/', 'jsem@tomaskulhanek.cz', '', NULL, 'b7edca14-0374-484d-9fba-64a4c5d54445', '2021-10-04 09:24:43', '2021-10-04 09:29:06');

INSERT INTO `sf_users_projects` (`project_id`, `user_id`) VALUES
(1, 1),
(1, 2),
(1, 3);


-- invoices
INSERT INTO `sf_invoice` (`id`, `project_id`, `proforma_invoice_code`, `tax_date`, `document_remark`, `code`, `paid`, `order_code`, `addresses_equal`, `is_valid`, `var_symbol`, `const_symbol`, `spec_symbol`, `creation_time`, `change_time`, `due_date`, `billing_method_id`, `billing_method_name`, `vat`, `vat_rate`, `to_pay`, `currency_code`, `with_vat`, `without_vat`, `exchange_rate`, `eshop_bank_account`, `eshop_iban`, `eshop_bic`, `eshop_tax_mode`, `eshop_document_remark`, `vat_payer`, `weight`, `complete_package_weight`, `external_system_id`, `external_system_last_sync_at`) VALUES
(1, 1, '2021000002', '2021-10-02', NULL, '2021000002', 1, '2021000001', 1, 1, 2021000001, '663', NULL, '2021-10-02 00:03:12', '2021-10-04 09:27:48', '2021-10-16', 2, 'Převodem', '0.00', NULL, '0.00', 'CZK', '0.00', '0.00', '1.00000000', '', '', '', 'ORDINARY', NULL, 0, '0.000', '0.000', NULL, NULL),
(2, 1, NULL, '2021-10-01', NULL, '2021000001', 1, NULL, 1, 1, 2021000001, NULL, NULL, '2021-10-01 10:33:04', '2021-10-04 09:27:47', '2021-10-15', NULL, NULL, '0.00', NULL, '33.00', 'CZK', '33.00', '33.00', '1.00000000', '', '', '', NULL, NULL, 0, '0.000', '0.000', NULL, NULL);

INSERT INTO `sf_invoice_billing_address` (`id`, `document_id`, `company`, `full_name`, `street`, `house_number`, `city`, `district`, `additional`, `zip`, `country_code`, `region_name`, `region_shortcut`) VALUES
(1, 1, NULL, 'Tomáš Kulhánek', 'Jankova', NULL, 'Praha', NULL, NULL, '10000', 'CZ', NULL, NULL),
(2, 2, 'asd', 'ads', NULL, NULL, NULL, NULL, NULL, NULL, 'CZ', NULL, NULL);

INSERT INTO `sf_invoice_item` (`id`, `document_id`, `product_guid`, `item_type`, `code`, `name`, `variant_name`, `brand`, `amount`, `amount_unit`, `weight`, `remark`, `price_ratio`, `additional_field`, `with_vat`, `without_vat`, `vat`, `vat_rate`, `control_hash`) VALUES
(1, 1, '98bbd2e4-43b0-11e8-a05f-0800273dc42e', 'product', '64', 'Koš odpadkový Curver FLIPBIN 25l NEW YORK', NULL, NULL, '1.000', 'ks', '0.000', NULL, '1.0000', NULL, '310.00', '310.00', '0.00', '0.00', '55db5323cc4630eb7287cf20c51beae4f00c9b4b'),
(2, 1, NULL, 'billing', NULL, 'Hotově', NULL, NULL, '1.000', NULL, '0.000', NULL, '1.0000', NULL, '0.00', '0.00', '0.00', '0.00', '859d844d39ab557c4de41adcb415e983399612cf'),
(3, 1, NULL, 'shipping', NULL, 'Osobní odběr', NULL, NULL, '1.000', NULL, '0.000', NULL, '1.0000', NULL, '0.00', '0.00', '0.00', '0.00', '85d9075b4b12c01682e35456b0f899e7e8de9675'),
(4, 2, NULL, 'product', NULL, 'sdfsdfsdf', NULL, NULL, '1.000', 'ks', '0.000', '', '1.0000', NULL, '33.00', '33.00', '0.00', '0.00', 'abfd105f3dc3ac0879fc047eec74c3b19a52ab44');


-- orders
INSERT INTO `sf_order` (`id`, `project_id`, `code`, `external_code`, `creation_time`, `change_time`, `email`, `phone`, `birth_date`, `client_code`, `company_id`, `vat_id`, `tax_id`, `vat_payer`, `customer_guid`, `addresses_equal`, `cash_desk_order`, `stock_id`, `paid`, `admin_url`, `online_payment_link`, `language`, `referer`, `billing_method_id`, `billing_method_name`, `status_id`, `status_name`, `price_vat`, `price_vat_rate`, `price_to_pay`, `price_currency_code`, `price_with_vat`, `price_without_vat`, `price_exchange_rate`, `client_ipaddress`) VALUES
(1, 1, '2021000002', NULL, '2021-10-03 22:07:28', '2021-10-04 09:25:32', 'jaruska@helppc.cz', '+420606606606', NULL, NULL, NULL, '', '', 0, NULL, 1, 0, 1, 0, 'https://shoptet.helppc.cz/admin/objednavky-detail?id=15', NULL, 'cs', 'https://crm.shoptet.cz/', 2, 'Převodem', -2, 'Vyřizuje se', '0.00', NULL, '310.00', 'CZK', '310.00', '310.00', '1.00000000', '89.177.74.186'),
(2, 1, 'DEMO000003', NULL, '2021-09-23 17:31:27', '2021-10-04 09:25:33', 'test@test.cz', '+420722123456', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, 0, 1, 0, 'https://shoptet.helppc.cz/admin/objednavky-detail?id=3', NULL, 'cs', NULL, NULL, NULL, -2, 'Vyřizuje se', '0.00', NULL, '0.00', 'CZK', '0.00', '0.00', '1.00000000', '127.0.0.1'),
(3, 1, 'DEMO000004', NULL, '2021-09-29 05:31:27', '2021-10-04 09:25:32', 'test@test.cz', '+420722123456', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, 0, 1, 0, 'https://shoptet.helppc.cz/admin/objednavky-detail?id=4', NULL, 'cs', NULL, NULL, NULL, -2, 'Vyřizuje se', '0.00', NULL, '0.00', 'CZK', '0.00', '0.00', '1.00000000', '127.0.0.1'),
(4, 1, 'DEMO000002', NULL, '2021-08-31 17:31:27', '2021-10-04 09:27:25', 'test@test.cz', '+420722123456', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, 0, 1, 0, 'https://shoptet.helppc.cz/admin/objednavky-detail?id=2', NULL, 'cs', NULL, NULL, NULL, -1, 'Nevyřízená', '0.00', NULL, '0.00', 'CZK', '0.00', '0.00', '1.00000000', '127.0.0.1'),
(5, 1, 'DEMO000001', NULL, '2021-06-02 17:31:27', '2021-10-04 09:27:25', 'test@test.cz', '+420722123456', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, 0, 1, 0, 'https://shoptet.helppc.cz/admin/objednavky-detail?id=1', NULL, 'cs', NULL, NULL, NULL, -1, 'Nevyřízená', '0.00', NULL, '0.00', 'CZK', '0.00', '0.00', '1.00000000', '127.0.0.1');

INSERT INTO `sf_order_billing_address` (`id`, `document_id`, `company`, `full_name`, `street`, `house_number`, `city`, `district`, `additional`, `zip`, `country_code`, `region_name`, `region_shortcut`) VALUES
(1, 1, NULL, 'Jaruška Maličká', 'Jánošíkova 320', NULL, 'Praha', NULL, NULL, '14900', 'CZ', NULL, NULL),
(2, 2, 'Název společnosti', 'Jméno a příjmení', 'Ulice', NULL, 'Město', NULL, NULL, '111 00', 'CZ', NULL, NULL),
(3, 3, 'Název společnosti', 'Jméno a příjmení', 'Ulice', NULL, 'Město', NULL, NULL, '111 00', 'CZ', NULL, NULL),
(4, 4, 'Název společnosti', 'Jméno a příjmení', 'Ulice', NULL, 'Město', NULL, NULL, '111 00', 'CZ', NULL, NULL),
(5, 5, 'Název společnosti', 'Jméno a příjmení', 'Ulice', NULL, 'Město', NULL, NULL, '111 00', 'CZ', NULL, NULL);

INSERT INTO `sf_order_delivery_address` (`id`, `document_id`, `company`, `full_name`, `street`, `house_number`, `city`, `district`, `additional`, `zip`, `country_code`, `region_name`, `region_shortcut`) VALUES
(1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

INSERT INTO `sf_order_item` (`id`, `document_id`, `supplier_name`, `amount_completed`, `buy_price_with_vat`, `buy_price_without_vat`, `buy_price_vat`, `buy_price_vat_rate`, `recycling_fee_category`, `recycling_fee`, `status_id`, `status_name`, `main_image_name`, `main_image_neo_name`, `main_image_cdn_name`, `main_image_priority`, `main_image_description`, `stock_location`, `item_id`, `warranty_description`, `product_guid`, `code`, `item_type`, `name`, `variant_name`, `brand`, `remark`, `weight`, `additional_field`, `amount`, `amount_unit`, `price_ratio`, `item_price_with_vat`, `item_price_without_vat`, `item_price_vat`, `item_price_vat_rate`, `control_hash`) VALUES
(1, 1, NULL, '0.000', NULL, NULL, NULL, NULL, NULL, NULL, '-1', 'Nevyřízená', NULL, NULL, NULL, NULL, NULL, NULL, 48, NULL, NULL, NULL, 'billing', 'Převodem', NULL, NULL, NULL, '0.000', NULL, '1.000', NULL, '1.0000', '0.00', '0.00', '0.00', '0.00', '12725e6c771279a0f30d92c02825f7534a05aeeb'),
(2, 2, NULL, '0.000', NULL, NULL, NULL, NULL, NULL, NULL, '-4', 'Stornována', NULL, NULL, NULL, NULL, NULL, NULL, 12, NULL, NULL, NULL, 'billing', 'Hotově', NULL, NULL, NULL, '0.000', NULL, '1.000', NULL, '1.0000', '0.00', '0.00', '0.00', '0.00', '859d844d39ab557c4de41adcb415e983399612cf'),
(3, 3, NULL, '0.000', NULL, NULL, NULL, NULL, NULL, NULL, '-4', 'Stornována', NULL, NULL, NULL, NULL, NULL, NULL, 15, NULL, NULL, NULL, 'billing', 'Hotově', NULL, NULL, NULL, '0.000', NULL, '1.000', NULL, '1.0000', '0.00', '0.00', '0.00', '0.00', '859d844d39ab557c4de41adcb415e983399612cf'),
(4, 1, NULL, '0.000', NULL, NULL, NULL, NULL, NULL, NULL, '-1', 'Nevyřízená', NULL, NULL, NULL, NULL, NULL, NULL, 45, NULL, NULL, NULL, 'shipping', 'GLS', NULL, NULL, NULL, '0.000', NULL, '1.000', NULL, '1.0000', '0.00', '0.00', '0.00', '0.00', 'aaa03eba5680afe47daf1fb39ef0bfbcedd9bc34'),
(5, 2, NULL, '0.000', NULL, NULL, NULL, NULL, NULL, NULL, '-4', 'Stornována', NULL, NULL, NULL, NULL, NULL, NULL, 11, NULL, NULL, NULL, 'shipping', 'Česká Pošta', NULL, NULL, NULL, '0.000', NULL, '1.000', NULL, '1.0000', '0.00', '0.00', '0.00', '0.00', 'a0f5f2ad2fd79ed41ccb6dcf908e489ca3e33af8'),
(6, 3, NULL, '0.000', NULL, NULL, NULL, NULL, NULL, NULL, '-4', 'Stornována', NULL, NULL, NULL, NULL, NULL, NULL, 14, NULL, NULL, NULL, 'shipping', 'Česká Pošta', NULL, NULL, NULL, '0.000', NULL, '1.000', NULL, '1.0000', '0.00', '0.00', '0.00', '0.00', 'a0f5f2ad2fd79ed41ccb6dcf908e489ca3e33af8'),
(7, 1, NULL, '0.000', NULL, NULL, NULL, NULL, NULL, NULL, '-1', 'Nevyřízená', NULL, NULL, NULL, NULL, NULL, NULL, 42, '2 roky', '98bbd2e4-43b0-11e8-a05f-0800273dc42e', '64', 'product', 'Koš odpadkový Curver FLIPBIN 25l NEW YORK', NULL, NULL, NULL, '0.000', NULL, '1.000', 'ks', '1.0000', '310.00', '310.00', '0.00', '0.00', '55db5323cc4630eb7287cf20c51beae4f00c9b4b'),
(8, 3, NULL, '0.000', '400.00', '400.00', '0.00', '0.00', NULL, NULL, '-4', 'Stornována', NULL, NULL, NULL, NULL, NULL, NULL, 13, NULL, '533bb41d-d978-11e0-b04f-57a43310b768', '0021', 'product', 'Nike The Next', NULL, 'Nike', NULL, '0.000', NULL, '2.000', 'ks', '1.0000', '520.00', '520.00', '0.00', '0.00', 'a36f8bbf6bee67451fdb61feb538179f06541297'),
(9, 2, NULL, '0.000', '400.00', '400.00', '0.00', '0.00', NULL, NULL, '-4', 'Stornována', NULL, NULL, NULL, NULL, NULL, NULL, 10, NULL, '533bb41d-d978-11e0-b04f-57a43310b768', '0021', 'product', 'Nike The Next', NULL, 'Nike', NULL, '0.000', NULL, '2.000', 'ks', '1.0000', '520.00', '520.00', '0.00', '0.00', 'a36f8bbf6bee67451fdb61feb538179f06541297'),
(10, 4, NULL, '0.000', NULL, NULL, NULL, NULL, NULL, NULL, '-4', 'Stornována', NULL, NULL, NULL, NULL, NULL, NULL, 21, NULL, NULL, NULL, 'shipping', 'Zásilkovna Z point', NULL, NULL, NULL, '0.000', 'Albrechtice nad Vltavou, Albrechtice nad Vltavou 79', '1.000', NULL, '1.0000', '0.00', '0.00', '0.00', '0.00', 'f485c0e9b50c50521abcad2175cdfdfcf76a2814'),
(11, 4, NULL, '0.000', NULL, NULL, NULL, NULL, NULL, NULL, '-4', 'Stornována', NULL, NULL, NULL, NULL, NULL, NULL, 9, NULL, NULL, NULL, 'billing', 'Hotově', NULL, NULL, NULL, '0.000', NULL, '1.000', NULL, '1.0000', '0.00', '0.00', '0.00', '0.00', '859d844d39ab557c4de41adcb415e983399612cf'),
(12, 4, NULL, '0.000', '400.00', '400.00', '0.00', '0.00', NULL, NULL, '-4', 'Stornována', NULL, NULL, NULL, NULL, NULL, NULL, 7, NULL, '533bb41d-d978-11e0-b04f-57a43310b768', '0021', 'product', 'Nike The Next', NULL, 'Nike', NULL, '0.000', NULL, '2.000', 'ks', '1.0000', '520.00', '520.00', '0.00', '0.00', 'a36f8bbf6bee67451fdb61feb538179f06541297'),
(13, 5, NULL, '0.000', NULL, NULL, NULL, NULL, NULL, NULL, '-4', 'Stornována', NULL, NULL, NULL, NULL, NULL, NULL, 6, NULL, NULL, NULL, 'billing', 'Hotově', NULL, NULL, NULL, '0.000', NULL, '1.000', NULL, '1.0000', '0.00', '0.00', '0.00', '0.00', '859d844d39ab557c4de41adcb415e983399612cf'),
(14, 5, NULL, '0.000', NULL, NULL, NULL, NULL, NULL, NULL, '-4', 'Stornována', NULL, NULL, NULL, NULL, NULL, NULL, 5, NULL, NULL, NULL, 'shipping', 'Česká Pošta', NULL, NULL, NULL, '0.000', NULL, '1.000', NULL, '1.0000', '0.00', '0.00', '0.00', '0.00', 'a0f5f2ad2fd79ed41ccb6dcf908e489ca3e33af8'),
(15, 5, NULL, '0.000', '400.00', '400.00', '0.00', '0.00', NULL, NULL, '-4', 'Stornována', NULL, NULL, NULL, NULL, NULL, NULL, 4, NULL, '533bb41d-d978-11e0-b04f-57a43310b768', '0021', 'product', 'Nike The Next', NULL, 'Nike', NULL, '0.000', NULL, '2.000', 'ks', '1.0000', '520.00', '520.00', '0.00', '0.00', 'a36f8bbf6bee67451fdb61feb538179f06541297');

INSERT INTO `sf_order_payment_method` (`id`, `document_id`, `guid`, `name`, `item_id`) VALUES
(1, 1, '6f2c8e36-3faf-11e2-a723-705ab6a2ba75', 'Převodem', 48),
(2, 2, NULL, 'Hotově', 12),
(3, 3, NULL, 'Hotově', 15),
(4, 4, NULL, 'Hotově', 9),
(5, 5, NULL, 'Hotově', 6);

INSERT INTO `sf_order_shipping_method` (`id`, `document_id`, `guid`, `name`, `item_id`) VALUES
(1, 1, 'fdd64137-e5f1-11ea-a065-0cc47a6c92bc', 'GLS', 45),
(2, 2, NULL, 'Česká Pošta', 11),
(3, 3, NULL, 'Česká Pošta', 14),
(4, 4, 'f72a566c-e5f1-11ea-a065-0cc47a6c92bc', 'Zásilkovna Z point', 21),
(5, 5, NULL, 'Česká Pošta', 5);

-- proforma

INSERT INTO `sf_proforma_invoice` (`id`, `project_id`, `code`, `paid`, `order_code`, `addresses_equal`, `is_valid`, `var_symbol`, `const_symbol`, `spec_symbol`, `creation_time`, `change_time`, `due_date`, `billing_method_id`, `billing_method_name`, `vat`, `vat_rate`, `to_pay`, `currency_code`, `with_vat`, `without_vat`, `exchange_rate`, `eshop_bank_account`, `eshop_iban`, `eshop_bic`, `eshop_tax_mode`, `eshop_document_remark`, `vat_payer`, `weight`, `complete_package_weight`, `external_system_id`, `external_system_last_sync_at`) VALUES
(1, 1, '2021000001', 0, '2020', 1, 1, 2021000001, '0300', 666, '2021-09-30 21:24:52', '2021-10-04 09:29:06', '2021-10-14', 4, 'Kartou', '0.00', NULL, '116.00', 'CZK', '116.02', '116.02', '1.00000000', '', '', '', NULL, NULL, 0, '0.000', '0.000', NULL, NULL);

INSERT INTO `sf_proforma_invoice_billing_address` (`id`, `document_id`, `company`, `full_name`, `street`, `house_number`, `city`, `district`, `additional`, `zip`, `country_code`, `region_name`, `region_shortcut`) VALUES
(1, 1, 'Pokusník', 'Jan', NULL, NULL, NULL, NULL, NULL, NULL, 'CZ', NULL, NULL);

INSERT INTO `sf_proforma_invoice_item` (`id`, `document_id`, `product_guid`, `item_type`, `code`, `name`, `variant_name`, `brand`, `amount`, `amount_unit`, `weight`, `remark`, `price_ratio`, `additional_field`, `with_vat`, `without_vat`, `vat`, `vat_rate`, `control_hash`) VALUES
(1, 1, NULL, 'product', 'asdasd', 'asdasd', NULL, NULL, '1.000', 'ks', '0.000', '', '1.0000', NULL, '2.00', '2.00', '0.00', '0.00', 'ca8f5d9668e72d80f102aced42dda0e750140c10'),
(2, 1, NULL, 'product', 'asd', 'asdasdasd', NULL, NULL, '2.000', 'ks', '0.000', 'asdasd', '0.9700', NULL, '64.02', '64.02', '0.00', '0.00', 'f08337e1e731c51cc11c93103e328f224d6675f4'),
(3, 1, NULL, 'shipping', NULL, 'Česká pošta do ruky', NULL, NULL, '1.000', NULL, '0.000', NULL, '1.0000', NULL, '50.00', '50.00', '0.00', '0.00', '2b646ca164115de3b7fabb81c93be8fda84c1fce');

