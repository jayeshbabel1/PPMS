-- ============================================================
--  PMS By Mingosoft Technologies — Database Schema v4.0
-- ============================================================
CREATE DATABASE IF NOT EXISTS pms_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pms_db;

CREATE TABLE IF NOT EXISTS users (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username   VARCHAR(60)  NOT NULL UNIQUE,
  password   VARCHAR(255) NOT NULL,
  full_name  VARCHAR(120) DEFAULT NULL,
  email      VARCHAR(180) DEFAULT NULL,
  role       ENUM('admin','viewer','developer') NOT NULL DEFAULT 'viewer',
  is_active  TINYINT(1)   NOT NULL DEFAULT 1,
  last_login DATETIME     DEFAULT NULL,
  created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS revenue_villages (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name       VARCHAR(120) NOT NULL UNIQUE,
  tehsil     VARCHAR(120) DEFAULT NULL,
  district   VARCHAR(120) DEFAULT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Unified plans table (regular + developer plans merged)
CREATE TABLE IF NOT EXISTS plans (
  id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  plan_name           VARCHAR(255) NOT NULL,
  aaraji_number       VARCHAR(300) NOT NULL,
  village_id          INT UNSIGNED DEFAULT NULL,
  google_location     TEXT         DEFAULT NULL,
  file_path           VARCHAR(500) DEFAULT NULL,
  file_name           VARCHAR(255) DEFAULT NULL,
  file_type           VARCHAR(50)  DEFAULT NULL,
  notes               TEXT         DEFAULT NULL,
  -- Developer-specific fields
  is_developer_plan   TINYINT(1)   NOT NULL DEFAULT 0,
  contact_number      VARCHAR(30)  DEFAULT NULL,
  approved_map_path   VARCHAR(500) DEFAULT NULL,
  approved_map_name   VARCHAR(255) DEFAULT NULL,
  approved_map_type   VARCHAR(50)  DEFAULT NULL,
  price_30ft          DECIMAL(15,2) DEFAULT NULL,
  price_40ft          DECIMAL(15,2) DEFAULT NULL,
  price_60ft          DECIMAL(15,2) DEFAULT NULL,
  price_80ft          DECIMAL(15,2) DEFAULT NULL,
  price_100ft         DECIMAL(15,2) DEFAULT NULL,
  price_highway       DECIMAL(15,2) DEFAULT NULL,
  price_unit          VARCHAR(20)   DEFAULT 'sq.ft',
  brokerage_rate      DECIMAL(5,2)  DEFAULT NULL,
  brokerage_notes     TEXT          DEFAULT NULL,
  -- Approval workflow (for developer plans)
  dev_status          ENUM('na','pending','approved','rejected') NOT NULL DEFAULT 'na',
  dev_admin_note      TEXT          DEFAULT NULL,
  approved_by         INT UNSIGNED  DEFAULT NULL,
  approved_at         DATETIME      DEFAULT NULL,
  -- Sponsorship
  is_sponsored        TINYINT(1)    NOT NULL DEFAULT 0,
  sponsored_label     VARCHAR(80)   DEFAULT 'Sponsored',
  -- Audit
  created_by          INT UNSIGNED  DEFAULT NULL,
  updated_by          INT UNSIGNED  DEFAULT NULL,
  created_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_plan_village  FOREIGN KEY (village_id)  REFERENCES revenue_villages(id) ON DELETE SET NULL,
  CONSTRAINT fk_plan_creator  FOREIGN KEY (created_by)  REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_plan_approver FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
  FULLTEXT KEY ft_search (plan_name, aaraji_number)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS plan_chain_documents (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  plan_id     INT UNSIGNED NOT NULL,
  file_path   VARCHAR(500) NOT NULL,
  file_name   VARCHAR(255) NOT NULL,
  file_type   ENUM('image','pdf') NOT NULL,
  file_size   INT UNSIGNED DEFAULT NULL,
  sort_order  SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  uploaded_by INT UNSIGNED DEFAULT NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_chain_plan FOREIGN KEY (plan_id) REFERENCES plans(id) ON DELETE CASCADE,
  CONSTRAINT fk_chain_user FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS dlc_rates (
  id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  village_id     INT UNSIGNED NOT NULL,
  financial_year VARCHAR(10)   NOT NULL,
  road_30ft      DECIMAL(15,2) DEFAULT NULL,
  road_40ft      DECIMAL(15,2) DEFAULT NULL,
  road_60ft      DECIMAL(15,2) DEFAULT NULL,
  road_80ft      DECIMAL(15,2) DEFAULT NULL,
  road_100ft     DECIMAL(15,2) DEFAULT NULL,
  near_highway   DECIMAL(15,2) DEFAULT NULL,
  effective_from DATE          NOT NULL,
  notes          TEXT          DEFAULT NULL,
  created_by     INT UNSIGNED  DEFAULT NULL,
  created_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uk_village_year (village_id, financial_year),
  CONSTRAINT fk_dlc_village FOREIGN KEY (village_id) REFERENCES revenue_villages(id) ON DELETE CASCADE,
  CONSTRAINT fk_dlc_creator FOREIGN KEY (created_by)  REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS subscriptions (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id       INT UNSIGNED NOT NULL,
  plan_type     ENUM('basic','advance') NOT NULL DEFAULT 'basic',
  billing_cycle ENUM('monthly','yearly') NOT NULL DEFAULT 'monthly',
  start_date    DATE          NOT NULL,
  end_date      DATE          NOT NULL,
  amount        DECIMAL(10,2) DEFAULT NULL,
  is_active     TINYINT(1)    NOT NULL DEFAULT 1,
  notes         TEXT          DEFAULT NULL,
  created_by    INT UNSIGNED  DEFAULT NULL,
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_sub_user    FOREIGN KEY (user_id)    REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_sub_creator FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS upgrade_requests (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id       INT UNSIGNED NOT NULL,
  current_plan  ENUM('none','basic','advance') NOT NULL DEFAULT 'none',
  request_plan  ENUM('basic','advance')        NOT NULL DEFAULT 'advance',
  billing_cycle ENUM('monthly','yearly')       NOT NULL DEFAULT 'monthly',
  message       TEXT DEFAULT NULL,
  status        ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  admin_note    TEXT DEFAULT NULL,
  reviewed_by   INT UNSIGNED DEFAULT NULL,
  reviewed_at   DATETIME DEFAULT NULL,
  created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_ur_user  FOREIGN KEY (user_id)    REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_ur_admin FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- App settings (marquee, footer, email config, theme colors, etc.)
CREATE TABLE IF NOT EXISTS app_settings (
  `key`      VARCHAR(80)  NOT NULL PRIMARY KEY,
  val        TEXT         NOT NULL DEFAULT '',
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT IGNORE INTO app_settings(`key`,val) VALUES
  ('marquee_text',    'Welcome to PMS – Property Management System by Mingosoft Technologies'),
  ('marquee_enabled', '1'),
  ('marquee_speed',   '60'),
  ('footer_text',     'PMS By Mingosoft Technologies &copy; 2025. All rights reserved.'),
  -- Email config
  ('mail_method',     'smtp'),
  ('mail_host',       ''),
  ('mail_port',       '587'),
  ('mail_user',       ''),
  ('mail_pass',       ''),
  ('mail_from',       ''),
  ('mail_from_name',  'PMS System'),
  ('mail_admin_email',''),
  ('mail_error_notify','0'),
  -- Theme colors
  ('theme_primary',   '#81A6C6'),
  ('theme_bg',        '#F3E3D0'),
  ('theme_surface',   '#FFFFFF'),
  ('theme_border',    '#D2C4B4'),
  ('theme_btn_text',  '#FFFFFF'),
  ('theme_heading',   '#2C3A4A'),
  ('theme_text',      '#4A5E70'),
  ('theme_sidebar_bg','#FFFFFF'),
  ('theme_topbar_bg', '#FFFFFF');

-- Editable permissions matrix
CREATE TABLE IF NOT EXISTS permissions (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  feature     VARCHAR(80)  NOT NULL UNIQUE,
  `group`     VARCHAR(40)  NOT NULL DEFAULT 'general',
  label       VARCHAR(120) NOT NULL,
  admin       TINYINT(1)   NOT NULL DEFAULT 1,
  developer   TINYINT(1)   NOT NULL DEFAULT 0,
  adv_viewer  TINYINT(1)   NOT NULL DEFAULT 0,
  bas_viewer  TINYINT(1)   NOT NULL DEFAULT 0
) ENGINE=InnoDB;

INSERT IGNORE INTO permissions(feature,`group`,label,admin,developer,adv_viewer,bas_viewer) VALUES
  ('view_plans',         'plans',   'View plan list',                    1,1,1,1),
  ('view_plan_image',    'plans',   'View plan image',                   1,1,1,1),
  ('view_location',      'plans',   'View Google Maps location',         1,1,1,1),
  ('view_dlc',           'plans',   'View DLC rates',                    1,1,1,0),
  ('download_plan',      'plans',   'Download plan image/PDF',           1,1,1,0),
  ('view_chain_docs',    'plans',   'View/Download chain documents',     1,1,1,0),
  ('add_plan',           'plans',   'Add/Register plan',                 1,0,0,0),
  ('edit_plan',          'plans',   'Edit plan',                         1,0,0,0),
  ('delete_plan',        'plans',   'Delete plan',                       1,0,0,0),
  ('view_dev_plans',     'devplans','View approved developer plans',     1,1,1,1),
  ('add_dev_plan',       'devplans','Add developer plan',                1,1,0,0),
  ('edit_own_dev_plan',  'devplans','Edit own developer plan',           1,1,0,0),
  ('delete_dev_plan',    'devplans','Delete developer plan',             1,0,0,0),
  ('approve_dev_plan',   'devplans','Approve/Reject developer plan',     1,0,0,0),
  ('sponsor_dev_plan',   'devplans','Mark plan as Sponsored',            1,0,0,0),
  ('view_dlc_page',      'dlc',     'View DLC rates page',               1,1,1,0),
  ('manage_dlc',         'dlc',     'Add/Edit/Delete DLC rates',         1,0,0,0),
  ('export_dlc',         'dlc',     'Export DLC to Excel',               1,1,1,0),
  ('import_dlc',         'dlc',     'Import DLC via CSV',                1,0,0,0),
  ('view_villages',      'villages','View villages list',                1,1,1,1),
  ('manage_villages',    'villages','Add/Edit/Delete villages',          1,0,0,0),
  ('manage_users',       'admin',   'Create user accounts',              1,0,0,0),
  ('manage_subs',        'admin',   'Assign subscriptions',              1,0,0,0),
  ('request_upgrade',    'account', 'Request plan upgrade',              0,0,1,1),
  ('manage_settings',    'admin',   'Manage Settings',                   1,0,0,0),
  ('edit_permissions',   'admin',   'Edit Permission Matrix',            1,0,0,0);

-- Password reset tokens
CREATE TABLE IF NOT EXISTS password_resets (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id    INT UNSIGNED NOT NULL,
  email      VARCHAR(180) NOT NULL,
  token      VARCHAR(80)  NOT NULL UNIQUE,
  expires_at DATETIME     NOT NULL,
  used       TINYINT(1)   NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_pr_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS audit_log (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id    INT UNSIGNED DEFAULT NULL,
  action     VARCHAR(50)  NOT NULL,
  table_name VARCHAR(60)  NOT NULL,
  record_id  INT UNSIGNED DEFAULT NULL,
  detail     TEXT         DEFAULT NULL,
  ip_address VARCHAR(45)  DEFAULT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- NOTE: Run setup.php to create the first admin user.
