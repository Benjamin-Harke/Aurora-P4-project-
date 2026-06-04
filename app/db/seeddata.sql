-- Insert default admin account
-- Email: admin@aurora.com
-- Password: Admin123!

INSERT IGNORE INTO Accounts (Email, Password, FirstName, LastName, IsActive) 
VALUES (
    'admin@aurora.com',
    '$2y$10$wfDGQ0Gmbo9X4OIQx.F88.0/HcrsmvvnjJ4uSAGhabUEp2sPBdAZC',
    'Admin',
    'Aurora',
    1
);
