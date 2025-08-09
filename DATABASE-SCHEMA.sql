-- 1. CORE AUTHENTICATION & USERS
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('super_admin', 'admin') NOT NULL DEFAULT 'admin',
    phone VARCHAR(20) NULL,
    position VARCHAR(255) NULL,
    avatar_path VARCHAR(500) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    last_login_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 2. CONTENT MANAGEMENT (CMS)
CREATE TABLE site_settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(255) UNIQUE NOT NULL,
    title VARCHAR(255) NULL,
    content LONGTEXT NULL,
    type ENUM('text', 'rich_text', 'json', 'image') DEFAULT 'text',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE staff_members (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    position VARCHAR(255) NULL,
    specialization VARCHAR(255) NULL,
    education TEXT NULL,
    email VARCHAR(255) NULL,
    phone VARCHAR(20) NULL,
    photo_path VARCHAR(500) NULL,
    bio TEXT NULL,
    research_interests TEXT NULL,
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE articles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    excerpt TEXT NULL,
    content LONGTEXT NULL,
    featured_image_path VARCHAR(500) NULL,
    author_name VARCHAR(255) NOT NULL, -- Manual input by admin
    category ENUM('research', 'news', 'announcement', 'publication') DEFAULT 'news',
    tags JSON NULL,
    is_published BOOLEAN DEFAULT FALSE,
    published_at TIMESTAMP NULL,
    published_by BIGINT UNSIGNED NULL, -- Track which admin published
    views_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (published_by) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE galleries (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    image_path VARCHAR(500) NOT NULL,
    alt_text VARCHAR(255) NULL,
    category ENUM('lab_facilities', 'equipment', 'activities', 'events') DEFAULT 'lab_facilities',
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 3. EQUIPMENT MANAGEMENT
CREATE TABLE categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    color_code VARCHAR(7) NULL,
    icon_class VARCHAR(100) NULL,
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE equipment (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category_id BIGINT UNSIGNED NOT NULL,
    model VARCHAR(255) NULL,
    manufacturer VARCHAR(255) NULL,
    specifications JSON NULL,
    total_quantity INT NOT NULL DEFAULT 1,
    available_quantity INT NOT NULL DEFAULT 1,
    status ENUM('active', 'maintenance', 'retired') DEFAULT 'active',
    condition_status ENUM('excellent', 'good', 'fair', 'poor') DEFAULT 'excellent',
    purchase_date DATE NULL,
    purchase_price DECIMAL(15,2) NULL,
    location VARCHAR(255) NULL,
    image_path VARCHAR(500) NULL,
    manual_file_path VARCHAR(500) NULL,
    notes TEXT NULL,
    last_maintenance_date DATE NULL,
    next_maintenance_date DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
);

CREATE TABLE borrow_requests (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    request_id VARCHAR(20) UNIQUE NOT NULL,
    status ENUM('pending', 'approved', 'rejected', 'active', 'completed', 'cancelled') DEFAULT 'pending',
    -- Borrower Info
    members JSON NOT NULL,
    supervisor_name VARCHAR(255) NOT NULL,
    supervisor_nip VARCHAR(50) NULL,
    supervisor_email VARCHAR(255) NOT NULL,
    supervisor_phone VARCHAR(20) NOT NULL,
    -- Schedule Info
    purpose TEXT NOT NULL,
    borrow_date DATE NOT NULL,
    return_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    -- System Fields
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reviewed_at TIMESTAMP NULL,
    reviewed_by BIGINT UNSIGNED NULL,
    approval_notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE borrow_request_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    borrow_request_id BIGINT UNSIGNED NOT NULL,
    equipment_id BIGINT UNSIGNED NOT NULL,
    quantity_requested INT NOT NULL,
    quantity_approved INT NULL,
    condition_before ENUM('excellent', 'good', 'fair', 'poor') NULL,
    condition_after ENUM('excellent', 'good', 'fair', 'poor') NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (borrow_request_id) REFERENCES borrow_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (equipment_id) REFERENCES equipment(id) ON DELETE CASCADE
);

-- 4. VISIT MANAGEMENT
CREATE TABLE visit_requests (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    request_id VARCHAR(20) UNIQUE NOT NULL,
    status ENUM('pending', 'approved', 'rejected', 'completed', 'cancelled') DEFAULT 'pending',
    -- Contact Info
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    institution VARCHAR(255) NOT NULL,
    -- Visit Info
    purpose ENUM('study-visit', 'research', 'learning', 'internship', 'others') NOT NULL,
    visit_date DATE NOT NULL,
    visit_time ENUM('morning', 'afternoon') NOT NULL,
    participants INT NOT NULL,
    additional_notes TEXT NULL,
    -- Documents
    request_letter_path VARCHAR(500) NULL,
    approval_letter_path VARCHAR(500) NULL,
    -- System Fields
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reviewed_at TIMESTAMP NULL,
    reviewed_by BIGINT UNSIGNED NULL,
    approval_notes TEXT NULL,
    agreement_accepted BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL
);

-- 5. TESTING/SAMPLE ANALYSIS
CREATE TABLE testing_requests (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    request_id VARCHAR(20) UNIQUE NOT NULL,
    status ENUM('pending', 'approved', 'rejected', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
    -- Client Info
    client_name VARCHAR(255) NOT NULL,
    client_organization VARCHAR(255) NOT NULL,
    client_email VARCHAR(255) NOT NULL,
    client_phone VARCHAR(20) NOT NULL,
    client_address TEXT NOT NULL,
    -- Sample Info
    sample_name VARCHAR(255) NOT NULL,
    sample_description TEXT NOT NULL,
    sample_quantity VARCHAR(100) NOT NULL,
    testing_type ENUM('uv_vis_spectroscopy', 'ftir_spectroscopy', 'optical_microscopy', 'custom') NOT NULL,
    testing_parameters JSON NULL,
    urgent_request BOOLEAN DEFAULT FALSE,
    -- Schedule
    preferred_date DATE NOT NULL,
    estimated_duration_hours INT NULL,
    actual_start_date DATE NULL,
    actual_completion_date DATE NULL,
    -- Results
    result_files_path JSON NULL,
    result_summary TEXT NULL,
    cost_estimate DECIMAL(15,2) NULL,
    final_cost DECIMAL(15,2) NULL,
    -- System Fields
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reviewed_at TIMESTAMP NULL,
    reviewed_by BIGINT UNSIGNED NULL,
    assigned_to BIGINT UNSIGNED NULL,
    approval_notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
);

-- 6. SYSTEM & LOGGING
CREATE TABLE activity_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    log_name VARCHAR(255) NULL,
    description TEXT NOT NULL,
    subject_type VARCHAR(255) NULL,
    subject_id BIGINT UNSIGNED NULL,
    causer_type VARCHAR(255) NULL,
    causer_id BIGINT UNSIGNED NULL,
    properties JSON NULL,
    batch_uuid CHAR(36) NULL,
    event VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_subject (subject_type, subject_id),
    INDEX idx_causer (causer_type, causer_id)
);

CREATE TABLE notifications (
    id CHAR(36) PRIMARY KEY,
    type VARCHAR(255) NOT NULL,
    notifiable_type VARCHAR(255) NOT NULL,
    notifiable_id BIGINT UNSIGNED NOT NULL,
    data JSON NOT NULL,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_notifiable (notifiable_type, notifiable_id)
);

-- INDEXES FOR PERFORMANCE
CREATE INDEX idx_equipment_category ON equipment(category_id);
CREATE INDEX idx_equipment_status ON equipment(status);
CREATE INDEX idx_borrow_requests_status ON borrow_requests(status);
CREATE INDEX idx_visit_requests_status ON visit_requests(status);
CREATE INDEX idx_testing_requests_status ON testing_requests(status);
CREATE INDEX idx_articles_published ON articles(is_published, published_at);
CREATE INDEX idx_staff_active ON staff_members(is_active, sort_order);
CREATE INDEX idx_galleries_category ON galleries(category, sort_order);
