UPDATE sf_project_setting sps
    inner join sf_projects sp on sps.project_id = sp.id
set sps.accounting_invoice_tags          = concat('shoptet,', REPLACE(REPLACE(REPLACE(sp.eshop_url, 'https://', ''),'http://',''),'/','')),
    sps.accounting_proforma_invoice_tags = concat('shoptet,', REPLACE(REPLACE(REPLACE(sp.eshop_url, 'https://', ''),'http://',''),'/','')),
    sps.accounting_credit_note_tags      = concat('shoptet,', REPLACE(REPLACE(REPLACE(sp.eshop_url, 'https://', ''),'http://',''),'/','')),
    sps.accounting_customer_tags         = concat('shoptet,', REPLACE(REPLACE(REPLACE(sp.eshop_url, 'https://', ''),'http://',''),'/',''))
