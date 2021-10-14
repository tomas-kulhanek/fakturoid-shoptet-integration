UPDATE sf_order SET billing_method = 'cod' where billing_method_id = 1;
UPDATE sf_order SET billing_method = 'bank' where billing_method_id = 2;
UPDATE sf_order SET billing_method = 'cash' where billing_method_id = 3;
UPDATE sf_order SET billing_method = 'card' where billing_method_id = 4;

UPDATE sf_invoice SET billing_method = 'cod' where billing_method_id = 1;
UPDATE sf_invoice SET billing_method = 'bank' where billing_method_id = 2;
UPDATE sf_invoice SET billing_method = 'cash' where billing_method_id = 3;
UPDATE sf_invoice SET billing_method = 'card' where billing_method_id = 4;


UPDATE sf_proforma_invoice SET billing_method = 'cod' where billing_method_id = 1;
UPDATE sf_proforma_invoice SET billing_method = 'bank' where billing_method_id = 2;
UPDATE sf_proforma_invoice SET billing_method = 'cash' where billing_method_id = 3;
UPDATE sf_proforma_invoice SET billing_method = 'card' where billing_method_id = 4;
