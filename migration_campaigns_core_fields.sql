-- Migration: add Core-fields support to the campaigns table
-- Run this ONCE on the live database (via phpMyAdmin in Hostinger hPanel,
-- or `mysql` CLI) BEFORE pulling the matching code update.
-- Safe to run on a live table: only adds columns / migrates the status column.

ALTER TABLE campaigns
  ADD COLUMN description TEXT NULL AFTER campaign_name,
  ADD COLUMN category VARCHAR(100) NULL AFTER note,
  ADD COLUMN kpi TEXT NULL AFTER category,
  ADD COLUMN terms_conditions TEXT NULL AFTER kpi,
  ADD COLUMN require_tnc TINYINT(1) NOT NULL DEFAULT 0 AFTER terms_conditions,
  ADD COLUMN devices VARCHAR(100) NOT NULL DEFAULT 'all' AFTER require_tnc,
  ADD COLUMN os VARCHAR(100) NOT NULL DEFAULT 'all' AFTER devices,
  ADD COLUMN redirect_type VARCHAR(20) NOT NULL DEFAULT '302' AFTER os,
  ADD COLUMN campaign_status ENUM('active','pending','paused') NOT NULL DEFAULT 'pending' AFTER status;

-- Convert the old status (1 = active, 0 = inactive) into the new 3-state column
UPDATE campaigns SET campaign_status = IF(status = 1, 'active', 'paused');

-- Drop the old numeric status column and rename the new one in its place
ALTER TABLE campaigns DROP COLUMN status;
ALTER TABLE campaigns CHANGE campaign_status status ENUM('active','pending','paused') NOT NULL DEFAULT 'pending';
