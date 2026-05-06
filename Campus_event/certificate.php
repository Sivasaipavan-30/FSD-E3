<?php
require_once 'api/db.php';

$attendeeId = $_GET['id'] ?? 0;

if (!$attendeeId) die("Invalid ID");

try {
    $stmt = $pdo->prepare("
        SELECT ba.*, e.name as event_name, e.datetime, u.name as buyer_name
        FROM booking_attendees ba
        JOIN bookings b ON ba.booking_id = b.id
        JOIN events e ON b.event_id = e.id
        JOIN users u ON b.user_id = u.id
        WHERE ba.id = ?
    ");
    $stmt->execute([$attendeeId]);
    $student = $stmt->fetch();

    if (!$student) die("Certificate not found");
    if (!$student['is_checked_in']) die("Certificate is only available for attendees who checked in at the event.");

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>College Certificate - <?php echo htmlspecialchars($student['name']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700;900&family=Outfit:wght@300;400;600&family=Sacramento&display=swap" rel="stylesheet">
    <style>
        :root {
            --gold: #b8860b;
            --navy: #1a365d;
            --cream: #fffdf5;
        }
        body { 
            font-family: 'Outfit', sans-serif; 
            background: #cbd5e1; 
            margin: 0; 
            padding: 40px 20px; 
            display: flex; 
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }
        .cert-wrapper {
            transform-origin: top center;
            transition: transform 0.3s ease;
        }
        @media screen and (max-width: 1200px) { .cert-wrapper { transform: scale(0.8); } }
        @media screen and (max-width: 1000px) { .cert-wrapper { transform: scale(0.6); } }
        @media screen and (max-width: 750px) { .cert-wrapper { transform: scale(0.4); } }

        .certificate {
            width: 1122px;
            height: 793px;
            background: var(--cream);
            padding: 20px;
            box-sizing: border-box;
            position: relative;
            border: 1px solid #94a3b8;
            box-shadow: 0 50px 100px -20px rgba(0,0,0,0.4);
            background-image: url('https://www.transparenttextures.com/patterns/paper-fibers.png');
            overflow: hidden;
        }
        .border-outer {
            border: 12px solid var(--navy);
            height: 100%;
            box-sizing: border-box;
            padding: 8px;
            position: relative;
        }
        .border-inner {
            border: 2px solid var(--gold);
            height: 100%;
            box-sizing: border-box;
            padding: 30px 60px;
            position: relative;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .corner {
            position: absolute;
            width: 40px;
            height: 40px;
            border: 3px solid var(--gold);
            z-index: 10;
        }
        .top-left { top: -4px; left: -4px; border-right: none; border-bottom: none; }
        .top-right { top: -4px; right: -4px; border-left: none; border-bottom: none; }
        .bottom-left { bottom: -4px; left: -4px; border-right: none; border-top: none; }
        .bottom-right { bottom: -4px; right: -4px; border-left: none; border-top: none; }

        .cert-info { position: absolute; top: 30px; left: 40px; font-size: 0.65rem; color: var(--gold); text-align: left; font-weight: 700; }
        .date-info { position: absolute; top: 30px; right: 40px; font-size: 0.65rem; color: var(--gold); text-align: right; font-weight: 700; }

        .college-header { text-align: center; margin-bottom: 15px; }
        .college-name { font-family: 'Cinzel', serif; font-weight: 900; font-size: 2rem; color: var(--navy); margin: 0; letter-spacing: 2px; text-transform: uppercase; }
        .college-sub { font-size: 0.8rem; color: var(--gold); letter-spacing: 3px; text-transform: uppercase; font-weight: 700; margin-top: 3px; }
        
        .main-title { font-family: 'Cinzel', serif; font-size: 3rem; color: var(--navy); margin: 10px 0 5px; font-weight: 700; }
        .presented-to { font-style: italic; font-size: 1.1rem; color: #64748b; margin-bottom: 5px; }
        .student-name { font-family: 'Sacramento', cursive; font-size: 4rem; color: var(--navy); margin: 5px 0; border-bottom: 1px solid #cbd5e1; display: inline-block; padding: 0 40px; }
        .description { font-size: 1rem; line-height: 1.6; color: #334155; max-width: 700px; margin: 10px auto; }
        .event-highlight { font-weight: 700; color: var(--navy); font-size: 1.2rem; display: block; margin: 5px 0; }

        .footer-section { display: flex; justify-content: space-around; align-items: flex-end; margin-top: 10px; padding-bottom: 10px; z-index: 5; }
        .sig-box { text-align: center; width: 220px; }
        .signature { font-family: 'Sacramento', cursive; font-size: 2.5rem; color: #1e293b; margin-bottom: -5px; }
        .sig-line { border-top: 2px solid var(--navy); padding-top: 5px; font-size: 0.8rem; font-weight: 700; color: var(--navy); text-transform: uppercase; }

        .seal-container { position: absolute; bottom: 20px; left: 30px; width: 100px; height: 100px; z-index: 2; }
        .seal-outer { width: 100%; height: 100%; border: 2px solid var(--gold); border-radius: 50%; display: flex; align-items: center; justify-content: center; background: rgba(255, 255, 255, 0.9); position: relative; box-shadow: 0 0 0 4px white, 0 0 0 6px var(--gold); }
        .seal-inner { width: 85%; height: 85%; border: 1px dashed var(--gold); border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; font-size: 0.5rem; font-weight: 800; color: var(--gold); text-align: center; }
        
        .rubber-stamp { position: absolute; bottom: 20px; right: 30px; width: 110px; height: 110px; border: 4px double #b91c1c; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #b91c1c; font-family: 'Cinzel', serif; font-weight: 900; text-transform: uppercase; transform: rotate(-15deg); opacity: 0.4; z-index: 2; pointer-events: none; }
        .stamp-inner { border: 2px solid #b91c1c; border-radius: 50%; width: 80%; height: 80%; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; font-size: 0.65rem; line-height: 1.1; }

        @media print {
            @page { 
                size: A4 landscape; 
                margin: 0; 
            }
            html, body {
                width: 297mm;
                height: 210mm;
                margin: 0;
                padding: 0;
                background: white !important;
            }
            .no-print { display: none; }
            .cert-wrapper { 
                transform: none !important; 
                width: 100% !important; 
                height: 100% !important;
                display: block !important;
            }
            .certificate { 
                box-shadow: none !important; 
                border: none !important;
                width: 297mm !important;
                height: 210mm !important;
                margin: 0 !important;
                padding: 10mm !important; /* Safety margin for printer bleed */
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="position: fixed; top: 20px; right: 20px; z-index: 100;">
        <button onclick="window.print()" style="padding: 12px 24px; background: #1a365d; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 700;">Print Certificate</button>
    </div>

    <div class="cert-wrapper">
        <div class="certificate">
            <div class="border-outer">
                <div class="border-inner">
                    <div class="corner top-left"></div>
                    <div class="corner top-right"></div>
                    <div class="corner bottom-left"></div>
                    <div class="corner bottom-right"></div>

                    <div class="cert-info">CERTIFICATE NO:<br>#ITS-<?php echo strtoupper(substr(md5($student['id']), 0, 8)); ?></div>
                    <div class="date-info">DATE OF ISSUE:<br><?php echo date('d-m-Y'); ?></div>

                    <div class="college-header">
                        <h1 class="college-name">Institute of Technology & Science</h1>
                        <div class="college-sub">Accredited Grade 'A' | Campus Event Council</div>
                    </div>

                    <div style="margin-top: 20px;">
                        <h2 class="main-title">CERTIFICATE</h2>
                        <p class="presented-to">of participation is proudly presented to</p>
                        <div class="student-name"><?php echo htmlspecialchars($student['name']); ?></div>
                        <div style="margin: 10px 0; font-size: 1rem; color: var(--navy); font-weight: 600;">
                            Reg No: <?php echo htmlspecialchars($student['reg_no']); ?> | VTU No: <?php echo htmlspecialchars($student['vtu_no']); ?>
                        </div>
                        <p class="description">
                            for their exemplary participation and successful completion of the campus event
                            <span class="event-highlight">"<?php echo htmlspecialchars($student['event_name']); ?>"</span>
                            organized by the Department of <?php echo htmlspecialchars($student['department']); ?>.
                        </p>
                    </div>

                    <div style="position: absolute; top: 250px; right: 80px; text-align: center;">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data=VERIFY-<?php echo $student['id']; ?>" alt="Verify QR" style="border: 2px solid var(--gold); padding: 5px; background: white;">
                        <div style="font-size: 0.6rem; color: var(--gold); margin-top: 5px; font-weight: 700;">SCAN TO VERIFY</div>
                    </div>

                    <div class="seal-container">
                        <div class="seal-outer">
                            <div class="seal-inner">CAMPUS EVENT<br>COUNCIL<br>★<br>OFFICIAL<br>SEAL</div>
                        </div>
                    </div>

                    <div class="rubber-stamp">
                        <div class="stamp-inner">VERIFIED<br>STUDENT<br>COUNCIL</div>
                    </div>

                    <div class="footer-section">
                        <div class="sig-box">
                            <div class="signature">S.K. Sharma</div>
                            <div class="sig-line">Program Coordinator</div>
                        </div>
                        <div class="sig-box">
                            <div class="signature">Principal</div>
                            <div class="sig-line">Principal / Dean</div>
                        </div>
                    </div>

                    <div style="position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); font-size: 0.7rem; color: #94a3b8;">
                        Digital Verification: certificate.its.edu.in/verify/<?php echo md5($student['id']); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
