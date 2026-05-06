import express from "express";
import { createServer as createViteServer } from "vite";
import Database from "better-sqlite3";
import path from "path";

const db = new Database("quiz.db");

// Initialize SQLite database (mimicking MySQL)
db.exec(`
  CREATE TABLE IF NOT EXISTS students (
    student_id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    reg_number TEXT NOT NULL UNIQUE
  );

  CREATE TABLE IF NOT EXISTS questions (
    question_id INTEGER PRIMARY KEY AUTOINCREMENT,
    question TEXT NOT NULL,
    option1 TEXT NOT NULL,
    option2 TEXT NOT NULL,
    option3 TEXT NOT NULL,
    option4 TEXT NOT NULL,
    correct_answer INTEGER NOT NULL
  );

  CREATE TABLE IF NOT EXISTS results (
    result_id INTEGER PRIMARY KEY AUTOINCREMENT,
    student_id INTEGER NOT NULL,
    score INTEGER NOT NULL,
    total_questions INTEGER NOT NULL,
    date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id)
  );
`);

// Seed initial questions if empty
const questionCount = db.prepare("SELECT COUNT(*) as count FROM questions").get() as { count: number };
if (questionCount.count === 0) {
  const insert = db.prepare("INSERT INTO questions (question, option1, option2, option3, option4, correct_answer) VALUES (?, ?, ?, ?, ?, ?)");
  insert.run('What does PHP stand for?', 'Personal Home Page', 'Hypertext Preprocessor', 'Pretext Hypertext Processor', 'Private Home Page', 2);
  insert.run('Which symbol is used to access a property of an object in PHP?', '.', '->', ':', '=>', 2);
  insert.run('Which of the following is used to start a session in PHP?', 'session_start()', 'start_session()', 'begin_session()', 'session_begin()', 1);
  insert.run('Which superglobal variable holds information about headers, paths, and script locations?', '$_GET', '$_POST', '$_SESSION', '$_SERVER', 4);
  insert.run('How do you write "Hello World" in PHP?', 'echo "Hello World";', 'Document.Write("Hello World");', '"Hello World";', 'print_f("Hello World");', 1);
}

async function startServer() {
  const app = express();
  app.use(express.json());

  // API Routes for the Preview
  app.post("/api/register", (req, res) => {
    const { name, email, reg_number, password } = req.body;
    try {
      const stmt = db.prepare("INSERT INTO students (name, email, reg_number, password) VALUES (?, ?, ?, ?)");
      const result = stmt.run(name, email, reg_number, password);
      res.json({ success: true, id: result.lastInsertRowid });
    } catch (e: any) {
      res.status(400).json({ error: e.message });
    }
  });

  app.post("/api/login", (req, res) => {
    const { email, password } = req.body;
    const user = db.prepare("SELECT * FROM students WHERE email = ?").get(email) as any;
    if (user && user.password === password) {
      res.json({ success: true, user });
    } else {
      res.status(401).json({ error: "Invalid credentials" });
    }
  });

  app.get("/api/questions", (req, res) => {
    const questions = db.prepare("SELECT * FROM questions ORDER BY RANDOM() LIMIT 5").all();
    res.json(questions);
  });

  app.post("/api/submit", (req, res) => {
    const { student_id, answers } = req.body;
    let score = 0;
    const total = answers.length;

    answers.forEach((ans: any) => {
      const q = db.prepare("SELECT correct_answer FROM questions WHERE question_id = ?").get(ans.question_id) as any;
      if (q && q.correct_answer === ans.answer) {
        score++;
      }
    });

    const stmt = db.prepare("INSERT INTO results (student_id, score, total_questions) VALUES (?, ?, ?)");
    const result = stmt.run(student_id, score, total);
    res.json({ success: true, result_id: result.lastInsertRowid, score, total });
  });

  app.get("/api/results/:student_id", (req, res) => {
    const results = db.prepare("SELECT * FROM results WHERE student_id = ? ORDER BY date DESC").all(req.params.student_id);
    res.json(results);
  });

  app.get("/api/certificate/:result_id", (req, res) => {
    const data = db.prepare(`
      SELECT r.*, s.name as student_name 
      FROM results r 
      JOIN students s ON r.student_id = s.student_id 
      WHERE r.result_id = ?
    `).get(req.params.result_id) as any;
    res.json(data);
  });

  // Vite middleware for development
  if (process.env.NODE_ENV !== "production") {
    const vite = await createViteServer({
      server: { middlewareMode: true },
      appType: "spa",
    });
    app.use(vite.middlewares);
  } else {
    app.use(express.static(path.join(__dirname, "dist")));
  }

  app.listen(3000, "0.0.0.0", () => {
    console.log("Server running on http://localhost:3000");
  });
}

startServer();
