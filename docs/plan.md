# Moodle → MasterStudy LMS Migration Plan

## Context

EIES (Escuela Internacional Para la Educación Superior) currently runs Moodle 3.7.1 at `virtual.eies.com.bo` with 221 courses, 6,558 users, and 52GB of course files. The goal is to migrate everything to WordPress + MasterStudy LMS, eliminating Moodle entirely.

**Demo domain:** `testeoprevio.eies.com.bo` → `/public_html/pretesting`
**Final domain:** `eies.com.bo`
**Local dev:** `eies.test` (DB: `eies_fresh`, Moodle data: `moodle_eies`)

---

## Data Mapping Summary

### Categories
| Moodle | MasterStudy |
|--------|-------------|
| `mdl_course_categories` (25 categories, nested) | `stm_lms_course_taxonomy` (hierarchical taxonomy) |

### Courses (221 total)
| Moodle | MasterStudy |
|--------|-------------|
| `mdl_course` (fullname, summary, format) | `wp_posts` type `stm-courses` + post_meta |
| `mdl_course_sections` (section name, summary) | `wp_stm_lms_curriculum_sections` table |
| `mdl_course_modules` → activities | `wp_stm_lms_curriculum_materials` table |

### Lessons
| Moodle | MasterStudy |
|--------|-------------|
| `mod_resource` (files/documents) | `wp_posts` type `stm-lessons` (video/text) |
| `mod_page` (HTML content) | `wp_posts` type `stm-lessons` (text) |
| `mod_url` (external links) | `wp_posts` type `stm-lessons` (text with link) |
| `mod_label` (text labels) | Embedded in lesson content |
| `mod_folder` (file collections) | `wp_posts` type `stm-lessons` with attachments |

### Quizzes & Questions
| Moodle (9,317 questions) | MasterStudy |
|--------------------------|-------------|
| `mdl_quiz` | `wp_posts` type `stm-quizzes` + meta (duration, passing_grade) |
| `mdl_question` (multichoice: 4,114) | `wp_posts` type `stm-questions` meta type=`single_choice`/`multi_choice` |
| `mdl_question` (truefalse: 3,411) | `wp_posts` type `stm-questions` meta type=`single_choice` (2 options) |
| `mdl_question` (essay: 893) | `wp_posts` type `stm-questions` meta type=`keywords` |
| `mdl_question` (match: 208) | `wp_posts` type `stm-questions` meta type=`item_match` |
| `mdl_question` (gapselect: 384) | `wp_posts` type `stm-questions` meta type=`fill_the_gap` |
| `mdl_question` (shortanswer: 37) | `wp_posts` type `stm-questions` meta type=`keywords` |

### Users (6,558 total)
| Moodle | MasterStudy |
|--------|-------------|
| `mdl_user` (username, email, firstname, lastname) | `wp_users` + `wp_usermeta` |
| Role: student (4,599) | WP role: `subscriber` |
| Role: editingteacher (59) | WP role: `author` + `stm_lms_instructor` capability |
| Role: teacher (36) | WP role: `author` + `stm_lms_instructor` capability |
| Role: manager (73) | WP role: `editor` |

### Enrollments (4,761)
| Moodle | MasterStudy |
|--------|-------------|
| `mdl_user_enrolments` + `mdl_enrol` | `wp_stm_lms_user_courses` table |

### Grades (15,989 graded entries)
| Moodle | MasterStudy |
|--------|-------------|
| `mdl_grade_grades` (finalgrade) | `wp_stm_lms_user_quizzes` (progress, status) |
| `mdl_grade_items` (course final grade) | `wp_stm_lms_user_courses` (progress_percent, final_grade) |

### Assignments (587)
| Moodle | MasterStudy |
|--------|-------------|
| `mdl_assign` + `mdl_assign_submission` | `wp_posts` type `stm-assignments` + `wp_stm_lms_user_assignments` |

### Files (70,316 with content)
| Moodle | MasterStudy |
|--------|-------------|
| `mdl_files` → `moodle-datos/filedir/{hash}` | `wp-content/uploads/` (WordPress media library) |
| Key areas: `mod_resource/content` (1,950), `course/section` (1,266), `mod_folder/content` (1,569) | Attached to lessons/courses via WP attachment posts |

---

## Implementation Steps

### Phase 1: Migration Script — Categories
**File:** `wp-content/plugins/eies-migration/migrate-categories.php`

1. Read `mdl_course_categories` (25 categories, parent-child structure)
2. Create `stm_lms_course_taxonomy` terms preserving hierarchy
3. Store mapping: `moodle_cat_id → wp_term_id`

### Phase 2: Migration Script — Users
**File:** `wp-content/plugins/eies-migration/migrate-users.php`

1. Read `mdl_user WHERE deleted = 0` (6,558 users)
2. Create `wp_users` entries (username, email, first/last name, hashed password)
3. Set passwords to random (users will need to reset) — Moodle bcrypt hashes are compatible with `wp_set_password()`
4. Assign WP roles based on Moodle role assignments:
   - student → subscriber
   - editingteacher/teacher → author + stm_lms_instructor
   - manager → editor
5. Store mapping: `moodle_user_id → wp_user_id`

### Phase 3: Migration Script — Course Files
**File:** `wp-content/plugins/eies-migration/migrate-files.php`

1. Query `mdl_files` for course-related files (mod_resource, course/section, mod_folder, course/overviewfiles)
2. For each file:
   - Locate in `moodle-datos/filedir/{sha1[0:2]}/{sha1}/`
   - Copy to `wp-content/uploads/moodle-import/YYYY/MM/`
   - Create WP attachment post in media library
3. Store mapping: `moodle_file_id → wp_attachment_id`
4. On server: files are at `/home/marceloeies/public_html/moodle-datos/filedir/`

### Phase 4: Migration Script — Courses & Curriculum
**File:** `wp-content/plugins/eies-migration/migrate-courses.php`

1. Read `mdl_course WHERE id > 1` (221 courses)
2. For each course:
   - Create `stm-courses` post (title=fullname, content=summary)
   - Set post_meta: `price`=0 (free), `level`, `duration_info`
   - Assign `stm_lms_course_taxonomy` term from category mapping
   - Set `post_author` from editingteacher role assignment
3. Read `mdl_course_sections` for the course
4. For each section:
   - Create entry in `wp_stm_lms_curriculum_sections`
5. Read `mdl_course_modules` for each section
6. For each module (activity):
   - If `mod_resource` / `mod_page` / `mod_url` / `mod_label` → Create `stm-lessons` post
   - If `mod_quiz` → Create `stm-quizzes` post (next phase)
   - If `mod_assign` → Create `stm-assignments` post
   - Link to section via `wp_stm_lms_curriculum_materials`
7. Store mapping: `moodle_course_id → wp_course_id`

### Phase 5: Migration Script — Quizzes & Questions
**File:** `wp-content/plugins/eies-migration/migrate-quizzes.php`

1. Read `mdl_quiz` (276 quizzes)
2. For each quiz:
   - Create `stm-quizzes` post
   - Set meta: `duration`, `passing_grade`, `re_take_cut`
3. Read quiz questions via `mdl_quiz_slots` → `mdl_question`
4. For each question:
   - Create `stm-questions` post
   - Map question type:
     - `multichoice` → `single_choice` or `multi_choice` (check `single` flag in options)
     - `truefalse` → `single_choice` with 2 options
     - `essay` → `keywords`
     - `match` → `item_match`
     - `gapselect` → `fill_the_gap`
     - `shortanswer` → `keywords`
   - Parse `mdl_question_answers` → serialize as MasterStudy `answers` meta
5. Link questions to quizzes

### Phase 6: Migration Script — Enrollments & Progress
**File:** `wp-content/plugins/eies-migration/migrate-enrollments.php`

1. Read `mdl_user_enrolments` joined with `mdl_enrol` (4,761 enrollments)
2. For each enrollment:
   - Insert into `wp_stm_lms_user_courses` with mapped user_id and course_id
   - Set `start_time`, `status`, `progress_percent`
3. Read `mdl_grade_grades` for course completion grades
4. Update `final_grade` and `progress_percent` in user_courses

### Phase 7: Migration Script — Assignment Submissions
**File:** `wp-content/plugins/eies-migration/migrate-assignments.php`

1. Read `mdl_assign_submission` with files
2. Create entries in `wp_stm_lms_user_assignments`
3. Copy submission files from moodledata

---

## File Structure

```
wp-content/plugins/eies-migration/
├── eies-migration.php          # Plugin bootstrap, admin menu
├── includes/
│   ├── class-migration-base.php    # DB connections, mapping storage, logging
│   ├── class-migrate-categories.php
│   ├── class-migrate-users.php
│   ├── class-migrate-files.php
│   ├── class-migrate-courses.php
│   ├── class-migrate-quizzes.php
│   ├── class-migrate-enrollments.php
│   └── class-migrate-assignments.php
└── admin/
    └── migration-page.php      # WP Admin page with step-by-step buttons
```

The plugin will:
- Connect to both databases (WordPress `eies_fresh` and Moodle `moodle_eies`)
- Store ID mappings in a temporary `wp_eies_migration_map` table
- Provide an admin page with buttons to run each phase
- Log progress and errors
- Be deletable after migration is complete

---

## Server Details

### Moodle Source
- **URL:** https://virtual.eies.com.bo
- **Path:** /home/marceloeies/public_html/cursos
- **Moodledata:** /home/marceloeies/public_html/moodle-datos (52 GB)
- **Database:** marceloeies_moodle (10 GB)
- **DB User:** marceloeies_soporte
- **Version:** Moodle 3.7.1
- **cPanel user:** marceloeies
- **Server IP:** 167.86.108.223

### WordPress Target
- **Demo:** testeoprevio.eies.com.bo → /public_html/pretesting
- **Production:** eies.com.bo
- **Local:** eies.test (DB: eies_fresh)
- **Theme:** MasterStudy v4.8.139
- **LMS Pro:** v4.8.11
- **WordPress:** 6.9.4

---

## Deployment Plan

1. **Local development** — Build and test migration script on `eies.test`
2. **Set up demo** — Install WordPress on `testeoprevio.eies.com.bo` (`/public_html/pretesting`)
3. **Run migration on server** — Script reads from `marceloeies_moodle` DB and `/public_html/moodle-datos/filedir/` directly
4. **Test on demo** — Verify all courses, users, files, quizzes work
5. **Move to production** — Switch to main domain `eies.com.bo`
6. **Delete Moodle** — Remove `/public_html/cursos`, `/public_html/moodle-datos`, and `marceloeies_moodle` DB

---

## Verification

1. **Category count:** 25 Moodle categories → 25 MasterStudy taxonomy terms
2. **Course count:** 221 courses with correct titles, descriptions, images
3. **User count:** 6,558 users with correct roles
4. **Enrollment count:** 4,761 enrollments visible in user dashboards
5. **Quiz count:** 276 quizzes with questions rendering correctly
6. **File integrity:** Course images, PDFs, documents accessible
7. **Browse test:** Open 10 random courses, verify curriculum structure, lessons load, quizzes work
8. **Student login test:** Log in as a student, verify enrolled courses and progress show correctly
