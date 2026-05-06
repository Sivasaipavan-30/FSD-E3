// Quiz Timer Logic
let timer;
let timeLeft = 600; // 10 minutes in seconds

function startTimer() {
    const timerDisplay = document.getElementById('timer-display');
    if (!timerDisplay) return;

    timer = setInterval(() => {
        timeLeft--;
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        timerDisplay.textContent = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;

        if (timeLeft <= 0) {
            clearInterval(timer);
            alert("Time is up! Submitting your quiz.");
            document.getElementById('quiz-form').submit();
        }
    }, 1000);
}

// Form Validation
function validateRegistration() {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    if (password !== confirmPassword) {
        alert("Passwords do not match!");
        return false;
    }
    return true;
}

// Confirmation for deletions
function confirmDelete() {
    return confirm("Are you sure you want to delete this question?");
}

// Auto-submit quiz on visibility change (optional, prevents cheating)
/*
document.addEventListener("visibilitychange", function() {
    if (document.hidden) {
        alert("Cheating detected! Quiz submitted.");
        document.getElementById('quiz-form').submit();
    }
});
*/

// Initialize timer if on quiz page
window.onload = () => {
    const countdownOverlay = document.getElementById('countdown-overlay');
    const actualQuiz = document.getElementById('actual-quiz');
    
    if (countdownOverlay && actualQuiz) {
        let count = 5;
        const countDisplay = document.getElementById('countdown-number');
        
        let countdownTimer = setInterval(() => {
            count--;
            if (count > 0) {
                countDisplay.textContent = count;
            } else {
                clearInterval(countdownTimer);
                countdownOverlay.style.display = 'none';
                actualQuiz.style.display = 'block';
                // start the 10 min timer now
                if (document.getElementById('timer-display')) {
                    startTimer();
                }
            }
        }, 1000);
    } else if (document.getElementById('timer-display')) {
        // Fallback for pages without countdown
        startTimer();
    }
};
