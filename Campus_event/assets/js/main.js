// assets/js/main.js

// Global state for discovery features
let allEvents = [];
let currentCategory = 'All';
let revenueChartInstance = null;
let trendsChartInstance = null;
let deptChartInstance = null;
let html5QrCode = null;

document.addEventListener('DOMContentLoaded', () => {
    // Determine which page we are on
    const path = window.location.pathname;

    if (path.includes('index.php') || path.endsWith('/')) {
        loadEvents();
        // Check if student features are present (integrated bookings)
        if (document.getElementById('upcoming-booking-list')) {
            loadUserBookings();
        }
        // Also check if admin features are present (integrated dashboard)
        if (document.getElementById('admin-event-list')) {
            loadAdminEvents();
            loadAdminStats();
            handleEventForm();
        }
    }

    if (path.includes('login.php')) {
        handleAuthForm('login-form', 'api/auth.php?action=login', 'login-alert');
    }

    if (path.includes('register.php')) {
        handleAuthForm('register-form', 'api/auth.php?action=register', 'register-alert');
    }

    if (path.includes('admin_login.php')) {
        handleAuthForm('admin-login-form', 'api/auth.php?action=login', 'admin-login-alert');
    }

    if (path.includes('admin.php')) {
        loadAdminEvents();
        loadAdminStats();
        handleEventForm();
    }

    if (path.includes('bookings.php')) {
        loadUserBookings();
    }
});

// Helper for AJAX responses
async function apiFetch(url, options = {}) {
    try {
        const response = await fetch(url, options);
        return await response.json();
    } catch (err) {
        return { success: false, message: "Network error occurred." };
    }
}

// Authentication handling
function handleAuthForm(formId, url, alertId) {
    const form = document.getElementById(formId);
    if (!form) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(form);
        const alertDiv = document.getElementById(alertId);

        // Check password matching for registration
        if (formId === 'register-form') {
            const pass = formData.get('password');
            const confirm = formData.get('confirm_password');
            if (pass !== confirm) {
                alertDiv.innerHTML = `<div class="alert alert-error">Passwords do not match</div>`;
                return;
            }
            if (pass.length < 6) {
                alertDiv.innerHTML = `<div class="alert alert-error">Password must be at least 6 characters</div>`;
                return;
            }
        }

        const res = await apiFetch(url, { method: 'POST', body: formData });
        
        if (res.success) {
            alertDiv.innerHTML = `<div class="alert alert-success">${res.message}</div>`;
            if (url.includes('login')) {
                setTimeout(() => window.location.href = 'index.php', 1000);
            } else {
                form.reset();
            }
        } else {
            alertDiv.innerHTML = `<div class="alert alert-error">${res.message}</div>`;
        }
    });
}

// Home Page: Load and display events
async function loadEvents() {
    try {
        console.log("Loading all events...");
        const res = await apiFetch('api/events.php?action=list&t=' + new Date().getTime());
        const container = document.getElementById('event-list');
        if (!container) return;

        if (res.success && res.data && res.data.length > 0) {
            allEvents = res.data;
            renderEvents(allEvents);
            renderCategoryFilters(allEvents);
        } else {
            container.innerHTML = '<p>No events found.</p>';
        }
    } catch (err) {
        console.error("loadEvents error:", err);
    }
}

function renderEvents(events) {
    const container = document.getElementById('event-list');
    if (events.length === 0) {
        container.innerHTML = '<p style="grid-column: 1/-1; text-align: center; color: var(--text-muted); padding: 3rem;">No events match your search or filter.</p>';
        return;
    }

    container.innerHTML = events.map(event => {
        const isSoldOut = event.available_seats <= 0;
        const isLimited = event.available_seats <= 10 && !isSoldOut;
        const isPopular = event.available_seats < (event.seats * 0.5) && !isSoldOut; // More than 50% booked
        
        const eventUrl = encodeURIComponent(window.location.origin + window.location.pathname + '#event-' + event.id);
        const shareText = encodeURIComponent(`Check out this event: ${event.name} at ${event.venue}! Register now on Smart Campus Events.`);

        return `
            <div class="event-card glass animate-fade" id="event-${event.id}">
                <div style="position: absolute; top: 1rem; right: 1rem; display: flex; flex-direction: column; gap: 0.5rem; align-items: flex-end;">
                    ${isSoldOut ? '<span class="badge" style="background: var(--danger);">Sold Out</span>' : ''}
                    ${isPopular ? '<span class="badge" style="background: #f59e0b; border: 1px solid #fff;">🔥 POPULAR</span>' : ''}
                    ${isLimited ? '<span class="badge" style="background: var(--accent);">⌛ CLOSING SOON</span>' : ''}
                </div>
                <h3>${event.name}</h3>
                <div class="event-meta">
                    <div>📅 ${new Date(event.datetime).toLocaleString([], { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' })}</div>
                    <div>📍 ${event.venue}</div>
                    <div>🏢 ${event.department}</div>
                    <div style="font-weight: 600; color: ${isSoldOut ? 'var(--danger)' : isLimited ? 'var(--accent)' : 'var(--primary)'}">
                        🎟️ ${event.available_seats} / ${event.seats} seats available
                    </div>
                </div>
                <div style="display: flex; gap: 0.5rem; margin: 1rem 0; padding-top: 1rem; border-top: 1px solid var(--glass-border);">
                    <a href="https://wa.me/?text=${shareText}%20${eventUrl}" target="_blank" title="Share on WhatsApp" style="color: #25d366; font-size: 1.2rem; text-decoration: none;">💬</a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url=${eventUrl}" target="_blank" title="Share on LinkedIn" style="color: #0077b5; font-size: 1.2rem; text-decoration: none;">🔗</a>
                    <a href="https://twitter.com/intent/tweet?text=${shareText}&url=${eventUrl}" target="_blank" title="Share on Twitter" style="color: #1da1f2; font-size: 1.2rem; text-decoration: none;">🐦</a>
                </div>
                <div class="price-tag">₹${event.price}</div>
                ${USER_ROLE !== 'admin' 
                    ? `<button class="btn btn-primary" ${isSoldOut ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : ''} onclick="openBookingModal(${event.id}, '${event.name}', ${event.price})">${isSoldOut ? 'Full' : 'Book Now'}</button>` 
                    : `<p style="color: var(--text-muted); font-size: 0.8rem;"><i>Admins cannot book events</i></p>`
                }
            </div>
        `;
    }).join('');
}

function filterEvents() {
    const searchTerm = document.getElementById('event-search')?.value.toLowerCase() || '';
    
    const filtered = allEvents.filter(e => {
        const matchesSearch = e.name.toLowerCase().includes(searchTerm) || e.department.toLowerCase().includes(searchTerm);
        const matchesCategory = currentCategory === 'All' || e.department === currentCategory;
        return matchesSearch && matchesCategory;
    });
    
    renderEvents(filtered);
}

function renderCategoryFilters(events) {
    const filterContainer = document.getElementById('category-filters');
    if (!filterContainer) return;
    
    const departments = ['All', ...new Set(events.map(e => e.department))];
    filterContainer.innerHTML = departments.map(dept => `
        <button class="btn btn-outline category-btn ${dept === currentCategory ? 'active' : ''}" 
                onclick="setCategory('${dept}')">${dept}</button>
    `).join('');
}

function setCategory(dept) {
    currentCategory = dept;
    const btns = document.querySelectorAll('.category-btn');
    btns.forEach(b => b.classList.remove('active'));
    // Find and highlight the clicked btn
    [...btns].find(b => b.textContent === dept)?.classList.add('active');
    filterEvents();
}

async function loadUserBookings() {
    const upcomingContainer = document.getElementById('upcoming-booking-list');
    const pastContainer = document.getElementById('past-booking-list');
    if (!upcomingContainer) return;

    console.log("Fetching user bookings...");
    try {
        const res = await apiFetch('api/bookings.php?action=user_list&t=' + new Date().getTime());
        console.log("Bookings API Response:", res);
        
        if (res.success && res.data.length > 0) {
            const now = new Date();
            const bookings = res.data;
            
            // Update Stats Safely
            const totalTicketsEl = document.getElementById('stat-total-tickets');
            const totalEventsEl = document.getElementById('stat-total-events');
            if (totalTicketsEl) totalTicketsEl.textContent = bookings.reduce((sum, b) => sum + parseInt(b.tickets || 0), 0);
            if (totalEventsEl) totalEventsEl.textContent = bookings.length;

            const upcoming = bookings.filter(b => new Date(b.datetime) >= now).sort((a, b) => new Date(a.datetime) - new Date(b.datetime));
            const past = bookings.filter(b => new Date(b.datetime) < now);

            // Update Next Event Highlight
            const nextEventCard = document.getElementById('next-event-card');
            if (nextEventCard && upcoming.length > 0) {
                const next = upcoming[0];
                document.getElementById('next-event-name').textContent = next.event_name;
                
                // Clear any existing interval
                if (window.countdownInterval) clearInterval(window.countdownInterval);
                
                const updateCountdown = () => {
                    const diff = new Date(next.datetime) - new Date();
                    if (diff <= 0) {
                        document.getElementById('next-event-time').textContent = "Started!";
                        clearInterval(window.countdownInterval);
                        return;
                    }
                    const d = Math.floor(diff / (1000 * 60 * 60 * 24));
                    const h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    const s = Math.floor((diff % (1000 * 60)) / 1000);
                    document.getElementById('next-event-time').innerHTML = `Starts in: <span style="color:var(--primary); font-weight:700;">${d}d ${h}h ${m}m ${s}s</span>`;
                };
                
                updateCountdown();
                window.countdownInterval = setInterval(updateCountdown, 1000);
                nextEventCard.style.display = 'block';
            } else if (nextEventCard) {
                nextEventCard.style.display = 'none';
            }

                upcomingContainer.innerHTML = upcoming.length > 0 ? upcoming.map(b => {
                    const daysAway = Math.ceil((new Date(b.datetime) - now) / (1000 * 60 * 60 * 24));
                    const calUrl = `https://www.google.com/calendar/render?action=TEMPLATE&text=${encodeURIComponent(b.event_name)}&dates=${b.datetime.replace(/[-:]/g, '').replace(' ', 'T')}Z/${b.datetime.replace(/[-:]/g, '').replace(' ', 'T')}Z&details=Your+Campus+Event+Ticket&location=${encodeURIComponent(b.venue)}`;

                    return `
                    <tr>
                        <td>
                            <div style="font-weight: 600;">${b.event_name}</div>
                            <span class="badge" style="background: ${daysAway <= 1 ? 'var(--danger)' : 'var(--primary)'}; font-size: 0.6rem; padding: 0.2rem 0.4rem;">
                                ${daysAway === 0 ? 'Today' : daysAway === 1 ? 'Tomorrow' : daysAway + ' days to go'}
                            </span>
                        </td>
                        <td>${b.tickets}</td>
                        <td>${new Date(b.datetime).toLocaleDateString()}</td>
                        <td>
                            <div style="font-size: 0.8rem;">${b.venue}</div>
                            <a href="${calUrl}" target="_blank" style="font-size: 0.7rem; color: var(--primary); text-decoration: none;">📅 Add to Calendar</a>
                        </td>
                        <td>
                            <a href="receipt.php?id=${b.id}" target="_blank" class="btn btn-outline" style="padding: 0.3rem 0.6rem; font-size: 0.8rem;">Download</a>
                        </td>
                    </tr>
                `}).join('') : '<tr><td colspan="5" style="text-align:center; color:var(--text-muted); padding: 2rem;">No upcoming events. Find something exciting above!</td></tr>';

            if (pastContainer) {
                pastContainer.innerHTML = past.map(b => `
                    <tr style="border-bottom: 1px solid var(--glass-border);">
                        <td style="padding: 0.8rem 0;">
                            <div style="font-weight: 600;">${b.event_name}</div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);">${new Date(b.datetime).toLocaleDateString()}</div>
                        </td>
                        <td style="text-align: right; vertical-align: middle; display: flex; gap: 0.8rem; justify-content: flex-end; align-items: center; height: 100%; padding: 0.8rem 0;">
                            <button onclick="openFeedbackModal(${b.event_id}, '${b.event_name}')" style="background: none; border: 1px solid var(--primary); color: var(--primary); font-size: 0.7rem; padding: 0.2rem 0.5rem; border-radius: 4px; cursor: pointer;">⭐ Rate</button>
                            <a href="receipt.php?id=${b.id}" target="_blank" style="text-decoration: none; color: var(--text-muted); font-size: 0.8rem;">Receipt 📄</a>
                        </td>
                    </tr>
                `).join('');
            }
        } else {
            upcomingContainer.innerHTML = '<tr><td colspan="5" style="text-align:center; color:var(--text-muted); padding: 2rem;">No bookings found. Try booking an event!</td></tr>';
            if (pastContainer) pastContainer.innerHTML = '<tr><td style="text-align:center; color:var(--text-muted); font-size: 0.8rem; padding: 1rem;">No past history found.</td></tr>';
        }
    } catch (err) {
        console.error("Booking load error:", err);
        upcomingContainer.innerHTML = '<tr><td colspan="5" style="text-align:center; color:var(--danger); padding: 2rem;">Failed to load bookings.</td></tr>';
    }
}

async function openProfileModal() {
    const res = await apiFetch('api/profile.php?action=get');
    if (res.success) {
        document.getElementById('profile-name').value = res.data.name;
        document.getElementById('profile-dept').value = res.data.department || '';
        document.getElementById('profile-modal').style.display = 'flex';
    }
}

async function openCertificateVault() {
    const modal = document.getElementById('cert-modal');
    const list = document.getElementById('cert-list');
    list.innerHTML = '<tr><td colspan="3" style="text-align:center; padding:2rem;">Fetching achievements...</td></tr>';
    modal.style.display = 'flex';
    
    const res = await apiFetch('api/bookings.php?action=attendee_list');
    if (res.success) {
        if (res.data.length === 0) {
            list.innerHTML = '<tr><td colspan="3" style="text-align:center; padding:2rem; color:var(--text-muted);">No certificates yet. Attend an event to earn one!</td></tr>';
            return;
        }
        
        list.innerHTML = res.data.map(ticket => `
            <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                <td style="padding: 1rem 0;">
                    <div style="font-weight: 600;">${ticket.name}</div>
                    <div style="font-size: 0.75rem; color: var(--text-muted);">${ticket.vtu_no || ''}</div>
                </td>
                <td style="padding: 1rem 0;">
                    <div style="font-size: 0.9rem;">${ticket.event_name}</div>
                    <div style="font-size: 0.75rem; color: var(--text-muted);">${new Date(ticket.datetime).toLocaleDateString()}</div>
                </td>
                <td style="padding: 1rem 0; text-align: center;">
                    ${ticket.is_checked_in == 1 
                        ? `<a href="certificate.php?id=${ticket.id}" target="_blank" class="btn btn-outline" style="font-size: 0.7rem; border-color: #10b981; color: #10b981; padding: 0.3rem 0.6rem;">📜 Download</a>`
                        : `<span style="font-size: 0.7rem; color: var(--text-muted); opacity: 0.5;">Pending Check-in</span>`
                    }
                </td>
            </tr>
        `).join('');
    }
}

// Global Event Handlers for User Portals
document.addEventListener('submit', async (e) => {
    if (e.target.id === 'profile-form') {
        e.preventDefault();
        const formData = new FormData(e.target);
        const res = await apiFetch('api/profile.php?action=update', {
            method: 'POST',
            body: formData
        });
        
        if (res.success) {
            alert('Profile updated! Refreshing...');
            location.reload();
        } else {
            alert(res.message);
        }
    }
});

async function loadAdminBookings() {
    const res = await apiFetch('api/bookings.php?action=admin_list');
    const container = document.getElementById('admin-booking-list');
    const section = document.getElementById('admin-booking-section');
    
    if (res.success) {
        section.style.display = 'block';
        if (res.data.length > 0) {
            container.innerHTML = res.data.map(b => `
                <tr>
                    <td>${b.user_name} (${b.user_email})</td>
                    <td>${b.event_name}</td>
                    <td>${b.tickets}</td>
                    <td>${new Date(b.booking_date).toLocaleDateString()}</td>
                </tr>
            `).join('');
        } else {
            container.innerHTML = '<tr><td colspan="4" style="text-align:center;">No bookings found in the system.</td></tr>';
        }
        // Scroll to section
        section.scrollIntoView({ behavior: 'smooth' });
    } else {
        alert(res.message);
    }
}

async function loadAdminStats() {
    const res = await apiFetch('api/bookings.php?action=admin_stats&t=' + new Date().getTime());
    if (res.success) {
        const s = res.data;
        document.getElementById('admin-stat-revenue').textContent = `₹${s.revenue.toLocaleString()}`;
        document.getElementById('admin-stat-tickets').textContent = s.tickets;
        document.getElementById('admin-stat-engagement').textContent = s.engagement;
        const trendingEl = document.getElementById('admin-stat-trending');
        if (trendingEl) {
            trendingEl.textContent = s.trending;
            trendingEl.title = s.trending;
        }
        
        const deptEl = document.getElementById('admin-stat-dept');
        if (deptEl) {
            deptEl.textContent = s.top_dept;
        }
        
        const checkinEl = document.getElementById('admin-stat-checkins');
        if (checkinEl) {
            checkinEl.textContent = s.checkins || 0;
        }

        renderAdminCharts(s);
        logActivity('Statistics and analytics charts updated.');
    }
}

function renderAdminCharts(s) {
    // --- Render Revenue Chart ---
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        if (revenueChartInstance) revenueChartInstance.destroy();
        revenueChartInstance = new Chart(revenueCtx, {
            type: 'bar',
            data: {
                labels: s.revenue_by_event.map(e => e.name),
                datasets: [{
                    label: 'Revenue (₹)',
                    data: s.revenue_by_event.map(e => e.total),
                    backgroundColor: '#2563eb',
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' } } }
            }
        });
    }

    // --- Render Trends Chart ---
    const trendsCtx = document.getElementById('trendsChart');
    if (trendsCtx) {
        if (trendsChartInstance) trendsChartInstance.destroy();
        trendsChartInstance = new Chart(trendsCtx, {
            type: 'line',
            data: {
                labels: s.booking_trends.map(t => new Date(t.date).toLocaleDateString([], {month:'short', day:'numeric'})),
                datasets: [{
                    label: 'Tickets Sold',
                    data: s.booking_trends.map(t => t.count),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });
    }

    // --- Render Dept Chart ---
    const deptCtx = document.getElementById('deptChart');
    if (deptCtx && s.dept_engagement) {
        if (deptChartInstance) deptChartInstance.destroy();
        deptChartInstance = new Chart(deptCtx, {
            type: 'doughnut',
            data: {
                labels: s.dept_engagement.map(d => d.department),
                datasets: [{
                    data: s.dept_engagement.map(d => d.count),
                    backgroundColor: ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#ec4899', '#8b5cf6'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { color: '#94a3b8', boxWidth: 12 } }
                },
                cutout: '70%'
            }
        });
    }
}

function logActivity(msg, type = 'INFO') {
    const feed = document.getElementById('admin-activity-feed');
    if (!feed) return;
    
    const time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    const color = type === 'ERROR' ? 'var(--danger)' : type === 'SUCCESS' ? 'var(--accent)' : 'var(--primary)';
    
    const item = document.createElement('div');
    item.style.marginBottom = '0.5rem';
    item.style.paddingBottom = '0.5rem';
    item.style.borderBottom = '1px solid rgba(255,255,255,0.05)';
    item.innerHTML = `
        <span style="color: ${color}; font-weight: 700;">[${time}]</span> ${msg}
    `;
    
    feed.prepend(item);
    if (feed.children.length > 20) feed.lastElementChild.remove();
}

async function loadAdminUsers() {
    const res = await apiFetch('api/auth.php?action=list');
    const container = document.getElementById('admin-user-list');
    const section = document.getElementById('admin-user-section');
    
    if (res.success) {
        // Hide other specific admin sections to declutter
        const bSection = document.getElementById('admin-booking-section');
        if (bSection) bSection.style.display = 'none';

        section.style.display = 'block';
        if (res.data.length > 0) {
            container.innerHTML = res.data.map(u => `
                <tr style="border-bottom: 1px solid var(--glass-border);">
                    <td style="padding: 1rem 0;">
                        <div style="font-weight: 600;">${u.name}</div>
                    </td>
                    <td>${u.email}</td>
                    <td><span class="badge" style="background: ${u.role === 'admin' ? 'var(--accent)' : 'var(--primary)'}; font-size: 0.7rem;">${u.role}</span></td>
                    <td>${new Date(u.created_at).toLocaleDateString()}</td>
                </tr>
            `).join('');
        } else {
            container.innerHTML = '<tr><td colspan="4" style="text-align:center;">No users found.</td></tr>';
        }
        section.scrollIntoView({ behavior: 'smooth' });
    } else {
        alert(res.message);
    }
}

function exportBookingsToCSV() {
    const table = document.getElementById('admin-booking-list');
    if (!table || table.rows.length === 0) return alert('No data to export');

    let csv = 'User,Email,Event,Tickets,Date\n';
    
    // Iterate through visible rows in the table
    [...table.rows].forEach(row => {
        const cells = row.cells;
        // Text cleaning: remove internal spaces and commas
        const user = cells[0].textContent.split('(')[0].trim().replace(',', '');
        const email = cells[0].textContent.split('(')[1]?.replace(')', '').trim() || '';
        const event = cells[1].textContent.replace(',', '');
        const tickets = cells[2].textContent;
        const date = cells[3].textContent;
        
        csv += `${user},${email},${event},${tickets},${date}\n`;
    });

    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.setAttribute('hidden', '');
    a.setAttribute('href', url);
    a.setAttribute('download', 'campus_bookings_report.csv');
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}

// Booking Modal Logic
let currentPrice = 0;
function openBookingModal(id, name, price) {
    currentPrice = price;
    document.getElementById('modal-event-id').value = id;
    document.getElementById('modal-event-name').textContent = name;
    document.getElementById('modal-event-info').textContent = `Price per ticket: ₹${price}`;
    document.getElementById('modal-total-price').textContent = `₹${price.toFixed(2)}`;
    document.getElementById('booking-modal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('booking-modal').style.display = 'none';
    document.getElementById('modal-alert').innerHTML = '';
    // Reset modal state
    document.getElementById('booking-form').style.display = 'block';
    document.getElementById('attendee-form').style.display = 'none';
    document.getElementById('payment-form').style.display = 'none';
    document.getElementById('payment-processing').style.display = 'none';
    switchPayment('card'); // Reset to card view
}

function generateAttendeeFields(count) {
    const container = document.getElementById('attendee-inputs');
    container.innerHTML = '';
    for (let i = 1; i <= count; i++) {
        container.innerHTML += `
            <div class="attendee-card" style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 3px solid var(--primary);">
                <div style="font-size: 0.8rem; color: var(--primary); font-weight: 700; margin-bottom: 0.8rem;">STUDENT ${i}</div>
                <div class="form-group">
                    <label>Student Name</label>
                    <input type="text" class="form-control attendee-name" placeholder="Enter full name" required>
                </div>
                <div class="form-group">
                    <label>Department</label>
                    <input type="text" class="form-control attendee-dept" placeholder="e.g. CSE, ECE" required>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label>VTU No</label>
                        <input type="text" class="form-control attendee-vtu" placeholder="e.g. VTU12345" required>
                    </div>
                    <div class="form-group">
                        <label>Registration No</label>
                        <input type="text" class="form-control attendee-reg" placeholder="e.g. 23UECS1234" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Attendee Email</label>
                    <input type="email" class="form-control attendee-email" placeholder="student@example.com" required>
                </div>
            </div>
        `;
    }
}

function switchPayment(method) {
    const cardSec = document.getElementById('payment-card-section');
    const upiSec = document.getElementById('payment-upi-section');
    const btnCard = document.getElementById('btn-card');
    const btnUpi = document.getElementById('btn-upi');

    if (method === 'card') {
        cardSec.style.display = 'block';
        upiSec.style.display = 'none';
        btnCard.style.background = 'var(--primary)';
        btnCard.classList.remove('btn-outline');
        btnUpi.style.background = 'transparent';
        btnUpi.classList.add('btn-outline');
    } else {
        cardSec.style.display = 'none';
        upiSec.style.display = 'block';
        btnUpi.style.background = 'var(--primary)';
        btnUpi.classList.remove('btn-outline');
        btnCard.style.background = 'transparent';
        btnCard.classList.add('btn-outline');
    }
}

function backToTickets() {
    if (document.getElementById('payment-form').style.display === 'block') {
        document.getElementById('payment-form').style.display = 'none';
        document.getElementById('attendee-form').style.display = 'block';
    } else {
        document.getElementById('attendee-form').style.display = 'none';
        document.getElementById('booking-form').style.display = 'block';
    }
}

document.getElementById('booking-tickets')?.addEventListener('input', (e) => {
    const total = e.target.value * currentPrice;
    document.getElementById('modal-total-price').textContent = `₹${total.toFixed(2)}`;
});

document.getElementById('booking-form')?.addEventListener('submit', (e) => {
    e.preventDefault();
    if (!IS_LOGGED_IN) {
        window.location.href = 'login.php';
        return;
    }
    const count = parseInt(document.getElementById('booking-tickets').value);
    generateAttendeeFields(count);
    
    document.getElementById('booking-form').style.display = 'none';
    document.getElementById('attendee-form').style.display = 'block';
});

document.getElementById('attendee-form')?.addEventListener('submit', (e) => {
    e.preventDefault();
    // Transition to payment
    document.getElementById('attendee-form').style.display = 'none';
    document.getElementById('payment-form').style.display = 'block';
    document.getElementById('payment-total-display').textContent = document.getElementById('modal-total-price').textContent;
});

document.getElementById('payment-form')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    // 1. Show processing state
    document.getElementById('payment-form').style.display = 'none';
    document.getElementById('payment-processing').style.display = 'block';

    // 2. Collect Attendee Data
    const attendeeNodes = document.querySelectorAll('.attendee-card');
    const attendees = Array.from(attendeeNodes).map(node => ({
        name: node.querySelector('.attendee-name').value,
        dept: node.querySelector('.attendee-dept').value,
        vtu: node.querySelector('.attendee-vtu').value,
        reg: node.querySelector('.attendee-reg').value,
        email: node.querySelector('.attendee-email').value
    }));

    // 3. Mock delay for "Processing"
    await new Promise(resolve => setTimeout(resolve, 2000));

    // 4. Finalize booking on backend
    const formData = new FormData();
    formData.append('event_id', document.getElementById('modal-event-id').value);
    formData.append('tickets', document.getElementById('booking-tickets').value);
    formData.append('attendees', JSON.stringify(attendees));

    const res = await apiFetch('api/bookings.php?action=book', { method: 'POST', body: formData });
    const alertDiv = document.getElementById('modal-alert');

    if (res.success) {
        document.getElementById('payment-processing').style.display = 'none';
        
        // Show Success UI
        alertDiv.innerHTML = `
            <div style="text-align: center; padding: 1rem;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">✅</div>
                <h3 style="color: #10b981;">Payment Successful!</h3>
                <p style="color: var(--text-muted); margin-bottom: 2rem;">Your tickets have been booked.</p>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <a href="receipt.php?id=${res.data.booking_id}" target="_blank" class="btn btn-primary">Download Receipt</a>
                    <button type="button" class="btn btn-outline" onclick="closeModal()">Close</button>
                </div>
            </div>
        `;
        
        loadEvents();
    } else {
        document.getElementById('payment-processing').style.display = 'none';
        document.getElementById('payment-form').style.display = 'block';
        alertDiv.innerHTML = `<div class="alert alert-error">${res.message}</div>`;
    }
});

// Admin: Event Modal Helpers
function showEventForm() { 
    // Reset form for "Create" mode
    const form = document.getElementById('event-form');
    if (form) {
        form.reset();
        document.getElementById('event-edit-id').value = '';
        document.getElementById('event-modal-title').textContent = 'Create Event';
    }
    const modal = document.getElementById('event-modal');
    if (modal) modal.style.display = 'flex'; 
}

async function openEditModal(id) {
    const res = await apiFetch(`api/events.php?action=get&id=${id}`);
    if (res.success) {
        const e = res.data;
        const form = document.getElementById('event-form');
        document.getElementById('event-edit-id').value = e.id;
        document.getElementById('event-modal-title').textContent = 'Edit Event';
        
        form.name.value = e.name;
        form.department.value = e.department || '';
        form.datetime.value = e.datetime.replace(' ', 'T').substring(0, 16); // Format for datetime-local
        form.venue.value = e.venue;
        form.seats.value = e.seats;
        form.price.value = e.price;
        form.description.value = e.description || '';

        document.getElementById('event-modal').style.display = 'flex';
    } else {
        alert(res.message);
    }
}

function closeEventModal() { 
    const modal = document.getElementById('event-modal');
    if (modal) modal.style.display = 'none'; 
}

// Admin: Load events for management
async function loadAdminEvents() {
    const res = await apiFetch('api/events.php?action=list');
    const container = document.getElementById('admin-event-list');
    if (!container) return;

    if (res.success && res.data) {
        allEvents = res.data;
        container.innerHTML = res.data.map(e => {
            const occupancy = Math.round(((e.seats - e.available_seats) / e.seats) * 100);
            const barColor = occupancy > 90 ? 'var(--danger)' : occupancy > 70 ? 'var(--accent)' : '#10b981';
            
            return `
                <tr>
                    <td>
                        <div style="font-weight: 600;">${e.name}</div>
                        <div style="font-size: 0.7rem; color: var(--text-muted);">${e.department}</div>
                    </td>
                    <td>${new Date(e.datetime).toLocaleDateString()}</td>
                    <td>${e.venue}</td>
                    <td>
                        <div style="font-size: 0.8rem; margin-bottom: 0.3rem;">${e.available_seats} / ${e.seats}</div>
                        <div style="width: 100%; height: 6px; background: rgba(255,255,255,0.1); border-radius: 3px; overflow: hidden;">
                            <div style="width: ${occupancy}%; height: 100%; background: ${barColor};"></div>
                        </div>
                    </td>
                    <td>₹${e.price}</td>
                    <td style="display:flex; gap:0.5rem; flex-wrap: wrap;">
                        <button class="btn btn-outline" style="padding: 0.3rem 0.6rem; font-size: 0.7rem; border-color: var(--accent); color: var(--accent);" onclick="openEditModal(${e.id})">Edit</button>
                        <button class="btn btn-outline" style="padding: 0.3rem 0.6rem; font-size: 0.7rem; border-color: #10b981; color: #10b981;" onclick="loadFeedbackDetails(${e.id}, '${e.name}')">Feedback</button>
                        <button class="btn btn-outline delete-btn" data-id="${e.id}" style="padding: 0.3rem 0.6rem; font-size: 0.7rem; border-color: var(--danger); color: var(--danger);" onclick="deleteEvent(${e.id}, this)">Delete</button>
                    </td>
                </tr>
            `;
        }).join('');
    }
}

async function deleteEvent(id, btn) {
    if(!confirm('Are you sure you want to delete this event? This will also remove all bookings and certificates for this event.')) return;
    
    const originalText = btn.textContent;
    btn.disabled = true;
    btn.textContent = '...';

    const body = new FormData();
    body.append('id', id);
    
    try {
        const res = await apiFetch('api/events.php?action=delete', { method: 'POST', body });
        if(res.success) {
            loadAdminEvents();
            logActivity(`Event ID: ${id} deleted successfully`, 'SUCCESS');
        } else {
            alert(res.message);
            btn.disabled = false;
            btn.textContent = originalText;
        }
    } catch (err) {
        alert("Network error occurred while deleting.");
        btn.disabled = false;
        btn.textContent = originalText;
    }
}

function handleEventForm() {
    const form = document.getElementById('event-form');
    form?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const body = new FormData(form);
        const id = document.getElementById('event-edit-id').value;
        const action = id ? 'update' : 'create';
        
        const res = await apiFetch(`api/events.php?action=${action}`, { method: 'POST', body });
        if(res.success) {
            form.reset();
            document.getElementById('event-modal').style.display = 'none';
            loadAdminEvents();
            loadEvents(); // Also refresh the cards for users
        } else {
            alert(res.message);
        }
    });
}

function toggleHelpCenter() {
    const modal = document.getElementById('help-modal');
    const studentSec = document.getElementById('student-help-section');
    const adminSec = document.getElementById('admin-help-section');
    
    if (modal.style.display === 'none') {
        modal.style.display = 'block';
        if (USER_ROLE === 'admin') {
            adminSec.style.display = 'block';
            studentSec.style.display = 'none';
        } else {
            studentSec.style.display = 'block';
            adminSec.style.display = 'none';
        }
    } else {
        modal.style.display = 'none';
    }
}
function filterAdminEvents(term) {
    const rows = document.querySelectorAll('#admin-event-list tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(term.toLowerCase()) ? '' : 'none';
    });
}

function showBroadcastModal() {
    const modal = document.getElementById('broadcast-modal');
    const select = document.getElementById('broadcast-event-id');
    
    if (allEvents.length === 0) {
        alert("Please wait for events to load.");
        return;
    }

    // Populate events list
    select.innerHTML = allEvents.map(e => `<option value="${e.id}">${e.name}</option>`).join('');
    modal.style.display = 'flex';
}

document.getElementById('broadcast-form')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = e.target.querySelector('button[type="submit"]');
    const originalText = btn.textContent;
    btn.disabled = true;
    btn.textContent = 'Sending... ⏳';

    const formData = new FormData(e.target);
    const res = await apiFetch('api/broadcast.php', { method: 'POST', body: formData });
    
    btn.disabled = false;
    btn.textContent = originalText;

    if (res.success) {
        alert(res.message);
        logActivity(`Broadcast sent: "${formData.get('subject')}"`, 'SUCCESS');
        document.getElementById('broadcast-modal').style.display = 'none';
        e.target.reset();
    } else {
        alert(res.message);
    }
});

function startScanner() {
    document.getElementById('scanner-modal').style.display = 'flex';
    document.getElementById('scanner-result').innerHTML = '<p style="color: var(--text-muted);">Position QR code within frame</p>';
    
    if (!html5QrCode) {
        html5QrCode = new Html5Qrcode("qr-reader");
    }
    
    const config = { fps: 10, qrbox: { width: 250, height: 250 } };

    html5QrCode.start({ facingMode: "environment" }, config, (decodedText) => {
        handleScannerResult(decodedText);
    }).catch(err => {
        document.getElementById('scanner-result').innerHTML = `<p style="color: var(--danger);">Error accessing camera: ${err}</p>`;
    });
}

async function handleScannerResult(url) {
    try {
        const urlObj = new URL(url);
        const id = urlObj.searchParams.get('id');
        
        if (!id) throw new Error("Invalid QR Code Format");

        await html5QrCode.stop();
        document.getElementById('scanner-result').innerHTML = '<p>Verifying... ⏳</p>';
        
        // Fetch verification result via AJAX
        const res = await apiFetch(`verify.php?id=${id}&ajax=1`);
        
        if (res.success) {
            const isWarning = res.status === 'already_scanned';
            if (isWarning) logActivity(`Duplicate scan attempt for ${res.data.name}`, 'INFO');
            else logActivity(`Access granted for ${res.data.name}`, 'SUCCESS');

            document.getElementById('scanner-result').innerHTML = `
                <div style="background: ${isWarning ? 'rgba(245, 158, 11, 0.1)' : 'rgba(16, 185, 129, 0.1)'}; padding: 1.5rem; border-radius: 12px; margin-bottom: 1.5rem; text-align: left; border-left: 4px solid ${isWarning ? 'var(--warning)' : 'var(--accent)'}">
                    <h4 style="color: ${isWarning ? 'var(--warning)' : 'var(--accent)'}; margin-bottom: 0.5rem;">${isWarning ? '⚠️ ' + res.message : '✅ Access Granted!'}</h4>
                    <p style="font-weight: 700; margin: 0.5rem 0;">${res.data.name}</p>
                    <p style="font-size: 0.8rem; color: var(--text-muted); margin: 0;">${res.data.event_name}</p>
                    <p style="font-size: 0.8rem; color: var(--text-muted);">${res.data.vtu_no || 'N/A'}</p>
                </div>
                <button class="btn btn-primary" style="width:100%" onclick="startScanner()">Next Scan</button>
            `;
            loadAdminStats();
        } else {
            document.getElementById('scanner-result').innerHTML = `
                <div style="background: rgba(239, 68, 68, 0.1); padding: 1.5rem; border-radius: 12px; margin-bottom: 1.5rem; border-left: 4px solid var(--danger);">
                    <h4 style="color: var(--danger);">❌ Denied</h4>
                    <p style="font-size: 0.9rem;">${res.message}</p>
                </div>
                <button class="btn btn-primary" style="width:100%" onclick="startScanner()">Try Again</button>
            `;
        }
    } catch (err) {
        document.getElementById('scanner-result').innerHTML = `<p style="color: var(--danger);">Error: ${err.message}</p>`;
        setTimeout(startScanner, 3000);
    }
}

function stopScanner() {
    if (html5QrCode && html5QrCode.isScanning) {
        html5QrCode.stop().then(() => {
            document.getElementById('scanner-modal').style.display = 'none';
        });
    } else {
        document.getElementById('scanner-modal').style.display = 'none';
    }
}

function openFeedbackModal(id, name) {
    document.getElementById('feedback-event-id').value = id;
    document.getElementById('feedback-event-name').textContent = name;
    document.getElementById('feedback-modal').style.display = 'flex';
    setRating(0); // Reset stars
}

function setRating(val) {
    document.getElementById('feedback-rating').value = val;
    const stars = document.querySelectorAll('.star-btn');
    stars.forEach(s => {
        if (parseInt(s.dataset.val) <= val) {
            s.classList.add('active');
            s.textContent = '★';
        } else {
            s.classList.remove('active');
            s.textContent = '☆';
        }
    });
}

document.getElementById('feedback-form')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const res = await apiFetch('api/feedback.php', { method: 'POST', body: formData });
    
    if (res.success) {
        alert(res.message);
        document.getElementById('feedback-modal').style.display = 'none';
        e.target.reset();
    } else {
        alert(res.message);
    }
});

async function loadAuditLogs() {
    const container = document.getElementById('admin-audit-feed');
    container.innerHTML = '<p>Loading logs... ⏳</p>';
    
    const res = await apiFetch('api/admin_advanced.php?action=audit_logs');
    if (res.success) {
        if (res.data.length === 0) {
            container.innerHTML = '<p style="color:var(--text-muted)">No activity recorded yet.</p>';
            return;
        }
        container.innerHTML = res.data.map(log => `
            <div style="padding: 0.8rem; border-bottom: 1px solid rgba(255,255,255,0.05); transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.3rem;">
                    <span class="badge" style="background: ${log.action.includes('DELETE') ? 'var(--danger)' : 'var(--primary)'}; font-size: 0.6rem;">${log.action}</span>
                    <span style="font-size: 0.7rem; color: var(--text-muted);">${new Date(log.created_at).toLocaleString()}</span>
                </div>
                <div style="font-weight: 600;">${log.admin_name}</div>
                <div style="font-size: 0.8rem; color: var(--text-muted);">${log.details}</div>
            </div>
        `).join('');
    } else {
        container.innerHTML = `<p style="color:var(--danger)">Error: ${res.message}</p>`;
    }
}

async function loadFeedbackDetails(id, name) {
    const container = document.getElementById('admin-feedback-overview');
    container.innerHTML = '<p>Analyzing feedback... ⏳</p>';
    
    const res = await apiFetch(`api/admin_advanced.php?action=feedback_details&event_id=${id}`);
    if (res.success) {
        const stats = res.stats;
        const avg = parseFloat(stats.avg_rating || 0).toFixed(1);
        
        let html = `
            <div style="background: rgba(255,255,255,0.05); padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; text-align: center;">
                <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.3rem;">EVENT: ${name}</div>
                <div style="font-size: 2.5rem; font-weight: 800; color: #f59e0b;">${avg} <span style="font-size: 1.5rem;">★</span></div>
                <div style="font-size: 0.8rem; color: var(--text-muted);">${stats.count} total reviews</div>
            </div>
        `;
        
        if (res.data.length === 0) {
            html += '<p style="color:var(--text-muted); text-align:center;">No feedback received for this event yet.</p>';
        } else {
            html += res.data.map(f => `
                <div style="padding: 1rem; border-bottom: 1px solid rgba(255,255,255,0.05);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <span style="font-weight: 700; font-size: 0.9rem;">${f.user_name}</span>
                        <span style="color: #f59e0b; font-weight: 800;">${'★'.repeat(f.rating)}${'☆'.repeat(5-f.rating)}</span>
                    </div>
                    <p style="font-size: 0.85rem; color: var(--text-muted); margin: 0; font-style: italic;">"${f.comment || 'No comment provided'}"</p>
                    <div style="font-size: 0.7rem; color: var(--text-muted); margin-top: 0.5rem;">${new Date(f.created_at).toLocaleDateString()}</div>
                </div>
            `).join('');
        }
        container.innerHTML = html;
        container.closest('.glass').scrollIntoView({ behavior: 'smooth' });
    } else {
        alert(res.message);
    }
}

async function loadWishlist() {
    const container = document.getElementById('wishlist-container');
    if (!container) return;
    
    const res = await apiFetch('api/wishlist.php?action=list');
    if (res.success) {
        if (res.data.length === 0) {
            container.innerHTML = '<p style="color:var(--text-muted); font-size: 0.8rem;">No events saved yet.</p>';
            return;
        }
        container.innerHTML = res.data.map(e => `
            <div style="display: flex; gap: 1rem; align-items: center; margin-bottom: 1rem; padding-bottom: 0.8rem; border-bottom: 1px solid rgba(255,255,255,0.05);">
                <div style="width: 40px; height: 40px; background: var(--primary); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 800;">${e.name.charAt(0)}</div>
                <div style="flex: 1;">
                    <div style="font-size: 0.85rem; font-weight: 600;">${e.name}</div>
                    <div style="font-size: 0.7rem; color: var(--text-muted);">${new Date(e.datetime).toLocaleDateString()}</div>
                </div>
                <button class="btn btn-outline" style="padding: 0.2rem 0.5rem; font-size: 0.7rem;" onclick="toggleWishlist(${e.id}, this)">Remove</button>
            </div>
        `).join('');
    }
}

async function loadRecommendations() {
    const container = document.getElementById('recommended-events-grid');
    if (!container) return;

    // Get all events first
    const resAll = await apiFetch('api/events.php?action=list');
    if (!resAll.success) return;

    // Get user profile for department
    const resProf = await apiFetch('api/profile.php');
    if (!resProf.success) return;
    
    const userDept = resProf.data.department;
    const recommended = resAll.data.filter(e => e.department === userDept).slice(0, 3);

    if (recommended.length === 0) {
        container.innerHTML = '<p style="color:var(--text-muted); font-size: 0.8rem;">No specific matches for your department right now, but check out these trending fests!</p>';
        return;
    }

    container.innerHTML = recommended.map(e => `
        <div class="glass" style="padding: 1rem; border: 1px solid rgba(255,255,255,0.05); cursor: pointer;" onclick="document.getElementById('event-search').value='${e.name}'; filterEvents(); window.scrollTo(0, 400);">
            <div style="font-size: 0.7rem; color: var(--primary); font-weight: 700; margin-bottom: 0.3rem;">FOR ${e.department.toUpperCase()}</div>
            <div style="font-weight: 600; font-size: 0.9rem; margin-bottom: 0.5rem;">${e.name}</div>
            <div style="font-size: 0.75rem; color: var(--text-muted);">₹${e.price} • ${new Date(e.datetime).toLocaleDateString()}</div>
        </div>
    `).join('');
}

async function updateBadges() {
    const badgeContainer = document.getElementById('user-badges');
    if (!badgeContainer) return;

    const res = await apiFetch('api/bookings.php');
    if (res.success) {
        const attended = res.data.filter(b => b.is_checked_in == 1).length;
        const badges = badgeContainer.querySelectorAll('.badge-item');
        
        if (attended >= 1) {
            badges[0].classList.remove('locked');
            badges[0].style.filter = 'none';
            badges[0].style.opacity = '1';
            badges[0].style.background = 'rgba(16, 185, 129, 0.1)';
        }
        if (attended >= 5) {
            badges[1].classList.remove('locked');
            badges[1].style.filter = 'none';
            badges[1].style.opacity = '1';
            badges[1].style.background = 'rgba(245, 158, 11, 0.1)';
        }
    }
}

// Initialize student features if logged in
if (IS_LOGGED_IN && USER_ROLE !== 'admin') {
    (async () => {
        // Fetch wishlist status once to populate heart icons
        const res = await apiFetch('api/wishlist.php?action=status');
        if (res.success) window.userWishlist = res.data;
        
        loadWishlist();
        loadRecommendations();
        updateBadges();
    })();
}
