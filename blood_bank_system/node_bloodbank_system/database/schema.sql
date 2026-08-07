CREATE DATABASE IF NOT EXISTS BloodBank_db;
USE BloodBank_db;

CREATE TABLE IF NOT EXISTS users (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(160) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin', 'donor', 'facility') NOT NULL DEFAULT 'donor',
  phone VARCHAR(30) NULL,
  address VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS donors (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL UNIQUE,
  blood_type VARCHAR(5) NOT NULL,
  age TINYINT UNSIGNED NOT NULL,
  gender VARCHAR(20) NOT NULL,
  weight DECIMAL(5,2) NULL,
  declaration_confirmed BOOLEAN NOT NULL DEFAULT FALSE,
  declaration_confirmed_at TIMESTAMP NULL,
  medical_notes TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_donors_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS medical_facilities (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL UNIQUE,
  facility_category ENUM('Hospital', 'Rural Health Unit', 'Red Cross') NOT NULL,
  facility_name VARCHAR(160) NOT NULL,
  license_number VARCHAR(80) NULL,
  contact_person VARCHAR(120) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_facilities_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS blood_inventories (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  medical_facility_id BIGINT UNSIGNED NULL,
  facility_name VARCHAR(160) NULL,
  blood_type VARCHAR(5) NOT NULL,
  component VARCHAR(60) NOT NULL DEFAULT 'Whole Blood',
  units_available INT UNSIGNED NOT NULL DEFAULT 0,
  expiry_date DATE NULL,
  notes TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY unique_facility_inventory (medical_facility_id, blood_type, component),
  CONSTRAINT fk_inventory_facility FOREIGN KEY (medical_facility_id) REFERENCES medical_facilities(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS donation_requests (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  donor_id BIGINT UNSIGNED NOT NULL,
  facility_category ENUM('Hospital', 'Rural Health Unit', 'Red Cross') NOT NULL,
  facility_name VARCHAR(160) NOT NULL,
  blood_type VARCHAR(5) NOT NULL,
  component VARCHAR(60) NOT NULL DEFAULT 'Whole Blood',
  units INT UNSIGNED NOT NULL DEFAULT 1,
  status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
  scheduled_date DATE NULL,
  start_time TIME NULL,
  end_time TIME NULL,
  donor_note TEXT NULL,
  facility_note TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_donation_requests_donor FOREIGN KEY (donor_id) REFERENCES donors(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS blood_requests (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  requester_id BIGINT UNSIGNED NOT NULL,
  requester_role ENUM('facility') NOT NULL DEFAULT 'facility',
  facility_category ENUM('Hospital', 'Rural Health Unit', 'Red Cross') NULL,
  facility_name VARCHAR(160) NULL,
  blood_type VARCHAR(5) NOT NULL,
  component VARCHAR(60) NOT NULL DEFAULT 'Whole Blood',
  units INT UNSIGNED NOT NULL DEFAULT 1,
  urgency ENUM('low', 'medium', 'high', 'critical') NOT NULL DEFAULT 'low',
  status ENUM('pending', 'approved', 'rejected', 'released') NOT NULL DEFAULT 'pending',
  reason TEXT NULL,
  admin_note TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_blood_requests_user FOREIGN KEY (requester_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS donations (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  donor_id BIGINT UNSIGNED NOT NULL,
  blood_type VARCHAR(5) NOT NULL,
  component VARCHAR(60) NOT NULL DEFAULT 'Whole Blood',
  units INT UNSIGNED NOT NULL DEFAULT 1,
  donation_date DATE NOT NULL,
  facility_name VARCHAR(160) NULL,
  notes TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_donations_donor FOREIGN KEY (donor_id) REFERENCES donors(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS sessions (
  session_id VARCHAR(128) NOT NULL PRIMARY KEY,
  expires INT UNSIGNED NOT NULL,
  data MEDIUMTEXT
);
