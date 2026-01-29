-- Remove StaffCaseManagementReportPermission from database
-- This permission was created for a new report that was later merged into the existing staff-detail-report
-- The existing report uses 'StaffCaseReportPermission' instead

DELETE FROM `user_access_control` WHERE `code` = 'StaffCaseManagementReportPermission';
