-- Sample data for testing
-- Run this after importing database.sql

-- Insert sample clients
INSERT INTO clients (name, client_code) VALUES
('First National Bank', 'FNB001'),
('Protea', 'PRO001'),
('IT Solutions', 'ITA001'),
('Absa Bank', 'ABS002'),
('Standard Bank', 'STD003');

-- Insert sample contacts
INSERT INTO contacts (name, surname, email) VALUES
('John', 'Doe', 'john.doe@email.com'),
('Jane', 'Smith', 'jane.smith@email.com'),
('Bob', 'Wilson', 'bob.wilson@email.com'),
('Alice', 'Brown', 'alice.brown@email.com'),
('Charlie', 'Davis', 'charlie.davis@email.com');

-- Insert sample links (assuming IDs from above)
INSERT INTO client_contact (client_id, contact_id) VALUES
(1, 1), -- FNB + John Doe
(1, 2), -- FNB + Jane Smith
(2, 2), -- Protea + Jane Smith
(2, 3), -- Protea + Bob Wilson
(3, 4), -- IT Solutions + Alice Brown
(4, 5); -- Absa + Charlie Davis
