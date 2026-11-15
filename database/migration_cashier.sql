-- Migration: cashier-register workflow fields + status history
USE food_ordering_system;

ALTER TABLE orders
    ADD COLUMN cash_received DECIMAL(10, 2) NULL AFTER total_amount,
    ADD COLUMN change_given DECIMAL(10, 2) NULL AFTER cash_received,
    ADD COLUMN paid_at TIMESTAMP NULL AFTER change_given,
    ADD COLUMN received_by BIGINT UNSIGNED NULL AFTER paid_at,
    ADD COLUMN needs_refund TINYINT(1) NOT NULL DEFAULT 0 AFTER received_by;

ALTER TABLE orders
    ADD CONSTRAINT fk_orders_received_by FOREIGN KEY (received_by) REFERENCES staff(id) ON DELETE SET NULL;

CREATE TABLE IF NOT EXISTS order_status_history (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT UNSIGNED NOT NULL,
    previous_status VARCHAR(20) NULL,
    new_status VARCHAR(20) NOT NULL,
    changed_by BIGINT UNSIGNED NULL,
    changed_by_name VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_osh_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    CONSTRAINT fk_osh_staff FOREIGN KEY (changed_by) REFERENCES staff(id) ON DELETE SET NULL,
    INDEX idx_osh_order_created (order_id, created_at)
);

-- Migrate old completed orders to Paid
UPDATE orders
SET payment_status = 'paid',
    cash_received = total_amount,
    change_given = 0.00,
    paid_at = COALESCE(updated_at, created_at),
    needs_refund = 0
WHERE order_status = 'completed' AND payment_status <> 'paid';
