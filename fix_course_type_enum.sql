-- Direct SQL fix for course_type ENUM column
-- Run this directly in MySQL if migrations don't work

-- First, update any invalid values
UPDATE courses SET course_type = 'Semester' WHERE course_type NOT IN ('Semester', 'Year') OR course_type IS NULL;

-- Modify the column to ensure correct ENUM values
ALTER TABLE courses MODIFY COLUMN course_type ENUM('Semester', 'Year') NOT NULL;


