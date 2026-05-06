<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Smart Campus Project Features</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --navy: #1e293b;
            --accent: #10b981;
        }
        body { 
            font-family: 'Outfit', sans-serif; 
            background: #f1f5f9; 
            margin: 0; 
            padding: 40px; 
            display: flex; 
            justify-content: center;
        }
        .report-a4 {
            width: 210mm;
            min-height: 297mm;
            background: white;
            padding: 20mm;
            box-sizing: border-box;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            position: relative;
        }
        .header {
            border-bottom: 3px solid var(--primary);
            padding-bottom: 20px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .title-group h1 {
            margin: 0;
            color: var(--navy);
            font-size: 2.2rem;
            text-transform: uppercase;
        }
        .title-group p {
            margin: 5px 0 0;
            color: var(--primary);
            font-weight: 600;
            letter-spacing: 2px;
        }
        .feature-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .feature-card {
            border: 1px solid #e2e8f0;
            padding: 15px;
            border-radius: 8px;
            background: #f8fafc;
        }
        .feature-card h3 {
            margin-top: 0;
            color: var(--navy);
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.1rem;
        }
        .feature-card p {
            font-size: 0.85rem;
            color: #475569;
            line-height: 1.5;
            margin-bottom: 0;
        }
        .icon {
            color: var(--primary);
            font-size: 1.3rem;
        }
        .section-title {
            font-size: 1.2rem;
            color: var(--navy);
            margin: 30px 0 15px;
            border-left: 5px solid var(--accent);
            padding-left: 10px;
            text-transform: uppercase;
            font-weight: 800;
        }
        .tech-stack {
            display: flex;
            gap: 10px;
            margin-top: 40px;
            flex-wrap: wrap;
        }
        .tech-badge {
            background: var(--navy);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        @media print {
            body { background: white; padding: 0; }
            .report-a4 { box-shadow: none; width: 100%; padding: 15mm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="position: fixed; top: 20px; right: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: var(--primary); color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: 700;">Print Project Report</button>
    </div>

    <div class="report-a4">
        <div class="header">
            <div class="title-group">
                <h1>Smart Campus</h1>
                <p>Event Management System v2.0</p>
            </div>
            <div style="text-align: right; font-size: 0.8rem; color: #64748b;">
                Generated: <?php echo date('d M, Y'); ?><br>
                Status: <b>Production Ready</b>
            </div>
        </div>

        <div class="section-title">1. Core Modules & Features</div>
        <div class="feature-grid">
            <div class="feature-card">
                <h3><span>🛡️</span> Admin Command Center</h3>
                <p>Full event lifecycle management, real-time analytics dashboard, and automated scheduling for campus-wide coordination.</p>
            </div>
            <div class="feature-card">
                <h3><span>🎓</span> Student Portal</h3>
                <p>Personalized dashboard featuring a digital campus pass, event discovery, and a secure certificate achievement vault.</p>
            </div>
            <div class="feature-card">
                <h3><span>📸</span> QR Attendance System</h3>
                <p>High-speed QR scanner for real-time check-ins. Prevents duplicate entries and automatically updates participation status.</p>
            </div>
            <div class="feature-card">
                <h3><span>📜</span> Certificate Engine</h3>
                <p>Automated generation of university-grade diplomas with unique verification IDs and security watermarks.</p>
            </div>
            <div class="feature-card">
                <h3><span>✉️</span> Broadcast System</h3>
                <p>Mass-email functionality using SMTP to send instant updates, confirmation receipts, and event reminders to students.</p>
            </div>
            <div class="feature-card">
                <h3><span>📊</span> Analytics & Reporting</h3>
                <p>Revenue tracking, department-wise engagement charts, and trend analysis using Chart.js visualization.</p>
            </div>
        </div>

        <div class="section-title">2. Advanced User Features</div>
        <ul style="font-size: 0.9rem; line-height: 2; color: #334155;">
            <li><b>Digital Campus Pass:</b> Unique QR-based ID for every student to enable instant identification.</li>
            <li><b>Multi-Attendee Booking:</b> Support for group registrations with individual ticket generation.</li>
            <li><b>Real-time Seat Tracking:</b> Dynamic inventory management to prevent overbooking.</li>
            <li><b>Security:</b> Session-based authentication and role-based access control (Admin/Student/Faculty).</li>
            <li><b>Interactive UI:</b> Modern glassmorphism design with responsive layouts for mobile and desktop.</li>
        </ul>

        <div class="section-title">3. Technical Architecture</div>
        <p style="font-size: 0.9rem; color: #475569; line-height: 1.6;">
            The system is built on a robust PHP/MySQL backend with a lightweight, high-performance Javascript frontend. It utilizes a modular API-first approach, allowing for seamless integration of future modules like Payment Gateways or SMS alerts.
        </p>

        <div class="tech-stack">
            <div class="tech-badge">PHP 8.x</div>
            <div class="tech-badge">MySQL / PDO</div>
            <div class="tech-badge">Vanilla JS (ES6+)</div>
            <div class="tech-badge">Chart.js</div>
            <div class="tech-badge">SMTP Mail</div>
            <div class="tech-badge">QR API</div>
            <div class="tech-badge">HTML5 Qrcode</div>
        </div>

        <div style="position: absolute; bottom: 20mm; left: 20mm; right: 20mm; border-top: 1px solid #e2e8f0; padding-top: 15px; display: flex; justify-content: space-between; font-size: 0.75rem; color: #94a3b8;">
            <span>Project Source: Campus_event_v2_final</span>
            <span>Verified System Report</span>
        </div>
    </div>
</body>
</html>
