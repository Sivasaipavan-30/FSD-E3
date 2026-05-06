import React, { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'motion/react';
import { 
  Trophy, 
  BookOpen, 
  User, 
  LogOut, 
  CheckCircle, 
  XCircle, 
  Clock, 
  Download, 
  PlusCircle, 
  Settings,
  ChevronRight,
  ShieldCheck
} from 'lucide-react';

// --- Types ---
interface Question {
  question_id: number;
  question: string;
  option1: string;
  option2: string;
  option3: string;
  option4: string;
  correct_answer: number;
}

interface User {
  student_id: number;
  name: string;
  email: string;
  reg_number: string;
}

interface Result {
  result_id: number;
  score: number;
  total_questions: number;
  date: string;
}

// --- Components ---

const Navbar = ({ user, onLogout, onNavigate }: { user: User | null, onLogout: () => void, onNavigate: (page: string) => void }) => (
  <nav className="bg-blue-600 text-white px-6 py-4 flex justify-between items-center shadow-lg">
    <div className="text-xl font-bold flex items-center gap-2 cursor-pointer" onClick={() => onNavigate('home')}>
      <BookOpen size={24} />
      <span>Quiz Engine</span>
    </div>
    <div className="flex gap-6 items-center">
      <button onClick={() => onNavigate('home')} className="hover:text-blue-200 transition-colors">Home</button>
      {user ? (
        <>
          <button onClick={() => onNavigate('dashboard')} className="hover:text-blue-200 transition-colors">Dashboard</button>
          <button onClick={onLogout} className="flex items-center gap-1 hover:text-blue-200 transition-colors">
            <LogOut size={18} /> Logout
          </button>
        </>
      ) : (
        <>
          <button onClick={() => onNavigate('login')} className="hover:text-blue-200 transition-colors">Login</button>
          <button onClick={() => onNavigate('register')} className="bg-white text-blue-600 px-4 py-1.5 rounded-full font-semibold hover:bg-blue-50 transition-colors">Register</button>
          <button onClick={() => onNavigate('admin')} className="text-blue-200 hover:text-white transition-colors text-sm">Admin</button>
        </>
      )}
    </div>
  </nav>
);

export default function App() {
  const [currentPage, setCurrentPage] = useState('home');
  const [user, setUser] = useState<User | null>(null);
  const [questions, setQuestions] = useState<Question[]>([]);
  const [currentQuizAnswers, setCurrentQuizAnswers] = useState<any[]>([]);
  const [quizResult, setQuizResult] = useState<any>(null);
  const [results, setResults] = useState<Result[]>([]);
  const [certificateData, setCertificateData] = useState<any>(null);
  const [quizTimeLeft, setQuizTimeLeft] = useState(600); // 10 minutes in seconds

  // Live countdown timer for the quiz page
  useEffect(() => {
    if (currentPage !== 'quiz') return;
    if (quizTimeLeft <= 0) {
      submitQuiz();
      return;
    }
    const interval = setInterval(() => {
      setQuizTimeLeft(t => t - 1);
    }, 1000);
    return () => clearInterval(interval);
  }, [currentPage, quizTimeLeft]);

  // --- Actions ---

  const handleLogin = async (e: React.FormEvent) => {
    e.preventDefault();
    const formData = new FormData(e.currentTarget as HTMLFormElement);
    const email = formData.get('email');
    const password = formData.get('password');

    const res = await fetch('/api/login', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email, password })
    });
    const data = await res.json();
    if (data.success) {
      setUser(data.user);
      setCurrentPage('dashboard');
      fetchResults(data.user.student_id);
    } else {
      alert(data.error);
    }
  };

  const handleRegister = async (e: React.FormEvent) => {
    e.preventDefault();
    const formData = new FormData(e.currentTarget as HTMLFormElement);
    const name = formData.get('name');
    const email = formData.get('email');
    const reg_number = formData.get('reg_number');
    const password = formData.get('password');

    const res = await fetch('/api/register', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ name, email, reg_number, password })
    });
    const data = await res.json();
    if (data.success) {
      alert("Registration successful! Please login.");
      setCurrentPage('login');
    } else {
      alert(data.error);
    }
  };

  const startQuiz = async () => {
    const res = await fetch('/api/questions');
    const data = await res.json();
    setQuestions(data);
    setCurrentQuizAnswers([]);
    setQuizTimeLeft(600); // reset timer on every new quiz
    setCurrentPage('quiz');
  };

  const submitQuiz = async () => {
    const res = await fetch('/api/submit', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ student_id: user?.student_id, answers: currentQuizAnswers })
    });
    const data = await res.json();
    setQuizResult(data);
    setCurrentPage('result');
    if (user) fetchResults(user.student_id);
  };

  const fetchResults = async (studentId: number) => {
    const res = await fetch(`/api/results/${studentId}`);
    const data = await res.json();
    setResults(data);
  };

  const viewCertificate = async (resultId: number) => {
    const res = await fetch(`/api/certificate/${resultId}`);
    const data = await res.json();
    setCertificateData(data);
    setCurrentPage('certificate');
  };

  const handleLogout = () => {
    setUser(null);
    setCurrentPage('home');
  };

  return (
    <div className="min-h-screen bg-slate-50 font-sans text-slate-900">
      <Navbar user={user} onLogout={handleLogout} onNavigate={setCurrentPage} />

      <main className="container mx-auto px-4 py-8 max-w-4xl">
        <AnimatePresence mode="wait">
          {/* --- Home Page --- */}
          {currentPage === 'home' && (
            <motion.div 
              key="home"
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              exit={{ opacity: 0, y: -20 }}
              className="text-center py-16"
            >
              <div className="bg-blue-600/10 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                <Trophy className="text-blue-600" size={40} />
              </div>
              <h1 className="text-5xl font-extrabold text-slate-900 mb-6 tracking-tight">Automated Quiz Engine</h1>
              <p className="text-xl text-slate-600 mb-10 max-w-2xl mx-auto leading-relaxed">
                Empower your learning journey. Take professional quizzes, track your progress, and earn verified certificates of achievement.
              </p>
              <div className="flex gap-4 justify-center">
                {user ? (
                  <button onClick={() => setCurrentPage('dashboard')} className="bg-blue-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-200">Go to Dashboard</button>
                ) : (
                  <>
                    <button onClick={() => setCurrentPage('register')} className="bg-blue-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-200">Get Started</button>
                    <button onClick={() => setCurrentPage('login')} className="bg-white border-2 border-blue-600 text-blue-600 px-8 py-3 rounded-xl font-bold hover:bg-blue-50 transition-all">Login</button>
                  </>
                )}
              </div>

              <div className="grid md:grid-cols-3 gap-8 mt-24">
                {[
                  { icon: <ShieldCheck className="text-emerald-500" />, title: "Secure Testing", desc: "Anti-cheat measures and timed sessions for integrity." },
                  { icon: <Download className="text-blue-500" />, title: "Instant PDF", desc: "Automated certificate generation upon passing." },
                  { icon: <Settings className="text-purple-500" />, title: "Admin Panel", desc: "Easy question management and result tracking." }
                ].map((item, i) => (
                  <div key={i} className="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 text-left">
                    <div className="mb-4">{item.icon}</div>
                    <h3 className="font-bold text-lg mb-2">{item.title}</h3>
                    <p className="text-slate-500 text-sm">{item.desc}</p>
                  </div>
                ))}
              </div>
            </motion.div>
          )}

          {/* --- Login Page --- */}
          {currentPage === 'login' && (
            <motion.div 
              key="login"
              initial={{ opacity: 0, scale: 0.95 }}
              animate={{ opacity: 1, scale: 1 }}
              className="max-w-md mx-auto bg-white p-8 rounded-2xl shadow-xl border border-slate-100"
            >
              <h2 className="text-2xl font-bold mb-6 text-center">Student Login</h2>
              <form onSubmit={handleLogin} className="space-y-4">
                <div>
                  <label className="block text-sm font-semibold mb-1">Email Address</label>
                  <input name="email" type="email" required className="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none" />
                </div>
                <div>
                  <label className="block text-sm font-semibold mb-1">Password</label>
                  <input name="password" type="password" required className="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none" />
                </div>
                <button type="submit" className="w-full bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700 transition-colors">Login</button>
              </form>
              <p className="mt-6 text-center text-slate-500 text-sm">
                Don't have an account? <button onClick={() => setCurrentPage('register')} className="text-blue-600 font-bold">Register</button>
              </p>
            </motion.div>
          )}

          {/* --- Register Page --- */}
          {currentPage === 'register' && (
            <motion.div 
              key="register"
              initial={{ opacity: 0, scale: 0.95 }}
              animate={{ opacity: 1, scale: 1 }}
              className="max-w-md mx-auto bg-white p-8 rounded-2xl shadow-xl border border-slate-100"
            >
              <h2 className="text-2xl font-bold mb-6 text-center">Create Account</h2>
              <form onSubmit={handleRegister} className="space-y-4">
                <div>
                  <label className="block text-sm font-semibold mb-1">Full Name</label>
                  <input name="name" type="text" required className="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none" />
                </div>
                <div>
                  <label className="block text-sm font-semibold mb-1">Email Address</label>
                  <input name="email" type="email" required className="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none" />
                </div>
                <div>
                  <label className="block text-sm font-semibold mb-1">Register Number</label>
                  <input name="reg_number" type="text" required className="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none" />
                </div>
                <div>
                  <label className="block text-sm font-semibold mb-1">Password</label>
                  <input name="password" type="password" required className="w-full px-4 py-2 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 outline-none" />
                </div>
                <button type="submit" className="w-full bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700 transition-colors">Register</button>
              </form>
              <p className="mt-6 text-center text-slate-500 text-sm">
                Already have an account? <button onClick={() => setCurrentPage('login')} className="text-blue-600 font-bold">Login</button>
              </p>
            </motion.div>
          )}

          {/* --- Dashboard --- */}
          {currentPage === 'dashboard' && (
            <motion.div 
              key="dashboard"
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              className="space-y-8"
            >
              <div className="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 flex justify-between items-center">
                <div>
                  <h2 className="text-3xl font-bold text-slate-900">Hello, {user?.name}!</h2>
                  <p className="text-slate-500">Ready to test your skills today?</p>
                </div>
                <button onClick={startQuiz} className="bg-blue-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-blue-700 transition-all flex items-center gap-2">
                  <PlusCircle size={20} /> Start New Quiz
                </button>
              </div>

              <div className="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div className="px-8 py-4 bg-slate-50 border-b border-slate-100">
                  <h3 className="font-bold">Your Quiz History</h3>
                </div>
                <div className="p-0">
                  {results.length > 0 ? (
                    <table className="w-full text-left">
                      <thead>
                        <tr className="text-slate-400 text-xs uppercase tracking-wider">
                          <th className="px-8 py-4">Date</th>
                          <th className="px-8 py-4">Score</th>
                          <th className="px-8 py-4">Status</th>
                          <th className="px-8 py-4">Action</th>
                        </tr>
                      </thead>
                      <tbody className="divide-y divide-slate-100">
                        {results.map((res) => {
                          const percentage = (res.score / res.total_questions) * 100;
                          const passed = percentage >= 60;
                          return (
                            <tr key={res.result_id} className="hover:bg-slate-50 transition-colors">
                              <td className="px-8 py-4 text-sm">{new Date(res.date).toLocaleDateString()}</td>
                              <td className="px-8 py-4 font-mono font-bold">{res.score}/{res.total_questions} ({percentage.toFixed(0)}%)</td>
                              <td className="px-8 py-4">
                                <span className={`px-3 py-1 rounded-full text-xs font-bold ${passed ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'}`}>
                                  {passed ? 'PASSED' : 'FAILED'}
                                </span>
                              </td>
                              <td className="px-8 py-4">
                                {passed && (
                                  <button onClick={() => viewCertificate(res.result_id)} className="text-blue-600 hover:text-blue-800 font-bold text-sm flex items-center gap-1">
                                    <Download size={14} /> Certificate
                                  </button>
                                )}
                              </td>
                            </tr>
                          );
                        })}
                      </tbody>
                    </table>
                  ) : (
                    <div className="p-12 text-center text-slate-400">
                      <p>No quiz history found. Take your first quiz to see results here!</p>
                    </div>
                  )}
                </div>
              </div>
            </motion.div>
          )}

          {/* --- Quiz Page --- */}
          {currentPage === 'quiz' && (
            <motion.div 
              key="quiz"
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              className="bg-white p-8 rounded-2xl shadow-xl border border-slate-100"
            >
              <div className="flex justify-between items-center mb-8 pb-4 border-b border-slate-100">
                <h2 className="text-2xl font-bold">Online Assessment</h2>
                <div className="flex items-center gap-2 text-rose-600 font-bold bg-rose-50 px-4 py-2 rounded-lg">
                  <Clock size={20} />
                  <span className={quizTimeLeft <= 60 ? 'text-red-600' : ''}>
                    {String(Math.floor(quizTimeLeft / 60)).padStart(2, '0')}:{String(quizTimeLeft % 60).padStart(2, '0')}
                  </span>
                </div>
              </div>

              <div className="space-y-10">
                {questions.map((q, idx) => (
                  <div key={q.question_id} className="space-y-4">
                    <p className="text-lg font-bold text-slate-800">{idx + 1}. {q.question}</p>
                    <div className="grid gap-3">
                      {[q.option1, q.option2, q.option3, q.option4].map((opt, i) => (
                        <label key={i} className="flex items-center gap-3 p-4 border-2 border-slate-100 rounded-xl cursor-pointer hover:border-blue-200 hover:bg-blue-50 transition-all">
                          <input 
                            type="radio" 
                            name={`q-${q.question_id}`} 
                            className="w-5 h-5 text-blue-600"
                            onChange={() => {
                              const newAnswers = [...currentQuizAnswers];
                              const existing = newAnswers.findIndex(a => a.question_id === q.question_id);
                              if (existing !== -1) newAnswers[existing].answer = i + 1;
                              else newAnswers.push({ question_id: q.question_id, answer: i + 1 });
                              setCurrentQuizAnswers(newAnswers);
                            }}
                          />
                          <span className="font-medium">{opt}</span>
                        </label>
                      ))}
                    </div>
                  </div>
                ))}
              </div>

              <button 
                onClick={submitQuiz}
                disabled={currentQuizAnswers.length < questions.length}
                className="w-full mt-12 bg-blue-600 text-white py-4 rounded-xl font-bold hover:bg-blue-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-blue-200"
              >
                Submit Assessment
              </button>
            </motion.div>
          )}

          {/* --- Result Page --- */}
          {currentPage === 'result' && quizResult && (
            <motion.div 
              key="result"
              initial={{ opacity: 0, scale: 0.9 }}
              animate={{ opacity: 1, scale: 1 }}
              className="max-w-xl mx-auto bg-white p-12 rounded-3xl shadow-2xl border border-slate-100 text-center"
            >
              <div className={`w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6 ${quizResult.score / quizResult.total >= 0.6 ? 'bg-emerald-100 text-emerald-600' : 'bg-rose-100 text-rose-600'}`}>
                {quizResult.score / quizResult.total >= 0.6 ? <CheckCircle size={48} /> : <XCircle size={48} />}
              </div>
              <h2 className="text-3xl font-bold mb-2">Quiz Completed!</h2>
              <div className="text-6xl font-black text-slate-900 mb-6">
                {quizResult.score}<span className="text-slate-300">/</span>{quizResult.total}
              </div>
              <p className="text-lg text-slate-500 mb-10">
                {quizResult.score / quizResult.total >= 0.6 
                  ? "Outstanding! You've successfully passed the assessment and earned your certificate." 
                  : "Don't give up! You need at least 60% to pass. Review the material and try again."}
              </p>
              <div className="flex flex-col gap-3">
                {quizResult.score / quizResult.total >= 0.6 && (
                  <button onClick={() => viewCertificate(quizResult.result_id)} className="bg-emerald-600 text-white py-3 rounded-xl font-bold hover:bg-emerald-700 transition-all flex items-center justify-center gap-2">
                    <Download size={20} /> Download Certificate
                  </button>
                )}
                <button onClick={() => setCurrentPage('dashboard')} className="bg-slate-900 text-white py-3 rounded-xl font-bold hover:bg-slate-800 transition-all">Back to Dashboard</button>
              </div>
            </motion.div>
          )}

          {/* --- Certificate Page --- */}
          {currentPage === 'certificate' && certificateData && (
            <motion.div 
              key="certificate"
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              className="flex flex-col items-center"
            >
              <div className="bg-white p-16 border-[20px] border-blue-600 shadow-2xl max-w-4xl w-full text-center relative overflow-hidden">
                <div className="absolute inset-4 border-2 border-blue-600 pointer-events-none opacity-20"></div>
                <h1 className="font-serif text-6xl text-blue-600 mb-8">Certificate of Achievement</h1>
                <p className="text-xl text-slate-500 italic mb-4">This is to certify that</p>
                <div className="text-5xl font-bold text-slate-900 mb-8 border-b-2 border-slate-200 inline-block px-12 pb-2">
                  {certificateData.student_name}
                </div>
                <p className="text-xl text-slate-500 italic mb-4">has successfully passed the Online Quiz with a score of</p>
                <div className="text-3xl font-bold text-emerald-600 mb-12">
                  {((certificateData.score / certificateData.total_questions) * 100).toFixed(1)}%
                </div>
                <div className="flex justify-between items-end mt-16 text-sm text-slate-400">
                  <div className="text-left">
                    <p>Date: {new Date(certificateData.date).toLocaleDateString()}</p>
                    <p>ID: CERT-{String(certificateData.result_id).padStart(6, '0')}</p>
                  </div>
                  <div className="text-right">
                    <div className="w-32 h-1 bg-slate-200 mb-2"></div>
                    <p className="font-bold text-slate-600">Authorized Signature</p>
                  </div>
                </div>
              </div>
              <button 
                onClick={() => window.print()} 
                className="mt-8 bg-blue-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-700 transition-all flex items-center gap-2 no-print"
              >
                <Download size={20} /> Print Certificate
              </button>
              <button onClick={() => setCurrentPage('dashboard')} className="mt-4 text-slate-500 hover:text-slate-800 font-bold no-print">Back to Dashboard</button>
            </motion.div>
          )}

          {/* --- Admin Panel (Simple Preview) --- */}
          {currentPage === 'admin' && (
            <motion.div 
              key="admin"
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              className="max-w-md mx-auto bg-white p-8 rounded-2xl shadow-xl border border-slate-100 text-center"
            >
              <Settings className="mx-auto text-slate-400 mb-4" size={48} />
              <h2 className="text-2xl font-bold mb-4">Admin Access</h2>
              <p className="text-slate-500 mb-8">The admin panel allows you to manage questions and view student results. Use the PHP version in XAMPP for full functionality.</p>
              <button onClick={() => setCurrentPage('home')} className="w-full bg-slate-900 text-white py-3 rounded-xl font-bold hover:bg-slate-800 transition-all">Back to Home</button>
            </motion.div>
          )}
        </AnimatePresence>
      </main>

      <footer className="mt-24 py-12 border-t border-slate-100 text-center text-slate-400 text-sm">
        <p>&copy; 2024 Automated Quiz Engine Project. Designed for Student Portfolios.</p>
        <div className="mt-4 flex justify-center gap-4">
          <span className="px-2 py-1 bg-slate-100 rounded text-[10px] font-bold">HTML5</span>
          <span className="px-2 py-1 bg-slate-100 rounded text-[10px] font-bold">CSS3</span>
          <span className="px-2 py-1 bg-slate-100 rounded text-[10px] font-bold">PHP</span>
          <span className="px-2 py-1 bg-slate-100 rounded text-[10px] font-bold">MYSQL</span>
        </div>
      </footer>

      <style>{`
        @media print {
          .no-print { display: none !important; }
          body { background: white !important; }
          nav { display: none !important; }
          footer { display: none !important; }
          main { padding: 0 !important; margin: 0 !important; max-width: none !important; }
        }
      `}</style>
    </div>
  );
}
