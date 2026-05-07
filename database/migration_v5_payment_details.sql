-- Migration: Enrich pagos with bank, currency, and payment method details
-- Date: 2026-04-12

-- Add bank and currency columns to pagos
ALTER TABLE `pagos`
    ADD COLUMN `banco` VARCHAR(100) DEFAULT NULL AFTER `metodo_pago`,
    ADD COLUMN `moneda_pago` ENUM('PEN','USD') DEFAULT 'PEN' AFTER `banco`;
