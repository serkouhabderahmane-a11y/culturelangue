# Cultulangues - Backend Requirements Document (BRD)

> Version: 1.0 | Date: 2026-07-28 | Status: Analysis Complete

---

## Table of Contents

1. Executive Summary
2. Project Architecture Overview
3. Complete Entity Inventory
4. Database Schema
5. ER Diagram
6. CRUD Matrix
7. API Specification
8. Permission Matrix
9. Authentication Flow
10. Booking Workflow
11. Payment Workflow
12. CMS Requirements
13. Notification Workflows
14. Validation Rules
15. Background Jobs
16. File Storage Strategy
17. Recommended Architecture & Stack
18. Deployment Architecture
19. Development Roadmap

---

## 1. Executive Summary

Cultulangues is a French language school website with **4 portals** (Public, Student, Teacher, Admin) and **60+ HTML pages**. Currently the entire application is a **static frontend** with zero backend integration -- all data is hardcoded in JavaScript objects, HTML, and static JSON files. Login is cosmetic (no authentication). Every piece of dynamic data needs a real backend.

### Scale of Work

| Metric | Count |
|--------|-------|
| HTML Pages | 60 |
| Distinct Entities | 51 |
| Database Tables | 40+ |
| API Endpoints | 200+ |
| Forms Requiring Backend | 15 |
| Hardcoded Data Arrays | 30+ |
| Charts Needing Data | 20+ |
| CRUD Operations | 100+ |
| Notification Types | 12+ |
| Background Jobs | 8+ |

---

## 2. Project Architecture Overview

### Current State (Static Frontend)

Each portal has **two parallel implementations**:
1. **Monolithic SPA** (`index.html`) -- All pages in one file, inline CSS/JS, self-contained
2. **Multi-page** (individual `.html` files) -- Share layout, load external CSS/JS, use `data-i18n`

Key files:
- `js/main.js` -- 4533-line monolith: i18n (650 keys FR/EN), animations, booking wizard, placement test
- `js/content-loader.js` -- Fetches static JSON, populates DOM via data attributes
- `content/services.json` -- Static service catalog (3597 lines)
- `booking.html` -- Multi-step wizard with 20+ hardcoded courses, placement test, solo scheduling
- `student/index.html` -- Standalone SPA with 8 sub-pages, all mock data
- `teacher/index.html` -- Standalone SPA with 9 sub-pages, all mock data
- `admin/index.html` -- Standalone SPA with 12 sub-pages, all mock data

### Critical Data Duplication

Course data is defined in **4+ separate places**: `window.courseContent`, `window.translations`, `programData`, `booking.courseDB`, and inline `booking.html` script. All must be consolidated into a single database.

---

## 3. Complete Entity Inventory

### 3.1 Core Entities (51 total)

| # | Entity | Purpose | Table |
|---|--------|---------|-------|
| 1 | User | All system users | `users` |
| 2 | Role | Permission groups (admin/teacher/student) | `roles`, `user_roles` |
| 3 | Permission | Granular access rights | `permissions`, `role_permissions` |
| 4 | Student | Extended student profile | `students` |
| 5 | Teacher | Extended teacher profile | `teachers` |
| 6 | Category | Service categories | `categories` |
| 7 | Program | Major program offerings | `programs` |
| 8 | CoursePackage | Solo hour packages (5h/10h/15h/20h) | `course_packages` |
| 9 | GroupSchedule | Repeating group class schedules | `group_schedules` |
| 10 | Session | Program cohort structure | `sessions` |
| 11 | Benefit | Program benefits | `benefits` |
| 12 | Lesson | Individual scheduled class meetings | `lessons` |
| 13 | Booking | Student enrollment request | `bookings` |
| 14 | Enrollment | Confirmed student-course link | `enrollments` |
| 15 | PlacementTest | French proficiency test | `placement_tests` |
| 16 | TestQuestion | Individual test questions | `test_questions` |
| 17 | TestAnswer | Student answers | `test_answers` |
| 18 | OralTestAppointment | Scheduled oral evaluation | `oral_test_appointments` |
| 19 | Test | Course-level assessment | `tests` |
| 20 | TestResult | Student test performance | `test_results` |
| 21 | Grade | Teacher-entered grades | `grades` |
| 22 | AttendanceRecord | Student presence per lesson | `attendance_records` |
| 23 | CalendarEvent | Master calendar events | `calendar_events` |
| 24 | TimeSlot | Bookable solo lesson slots | `time_slots` |
| 25 | Payment | Transaction records | `payments` |
| 26 | Invoice | Generated invoices | `invoices` |
| 27 | InvoiceLine | Invoice line items | `invoice_lines` |
| 28 | TeacherPayroll | Teacher compensation | `teacher_payrolls` |
| 29 | TeacherHours | Teacher work hour tracking | `teacher_hours` |
| 30 | Notification | In-app notifications | `notifications` |
| 31 | SupportTicket | Student support requests | `support_tickets` |
| 32 | SupportMessage | Messages within ticket | `support_messages` |
| 33 | ContactMessage | Public contact form submissions | `contact_messages` |
| 34 | EmailLog | Sent email records | `email_logs` |
| 35 | Page | CMS-managed pages | `pages` |
| 36 | PageSection | Sections within a page | `page_sections` |
| 37 | Testimonial | Student testimonials | `testimonials` |
| 38 | FAQ | Frequently asked questions | `faqs` |
| 39 | Media | Uploaded files | `media` |
| 40 | SpeechBubble | Homepage bubble content | `speech_bubbles` |
| 41 | Statistic | Homepage stats | `statistics` |
| 42 | SiteSetting | Global configuration | `site_settings` |
| 43 | EmailTemplate | Reusable email templates | `email_templates` |
| 44 | AuditLog | Change tracking | `audit_logs` |
| 45 | UserSession | JWT refresh tokens | `user_sessions` |
| 46 | PasswordReset | Password reset tokens | `password_resets` |
| 47 | TeacherSpecialty | Teacher specializations | `teacher_specialties` |
| 48 | TeacherAvailability | Teacher available hours | `teacher_availabilities` |
| 49 | ProgramSession | Program content sessions (cohort) | `program_sessions` |
| 50 | StudentSkillProgress | 4-skill breakdown | `student_skill_progress` |
| 51 | TeacherNote | Admin notes about teachers | `teacher_notes` |

---

## 4. Database Schema

### 4.1 users

```sql
CREATE TABLE users (
    id              UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    email           VARCHAR(255) NOT NULL UNIQUE,
    password_hash   VARCHAR(255) NOT NULL,
    first_name      VARCHAR(100) NOT NULL,
    last_name       VARCHAR(100) NOT NULL,
    phone           VARCHAR(20),
    avatar_url      VARCHAR(500),
    role            VARCHAR(20) NOT NULL CHECK (role IN ('admin','teacher','student')),
    language        VARCHAR(2) DEFAULT 'fr' CHECK (language IN ('fr','en')),
    email_verified  BOOLEAN DEFAULT FALSE,
    is_active       BOOLEAN DEFAULT TRUE,
    last_login_at   TIMESTAMP,
    created_at      TIMESTAMP DEFAULT NOW(),
    updated_at      TIMESTAMP DEFAULT NOW(),
    deleted_at      TIMESTAMP
);
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_role ON users(role);
```

### 4.2 roles & permissions

```sql
CREATE TABLE roles (
    id          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    name        VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at  TIMESTAMP DEFAULT NOW()
);

CREATE TABLE permissions (
    id          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    name        VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    category    VARCHAR(50) NOT NULL
);

CREATE TABLE role_permissions (
    role_id       UUID REFERENCES roles(id) ON DELETE CASCADE,
    permission_id UUID REFERENCES permissions(id) ON DELETE CASCADE,
    PRIMARY KEY (role_id, permission_id)
);
```

### 4.3 students

```sql
CREATE TABLE students (
    id                UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id           UUID NOT NULL UNIQUE REFERENCES users(id) ON DELETE CASCADE,
    student_number    VARCHAR(20) UNIQUE,
    native_language   VARCHAR(50),
    current_level     VARCHAR(2) CHECK (current_level IN ('A1','A2','B1','B2','C1','C2')),
    target_level      VARCHAR(2) CHECK (target_level IN ('A1','A2','B1','B2','C1','C2')),
    goal              VARCHAR(100),
    enrollment_date   DATE,
    emergency_contact JSONB,
    notes             TEXT,
    created_at        TIMESTAMP DEFAULT NOW(),
    updated_at        TIMESTAMP DEFAULT NOW()
);
```

### 4.4 teachers

```sql
CREATE TABLE teachers (
    id                   UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id              UUID NOT NULL UNIQUE REFERENCES users(id) ON DELETE CASCADE,
    employee_number      VARCHAR(20) UNIQUE,
    department           VARCHAR(100),
    hourly_rate_solo     DECIMAL(8,2) DEFAULT 55.00,
    hourly_rate_group    DECIMAL(8,2) DEFAULT 45.00,
    contract_hours_month INT DEFAULT 80,
    rating               DECIMAL(2,1) DEFAULT 0.0,
    hire_date            DATE,
    created_at           TIMESTAMP DEFAULT NOW(),
    updated_at           TIMESTAMP DEFAULT NOW()
);

CREATE TABLE teacher_specialties (
    id          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    teacher_id  UUID NOT NULL REFERENCES teachers(id) ON DELETE CASCADE,
    specialty   VARCHAR(100) NOT NULL
);

CREATE TABLE teacher_availabilities (
    id          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    teacher_id  UUID NOT NULL REFERENCES teachers(id) ON DELETE CASCADE,
    day_of_week INT NOT NULL CHECK (day_of_week BETWEEN 0 AND 6),
    start_time  TIME NOT NULL,
    end_time    TIME NOT NULL
);

CREATE TABLE teacher_notes (
    id          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    teacher_id  UUID NOT NULL REFERENCES teachers(id) ON DELETE CASCADE,
    content     TEXT,
    created_by  UUID REFERENCES users(id),
    updated_at  TIMESTAMP DEFAULT NOW()
);
```

### 4.5 categories

```sql
CREATE TABLE categories (
    id              UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    name            VARCHAR(100) NOT NULL,
    name_en         VARCHAR(100),
    slug            VARCHAR(100) NOT NULL UNIQUE,
    description     TEXT,
    description_en  TEXT,
    icon            VARCHAR(50),
    sort_order      INT DEFAULT 0,
    is_active       BOOLEAN DEFAULT TRUE,
    created_at      TIMESTAMP DEFAULT NOW()
);
```

### 4.6 programs

```sql
CREATE TABLE programs (
    id              UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    category_id     UUID REFERENCES categories(id),
    name            VARCHAR(200) NOT NULL,
    name_en         VARCHAR(200),
    slug            VARCHAR(200) NOT NULL UNIQUE,
    description     TEXT,
    description_en  TEXT,
    short_desc      TEXT,
    short_desc_en   TEXT,
    type            VARCHAR(20) NOT NULL,
    level_min       VARCHAR(2),
    level_max       VARCHAR(2),
    duration_weeks  INT,
    price_monthly   DECIMAL(10,2),
    price_total     DECIMAL(10,2),
    currency        VARCHAR(3) DEFAULT 'CAD',
    image_url       VARCHAR(500),
    banner_url      VARCHAR(500),
    hero_url        VARCHAR(500),
    page_url        VARCHAR(200),
    teacher_id      UUID REFERENCES teachers(id),
    max_students    INT,
    status          VARCHAR(20) DEFAULT 'active',
    sort_order      INT DEFAULT 0,
    is_featured     BOOLEAN DEFAULT FALSE,
    meta_title      VARCHAR(200),
    meta_desc       TEXT,
    created_at      TIMESTAMP DEFAULT NOW(),
    updated_at      TIMESTAMP DEFAULT NOW(),
    deleted_at      TIMESTAMP
);
CREATE INDEX idx_programs_category ON programs(category_id);
CREATE INDEX idx_programs_slug ON programs(slug);
```

### 4.7 course_packages

```sql
CREATE TABLE course_packages (
    id            UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    program_id    UUID NOT NULL REFERENCES programs(id) ON DELETE CASCADE,
    name          VARCHAR(100) NOT NULL,
    name_en       VARCHAR(100),
    hours         INT NOT NULL,
    sessions      INT NOT NULL,
    price         DECIMAL(10,2) NOT NULL,
    rate_per_hour DECIMAL(8,2),
    badge         VARCHAR(50),
    badge_color   VARCHAR(20),
    is_popular    BOOLEAN DEFAULT FALSE,
    sort_order    INT DEFAULT 0,
    created_at    TIMESTAMP DEFAULT NOW()
);
```

### 4.8 group_schedules

```sql
CREATE TABLE group_schedules (
    id               UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    program_id       UUID NOT NULL REFERENCES programs(id) ON DELETE CASCADE,
    label            VARCHAR(100) NOT NULL,
    day_of_week      INT NOT NULL CHECK (day_of_week BETWEEN 0 AND 6),
    start_time       TIME NOT NULL,
    end_time         TIME NOT NULL,
    start_date       DATE,
    end_date         DATE,
    room             VARCHAR(50),
    max_students     INT DEFAULT 15,
    current_students INT DEFAULT 0,
    status           VARCHAR(20) DEFAULT 'active',
    created_at       TIMESTAMP DEFAULT NOW()
);
```

### 4.9 program_sessions (cohort structure)

```sql
CREATE TABLE program_sessions (
    id                UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    program_id        UUID NOT NULL REFERENCES programs(id) ON DELETE CASCADE,
    title             VARCHAR(200) NOT NULL,
    date_range        VARCHAR(100),
    schedule_text     VARCHAR(100),
    duration_text     VARCHAR(50),
    state             VARCHAR(20) DEFAULT 'available',
    availability_text VARCHAR(200),
    cta_primary       VARCHAR(100) DEFAULT 'Reserver',
    cta_secondary     VARCHAR(100) DEFAULT 'Voir les dates',
    pause_message     TEXT,
    sort_order        INT DEFAULT 0,
    created_at        TIMESTAMP DEFAULT NOW()
);
```

### 4.10 benefits

```sql
CREATE TABLE benefits (
    id          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    program_id  UUID NOT NULL REFERENCES programs(id) ON DELETE CASCADE,
    title       VARCHAR(200) NOT NULL,
    description TEXT,
    icon        VARCHAR(50),
    sort_order  INT DEFAULT 0,
    created_at  TIMESTAMP DEFAULT NOW()
);
```

### 4.11 bookings

```sql
CREATE TABLE bookings (
    id                UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    booking_ref       VARCHAR(20) NOT NULL UNIQUE,
    student_id        UUID NOT NULL REFERENCES students(id),
    program_id        UUID NOT NULL REFERENCES programs(id),
    package_id        UUID REFERENCES course_packages(id),
    group_schedule_id UUID REFERENCES group_schedules(id),
    contact_name      VARCHAR(200),
    contact_email     VARCHAR(255),
    contact_phone     VARCHAR(20),
    contact_method    VARCHAR(50),
    notes             TEXT,
    preferred_date    DATE,
    preferred_slot    VARCHAR(10),
    status            VARCHAR(20) DEFAULT 'pending',
    placement_score   DECIMAL(5,2),
    placement_level   VARCHAR(2),
    oral_test_date    DATE,
    oral_test_slot    VARCHAR(10),
    oral_test_status  VARCHAR(20),
    total_amount      DECIMAL(10,2),
    currency          VARCHAR(3) DEFAULT 'CAD',
    payment_status    VARCHAR(20) DEFAULT 'unpaid',
    source            VARCHAR(50) DEFAULT 'website',
    ip_address        INET,
    created_at        TIMESTAMP DEFAULT NOW(),
    updated_at        TIMESTAMP DEFAULT NOW()
);
CREATE INDEX idx_bookings_student ON bookings(student_id);
CREATE INDEX idx_bookings_program ON bookings(program_id);
CREATE INDEX idx_bookings_status ON bookings(status);
```

### 4.12 enrollments

```sql
CREATE TABLE enrollments (
    id          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    student_id  UUID NOT NULL REFERENCES students(id),
    program_id  UUID NOT NULL REFERENCES programs(id),
    booking_id  UUID REFERENCES bookings(id),
    start_date  DATE NOT NULL,
    end_date    DATE,
    progress    DECIMAL(5,2) DEFAULT 0,
    status      VARCHAR(20) DEFAULT 'active',
    enrolled_at TIMESTAMP DEFAULT NOW(),
    created_at  TIMESTAMP DEFAULT NOW(),
    updated_at  TIMESTAMP DEFAULT NOW(),
    UNIQUE(student_id, program_id)
);
```

### 4.13 lessons

```sql
CREATE TABLE lessons (
    id                UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    program_id        UUID NOT NULL REFERENCES programs(id),
    teacher_id        UUID NOT NULL REFERENCES teachers(id),
    group_schedule_id UUID REFERENCES group_schedules(id),
    title             VARCHAR(200),
    date              DATE NOT NULL,
    start_time        TIME NOT NULL,
    end_time          TIME NOT NULL,
    room              VARCHAR(50),
    lesson_type       VARCHAR(20) DEFAULT 'class',
    status            VARCHAR(20) DEFAULT 'scheduled',
    notes             TEXT,
    created_at        TIMESTAMP DEFAULT NOW(),
    updated_at        TIMESTAMP DEFAULT NOW()
);
CREATE INDEX idx_lessons_program ON lessons(program_id);
CREATE INDEX idx_lessons_teacher ON lessons(teacher_id);
CREATE INDEX idx_lessons_date ON lessons(date);
```

### 4.14 attendance_records

```sql
CREATE TABLE attendance_records (
    id          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    lesson_id   UUID NOT NULL REFERENCES lessons(id) ON DELETE CASCADE,
    student_id  UUID NOT NULL REFERENCES students(id),
    status      VARCHAR(20) NOT NULL,
    notes       TEXT,
    marked_by   UUID REFERENCES users(id),
    marked_at   TIMESTAMP DEFAULT NOW(),
    UNIQUE(lesson_id, student_id)
);
```

### 4.15 placement_tests & test_questions & test_answers

```sql
CREATE TABLE placement_tests (
    id              UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    booking_id      UUID REFERENCES bookings(id),
    student_id      UUID REFERENCES students(id),
    total_questions INT NOT NULL,
    score           DECIMAL(5,2),
    level           VARCHAR(2),
    category_scores JSONB,
    time_taken      INT,
    started_at      TIMESTAMP,
    completed_at    TIMESTAMP,
    created_at      TIMESTAMP DEFAULT NOW()
);

CREATE TABLE test_questions (
    id            UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    test_type     VARCHAR(20) NOT NULL,
    category      VARCHAR(50),
    question_text TEXT NOT NULL,
    passage_text  TEXT,
    options       JSONB NOT NULL,
    correct_index INT NOT NULL,
    sort_order    INT DEFAULT 0,
    is_active     BOOLEAN DEFAULT TRUE,
    created_at    TIMESTAMP DEFAULT NOW()
);

CREATE TABLE test_answers (
    id                UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    placement_test_id UUID NOT NULL REFERENCES placement_tests(id) ON DELETE CASCADE,
    question_id       UUID NOT NULL REFERENCES test_questions(id),
    selected_index    INT,
    is_correct        BOOLEAN,
    created_at        TIMESTAMP DEFAULT NOW()
);
```

### 4.16 tests & test_results & grades

```sql
CREATE TABLE tests (
    id          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    program_id  UUID REFERENCES programs(id),
    name        VARCHAR(200) NOT NULL,
    test_type   VARCHAR(20) NOT NULL,
    total_score INT,
    duration    INT,
    is_active   BOOLEAN DEFAULT TRUE,
    created_at  TIMESTAMP DEFAULT NOW()
);

CREATE TABLE test_results (
    id          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    test_id     UUID NOT NULL REFERENCES tests(id),
    student_id  UUID NOT NULL REFERENCES students(id),
    teacher_id  UUID REFERENCES teachers(id),
    score       DECIMAL(5,2),
    level       VARCHAR(2),
    letter_grade VARCHAR(2),
    status      VARCHAR(20),
    sections    JSONB,
    taken_at    TIMESTAMP DEFAULT NOW(),
    created_at  TIMESTAMP DEFAULT NOW()
);

CREATE TABLE grades (
    id          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    student_id  UUID NOT NULL REFERENCES students(id),
    test_id     UUID REFERENCES tests(id),
    lesson_id   UUID REFERENCES lessons(id),
    teacher_id  UUID NOT NULL REFERENCES teachers(id),
    letter_grade VARCHAR(2),
    score       DECIMAL(5,2),
    notes       TEXT,
    created_at  TIMESTAMP DEFAULT NOW(),
    updated_at  TIMESTAMP DEFAULT NOW()
);
```

### 4.17 calendar_events

```sql
CREATE TABLE calendar_events (
    id          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    title       VARCHAR(200) NOT NULL,
    event_date  DATE NOT NULL,
    event_type  VARCHAR(20) NOT NULL,
    start_time  TIME NOT NULL,
    end_time    TIME NOT NULL,
    teacher_id  UUID REFERENCES teachers(id),
    program_id  UUID REFERENCES programs(id),
    room        VARCHAR(50),
    created_by  UUID REFERENCES users(id),
    created_at  TIMESTAMP DEFAULT NOW()
);
```

### 4.18 time_slots

```sql
CREATE TABLE time_slots (
    id          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    program_id  UUID NOT NULL REFERENCES programs(id),
    slot_date   DATE NOT NULL,
    slot_time   VARCHAR(10) NOT NULL,
    is_available BOOLEAN DEFAULT TRUE,
    booked_by   UUID REFERENCES students(id),
    created_at  TIMESTAMP DEFAULT NOW()
);
```

### 4.19 payments

```sql
CREATE TABLE payments (
    id            UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    transaction_ref VARCHAR(20) NOT NULL UNIQUE,
    student_id    UUID NOT NULL REFERENCES students(id),
    booking_id    UUID REFERENCES bookings(id),
    program_id    UUID REFERENCES programs(id),
    amount        DECIMAL(10,2) NOT NULL,
    currency      VARCHAR(3) DEFAULT 'CAD',
    payment_method VARCHAR(20),
    status        VARCHAR(20) DEFAULT 'pending',
    paid_at       TIMESTAMP,
    created_at    TIMESTAMP DEFAULT NOW()
);
CREATE INDEX idx_payments_student ON payments(student_id);
CREATE INDEX idx_payments_status ON payments(status);
```

### 4.20 invoices

```sql
CREATE TABLE invoices (
    id            UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    invoice_number VARCHAR(20) NOT NULL UNIQUE,
    student_id    UUID NOT NULL REFERENCES students(id),
    program_id    UUID REFERENCES programs(id),
    total_amount  DECIMAL(10,2) NOT NULL,
    currency      VARCHAR(3) DEFAULT 'CAD',
    status        VARCHAR(20) DEFAULT 'pending',
    due_date      DATE,
    paid_at       TIMESTAMP,
    created_at    TIMESTAMP DEFAULT NOW()
);

CREATE TABLE invoice_lines (
    id          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    invoice_id  UUID NOT NULL REFERENCES invoices(id) ON DELETE CASCADE,
    description VARCHAR(200) NOT NULL,
    amount      DECIMAL(10,2) NOT NULL,
    sort_order  INT DEFAULT 0
);
```

### 4.21 teacher_payrolls & teacher_hours

```sql
CREATE TABLE teacher_hours (
    id          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    teacher_id  UUID NOT NULL REFERENCES teachers(id),
    lesson_id   UUID REFERENCES lessons(id),
    hours       DECIMAL(4,2) NOT NULL,
    hour_type   VARCHAR(10) NOT NULL,
    work_date   DATE NOT NULL,
    created_at  TIMESTAMP DEFAULT NOW()
);

CREATE TABLE teacher_payrolls (
    id            UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    teacher_id    UUID NOT NULL REFERENCES teachers(id),
    period_start  DATE NOT NULL,
    period_end    DATE NOT NULL,
    solo_hours    DECIMAL(6,2) DEFAULT 0,
    group_hours   DECIMAL(6,2) DEFAULT 0,
    solo_rate     DECIMAL(8,2),
    group_rate    DECIMAL(8,2),
    total_amount  DECIMAL(10,2),
    status        VARCHAR(20) DEFAULT 'pending',
    paid_at       TIMESTAMP,
    created_at    TIMESTAMP DEFAULT NOW()
);
```

### 4.22 notifications

```sql
CREATE TABLE notifications (
    id          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id     UUID NOT NULL REFERENCES users(id),
    title       VARCHAR(200) NOT NULL,
    message     TEXT NOT NULL,
    type        VARCHAR(50),
    is_read     BOOLEAN DEFAULT FALSE,
    link        VARCHAR(500),
    created_at  TIMESTAMP DEFAULT NOW()
);
CREATE INDEX idx_notifications_user ON notifications(user_id, is_read);
```

### 4.23 support_tickets & support_messages

```sql
CREATE TABLE support_tickets (
    id          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    student_id  UUID NOT NULL REFERENCES students(id),
    subject     VARCHAR(200) NOT NULL,
    category    VARCHAR(50),
    status      VARCHAR(20) DEFAULT 'open',
    created_at  TIMESTAMP DEFAULT NOW(),
    updated_at  TIMESTAMP DEFAULT NOW()
);

CREATE TABLE support_messages (
    id          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    ticket_id   UUID NOT NULL REFERENCES support_tickets(id) ON DELETE CASCADE,
    sender_id   UUID NOT NULL REFERENCES users(id),
    body        TEXT NOT NULL,
    is_read     BOOLEAN DEFAULT FALSE,
    created_at  TIMESTAMP DEFAULT NOW()
);
```

### 4.24 contact_messages

```sql
CREATE TABLE contact_messages (
    id          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    first_name  VARCHAR(100) NOT NULL,
    last_name   VARCHAR(100) NOT NULL,
    email       VARCHAR(255) NOT NULL,
    phone       VARCHAR(20),
    subject     VARCHAR(100),
    message     TEXT NOT NULL,
    consent     BOOLEAN DEFAULT TRUE,
    status      VARCHAR(20) DEFAULT 'new',
    notes       TEXT,
    created_at  TIMESTAMP DEFAULT NOW()
);
```

### 4.25 CMS tables

```sql
CREATE TABLE pages (
    id          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    slug        VARCHAR(100) NOT NULL UNIQUE,
    title       VARCHAR(200) NOT NULL,
    title_en    VARCHAR(200),
    meta_title  VARCHAR(200),
    meta_desc   TEXT,
    is_published BOOLEAN DEFAULT TRUE,
    created_at  TIMESTAMP DEFAULT NOW(),
    updated_at  TIMESTAMP DEFAULT NOW()
);

CREATE TABLE page_sections (
    id          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    page_id     UUID NOT NULL REFERENCES pages(id) ON DELETE CASCADE,
    section_key VARCHAR(100) NOT NULL,
    title       TEXT,
    title_en    TEXT,
    content     TEXT,
    content_en  TEXT,
    image_url   VARCHAR(500),
    sort_order  INT DEFAULT 0,
    metadata    JSONB,
    created_at  TIMESTAMP DEFAULT NOW()
);

CREATE TABLE testimonials (
    id          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    name        VARCHAR(100) NOT NULL,
    program     VARCHAR(200),
    text        TEXT NOT NULL,
    text_en     TEXT,
    rating      INT DEFAULT 5,
    image_url   VARCHAR(500),
    is_active   BOOLEAN DEFAULT TRUE,
    sort_order  INT DEFAULT 0,
    created_at  TIMESTAMP DEFAULT NOW()
);

CREATE TABLE faqs (
    id          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    category    VARCHAR(50),
    question    TEXT NOT NULL,
    question_en TEXT,
    answer      TEXT NOT NULL,
    answer_en   TEXT,
    sort_order  INT DEFAULT 0,
    is_active   BOOLEAN DEFAULT TRUE,
    created_at  TIMESTAMP DEFAULT NOW()
);

CREATE TABLE media (
    id            UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    filename      VARCHAR(255) NOT NULL,
    original_name VARCHAR(255),
    mime_type     VARCHAR(100),
    size          BIGINT,
    url           VARCHAR(500) NOT NULL,
    alt_text      VARCHAR(200),
    uploaded_by   UUID REFERENCES users(id),
    folder        VARCHAR(100),
    created_at    TIMESTAMP DEFAULT NOW()
);

CREATE TABLE speech_bubbles (
    id          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    section     VARCHAR(50) NOT NULL,
    text        TEXT NOT NULL,
    text_en     TEXT,
    color       VARCHAR(20),
    sort_order  INT DEFAULT 0,
    is_active   BOOLEAN DEFAULT TRUE,
    created_at  TIMESTAMP DEFAULT NOW()
);

CREATE TABLE statistics (
    id          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    stat_key    VARCHAR(50) NOT NULL UNIQUE,
    value       VARCHAR(100) NOT NULL,
    label       VARCHAR(100),
    label_en    VARCHAR(100),
    icon        VARCHAR(50),
    sort_order  INT DEFAULT 0,
    is_active   BOOLEAN DEFAULT TRUE,
    created_at  TIMESTAMP DEFAULT NOW()
);

CREATE TABLE site_settings (
    id          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    setting_type VARCHAR(20) DEFAULT 'string',
    group_name  VARCHAR(50),
    updated_at  TIMESTAMP DEFAULT NOW()
);

CREATE TABLE email_templates (
    id          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    name        VARCHAR(100) NOT NULL UNIQUE,
    subject     VARCHAR(200) NOT NULL,
    subject_en  VARCHAR(200),
    body_html   TEXT NOT NULL,
    body_html_en TEXT,
    variables   JSONB,
    is_active   BOOLEAN DEFAULT TRUE,
    created_at  TIMESTAMP DEFAULT NOW()
);
```

### 4.26 System tables

```sql
CREATE TABLE audit_logs (
    id          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id     UUID REFERENCES users(id),
    action      VARCHAR(50) NOT NULL,
    entity_type VARCHAR(50) NOT NULL,
    entity_id   UUID,
    old_values  JSONB,
    new_values  JSONB,
    ip_address  INET,
    created_at  TIMESTAMP DEFAULT NOW()
);

CREATE TABLE user_sessions (
    id           UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id      UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    refresh_token VARCHAR(255) NOT NULL,
    ip_address   INET,
    user_agent   TEXT,
    expires_at   TIMESTAMP NOT NULL,
    created_at   TIMESTAMP DEFAULT NOW()
);

CREATE TABLE password_resets (
    id          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id     UUID NOT NULL REFERENCES users(id),
    token       VARCHAR(255) NOT NULL,
    expires_at  TIMESTAMP NOT NULL,
    used_at     TIMESTAMP,
    created_at  TIMESTAMP DEFAULT NOW()
);

CREATE TABLE student_skill_progress (
    id          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    student_id  UUID NOT NULL REFERENCES students(id),
    skill       VARCHAR(20) NOT NULL,
    percentage  DECIMAL(5,2) DEFAULT 0,
    updated_at  TIMESTAMP DEFAULT NOW(),
    UNIQUE(student_id, skill)
);
```

---

## 5. ER Diagram (Textual)

```
users (1) ──── (1) students
users (1) ──── (1) teachers
users (1) ──── (N) user_sessions
users (1) ──── (N) password_resets
users (1) ──── (N) notifications
users (1) ──── (N) audit_logs

students (1) ──── (N) bookings
students (1) ──── (N) enrollments
students (1) ──── (N) placement_tests
students (1) ──── (N) attendance_records
students (1) ──── (N) test_results
students (1) ──── (N) grades
students (1) ──── (N) payments
students (1) ──── (N) invoices
students (1) ──── (N) support_tickets
students (1) ──── (N) student_skill_progress

teachers (1) ──── (N) lessons
teachers (1) ──── (N) teacher_specialties
teachers (1) ──── (N) teacher_availabilities
teachers (1) ──── (N) teacher_hours
teachers (1) ──── (N) teacher_payrolls
teachers (1) ──── (N) teacher_notes

categories (1) ──── (N) programs

programs (1) ──── (N) course_packages
programs (1) ──── (N) group_schedules
programs (1) ──── (N) program_sessions
programs (1) ──── (N) benefits
programs (1) ──── (N) lessons
programs (1) ──── (N) bookings
programs (1) ──── (N) enrollments
programs (1) ──── (N) tests
programs (1) ──── (N) time_slots
programs (1) ──── (N) calendar_events

bookings (1) ──── (N) test_answers (via placement_tests)
bookings (1) ──── (1) placement_tests
bookings (1) ──── (N) payments

lessons (1) ──── (N) attendance_records
lessons (1) ──── (N) teacher_hours

placement_tests (1) ──── (N) test_answers

tests (1) ──── (N) test_results

support_tickets (1) ──── (N) support_messages

invoices (1) ──── (N) invoice_lines

pages (1) ──── (N) page_sections
```

---

## 6. CRUD Matrix

### Students (Admin)

| Operation | UI Location | Notes |
|-----------|------------|-------|
| Create | Admin > Students > "Ajouter" button | Form with name, email, level, program |
| Read | Admin > Students table + detail view | List with filters (course, level, status, search) |
| Update | Admin > Students > Edit icon | Inline edit or modal |
| Delete | Admin > Students | Soft delete |
| Export | Admin > Students > "Exporter" button | CSV/Excel download |
| View Profile | Admin > Students > Eye icon | 5 tabs: Overview, Attendance, Tests, Schedule, Payments |

### Teachers (Admin)

| Operation | UI Location | Notes |
|-----------|------------|-------|
| Create | Admin > Teachers > "Ajouter" button | Form with name, email, specialties |
| Read | Admin > Teachers table + detail view | List with filters (dept, search) |
| Update | Admin > Teachers > Edit icon | Edit info, rates, contract hours, notes |
| Delete | Admin > Teachers | Soft delete |
| Mark Paid | Admin > Teacher > Payroll tab | Updates payment status |
| Edit Rates | Admin > Teacher > Overview tab | Inline editable hourly rates |
| Edit Contract | Admin > Teacher > Hours tab | Contract hours/month |
| Admin Notes | Admin > Teacher > Notes tab | Textarea with save |

### Programs/Courses (Admin)

| Operation | UI Location | Notes |
|-----------|------------|-------|
| Create | Admin > Programs > "Nouveau programme" | Modal: name, type, level, duration, price, description |
| Read | Admin > Programs table + card grid | List with filters (category, status) |
| Update | Admin > Programs > Edit icon | Edit all fields |
| Delete | Admin > Programs > X button | Soft delete |
| Manage Sessions | Admin > Program > Sessions tab | Full CRUD + reorder + duplicate |
| Manage Benefits | Admin > Program > Benefits tab | Full CRUD + reorder |

### Lessons (Admin + Teacher)

| Operation | UI Location | Notes |
|-----------|------------|-------|
| Create | Admin > Lessons > "Planifier" | Schedule new lesson |
| Read | Admin > Lessons table + calendar view | List with filters (period) |
| Update | Admin > Lessons > View/Edit | Edit details |
| Delete | Admin > Lessons | Remove lesson |
| Complete | Teacher > Sessions > Checkmark | Mark lesson as completed |

### Bookings (Admin)

| Operation | UI Location | Notes |
|-----------|------------|-------|
| Create | Public > Booking wizard (4 steps) | Multi-step: info, test, results, oral test |
| Read | Admin > Bookings table | List with status filter |
| Update | Admin > Bookings > Edit | Modify booking |
| Confirm | Admin > Bookings > Checkmark button | Approve pending booking |
| Cancel | Admin > Bookings > X button | Reject pending booking |

### Payments (Admin + Student)

| Operation | UI Location | Notes |
|-----------|------------|-------|
| Create | Auto on booking confirmation | Generates payment record |
| Read (Admin) | Admin > Payments table | List with stats |
| Read (Student) | Student > Payments page | Summary + invoice table |
| Download | Student > Payments > Download button | PDF invoice download |

### Tests & Grades (Teacher + Admin)

| Operation | UI Location | Notes |
|-----------|------------|-------|
| Create | Auto on placement test completion | Generates test result |
| Read | Admin > Tests table + detail | List with filters |
| Grade | Teacher > Tests > Grade inputs | Editable letter grade column |
| Save Grades | Teacher > Tests > "Enregistrer" button | Bulk save grades |

### Attendance (Teacher + Admin + Student)

| Operation | UI Location | Notes |
|-----------|------------|-------|
| Mark | Teacher > Attendance > Dropdown | Present/Late/Absent per student |
| Read (Teacher) | Teacher > Attendance table | Per-course summary |
| Read (Student) | Student > Attendance page | KPIs + monthly heatmap |
| Read (Admin) | Admin > Attendance page | KPIs, trends, by-program |

### Calendar Events (Admin)

| Operation | UI Location | Notes |
|-----------|------------|-------|
| Create | Admin > Calendar > "Ajouter evenement" | Modal: title, date, type, time, teacher |
| Read | Admin > Calendar grid | Monthly view with event badges |
| Update | Admin > Calendar > Click event | Edit event |
| Delete | Admin > Calendar | Remove event |

### Settings (Admin)

| Operation | UI Location | Notes |
|-----------|------------|-------|
| Read | Admin > Settings tabs | General, Payment, Email, Security |
| Update General | Admin > Settings > General tab | School name, email, currency, timezone |
| Update Payment | Admin > Settings > Payment tab | Methods, service fee |
| Update Email | Admin > Settings > Email tab | Notification toggles |
| Update Security | Admin > Settings > Security tab | Password change |

### Reports (Admin)

| Operation | UI Location | Notes |
|-----------|------------|-------|
| View | Admin > Reports > 6 report cards | Performance, Attendance, Revenue, Workload, Top Performers, Compliance |
| Export | Admin > Reports > Export button | PDF/CSV download |

### CMS Content (Admin)

| Operation | UI Location | Notes |
|-----------|------------|-------|
| Manage Pages | Admin > CMS | Edit page sections |
| Manage Testimonials | Admin > CMS | CRUD testimonials |
| Manage FAQs | Admin > CMS | CRUD FAQs |
| Manage Media | Admin > Media Library | Upload, replace, delete images/documents |
| Manage Homepage | Admin > CMS | Stats, bubbles, sections |
| Manage Email Templates | Admin > Settings | Edit email templates |

---

## 7. API Specification

Base URL: `/api/v1`

### 7.1 Authentication

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/auth/register` | No | Create new student account |
| POST | `/auth/login` | No | Login, returns JWT + refresh token |
| POST | `/auth/logout` | Yes | Invalidate session |
| POST | `/auth/refresh` | No (refresh token) | Get new access token |
| POST | `/auth/forgot-password` | No | Send reset email |
| POST | `/auth/reset-password` | No | Reset password with token |
| GET | `/auth/me` | Yes | Current user info |
| PUT | `/auth/verify-email` | Yes | Verify email address |

### 7.2 Students

| Method | Endpoint | Auth | Role | Description |
|--------|----------|------|------|-------------|
| GET | `/students` | Yes | admin | List all students (filters: course, level, status, search, pagination) |
| GET | `/students/:id` | Yes | admin/self | Student detail |
| POST | `/students` | Yes | admin | Create student |
| PUT | `/students/:id` | Yes | admin/self | Update student |
| DELETE | `/students/:id` | Yes | admin | Soft delete |
| GET | `/students/export` | Yes | admin | Export CSV/Excel |
| GET | `/students/:id/attendance` | Yes | admin/teacher/self | Attendance records |
| GET | `/students/:id/tests` | Yes | admin/teacher/self | Test results |
| GET | `/students/:id/schedule` | Yes | admin/self | Schedule |
| GET | `/students/:id/payments` | Yes | admin/self | Payment history |
| GET | `/students/:id/skills` | Yes | admin/teacher/self | 4-skill progress |
| PUT | `/students/:id/profile` | Yes | self | Update personal info |
| PUT | `/students/:id/language-prefs` | Yes | self | Update language preferences |
| POST | `/students/:id/avatar` | Yes | self | Upload avatar |

### 7.3 Teachers

| Method | Endpoint | Auth | Role | Description |
|--------|----------|------|------|-------------|
| GET | `/teachers` | Yes | admin | List teachers (filters: dept, search) |
| GET | `/teachers/:id` | Yes | admin/self | Teacher detail |
| POST | `/teachers` | Yes | admin | Create teacher |
| PUT | `/teachers/:id` | Yes | admin/self | Update teacher |
| DELETE | `/teachers/:id` | Yes | admin | Delete teacher |
| GET | `/teachers/:id/classes` | Yes | admin/self | Assigned courses |
| GET | `/teachers/:id/hours` | Yes | admin/self | Work hours |
| PUT | `/teachers/:id/hours` | Yes | admin | Update rates/contract |
| GET | `/teachers/:id/payroll` | Yes | admin/self | Payroll records |
| PUT | `/teachers/:id/payroll/mark-paid` | Yes | admin | Mark payroll as paid |
| GET | `/teachers/:id/notes` | Yes | admin | Admin notes |
| PUT | `/teachers/:id/notes` | Yes | admin | Update notes |
| GET | `/teachers/:id/availability` | Yes | admin/self | Availability schedule |
| PUT | `/teachers/:id/availability` | Yes | admin/self | Update availability |
| GET | `/teachers/:id/specialties` | Yes | admin/self | Specialties |
| PUT | `/teachers/:id/specialties` | Yes | admin | Update specialties |

### 7.4 Programs

| Method | Endpoint | Auth | Role | Description |
|--------|----------|------|------|-------------|
| GET | `/programs` | Yes/No | Public reads | List programs (filters: category, status) |
| GET | `/programs/:id` | Yes/No | Public reads | Program detail |
| POST | `/programs` | Yes | admin | Create program |
| PUT | `/programs/:id` | Yes | admin | Update program |
| DELETE | `/programs/:id` | Yes | admin | Soft delete |
| GET | `/programs/:id/sessions` | Yes | admin | List sessions |
| POST | `/programs/:id/sessions` | Yes | admin | Create session |
| PUT | `/programs/:id/sessions/:sid` | Yes | admin | Update session |
| DELETE | `/programs/:id/sessions/:sid` | Yes | admin | Delete session |
| PUT | `/programs/:id/sessions/reorder` | Yes | admin | Reorder sessions |
| POST | `/programs/:id/sessions/:sid/duplicate` | Yes | admin | Duplicate session |
| GET | `/programs/:id/benefits` | Yes | admin | List benefits |
| POST | `/programs/:id/benefits` | Yes | admin | Create benefit |
| PUT | `/programs/:id/benefits/:bid` | Yes | admin | Update benefit |
| DELETE | `/programs/:id/benefits/:bid` | Yes | admin | Delete benefit |
| PUT | `/programs/:id/benefits/reorder` | Yes | admin | Reorder benefits |
| GET | `/programs/:id/packages` | Yes/No | Public reads | List packages |
| GET | `/programs/:id/schedules` | Yes/No | Public reads | Group schedules |

### 7.5 Categories

| Method | Endpoint | Auth | Role | Description |
|--------|----------|------|------|-------------|
| GET | `/categories` | No | Public | List categories |
| POST | `/categories` | Yes | admin | Create |
| PUT | `/categories/:id` | Yes | admin | Update |
| DELETE | `/categories/:id` | Yes | admin | Delete |

### 7.6 Bookings

| Method | Endpoint | Auth | Role | Description |
|--------|----------|------|------|-------------|
| GET | `/bookings` | Yes | admin | List bookings (filters: status) |
| GET | `/bookings/:id` | Yes | admin/self | Booking detail |
| POST | `/bookings` | No | Public | Create booking (from wizard) |
| PUT | `/bookings/:id` | Yes | admin | Update booking |
| PUT | `/bookings/:id/confirm` | Yes | admin | Approve booking |
| PUT | `/bookings/:id/cancel` | Yes | admin | Cancel booking |
| GET | `/bookings/:id/timeline` | Yes | admin/self | Booking status timeline |

### 7.7 Enrollments

| Method | Endpoint | Auth | Role | Description |
|--------|----------|------|------|-------------|
| GET | `/enrollments` | Yes | admin | List enrollments |
| GET | `/students/:id/enrollments` | Yes | admin/self | Student's enrollments |
| PUT | `/enrollments/:id` | Yes | admin | Update enrollment (status, progress) |
| DELETE | `/enrollments/:id` | Yes | admin | Drop enrollment |

### 7.8 Lessons

| Method | Endpoint | Auth | Role | Description |
|--------|----------|------|------|-------------|
| GET | `/lessons` | Yes | admin/teacher | List lessons (filters: period, course, teacher) |
| GET | `/lessons/:id` | Yes | admin/teacher | Lesson detail |
| POST | `/lessons` | Yes | admin | Create lesson |
| PUT | `/lessons/:id` | Yes | admin/teacher | Update lesson |
| DELETE | `/lessons/:id` | Yes | admin | Delete lesson |
| PUT | `/lessons/:id/complete` | Yes | teacher | Mark completed |
| GET | `/lessons/timetable` | Yes | admin/teacher | Weekly timetable data |

### 7.9 Attendance

| Method | Endpoint | Auth | Role | Description |
|--------|----------|------|------|-------------|
| GET | `/attendance/lesson/:lessonId` | Yes | admin/teacher | Attendance for a lesson |
| PUT | `/attendance/:id` | Yes | teacher | Update status (present/late/absent) |
| POST | `/attendance/bulk` | Yes | teacher | Bulk mark attendance |
| GET | `/attendance/kpis` | Yes | admin | Dashboard KPIs |
| GET | `/attendance/trend` | Yes | admin | Monthly trend |
| GET | `/attendance/heatmap` | Yes | admin/student | Calendar heatmap |
| GET | `/attendance/by-program` | Yes | admin | Per-program breakdown |

### 7.10 Tests & Grades

| Method | Endpoint | Auth | Role | Description |
|--------|----------|------|------|-------------|
| GET | `/tests` | Yes | admin/teacher | List tests (filters: type, student) |
| GET | `/tests/:id` | Yes | admin/teacher | Test detail with questions |
| POST | `/tests` | Yes | admin/teacher | Create test |
| PUT | `/tests/:id` | Yes | admin/teacher | Update test |
| DELETE | `/tests/:id` | Yes | admin | Delete test |
| GET | `/tests/stats` | Yes | admin | Distribution + pass rate |
| GET | `/test-results` | Yes | admin/teacher | List results |
| POST | `/test-results` | Yes | admin/teacher | Submit result |
| PUT | `/grades/bulk` | Yes | teacher | Save multiple grades |

### 7.11 Placement Test

| Method | Endpoint | Auth | Role | Description |
|--------|----------|------|------|-------------|
| POST | `/placement-test/start` | No | Public | Start test, returns questions |
| POST | `/placement-test/submit` | No | Public | Submit answers, returns score + level |
| GET | `/placement-test/:id/result` | Yes | self | Get test result |

### 7.12 Time Slots (Solo)

| Method | Endpoint | Auth | Role | Description |
|--------|----------|------|------|-------------|
| GET | `/slots?programId=&date=` | No | Public | Available slots |
| POST | `/slots/book` | No | Public | Reserve a slot |

### 7.13 Payments

| Method | Endpoint | Auth | Role | Description |
|--------|----------|------|------|-------------|
| GET | `/payments` | Yes | admin | List payments (filters: status) |
| GET | `/payments/kpis` | Yes | admin | Dashboard KPIs |
| GET | `/payments/:id` | Yes | admin/self | Payment detail |
| POST | `/payments` | Yes | admin | Record payment |
| POST | `/payments/refund/:id` | Yes | admin | Refund |
| GET | `/students/:id/payments` | Yes | admin/self | Student payments |

### 7.14 Invoices

| Method | Endpoint | Auth | Role | Description |
|--------|----------|------|------|-------------|
| GET | `/invoices` | Yes | admin | List invoices |
| GET | `/students/:id/invoices` | Yes | admin/self | Student invoices |
| GET | `/invoices/:id/download` | Yes | self | Download PDF |
| POST | `/invoices` | Yes | admin | Generate invoice |

### 7.15 Teacher Hours & Payroll

| Method | Endpoint | Auth | Role | Description |
|--------|----------|------|------|-------------|
| GET | `/hours` | Yes | admin | List all hours |
| GET | `/hours/kpis` | Yes | admin | Dashboard KPIs |
| GET | `/hours/by-teacher` | Yes | admin | Per-teacher breakdown |
| GET | `/payroll` | Yes | admin | List payroll records |
| PUT | `/payroll/:id/mark-paid` | Yes | admin | Mark as paid |

### 7.16 Calendar

| Method | Endpoint | Auth | Role | Description |
|--------|----------|------|------|-------------|
| GET | `/calendar/events` | Yes | All | List events (filters: month, year, type) |
| GET | `/calendar/events/:id` | Yes | All | Event detail |
| POST | `/calendar/events` | Yes | admin | Create event |
| PUT | `/calendar/events/:id` | Yes | admin | Update event |
| DELETE | `/calendar/events/:id` | Yes | admin | Delete event |

### 7.17 Notifications

| Method | Endpoint | Auth | Role | Description |
|--------|----------|------|------|-------------|
| GET | `/notifications` | Yes | All | List notifications |
| PUT | `/notifications/:id/read` | Yes | All | Mark as read |
| PUT | `/notifications/read-all` | Yes | All | Mark all as read |

### 7.18 Support

| Method | Endpoint | Auth | Role | Description |
|--------|----------|------|------|-------------|
| GET | `/support/tickets` | Yes | admin/self | List tickets |
| POST | `/support/tickets` | Yes | student | Create ticket |
| GET | `/support/tickets/:id` | Yes | admin/self | Ticket with messages |
| POST | `/support/tickets/:id/messages` | Yes | admin/student | Add message |
| PUT | `/support/tickets/:id/close` | Yes | admin | Close ticket |

### 7.19 Contact

| Method | Endpoint | Auth | Role | Description |
|--------|----------|------|------|-------------|
| POST | `/contact` | No | Public | Submit contact form |

### 7.20 CMS

| Method | Endpoint | Auth | Role | Description |
|--------|----------|------|------|-------------|
| GET | `/cms/pages/:slug` | No | Public | Get page with sections |
| PUT | `/cms/pages/:slug` | Yes | admin | Update page |
| GET | `/cms/testimonials` | No | Public | List testimonials |
| CRUD | `/cms/testimonials` | Yes | admin | Manage testimonials |
| GET | `/cms/faqs` | No | Public | List FAQs |
| CRUD | `/cms/faqs` | Yes | admin | Manage FAQs |
| CRUD | `/cms/bubbles` | Yes | admin | Manage speech bubbles |
| CRUD | `/cms/statistics` | Yes | admin | Manage homepage stats |
| CRUD | `/cms/media` | Yes | admin | Upload, list, delete media |
| GET | `/cms/settings` | Yes | admin | Get all settings |
| PUT | `/cms/settings` | Yes | admin | Update settings |
| CRUD | `/cms/email-templates` | Yes | admin | Manage email templates |

### 7.21 Reports & Analytics

| Method | Endpoint | Auth | Role | Description |
|--------|----------|------|------|-------------|
| GET | `/reports/performance` | Yes | admin | Student performance report |
| GET | `/reports/attendance` | Yes | admin | Attendance report |
| GET | `/reports/revenue` | Yes | admin | Revenue report |
| GET | `/reports/workload` | Yes | admin | Teacher workload report |
| GET | `/reports/top-performers` | Yes | admin | Top students |
| GET | `/reports/compliance` | Yes | admin | Compliance report |
| GET | `/reports/export/:type` | Yes | admin | Export PDF/CSV |
| GET | `/analytics/enrollment-growth` | Yes | admin | Enrollment trends |
| GET | `/analytics/program-distribution` | Yes | admin | Program breakdown |
| GET | `/analytics/success-rate` | Yes | admin | Success rate |
| GET | `/analytics/retention-rate` | Yes | admin | Retention |
| GET | `/analytics/satisfaction` | Yes | admin | Satisfaction score |

### 7.22 Dashboard

| Method | Endpoint | Auth | Role | Description |
|--------|----------|------|------|-------------|
| GET | `/dashboard/admin/kpis` | Yes | admin | Admin dashboard KPIs |
| GET | `/dashboard/admin/enrollment-growth` | Yes | admin | Chart data |
| GET | `/dashboard/admin/insights` | Yes | admin | Insight cards |
| GET | `/dashboard/student/stats` | Yes | student | Student dashboard stats |
| GET | `/dashboard/student/next-session` | Yes | student | Next session |
| GET | `/dashboard/student/skills` | Yes | student | Skill progress |
| GET | `/dashboard/teacher/stats` | Yes | teacher | Teacher dashboard stats |
| GET | `/dashboard/teacher/today-schedule` | Yes | teacher | Today's sessions |
| GET | `/dashboard/teacher/performance` | Yes | teacher | Per-course performance |

### 7.23 Global Search

| Method | Endpoint | Auth | Role | Description |
|--------|----------|------|------|-------------|
| GET | `/search?q=` | Yes | All | Search across students, programs, lessons |

---

## 8. Permission Matrix

### Roles

| Role | Description |
|------|-------------|
| `admin` | Full access to everything |
| `teacher` | Access to own courses, students, grades, attendance, schedule, profile |
| `student` | Access to own profile, programs, payments, tests, calendar, support |
| `accountant` (planned) | Revenue, payments, reports, invoices |

### Permission Definitions

| Permission | Admin | Teacher | Student |
|------------|-------|---------|---------|
| students.view | All | Own course students | - |
| students.create | Yes | - | - |
| students.update | All | Own course students (limited) | Self only |
| students.delete | Yes | - | - |
| students.export | Yes | - | - |
| teachers.view | All | - | - |
| teachers.create | Yes | - | - |
| teachers.update | All | Self only | - |
| teachers.delete | Yes | - | - |
| programs.view | All | Assigned | Enrolled |
| programs.create | Yes | - | - |
| programs.update | Yes | - | - |
| programs.delete | Yes | - | - |
| lessons.view | All | Assigned | Enrolled |
| lessons.create | Yes | - | - |
| lessons.update | Yes | Assigned | - |
| lessons.delete | Yes | - | - |
| attendance.view | All | Own lessons | Self |
| attendance.mark | Yes | Own lessons | - |
| tests.view | All | Own courses | Self |
| tests.create | Yes | Yes | - |
| grades.view | All | Own courses | Self |
| grades.enter | Yes | Own courses | - |
| bookings.view | All | - | Self |
| bookings.confirm | Yes | - | - |
| bookings.cancel | Yes | - | - |
| payments.view | All | - | Self |
| payments.refund | Yes | - | - |
| reports.view | All | - | - |
| reports.export | Yes | - | - |
| settings.view | Yes | - | - |
| settings.update | Yes | - | - |
| cms.view | All | - | - |
| cms.update | Yes | - | - |
| notifications.view | All | All | All |
| support.view | All | - | Own tickets |
| support.reply | Yes | - | Own tickets |
| calendar.view | All | Own | Self |
| calendar.create | Yes | - | - |

---

## 9. Authentication Flow

### Registration Flow
1. Student fills registration form (name, email, password, level, goal)
2. Backend validates email uniqueness
3. Backend hashes password (bcrypt, 12 rounds)
4. Backend creates user record (role=student)
5. Backend creates student profile record
6. Backend generates student_number (STU-YYYY-NNNN)
7. Backend sends verification email
8. Backend returns JWT access token + refresh token
9. Frontend stores tokens in httpOnly cookies

### Login Flow
1. User submits email + password
2. Backend looks up user by email
3. Backend verifies password hash
4. Backend updates `last_login_at`
5. Backend generates JWT access token (15min) + refresh token (7 days)
6. Backend creates session record
7. Backend returns tokens
8. Frontend stores in httpOnly cookies
9. Frontend redirects to role-appropriate dashboard

### Token Refresh Flow
1. Access token expires (15min)
2. Frontend sends refresh token
3. Backend validates refresh token against session table
4. Backend generates new access token
5. Backend returns new access token

### Password Reset Flow
1. User clicks "Forgot password"
2. User submits email
3. Backend generates secure token (crypto.randomBytes)
4. Backend stores in `password_resets` table with 1h expiry
5. Backend sends reset email with link
6. User clicks link, submits new password
7. Backend validates token and expiry
8. Backend hashes new password
9. Backend invalidates all refresh tokens for user
10. User redirected to login

### Session Management
- JWT access token: 15-minute expiry, stored in httpOnly cookie
- Refresh token: 7-day expiry, stored in httpOnly cookie
- Maximum 5 concurrent sessions per user
- Logout invalidates current session

---

## 10. Booking Workflow

### Solo Course Booking

```
Step 1: Service Selection
  └─> User selects program (e.g., Formation en Solo)
      └─> For solo: show package picker (5h/10h/15h/20h)

Step 2: Personal Information
  └─> Name, Email, Phone, Contact preference, Notes
      └─> Validates required fields

Step 3: Scheduling
  └─> Calendar date picker (weekends/past dates disabled)
      └─> Time slot picker (09:00-16:30, 30min intervals)
          └─> Slots available from backend

Step 4: Placement Test (Optional)
  └─> 20-question French test (4 categories)
      └─> Auto-scored, determines CEFR level
          └─> Results shown with level badge

Step 5: Oral Test Scheduling (Optional)
  └─> Calendar + time slot picker for oral evaluation

Step 6: Confirmation
  └─> Summary of all selections
      └─> Create booking (status=pending)
          └─> Generate booking_ref (BK-YYYYMMDD-NNN)
              └─> Send confirmation email
                  └─> Redirect to student dashboard
```

### Group Course Booking

```
Step 1: Service Selection
  └─> User selects group program

Step 2: Schedule Selection
  └─> Show available groups (day, time, seats remaining)
      └─> User selects group

Step 3: Personal Information
  └─> Name, Email, Phone, Notes

Step 4: Placement Test + Oral Test (same as solo)

Step 5: Confirmation
  └─> Create booking + enrollment
      └─> Decrease available seats
          └─> Send confirmation email
```

### Booking Statuses

| Status | Description | Next States |
|--------|-------------|-------------|
| `pending` | Awaiting admin confirmation | confirmed, cancelled |
| `confirmed` | Approved, enrollment created | completed |
| `cancelled` | Rejected or student cancelled | - |
| `completed` | Booking fully processed | - |

### Booking Actions

| Action | Trigger | Side Effects |
|--------|---------|-------------|
| Confirm | Admin clicks confirm | Create enrollment, send confirmation email, decrease seats |
| Cancel | Admin clicks cancel | Send cancellation email, release seats |
| Complete | Auto after payment | Update enrollment status |

---

## 11. Payment Workflow

### Payment Flow

```
1. Booking confirmed
   └─> System generates invoice
       └─> Invoice number: FAC-YYYY-NNN

2. Payment processing
   └─> Payment methods: Credit Card, PayPal, Bank Transfer, Cash
       └─> Credit Card: Stripe integration
           └─> PayPal: PayPal API
               └─> Bank Transfer: Manual verification by admin

3. Payment recorded
   └─> Transaction ref: TXN-NNN
       └─> Status: paid, pending, failed
           └─> Receipt email sent

4. Teacher compensation
   └─> Hours tracked per lesson
       └─> Monthly payroll calculation
           └─> Admin marks as paid
```

### Payment Statuses

| Status | Description |
|--------|-------------|
| `pending` | Awaiting payment |
| `paid` | Payment received |
| `failed` | Payment failed |
| `refunded` | Payment refunded |

### Invoice Generation
- Auto-generated on booking confirmation
- Invoice number: `FAC-YYYY-NNN`
- Contains line items (program, package, extras)
- PDF downloadable from student portal
- Email notification on generation

---

## 12. CMS Requirements

### Homepage Sections (All CMS-Editable)

| Section | Editable Fields |
|---------|----------------|
| Hero | Title, subtitle, background image, search placeholder |
| Statistics | 4 stat values + labels + icons |
| Journey Cards | 6 cards with title, description, link, image |
| Program Cards (per category) | Name, description, price, schedule, image, badges |
| Comparison Table | Programs, criteria, values |
| "Why Us" Section | Title, 4 cards with icon + title + description |
| Testimonials | Name, program, text, rating, image |
| CTA Banner | Title, description, button text, link |
| Speech Bubbles | Per section: text, color |
| Footer | Contact info, social links, legal links |

### Service Pages (All CMS-Editable)

| Element | Editable Fields |
|---------|----------------|
| Page Header | Title, subtitle, hero image |
| Description | Full rich text |
| Sessions/Cohorts | Title, date range, schedule, duration, state, CTAs |
| Benefits | Title, description, icon |
| Pricing Cards | Name, hours, price, rate, features, badge |
| Images | Hero, banner, thumbnail, gallery |

### Content Translation
- Every CMS field has a `_en` counterpart
- Default language: French
- English fallback for missing translations
- Admin can edit both languages

---

## 13. Notification Workflows

### Email Notifications

| Trigger | Recipient | Template |
|---------|-----------|----------|
| Booking created | Student | booking_confirmation |
| Booking confirmed | Student | booking_approved |
| Booking cancelled | Student | booking_cancelled |
| Password reset | User | password_reset |
| Email verification | User | email_verify |
| Invoice generated | Student | invoice_available |
| Payment received | Student | payment_receipt |
| Test result ready | Student | test_result |
| Session reminder (24h) | Student + Teacher | session_reminder |
| Weekly report | Teacher | weekly_report |
| Weekly report | Admin | weekly_summary |
| Contact form submitted | Admin | contact_alert |
| Support ticket created | Admin | support_alert |

### In-App Notifications

| Trigger | User | Title | Type |
|---------|------|-------|------|
| Booking confirmed | Student | "Inscription confirmee" | booking |
| New enrollment | Teacher | "Nouvel etudiant" | enrollment |
| Test to correct | Teacher | "Tests a corriger" | academic |
| Grade published | Student | "Note disponible" | academic |
| Attendance alert (<80%) | Admin + Teacher | "Presence insuffisante" | alert |
| Payment overdue | Student + Admin | "Paiement en retard" | payment |
| New support ticket | Admin | "Nouvelle demande support" | support |
| Session in 24h | Student + Teacher | "Rappel de seance" | reminder |
| Level progression | Student | "Niveau superieur atteint!" | achievement |
| Monthly report ready | Admin | "Rapport mensuel" | report |
| Teacher hours submitted | Admin | "Heures a valider" | approval |
| At-risk student detected | Admin | "Etudiant a risque" | alert |

### Notification Preferences (per user)
- Email on/off per type
- In-app always on
- Digest mode (daily/weekly summary)

---

## 14. Validation Rules

### Registration Form

| Field | Rules |
|-------|-------|
| first_name | Required, 2-100 chars, alpha with accents |
| last_name | Required, 2-100 chars, alpha with accents |
| email | Required, valid email format, unique |
| password | Required, min 8 chars, at least 1 uppercase, 1 lowercase, 1 number |
| level | Optional, valid CEFR level |
| goal | Optional, valid goal enum |

### Booking Form

| Field | Rules |
|-------|-------|
| name | Required, 2-200 chars |
| email | Required, valid email |
| phone | Required, valid phone format |
| course | Required, valid course ID |
| package | Required for solo programs, valid package ID |
| date | Required, future date, not weekend |
| slot | Required, valid time slot |

### Contact Form

| Field | Rules |
|-------|-------|
| first_name | Required, 2-100 chars |
| last_name | Required, 2-100 chars |
| email | Required, valid email |
| phone | Optional, valid phone |
| subject | Required, one of 6 predefined options |
| message | Required, 10-5000 chars |

### Teacher Profile

| Field | Rules |
|-------|-------|
| first_name | Required, 2-100 chars |
| last_name | Required, 2-100 chars |
| email | Required, valid email, unique |
| phone | Optional, valid phone |
| hourly_rate_solo | Required, numeric, min 0 |
| hourly_rate_group | Required, numeric, min 0 |
| contract_hours | Required, integer, min 1, max 200 |

### Program CRUD

| Field | Rules |
|-------|-------|
| name | Required, 2-200 chars |
| slug | Required, unique, URL-safe |
| type | Required, valid type enum |
| duration_weeks | Required, positive integer |
| price_monthly | Required, numeric, min 0 |
| description | Required, 10-10000 chars |

### Test Questions

| Field | Rules |
|-------|-------|
| question_text | Required, 10-1000 chars |
| options | Required, array of 4 strings |
| correct_index | Required, 0-3 |
| category | Required, valid category |

### Server-Side Validation
- All inputs sanitized against XSS
- SQL injection prevention via parameterized queries
- CSRF tokens on all forms
- Rate limiting: 100 requests/minute per IP for public endpoints, 1000/minute for authenticated
- File upload: max 10MB, allowed types: jpg, png, gif, webp, pdf

---

## 15. Background Jobs

| Job | Schedule | Description |
|-----|----------|-------------|
| `send-email` | On event | Queue and send emails via SMTP/SendGrid |
| `session-reminder` | Daily 8:00 | Send 24h reminders for tomorrow's sessions |
| `weekly-report` | Monday 7:00 | Generate and email weekly teacher reports |
| `weekly-admin-summary` | Monday 8:00 | Generate admin weekly summary |
| `invoice-generation` | 1st of month | Generate monthly invoices for active enrollments |
| `overdue-payment-check` | Daily 9:00 | Flag overdue payments, send reminders |
| `at-risk-student-detection` | Weekly | Identify students with attendance < 80% or scores < 65% |
| `cleanup-expired-tokens` | Daily 2:00 | Remove expired password reset tokens and sessions |
| `generate-payslips` | End of month | Calculate teacher payroll |
| `backup-database` | Daily 2:00 | Automated database backup |
| `archive-completed-bookings` | Weekly | Archive old completed bookings |
| `notification-digest` | Daily 7:00 | Send notification digest emails |

---

## 16. File Storage Strategy

### Storage Architecture
- **Local storage** for development: `./uploads/` directory
- **Cloud storage** for production: AWS S3 / Cloudflare R2 / DigitalOcean Spaces
- **CDN** for serving: CloudFront / Cloudflare

### Upload Directories
```
uploads/
├── avatars/          # User profile photos
│   ├── students/
│   ├── teachers/
│   └── admins/
├── programs/         # Program images
│   ├── banners/
│   ├── heroes/
│   └── thumbnails/
├── media/            # CMS media library
│   ├── images/
│   ├── documents/
│   └── videos/
├── invoices/         # Generated PDF invoices
└── exports/          # Temporary export files (auto-delete after 24h)
```

### Image Processing
- On upload: generate 3 versions (thumbnail 150px, medium 800px, full 1920px)
- Format: WebP preferred, JPEG/PNG fallback
- Max upload size: 10MB
- Allowed types: jpg, jpeg, png, gif, webp, svg, pdf
- EXIF data stripped on upload

### File Naming
- UUID-based: `{uuid}.{ext}` (prevents conflicts)
- Original name stored in `original_name` field

---

## 17. Recommended Architecture & Stack

### Technology Stack

| Layer | Technology | Rationale |
|-------|-----------|-----------|
| **Runtime** | Node.js 20 LTS | Matches existing vanilla JS frontend, fast development |
| **Framework** | Express.js | Lightweight, flexible, huge ecosystem |
| **Database** | PostgreSQL 16 | JSONB support, UUID, full-text search, mature |
| **ORM** | Prisma | Type-safe, great migrations, excellent DX |
| **Auth** | JWT (access) + httpOnly cookies (refresh) | Stateless, secure, mobile-friendly |
| **Email** | Nodemailer + SendGrid/SMTP | Reliable delivery, templates |
| **File Storage** | Local (dev) / S3-compatible (prod) | Flexible, cost-effective |
| **Validation** | Zod | Type-safe schema validation |
| **Testing** | Vitest + Supertest | Fast, modern, good DX |
| **API Docs** | OpenAPI/Swagger | Auto-generated documentation |
| **Logging** | Pino | Fast, structured JSON logging |
| **Rate Limiting** | express-rate-limit | Built-in, simple |
| **PDF Generation** | PDFKit or Puppeteer | Invoice PDFs |

### Architecture Pattern

```
┌─────────────────────────────────────────┐
│              Frontend (Static)           │
│   HTML + CSS + Vanilla JS               │
│   fetch() to /api/v1/*                   │
└───────────────┬─────────────────────────┘
                │ HTTP/HTTPS
┌───────────────▼─────────────────────────┐
│           API Gateway (Express)          │
│   Rate Limiting · CORS · Auth Middleware │
│   Request Validation (Zod)              │
└───────────────┬─────────────────────────┘
                │
┌───────────────▼─────────────────────────┐
│           Controllers (Route Handlers)   │
│   AuthController · StudentController     │
│   TeacherController · ProgramController  │
│   BookingController · PaymentController  │
│   ... (20+ controllers)                 │
└───────────────┬─────────────────────────┘
                │
┌───────────────▼─────────────────────────┐
│           Services (Business Logic)      │
│   AuthService · BookingService           │
│   PaymentService · EmailService          │
│   NotificationService · ReportService    │
│   ... (20+ services)                    │
└───────────────┬─────────────────────────┘
                │
┌───────────────▼─────────────────────────┐
│           Repositories (Data Access)     │
│   Prisma Client (auto-generated)        │
│   Query optimization, caching           │
└───────────────┬─────────────────────────┘
                │
┌───────────────▼─────────────────────────┐
│           PostgreSQL Database            │
│   40+ tables · UUIDs · JSONB · Indexes  │
└─────────────────────────────────────────┘
```

### Middleware Stack (Request Order)

1. `helmet` - Security headers
2. `cors` - Cross-origin policy
3. `express.json` - Body parsing
4. `express.urlencoded` - Form parsing
5. `morgan` / `pino-http` - Request logging
6. `rateLimiter` - Rate limiting
7. `authenticate` - JWT verification (protected routes)
8. `authorize(...)` - Role/permission check (protected routes)
9. `validate(schema)` - Request validation (Zod)
10. Controller handler

---

## 18. Recommended Folder Structure

```
server/
├── src/
│   ├── config/
│   │   ├── database.ts          # Prisma client instance
│   │   ├── env.ts               # Environment variable validation
│   │   ├── cors.ts              # CORS configuration
│   │   └── email.ts             # Email transport config
│   ├── middleware/
│   │   ├── authenticate.ts      # JWT verification
│   │   ├── authorize.ts         # Role/permission check
│   │   ├── validate.ts          # Zod schema validation
│   │   ├── rateLimiter.ts       # Rate limiting
│   │   ├── upload.ts            # Multer file upload config
│   │   ├── errorHandler.ts      # Global error handler
│   │   └── auditLog.ts          # Audit logging middleware
│   ├── modules/
│   │   ├── auth/
│   │   │   ├── auth.controller.ts
│   │   │   ├── auth.service.ts
│   │   │   ├── auth.routes.ts
│   │   │   ├── auth.schema.ts       # Zod validation schemas
│   │   │   └── auth.middleware.ts    # Auth-specific middleware
│   │   ├── students/
│   │   │   ├── student.controller.ts
│   │   │   ├── student.service.ts
│   │   │   ├── student.routes.ts
│   │   │   └── student.schema.ts
│   │   ├── teachers/
│   │   │   ├── teacher.controller.ts
│   │   │   ├── teacher.service.ts
│   │   │   ├── teacher.routes.ts
│   │   │   └── teacher.schema.ts
│   │   ├── programs/
│   │   │   ├── program.controller.ts
│   │   │   ├── program.service.ts
│   │   │   ├── program.routes.ts
│   │   │   └── program.schema.ts
│   │   ├── bookings/
│   │   │   ├── booking.controller.ts
│   │   │   ├── booking.service.ts
│   │   │   ├── booking.routes.ts
│   │   │   └── booking.schema.ts
│   │   ├── lessons/
│   │   │   ├── lesson.controller.ts
│   │   │   ├── lesson.service.ts
│   │   │   ├── lesson.routes.ts
│   │   │   └── lesson.schema.ts
│   │   ├── attendance/
│   │   │   ├── attendance.controller.ts
│   │   │   ├── attendance.service.ts
│   │   │   ├── attendance.routes.ts
│   │   │   └── attendance.schema.ts
│   │   ├── tests/
│   │   │   ├── test.controller.ts
│   │   │   ├── test.service.ts
│   │   │   ├── test.routes.ts
│   │   │   └── test.schema.ts
│   │   ├── payments/
│   │   │   ├── payment.controller.ts
│   │   │   ├── payment.service.ts
│   │   │   ├── payment.routes.ts
│   │   │   └── payment.schema.ts
│   │   ├── invoices/
│   │   │   ├── invoice.controller.ts
│   │   │   ├── invoice.service.ts
│   │   │   ├── invoice.routes.ts
│   │   │   └── invoice.schema.ts
│   │   ├── calendar/
│   │   │   ├── calendar.controller.ts
│   │   │   ├── calendar.service.ts
│   │   │   ├── calendar.routes.ts
│   │   │   └── calendar.schema.ts
│   │   ├── notifications/
│   │   │   ├── notification.controller.ts
│   │   │   ├── notification.service.ts
│   │   │   └── notification.routes.ts
│   │   ├── support/
│   │   │   ├── support.controller.ts
│   │   │   ├── support.service.ts
│   │   │   ├── support.routes.ts
│   │   │   └── support.schema.ts
│   │   ├── cms/
│   │   │   ├── cms.controller.ts
│   │   │   ├── cms.service.ts
│   │   │   ├── cms.routes.ts
│   │   │   └── cms.schema.ts
│   │   ├── reports/
│   │   │   ├── report.controller.ts
│   │   │   ├── report.service.ts
│   │   │   └── report.routes.ts
│   │   ├── settings/
│   │   │   ├── setting.controller.ts
│   │   │   ├── setting.service.ts
│   │   │   └── setting.routes.ts
│   │   └── dashboard/
│   │       ├── dashboard.controller.ts
│   │       ├── dashboard.service.ts
│   │       └── dashboard.routes.ts
│   ├── services/
│   │   ├── email.service.ts        # Email sending (SendGrid/SMTP)
│   │   ├── storage.service.ts      # File upload (S3/local)
│   │   ├── pdf.service.ts          # Invoice PDF generation
│   │   └── search.service.ts       # Full-text search
│   ├── jobs/
│   │   ├── email-queue.job.ts
│   │   ├── session-reminder.job.ts
│   │   ├── weekly-report.job.ts
│   │   ├── invoice-generation.job.ts
│   │   ├── overdue-check.job.ts
│   │   └── cleanup.job.ts
│   ├── utils/
│   │   ├── errors.ts               # Custom error classes
│   │   ├── helpers.ts              # Utility functions
│   │   ├── constants.ts            # App constants
│   │   └── logger.ts               # Pino logger
│   ├── types/
│   │   └── index.ts                # TypeScript types/interfaces
│   └── app.ts                      # Express app setup
├── prisma/
│   ├── schema.prisma               # Database schema
│   ├── migrations/                 # Auto-generated migrations
│   └── seed.ts                     # Seed data
├── uploads/                        # Local file storage
├── tests/
│   ├── unit/
│   ├── integration/
│   └── fixtures/
├── scripts/
│   └── seed.ts                     # Database seeder
├── .env.example
├── package.json
├── tsconfig.json
└── README.md
```

---

## 19. Deployment Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                        Cloudflare                            │
│                    (CDN + SSL + DDoS)                        │
└──────────┬───────────────────────────────┬──────────────────┘
           │ Static files                  │ API requests
           ▼                               ▼
┌──────────────────┐          ┌──────────────────────────────┐
│   Static Host    │          │        Application Host       │
│  (Netlify/Vercel)│          │    (Railway/Render/Fly.io)   │
│                  │          │                              │
│  index.html      │          │   Node.js + Express          │
│  pages/*         │          │   PM2 process manager        │
│  css/*           │          │   Port 3000                  │
│  js/*            │          │                              │
│  img/*           │          │   ┌──────────────────────┐   │
│                  │          │   │   Background Jobs     │   │
│                  │          │   │   (node-cron)         │   │
│                  │          │   └──────────────────────┘   │
└──────────────────┘          └──────────┬───────────────────┘
                                         │
                              ┌──────────▼───────────────────┐
                              │      PostgreSQL Database      │
                              │   (Neon/Supabase/Railway)     │
                              │                              │
                              │   - 40+ tables               │
                              │   - Daily backups            │
                              │   - Connection pooling       │
                              └──────────┬───────────────────┘
                                         │
                              ┌──────────▼───────────────────┐
                              │      Object Storage           │
                              │   (S3/R2/Spaces)             │
                              │                              │
                              │   - Avatars                  │
                              │   - Program images           │
                              │   - CMS media                │
                              │   - Invoice PDFs             │
                              └──────────────────────────────┘
```

### Environment Variables

```
# Database
DATABASE_URL=postgresql://user:pass@host:5432/cultulangues

# Auth
JWT_SECRET=your-jwt-secret
JWT_REFRESH_SECRET=your-refresh-secret
JWT_EXPIRY=15m
JWT_REFRESH_EXPIRY=7d

# Email
SMTP_HOST=smtp.sendgrid.net
SMTP_PORT=587
SMTP_USER=apikey
SMTP_PASS=your-sendgrid-key
EMAIL_FROM=noreply@cultulangues.com

# File Storage
STORAGE_PROVIDER=local  # or 's3'
S3_BUCKET=culutlangues-uploads
S3_REGION=us-east-1
S3_ACCESS_KEY=...
S3_SECRET_KEY=...

# App
PORT=3000
NODE_ENV=development
FRONTEND_URL=http://localhost:8080
CORS_ORIGIN=http://localhost:8080

# Rate Limiting
RATE_LIMIT_WINDOW_MS=60000
RATE_LIMIT_MAX=100
```

---

## 20. Development Roadmap

### Phase 1: Foundation (Weeks 1-3)

| Task | Priority | Effort |
|------|----------|--------|
| Project setup (Express + TypeScript + Prisma) | High | 2 days |
| Database schema + migrations | High | 2 days |
| Auth module (register, login, JWT, refresh, logout) | High | 3 days |
| User management (CRUD) | High | 2 days |
| Seed data (categories, programs, packages, test questions) | High | 2 days |
| Middleware (auth, validate, error handler, rate limiter) | High | 1 day |
| File upload service (local dev) | Medium | 1 day |

### Phase 2: Core Academic (Weeks 4-6)

| Task | Priority | Effort |
|------|----------|--------|
| Programs CRUD + sessions + benefits | High | 3 days |
| Students CRUD + profiles | High | 2 days |
| Teachers CRUD + specialties + availability | High | 2 days |
| Lessons CRUD + scheduling | High | 2 days |
| Attendance tracking | High | 2 days |
| Group schedules | Medium | 1 day |

### Phase 3: Booking & Assessment (Weeks 7-9)

| Task | Priority | Effort |
|------|----------|--------|
| Booking workflow (create, confirm, cancel) | High | 3 days |
| Placement test (question bank, scoring, level calc) | High | 3 days |
| Time slots for solo booking | Medium | 1 day |
| Tests & grades management | High | 2 days |
| Calendar events | Medium | 2 days |

### Phase 4: Financial (Weeks 10-11)

| Task | Priority | Effort |
|------|----------|--------|
| Payments CRUD | High | 2 days |
| Invoice generation (PDF) | High | 2 days |
| Teacher payroll calculation | Medium | 2 days |
| Teacher hours tracking | Medium | 1 day |

### Phase 5: Communication (Weeks 12-13)

| Task | Priority | Effort |
|------|----------|--------|
| Email service (SendGrid/SMTP) | High | 2 days |
| Email templates (all 12+ types) | High | 2 days |
| In-app notifications | High | 2 days |
| Support tickets + messaging | Medium | 2 days |
| Contact form handler | Medium | 1 day |

### Phase 6: CMS & Analytics (Weeks 14-15)

| Task | Priority | Effort |
|------|----------|--------|
| CMS pages + sections | Medium | 2 days |
| Testimonials CRUD | Medium | 1 day |
| FAQs CRUD | Medium | 1 day |
| Speech bubbles + statistics CRUD | Medium | 1 day |
| Media library | Medium | 2 days |
| Dashboard APIs (admin/teacher/student) | High | 2 days |
| Reports (6 types) | Medium | 2 days |
| Analytics endpoints | Medium | 1 day |

### Phase 7: Frontend Integration (Weeks 16-20)

| Task | Priority | Effort |
|------|----------|--------|
| Replace login.html with real auth | High | 2 days |
| Replace register.html with real registration | High | 1 day |
| Connect booking.html to API | High | 3 days |
| Connect admin portal to API | High | 5 days |
| Connect student portal to API | High | 4 days |
| Connect teacher portal to API | High | 3 days |
| Connect contact.html to API | Medium | 1 day |
| i18n via API | Medium | 2 days |

### Phase 8: Background Jobs & Hardening (Weeks 21-22)

| Task | Priority | Effort |
|------|----------|--------|
| Background job scheduler (node-cron) | High | 2 days |
| Session reminders | High | 1 day |
| Weekly reports | Medium | 1 day |
| Invoice generation job | Medium | 1 day |
| Cleanup jobs | Low | 1 day |
| Security audit | High | 2 days |
| Rate limiting tuning | Medium | 1 day |
| Performance optimization | Medium | 2 days |

### Phase 9: Testing & Deployment (Weeks 23-24)

| Task | Priority | Effort |
|------|----------|--------|
| Unit tests (services) | High | 3 days |
| Integration tests (API endpoints) | High | 3 days |
| Load testing | Medium | 1 day |
| Production deployment | High | 2 days |
| Database backups configured | High | 1 day |
| Monitoring + logging setup | Medium | 1 day |
| Documentation (API docs, README) | Medium | 1 day |

---

## Appendix A: Hardcoded Data Inventory

### Data Currently in main.js That Must Move to Database

| Data | Location | Records |
|------|----------|---------|
| `window.translations` | main.js lines 7-2627 | ~650 keys x 2 languages |
| `window.courseContent.programs` | main.js lines 2632-2680 | 11 programs x 2 languages |
| `window.courseContent.tcf` | main.js lines 2681-2700 | 6 TCF programs |
| `window.courseContent.private` | main.js lines 2701-2730 | 7 private lesson types |
| `window.courseContent.workshops` | main.js lines 2731-2750 | 3 workshop types |
| `window.courseContent.packages` | main.js lines 2751-2780 | 4 solo packages |
| `window.courseContent.booking.courseDB` | main.js lines 2781-2825 | 20+ courses |
| `programData` | main.js lines 3351-3462 | 11 program details |
| `flowConfigs` | main.js lines 3825-3836 | 10 booking flows |
| `groupSchedules` | main.js lines 3840-3867 | 6 group types |
| `placementTest.questionsData` | main.js lines 4278-4311 | 32 test questions |

### Data Currently in booking.html That Must Move to Database

| Data | Records |
|------|---------|
| `courseDB` (inline script) | 20+ course definitions with prices |
| `bannerMap` | 20+ image mappings |
| `programData` (inline) | Program name mappings |
| `descriptionOverrides` | Custom descriptions |
| `groupData` | Group schedules with seat counts |
| `testQuestions` | 10 placement test questions |
| Time slot generation | 14 slots per day |

### Data Currently in Portal SPAs That Must Move to Database

| File | Data |
|------|------|
| student/index.html | myCourses (3), myLessons (5), myTests (4), myPayments (3), attendance heatmap |
| teacher/index.html | tCourses (6), tLessons (7), tStudents (17), grades (10), hours (6), calendar (21) |
| admin/index.html | students (12), teachers (6), courses (18), lessons (21), payments (10), tests (6), all reports, all settings |

---

## Appendix B: URL Parameters Used by Frontend

| Page | Parameter | Purpose |
|------|-----------|---------|
| booking.html | `?course=<id>` | Pre-select course |
| booking.html | `?program=<id>` | Pre-select package |
| program-detail.html | `?id=<slug>` | Load program detail |
| booking.html | `?flow=<type>` | Select booking wizard flow |

---

## Appendix C: localStorage Keys

| Key | Set By | Read By | Content |
|-----|--------|---------|---------|
| `cultulangues_lang` | main.js (switchLanguage) | main.js, booking.html | Language preference ("fr"/"en") |
| `cultulangues_oral_test` | booking.html | student/index.html | Oral test appointment JSON |

Both must be replaced with backend API calls (user preferences stored in DB, appointments in DB).

---

*End of Backend Requirements Document*
