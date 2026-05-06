<?php
include 'config/db.php';
session_start();

if (!isset($_SESSION['student_id']) || !isset($_GET['id'])) {
    header("Location: login.php");
    exit();
}

$result_id  = (int)$_GET['id'];
$student_id = $_SESSION['student_id'];

$sql    = "SELECT r.*, s.name as student_name, s.reg_number FROM results r
           JOIN students s ON r.student_id = s.student_id
           WHERE r.result_id = ? AND r.student_id = ?";
$stmt   = $conn->prepare($sql);
$stmt->bind_param("ii", $result_id, $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Result not found or unauthorized access.");
}

$data       = $result->fetch_assoc();
$percentage = ($data['score'] / $data['total_questions']) * 100;

if ($percentage < 60) {
    die("Certificate only available for passing scores (60% or above).");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate of Achievement – <?php echo htmlspecialchars($data['student_name']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Merriweather:ital,wght@0,400;0,700;1,400&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <!-- html2pdf.js library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: #e8e0d0;
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            padding: 40px 20px;
        }

        .btn-print, .btn-download {
            position: fixed;
            top: 20px;
            padding: 10px 24px;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 700;
            font-size: 0.85rem;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            box-shadow: 0 3px 12px rgba(0,0,0,.3);
            z-index: 1000;
        }
        .btn-print {
            right: 20px;
            background: #555;
        }
        .btn-download {
            right: 210px;
            background: #1a2e5a;
        }
        .btn-back {
            position: fixed;
            top: 20px;
            left: 20px;
            padding: 10px 20px;
            background: #555;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.82rem;
            font-weight: 600;
            text-transform: uppercase;
            text-decoration: none;
            display: inline-block;
        }

        /* Certificate */
        .certificate-wrapper {
            max-width: 860px;
            margin: 0 auto;
        }

        .certificate {
            background: #fffdf7;
            padding: 60px 70px;
            position: relative;
            box-shadow: 0 12px 40px rgba(0,0,0,.2);
        }

        /* Outer border */
        .certificate::before {
            content: "";
            position: absolute;
            inset: 14px;
            border: 2px solid #b8860b;
            pointer-events: none;
        }

        /* Corner element top */
        .certificate::after {
            content: "";
            position: absolute;
            inset: 8px;
            border: 6px solid #1a2e5a;
            pointer-events: none;
        }

        .cert-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .college-name {
            font-family: 'Cinzel', serif;
            font-size: 1rem;
            font-weight: 700;
            color: #1a2e5a;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .divider {
            width: 120px;
            height: 3px;
            background: linear-gradient(to right, #b8860b, #f5c842, #b8860b);
            margin: 12px auto;
            border-radius: 2px;
        }

        .cert-title {
            font-family: 'Cinzel', serif;
            font-size: 2.4rem;
            color: #1a2e5a;
            letter-spacing: 0.05em;
            margin: 8px 0 4px;
        }

        .cert-subtitle {
            font-family: 'Merriweather', serif;
            font-size: 0.9rem;
            color: #666;
            font-style: italic;
        }

        .cert-body {
            text-align: center;
            margin: 28px 0;
        }

        .presented-to {
            font-family: 'Merriweather', serif;
            font-size: 1rem;
            color: #555;
            font-style: italic;
            margin-bottom: 8px;
        }

        .student-name {
            font-family: 'Cinzel', serif;
            font-size: 2.2rem;
            color: #1a2e5a;
            margin: 4px 0 8px;
            border-bottom: 2px solid #b8860b;
            display: inline-block;
            padding: 0 40px 8px;
        }

        .reg-number {
            font-size: 0.85rem;
            color: #888;
            margin-bottom: 20px;
        }

        .cert-text {
            font-family: 'Merriweather', serif;
            font-size: 1rem;
            color: #444;
            line-height: 1.8;
            max-width: 580px;
            margin: 0 auto 12px;
        }

        .score-badge {
            display: inline-block;
            background: linear-gradient(135deg, #1a2e5a, #243d78);
            color: white;
            padding: 10px 32px;
            border-radius: 50px;
            font-size: 1.4rem;
            font-weight: 800;
            margin: 12px 0;
            letter-spacing: 0.03em;
        }

        .cert-footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 48px;
            padding-top: 24px;
            border-top: 1px solid #ddd;
        }

        .footer-left p, .footer-right p {
            font-size: 0.78rem;
            color: #888;
            margin-bottom: 2px;
        }

        .signature-line {
            width: 140px;
            height: 1.5px;
            background: #1a2e5a;
            margin-bottom: 6px;
        }

        .footer-right { text-align: right; }

        .cert-id {
            font-size: 0.7rem;
            color: #b8860b;
            font-weight: 700;
            letter-spacing: 0.08em;
        }

        @media print {
            body { background: white; padding: 0; }
            .btn-print, .btn-back, .btn-download { display: none; }
            .certificate { box-shadow: none; }
        }
    </style>
</head>
<body>
    <button class="btn-download" id="download-btn">📥 Download PDF</button>
    <button class="btn-print" onclick="window.print()">🖨 Print</button>
    <a class="btn-back" href="dashboard.php">← Dashboard</a>

    <div class="certificate-wrapper" id="certificate-content">
        <div class="certificate">
            <div class="cert-header">
                <div class="college-name">Official Certification Authority</div>
                <div class="college-name" style="font-size: 0.75rem; letter-spacing: 0.08em; color: #b8860b;">Global Academic Assessment</div>
                <div class="divider"></div>
                <div class="cert-title">Certificate of Achievement</div>
                <div class="cert-subtitle">Online Examination Portal</div>
            </div>

            <div class="cert-body">
                <p class="presented-to">This is to certify that</p>
                <div class="student-name"><?php echo htmlspecialchars($data['student_name']); ?></div>
                <p class="reg-number">Register No: <?php echo htmlspecialchars($data['reg_number']); ?></p>
                <p class="cert-text">
                    has successfully completed and passed the Online Examination conducted through the
                    <strong>Online Examination Portal</strong> with the following result:
                </p>
                <div class="score-badge">
                    <?php echo number_format($percentage, 1); ?>%
                </div>
                <p class="cert-text" style="margin-top: 12px; font-size: 0.9rem; color: #777;">
                    This certifies academic excellence and successful completion of the online assessment.
                </p>
            </div>

            <div class="cert-footer">
                <div class="footer-left">
                    <p>Date of Examination</p>
                    <p><strong><?php echo date('d F Y', strtotime($data['date'])); ?></strong></p>
                </div>

                <div class="footer-right">
                    <div class="signature-line" style="margin-left: auto;"></div>
                    <p><strong>Examination Controller</strong></p>
                    <p>Academic Affairs Division</p>
                    <p class="cert-id">CERT-<?php echo str_pad($data['result_id'], 6, '0', STR_PAD_LEFT); ?></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('download-btn').addEventListener('click', function () {
            const element = document.getElementById('certificate-content');
            const studentName = "<?php echo addslashes($data['student_name']); ?>";
            const opt = {
                margin:       0,
                filename:     `Certificate_${studentName.replace(/\s+/g, '_')}.pdf`,
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, useCORS: true },
                jsPDF:        { unit: 'in', format: 'letter', orientation: 'landscape' }
            };

            // Remove box-shadow before capturing to avoid artifacting in PDF
            const certElement = element.querySelector('.certificate');
            const originalShadow = certElement.style.boxShadow;
            certElement.style.boxShadow = 'none';

            html2pdf().set(opt).from(element).save().then(() => {
                certElement.style.boxShadow = originalShadow;
            });
        });
    </script>
</body>
</html>
