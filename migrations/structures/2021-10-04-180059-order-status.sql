
UPDATE sf_order
    inner join system_order_status on sf_order.status_id = system_order_status.shoptet_id
set sf_order.status_id = system_order_status.id;
ALTER TABLE sf_order ADD CONSTRAINT FK_6148EE626BF700BD FOREIGN KEY (status_id) REFERENCES system_order_status (id) ON DELETE RESTRICT;
CREATE INDEX IDX_6148EE626BF700BD ON sf_order (status_id);
