CREATE TABLE IF NOT EXISTS "migrations"(
  "id" integer primary key autoincrement not null,
  "migration" varchar not null,
  "batch" integer not null
);
CREATE TABLE IF NOT EXISTS "password_reset_tokens"(
  "email" varchar not null,
  "token" varchar not null,
  "created_at" datetime,
  primary key("email")
);
CREATE TABLE IF NOT EXISTS "sessions"(
  "id" varchar not null,
  "user_id" integer,
  "ip_address" varchar,
  "user_agent" text,
  "payload" text not null,
  "last_activity" integer not null,
  primary key("id")
);
CREATE INDEX "sessions_user_id_index" on "sessions"("user_id");
CREATE INDEX "sessions_last_activity_index" on "sessions"("last_activity");
CREATE TABLE IF NOT EXISTS "cache"(
  "key" varchar not null,
  "value" text not null,
  "expiration" integer not null,
  primary key("key")
);
CREATE TABLE IF NOT EXISTS "cache_locks"(
  "key" varchar not null,
  "owner" varchar not null,
  "expiration" integer not null,
  primary key("key")
);
CREATE TABLE IF NOT EXISTS "companies"(
  "id" varchar not null,
  "name" varchar not null,
  "slug" varchar not null,
  "description" text,
  "mission" text,
  "culture" text,
  "logo_url" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  primary key("id")
);
CREATE UNIQUE INDEX "companies_slug_unique" on "companies"("slug");
CREATE TABLE IF NOT EXISTS "job_categories"(
  "id" integer primary key autoincrement not null,
  "company_id" varchar not null,
  "key" varchar not null,
  "label" varchar not null,
  "order" integer not null default '0',
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "job_categories_company_id_key_unique" on "job_categories"(
  "company_id",
  "key"
);
CREATE TABLE IF NOT EXISTS "jobs"(
  "id" integer primary key autoincrement not null,
  "company_id" varchar not null,
  "job_category_id" integer,
  "title" varchar not null,
  "description" text,
  "status" varchar not null default 'open',
  "created_at" datetime,
  "updated_at" datetime,
  "share_token" varchar,
  "location" varchar,
  "employment_type" varchar,
  "salary" varchar,
  "working_hours" varchar,
  "requirements" text,
  "benefits" text,
  "notes" text
);
CREATE TABLE IF NOT EXISTS "candidates"(
  "id" integer primary key autoincrement not null,
  "company_id" varchar not null,
  "name" varchar not null,
  "email" varchar not null,
  "phone" varchar,
  "memo" text,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE INDEX "candidates_email_index" on "candidates"("email");
CREATE TABLE IF NOT EXISTS "selection_steps"(
  "id" integer primary key autoincrement not null,
  "company_id" integer not null,
  "key" varchar not null,
  "label" varchar not null,
  "order" integer not null,
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("company_id") references "companies"("id") on delete cascade
);
CREATE UNIQUE INDEX "selection_steps_company_id_key_unique" on "selection_steps"(
  "company_id",
  "key"
);
CREATE UNIQUE INDEX "selection_steps_company_id_order_unique" on "selection_steps"(
  "company_id",
  "order"
);
CREATE TABLE IF NOT EXISTS "evaluations"(
  "id" integer primary key autoincrement not null,
  "application_id" integer not null,
  "user_id" integer not null,
  "step_key" varchar not null,
  "overall_score" integer not null,
  "skill_score" integer,
  "communication_score" integer,
  "culture_fit_score" integer,
  "motivation_score" integer,
  "recommendation" varchar not null,
  "comment" text,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("application_id") references "applications"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE UNIQUE INDEX "evaluations_application_id_user_id_step_key_unique" on "evaluations"(
  "application_id",
  "user_id",
  "step_key"
);
CREATE TABLE IF NOT EXISTS "applications"(
  "id" integer primary key autoincrement not null,
  "company_id" varchar not null,
  "job_id" varchar not null,
  "candidate_id" varchar not null,
  "status" varchar not null,
  "created_at" datetime,
  "updated_at" datetime,
  "selection_step_id" integer,
  "opinio_meet_url" varchar,
  foreign key("selection_step_id") references "selection_steps"("id") on delete set null
);
CREATE INDEX "applications_company_id_status_index" on "applications"(
  "company_id",
  "status"
);
CREATE UNIQUE INDEX "applications_job_id_candidate_id_unique" on "applications"(
  "job_id",
  "candidate_id"
);
CREATE UNIQUE INDEX "jobs_share_token_unique" on "jobs"("share_token");
CREATE TABLE IF NOT EXISTS "users"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "email" varchar not null,
  "email_verified_at" datetime,
  "password" varchar not null,
  "remember_token" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "company_id" integer not null,
  foreign key("company_id") references "companies"("id") on delete cascade
);
CREATE UNIQUE INDEX "users_email_unique" on "users"("email");
CREATE TABLE IF NOT EXISTS "job_roles"(
  "id" integer primary key autoincrement not null,
  "company_id" varchar not null,
  "internal_name" varchar not null,
  "display_name" varchar not null,
  "description" text,
  "sort_order" integer not null default '0',
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE INDEX "job_roles_company_id_index" on "job_roles"("company_id");
CREATE TABLE IF NOT EXISTS "pages"(
  "id" integer primary key autoincrement not null,
  "job_id" integer,
  "title" varchar not null,
  "slug" varchar not null,
  "content" text,
  "status" varchar check("status" in('draft', 'published')) not null default 'draft',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("job_id") references "jobs"("id") on delete set null
);
CREATE UNIQUE INDEX "pages_slug_unique" on "pages"("slug");

INSERT INTO migrations VALUES(1,'0001_01_01_000000_create_users_table',1);
INSERT INTO migrations VALUES(2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO migrations VALUES(3,'2025_12_17_054257_create_companies_table',1);
INSERT INTO migrations VALUES(4,'2025_12_17_054344_create_job_categories_table',1);
INSERT INTO migrations VALUES(5,'2025_12_17_054425_create_jobs_table',1);
INSERT INTO migrations VALUES(6,'2025_12_17_054659_create_candidates_table',1);
INSERT INTO migrations VALUES(7,'2025_12_17_054848_create_applications_table',1);
INSERT INTO migrations VALUES(8,'2025_12_17_054921_create_selection_steps_table',1);
INSERT INTO migrations VALUES(9,'2025_12_17_054955_create_evaluations_table',1);
INSERT INTO migrations VALUES(10,'2025_12_17_061500_add_selection_step_id_to_applications_table',1);
INSERT INTO migrations VALUES(11,'2025_12_17_070323_add_share_token_to_jobs_table',1);
INSERT INTO migrations VALUES(12,'2025_12_17_071156_add_opinio_meet_url_to_applications_table',1);
INSERT INTO migrations VALUES(13,'2025_12_18_002659_add_detail_columns_to_jobs_table',1);
INSERT INTO migrations VALUES(14,'2025_12_18_003355_add_company_id_to_users_table',1);
INSERT INTO migrations VALUES(15,'2025_12_18_054407_create_job_roles_table',2);
INSERT INTO migrations VALUES(16,'2025_12_19_005302_create_pages_table',3);
