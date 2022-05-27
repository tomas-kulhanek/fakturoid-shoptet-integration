update sf_project_setting set accounting_code = SHA1(accounting_email) where accounting_email is not null;
