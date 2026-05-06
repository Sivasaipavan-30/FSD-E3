-- Database: quiz_db
CREATE DATABASE IF NOT EXISTS quiz_db;
USE quiz_db;

-- Table: students
CREATE TABLE IF NOT EXISTS students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    reg_number VARCHAR(50) NOT NULL UNIQUE
);

-- Table: admin
CREATE TABLE IF NOT EXISTS admin (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Table: questions
CREATE TABLE IF NOT EXISTS questions (
    question_id INT AUTO_INCREMENT PRIMARY KEY,
    question TEXT NOT NULL,
    option1 VARCHAR(255) NOT NULL,
    option2 VARCHAR(255) NOT NULL,
    option3 VARCHAR(255) NOT NULL,
    option4 VARCHAR(255) NOT NULL,
    correct_answer INT NOT NULL, -- 1, 2, 3, or 4
    category VARCHAR(100) DEFAULT 'General Knowledge'
);

-- Table: results
CREATE TABLE IF NOT EXISTS results (
    result_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    score INT NOT NULL,
    total_questions INT NOT NULL,
    category VARCHAR(100) DEFAULT 'General Knowledge',
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id)
);

-- Default Admin (password: admin123, hashed with PASSWORD_BCRYPT)
-- To regenerate: php -r "echo password_hash('admin123', PASSWORD_BCRYPT);"
INSERT INTO admin (username, password)
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Sample Questions
INSERT INTO questions (question, option1, option2, option3, option4, correct_answer, category) VALUES
('What does PHP stand for?', 'Personal Home Page', 'Hypertext Preprocessor', 'Pretext Hypertext Processor', 'Private Home Page', 2, 'General Knowledge'),
('Which symbol is used to access a property of an object in PHP?', '.', '->', ':', '=>', 2, 'General Knowledge'),
('Which of the following is used to start a session in PHP?', 'session_start()', 'start_session()', 'begin_session()', 'session_begin()', 1, 'General Knowledge'),
('Which superglobal variable holds information about headers, paths, and script locations?', '$_GET', '$_POST', '$_SESSION', '$_SERVER', 4, 'General Knowledge'),
('How do you write "Hello World" in PHP?', 'echo "Hello World";', 'Document.Write("Hello World");', '"Hello World";', 'print_f("Hello World");', 1, 'General Knowledge');

-- Web Development Questions
INSERT INTO questions (question, option1, option2, option3, option4, correct_answer, category) VALUES
('What does HTML stand for?', 'Hyper Text Markup Language', 'High Tech Modern Language', 'Hyper Tool Multi Language', 'Hyper Text Main Link', 1, 'Web Development'),
('Which tag is used for the largest heading in HTML?', '<h6>', '<header>', '<h1>', '<div>', 3, 'Web Development'),
('What is the purpose of CSS?', 'To define website structure', 'To add interactive behavior', 'To style and layout web pages', 'To handle server requests', 3, 'Web Development'),
('Which JavaScript keyword is used to declare a constant?', 'var', 'let', 'const', 'def', 3, 'Web Development'),
('What does DOM stand for?', 'Data Object Model', 'Document Object Model', 'Design Object Mode', 'Digital Object Maker', 2, 'Web Development'),
('Which property is used to change the background color in CSS?', 'color', 'bgcolor', 'background-color', 'fill', 3, 'Web Development'),
('In JavaScript, how do you focus an element?', 'element.focus()', 'element.select()', 'element.active()', 'element.click()', 1, 'Web Development'),
('Which HTML attribute is used to define inline styles?', 'class', 'style', 'font', 'id', 2, 'Web Development'),
('What is the correct syntax for a JavaScript arrow function?', '() => {}', 'function() => {}', '() -> {}', '=> () {}', 1, 'Web Development'),
('Which HTML element is used for adding a line break?', '<br>', '<lb>', '<break>', '<hr>', 1, 'Web Development');

-- Python Programming Questions
INSERT INTO questions (question, option1, option2, option3, option4, correct_answer, category) VALUES
('Who developed Python?', 'Guido van Rossum', 'Dennis Ritchie', 'Bjarne Stroustrup', 'James Gosling', 1, 'Python Programming'),
('Which extension is used for Python files?', '.py', '.python', '.pyth', '.p', 1, 'Python Programming'),
('What is the correct way to create a list in Python?', '(1, 2, 3)', '{1, 2, 3}', '[1, 2, 3]', '<1, 2, 3>', 3, 'Python Programming'),
('How do you output "Hello World" in Python?', 'print("Hello World")', 'echo("Hello World")', 'printf("Hello World")', 'System.out.println("Hello World")', 1, 'Python Programming'),
('Which keyword is used to define a function in Python?', 'func', 'define', 'def', 'function', 3, 'Python Programming'),
('Which data type is used for a single true or false value?', 'float', 'bool', 'int', 'str', 2, 'Python Programming'),
('How do you insert comments in Python code?', '// comment', '/* comment */', '# comment', '-- comment', 3, 'Python Programming'),
('What is the result of 3 ** 2 in Python?', '6', '9', '5', '8', 2, 'Python Programming'),
('Which method is used to remove whitespace from both ends of a string?', 'strip()', 'clean()', 'trim()', 'remove()', 1, 'Python Programming'),
('How do you get the number of items in a list?', 'count(list)', 'size(list)', 'length(list)', 'len(list)', 4, 'Python Programming');

-- Database Management Questions
INSERT INTO questions (question, option1, option2, option3, option4, correct_answer, category) VALUES
('What does SQL stand for?', 'Structured Query Language', 'Simple Question Link', 'Strong Quick Logic', 'Storage Queue List', 1, 'Database Management'),
('Which SQL statement is used to extract data from a database?', 'EXTRACT', 'GET', 'SELECT', 'OPEN', 3, 'Database Management'),
('Which SQL keyword is used to delete records from a table?', 'REMOVE', 'DELETE', 'DROP', 'TRUNCATE', 2, 'Database Management'),
('What is a primary key?', 'A key used for indexing', 'A unique identifier for a record', 'A key used to encrypt data', 'A foreign connection', 2, 'Database Management'),
('What does ACID stand for in databases?', 'Atomicity, Consistency, Isolation, Durability', 'Active, Current, Input, Data', 'Access, Control, Internal, Drive', 'Area, Cluster, Index, Display', 1, 'Database Management'),
('Which SQL clause is used to sort the result-set?', 'SORT BY', 'ARRANGE BY', 'ORDER BY', 'GROUP BY', 3, 'Database Management'),
('What is a foreign key?', 'A key from another database', 'A field that refers to a primary key in another table', 'A temporary key', 'An invisible index', 2, 'Database Management'),
('Which join returns all records when there is a match in either left or right table?', 'INNER JOIN', 'LEFT JOIN', 'RIGHT JOIN', 'FULL JOIN', 4, 'Database Management'),
('To select unique values, which SQL keyword is used?', 'UNIQUE', 'DIFFERENT', 'DISTINCT', 'ONLY', 3, 'Database Management'),
('Which normal form deals with partial functional dependency?', '1NF', '2NF', '3NF', 'BCNF', 2, 'Database Management');

-- Computer Networks Questions
INSERT INTO questions (question, option1, option2, option3, option4, correct_answer, category) VALUES
('How many layers are in the OSI model?', '5', '6', '7', '8', 3, 'Computer Networks'),
('Which layer is responsible for routing?', 'Data Link Layer', 'Network Layer', 'Transport Layer', 'Physical Layer', 2, 'Computer Networks'),
('What does HTTP stand for?', 'HyperText Transfer Protocol', 'High Technology Transfer Part', 'Hyper Tool Transfer Page', 'Hyper Time Text Program', 1, 'Computer Networks'),
('Which protocol is considered unreliable?', 'TCP', 'UDP', 'HTTP', 'HTTPS', 2, 'Computer Networks'),
('What is the default port for HTTP?', '21', '25', '80', '443', 3, 'Computer Networks'),
('Which layer provides end-to-end communication services?', 'Transport Layer', 'Session Layer', 'Application Layer', 'Presentation Layer', 1, 'Computer Networks'),
('What does DNS stand for?', 'Data Name System', 'Domain Name System', 'Digital Network Service', 'Drive Node Slot', 2, 'Computer Networks'),
('Which device operates at the Physical layer?', 'Switch', 'Router', 'Hub', 'Bridge', 3, 'Computer Networks'),
('What is the length of an IPv4 address?', '16 bits', '32 bits', '64 bits', '128 bits', 2, 'Computer Networks'),
('Which protocol is used for sending emails?', 'FTP', 'SMTP', 'POP3', 'IMAP', 2, 'Computer Networks');

