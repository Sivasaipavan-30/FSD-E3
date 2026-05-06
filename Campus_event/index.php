<?php include 'includes/header.php'; ?>

<!-- Main Hero -->
<header class="hero animate-fade">
    <div style="position: absolute; top: -100px; left: -100px; width: 300px; height: 300px; background: var(--primary); opacity: 0.1; filter: blur(100px); border-radius: 50%;"></div>
    <div style="position: absolute; bottom: -100px; right: -100px; width: 400px; height: 400px; background: var(--secondary); opacity: 0.1; filter: blur(100px); border-radius: 50%;"></div>
    
    <h1 style="background: linear-gradient(135deg, var(--text-main), var(--primary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Revolutionize Your <br> Campus Life</h1>
    <p>The smartest way to discover, book, and manage campus events. Experience technology-driven event management at your fingertips.</p>
    <div style="display: flex; gap: 1.5rem; justify-content: center; margin-top: 2rem;">
        <a href="#events" class="btn btn-primary" style="padding: 1rem 2.5rem; font-size: 1.1rem; border-radius: 50px;">Explore Events <span style="font-size: 1.2rem;">🚀</span></a>
        <a href="login.php" class="btn btn-outline" style="padding: 1rem 2.5rem; font-size: 1.1rem; border-radius: 50px;">Student Login</a>
    </div>
</header>

<!-- Feature list -->
<div class="container" style="margin-top: 4rem; margin-bottom: 8rem;">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2.5rem;">
        <div class="glass animate-fade" style="padding: 2.5rem; text-align: left;">
            <div style="width: 60px; height: 60px; background: rgba(99, 102, 241, 0.1); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin-bottom: 1.5rem;">🎫</div>
            <h3 style="margin-bottom: 1rem;">Instant Booking</h3>
            <p style="color: var(--text-muted); font-size: 0.95rem;">One-tap registration for technical fests, concerts, and cultural workshops across departments.</p>
        </div>
        <div class="glass animate-fade" style="padding: 2.5rem; text-align: left; animation-delay: 0.2s;">
            <div style="width: 60px; height: 60px; background: rgba(16, 185, 129, 0.1); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin-bottom: 1.5rem;">🤳</div>
            <h3 style="margin-bottom: 1rem;">Digital Tickets</h3>
            <p style="color: var(--text-muted); font-size: 0.95rem;">Forget paper. Access your unique QR code ticket anytime from your dashboard for quick entry.</p>
        </div>
        <div class="glass animate-fade" style="padding: 2.5rem; text-align: left; animation-delay: 0.4s;">
            <div style="width: 60px; height: 60px; background: rgba(236, 72, 153, 0.1); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin-bottom: 1.5rem;">📜</div>
            <h3 style="margin-bottom: 1rem;">Achievement Vault</h3>
            <p style="color: var(--text-muted); font-size: 0.95rem;">Earn and store digital certificates for your participation and achievements in campus fests.</p>
        </div>
    </div>
</div>

<div class="container" id="events" style="margin-top: 4rem;">
    <div class="animate-fade" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3rem; flex-wrap: wrap; gap: 1.5rem;">
        <div>
            <h2 style="font-size: clamp(2.5rem, 5vw, 3.5rem); margin-bottom: 0.5rem; color: var(--text-main); font-weight: 800; letter-spacing: -1.5px; line-height: 1.1;">Trending Events</h2>
            <p style="color: var(--text-muted); font-size: 1.1rem; max-width: 500px;">Explore the most anticipated technical and cultural festivals happening on campus this season.</p>
        </div>
        <div style="display: flex; flex-direction: column; gap: 1rem; align-items: flex-end; width: 100%; max-width: 500px;">
            <div class="form-group" style="width: 100%; margin: 0;">
                <input type="text" id="event-search" class="form-control" placeholder="🔍 Search for fests, concerts..." oninput="filterEvents()" style="border-radius: 30px; padding: 0.8rem 1.5rem;">
            </div>
            <div id="category-filters">
                <button class="btn btn-outline category-btn active" onclick="setCategory('All')">All</button>
                <!-- Departments will be added here dynamically -->
            </div>
        </div>
    </div>
    
    <div id="event-list" class="event-grid">
        <p style="color: var(--text-muted)">Loading events...</p>
    </div>
</div>

<?php if ($isLoggedIn && $userRole !== 'admin'): ?>
<!-- User Bookings Section -->
<div class="container" id="my-bookings" style="margin-top: 5rem; padding-bottom: 5rem;">
    <hr style="border: 0; border-top: 1px solid var(--glass-border); margin-bottom: 4rem;">
    
    <div style="display: grid; grid-template-columns: 1fr 300px; gap: 2rem; align-items: start;">
        <div>
            <h2 style="font-size: 2.5rem; margin-bottom: 2rem;">Booking History</h2>
            
            <div class="glass" style="padding: 2rem; margin-bottom: 2rem; border-top: 4px solid #6366f1;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h3 style="margin:0;">Recommended For You 💡</h3>
                    <div style="font-size: 0.8rem; color: var(--text-muted);" id="recommendation-reason">Based on your department</div>
                </div>
                <div id="recommended-events-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                    <p style="color:var(--text-muted); font-size: 0.8rem;">Loading recommendations...</p>
                </div>
            </div>

            <div class="glass" style="padding: 2rem; margin-bottom: 2rem;">
                <h3 style="margin-bottom: 1.5rem;">Upcoming Events</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th>Tickets</th>
                            <th>Date</th>
                            <th>Venue</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="upcoming-booking-list">
                        <tr><td colspan="5" style="text-align:center; color:var(--text-muted);">Loading...</td></tr>
                    </tbody>
                </table>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="glass" style="padding: 1.5rem;">
                    <h3 style="margin-bottom: 1rem; font-size: 1.1rem;">Your Wishlist ❤️</h3>
                    <div id="wishlist-container" style="max-height: 250px; overflow-y: auto;">
                        <p style="color:var(--text-muted); font-size: 0.8rem;">No events saved yet.</p>
                    </div>
                </div>
                <div class="glass" style="padding: 1.5rem; opacity: 0.8;">
                    <h3 style="margin-bottom: 1rem; font-size: 1.1rem; color: var(--text-muted);">Past Events</h3>
                    <table style="font-size: 0.85rem;">
                        <tbody id="past-booking-list">
                            <!-- Loaded via JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Student Dashboard Sidebar -->
        <aside style="display: grid; gap: 2rem; position: sticky; top: 100px;">
            <!-- Digital Campus Pass -->
            <div class="glass" style="padding: 2rem; text-align: center; background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(16, 185, 129, 0.1)); border: 1px solid rgba(255,255,255,0.1); overflow: hidden; position: relative;">
                <div style="position: absolute; top: -20px; right: -20px; width: 100px; height: 100px; background: var(--primary); opacity: 0.1; filter: blur(40px); border-radius: 50%;"></div>
                <div style="background: var(--primary); width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 2rem; border: 3px solid rgba(255,255,255,0.1);">👤</div>
                <h3 style="margin: 0; font-size: 1.4rem; color: var(--text-main);"><?php echo htmlspecialchars($userName); ?></h3>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1.5rem;"><?php echo htmlspecialchars($_SESSION['user_email'] ?? 'Student Portal'); ?></p>
                
                <div style="display: flex; justify-content: center; gap: 1rem; margin-bottom: 1.5rem;">
                    <div style="text-align: left;">
                        <div style="font-size: 0.65rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px;">Status</div>
                        <div style="font-size: 0.9rem; font-weight: 700; color: #10b981;">● Active</div>
                    </div>
                    <div style="text-align: left;">
                        <div style="font-size: 0.65rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px;">Access</div>
                        <div style="font-size: 0.9rem; font-weight: 700;">Full Access</div>
                    </div>
                </div>

                <div style="background: rgba(0,0,0,0.2); padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; border: 1px solid rgba(255,255,255,0.05);">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=USER-<?php echo $_SESSION['user_id']; ?>" alt="User QR" style="width: 80px; height: 80px; opacity: 0.8;">
                    <div style="font-size: 0.65rem; margin-top: 0.5rem; color: var(--text-muted);">SCAN FOR QUICK PROFILE</div>
                </div>
                <button class="btn btn-outline" style="width: 100%; font-size: 0.8rem; padding: 0.6rem;" onclick="openProfileModal()">Edit Profile</button>
            </div>

            <!-- Next Event Countdown -->
            <div id="next-event-card" class="glass" style="padding: 1.5rem; border-left: 4px solid var(--accent); display: none;">
                <h4 style="margin: 0 0 1rem; font-size: 0.9rem; display: flex; justify-content: space-between; align-items: center;">
                    Your Next Event 
                    <span style="background: var(--accent); color: #fff; font-size: 0.6rem; padding: 0.2rem 0.5rem; border-radius: 10px;">UPCOMING</span>
                </h4>
                <div id="next-event-name" style="font-weight: 700; font-size: 1.1rem; margin-bottom: 0.5rem;">Loading...</div>
                <div id="next-event-time" style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 1rem;">---</div>
                <div style="display: flex; gap: 0.5rem;">
                    <button class="btn btn-primary" style="flex:1; font-size: 0.75rem; padding: 0.5rem;" onclick="document.getElementById('my-bookings').scrollIntoView({behavior:'smooth'})">View Ticket</button>
                </div>
            </div>
            
            <div style="display: grid; gap: 1rem;">
                <div style="background: rgba(255,255,255,0.05); padding: 1.2rem; border-radius: 12px; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="font-size: 0.7rem; color: var(--text-muted); text-transform: uppercase;">Total Tickets</div>
                        <h2 id="stat-total-tickets" style="margin: 0; color: var(--accent);">0</h2>
                    </div>
                    <div style="font-size: 2rem; opacity: 0.2;">🎟️</div>
                </div>
                <div style="background: rgba(255,255,255,0.05); padding: 1.2rem; border-radius: 12px; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="font-size: 0.7rem; color: var(--text-muted); text-transform: uppercase;">Events Attended</div>
                        <h2 id="stat-total-events" style="margin: 0;">0</h2>
                    </div>
                    <div style="font-size: 2rem; opacity: 0.2;">🎓</div>
                </div>
            </div>

            <!-- Achievement Badges -->
            <div class="glass" style="padding: 1.5rem;">
                <h4 style="margin: 0 0 1rem; font-size: 0.8rem; color: var(--text-muted);">BADGES & ACHIEVEMENTS</h4>
                <div id="user-badges" style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                    <div class="badge-item locked" title="Attend your first event" style="width: 40px; height: 40px; background: rgba(255,255,255,0.05); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; filter: grayscale(1); opacity: 0.5;">🐣</div>
                    <div class="badge-item locked" title="Attend 5 events" style="width: 40px; height: 40px; background: rgba(255,255,255,0.05); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; filter: grayscale(1); opacity: 0.5;">🌟</div>
                    <div class="badge-item locked" title="Give feedback" style="width: 40px; height: 40px; background: rgba(255,255,255,0.05); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; filter: grayscale(1); opacity: 0.5;">✍️</div>
                </div>
            </div>

            <!-- Certificate Repository Link -->
            <div class="glass" style="margin-top: 1rem; padding: 1rem; border: 1px solid rgba(16, 185, 129, 0.2); text-align: center; background: rgba(16, 185, 129, 0.05);">
                <p style="font-size: 0.8rem; margin-bottom: 0.8rem;">🎉 <b>Achievement Unlocked?</b></p>
                <button class="btn btn-outline" style="width: 100%; border-color: #10b981; color: #10b981; font-size: 0.75rem;" onclick="openCertificateVault()">
                    📜 Certificate Vault
                </button>
            </div>
        </aside>
    </div>
</div>
<?php endif; ?>





<?php if ($userRole === 'admin'): ?>
<!-- Admin Dashboard Integration Section -->
<div class="container" style="margin-top: 5rem;">
    <hr style="border: 0; border-top: 1px solid var(--glass-border); margin-bottom: 4rem;">
    <h2 style="font-size: 2.5rem; margin-bottom: 2rem;">Admin Dashboard</h2>

    <!-- KPI Command Center -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
        <div class="glass" style="padding: 1.5rem; border-left: 4px solid var(--primary);">
            <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.5rem;">Total Revenue</div>
            <h2 id="admin-stat-revenue" style="margin: 0; color: var(--primary);">₹0</h2>
        </div>
        <div class="glass" style="padding: 1.5rem; border-left: 4px solid var(--accent);">
            <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.5rem;">Tickets Sold</div>
            <h2 id="admin-stat-tickets" style="margin: 0; color: var(--accent);">0</h2>
        </div>
        <div class="glass" style="padding: 1.5rem; border-left: 4px solid #10b981;">
            <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.5rem;">Unique Students</div>
            <h2 id="admin-stat-engagement" style="margin: 0; color: #10b981;">0</h2>
        </div>
        <div class="glass" style="padding: 1.5rem; border-left: 4px solid #f59e0b;">
            <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.5rem;">Leading Department</div>
            <h2 id="admin-stat-dept" style="margin: 0; font-size: 1.2rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">N/A</h2>
        </div>
        <div class="glass" style="padding: 1.5rem; border-left: 4px solid #6366f1;">
            <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.5rem;">Trending Event</div>
            <h2 id="admin-stat-trending" style="margin: 0; font-size: 1.2rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">None</h2>
        </div>
    </div>
    
    <!-- Analytics Charts -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
        <div class="glass" style="padding: 2rem;">
            <h4 style="margin-bottom: 1.5rem;">Revenue by Event</h4>
            <div style="height: 300px;"><canvas id="revenueChart"></canvas></div>
        </div>
        <div class="glass" style="padding: 2rem;">
            <h4 style="margin-bottom: 1.5rem;">Booking Trends</h4>
            <div style="height: 300px;"><canvas id="trendsChart"></canvas></div>
        </div>
        <div class="glass" style="padding: 2rem;">
            <h4 style="margin-bottom: 1.5rem;">Departmental Engagement</h4>
            <div style="height: 300px;"><canvas id="deptChart"></canvas></div>
        </div>
    </div>

    <!-- Advanced Admin Tools -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 3rem;">
        <div class="glass" style="padding: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="margin:0;">Recent Activity Log</h3>
                <button class="btn btn-outline" style="font-size: 0.7rem; padding: 0.3rem 0.6rem;" onclick="loadAuditLogs()">Refresh Log</button>
            </div>
            <div id="admin-audit-feed" style="max-height: 300px; overflow-y: auto; font-size: 0.85rem;">
                <p style="color:var(--text-muted)">Click refresh to load system logs...</p>
            </div>
        </div>
        <div class="glass" style="padding: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="margin:0;">Event Feedback Analysis</h3>
            </div>
            <div id="admin-feedback-overview" style="max-height: 300px; overflow-y: auto;">
                <p style="color:var(--text-muted)">Select an event from management table to view ratings.</p>
            </div>
        </div>
    </div>

    <div style="display: flex; gap: 1.5rem; margin-bottom: 3rem; flex-wrap: wrap;">
        <button class="btn btn-primary" onclick="showEventForm()">+ Create New Event</button>
        <button class="btn btn-outline" onclick="loadAdminBookings()">View All Bookings</button>
        <button class="btn btn-outline" onclick="loadAdminUsers()">Manage Users</button>
        <button class="btn btn-outline" onclick="showBroadcastModal()">📢 Broadcast Message</button>
        <button class="btn btn-outline" onclick="startScanner()">📷 Launch Scanner</button>
    </div>

    <!-- Event Management Table -->
    <div class="glass" style="padding: 2rem; margin-bottom: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="margin:0;">Manage Events</h3>
            <div style="display: flex; gap: 1rem;">
                <input type="text" placeholder="Filter events..." class="form-control" style="width: 200px; padding: 0.4rem 1rem; font-size: 0.8rem;" oninput="filterAdminEvents(this.value)">
                <button class="btn btn-outline" style="font-size: 0.8rem; padding: 0.4rem 1rem;" onclick="exportBookingsToCSV()">Export All CSV</button>
            </div>
        </div>
        <table id="admin-event-table">
            <thead>
                <tr>
                    <th>Event Name</th>
                    <th>Date</th>
                    <th>Venue</th>
                    <th>Seats</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="admin-event-list">
                <!-- Loaded via JS -->
            </tbody>
        </table>
    </div>

    <!-- User Management (Initially hidden) -->
    <div id="admin-user-section" class="glass" style="padding: 2rem; display:none; margin-bottom: 5rem;">
        <h3 style="margin-bottom: 1.5rem;">Global User Directory</h3>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Joined Date</th>
                </tr>
            </thead>
            <tbody id="admin-user-list">
                <!-- Loaded via JS -->
            </tbody>
        </table>
    </div>

    <!-- Booking Management (Initially hidden) -->
    <div id="admin-booking-section" class="glass" style="padding: 2rem; display:none;">
        <h3>All Bookings</h3>
        <table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>Event</th>
                    <th>Tickets</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody id="admin-booking-list">
                <!-- Loaded via JS -->
            </tbody>
        </table>
    </div>
</div>

<!-- Add/Edit Event Modal -->
<div id="event-modal" class="modal-overlay" style="display:none;">
    <div class="modal-content glass" style="max-width: 500px;">
        <h3 id="event-modal-title">Create Event</h3>
        <span style="position: absolute; top: 1rem; right: 1.5rem; cursor: pointer; color: var(--text-muted);" onclick="closeEventModal()">&times; Close</span>
        <form id="event-form" style="margin-top: 1rem;">
            <input type="hidden" name="id" id="event-edit-id">
            <div class="form-group">
                <label>Event Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label>Department</label>
                    <input type="text" name="department" class="form-control">
                </div>
                <div class="form-group">
                    <label>DateTime</label>
                    <input type="datetime-local" name="datetime" class="form-control" required>
                </div>
            </div>
            <div class="form-group">
                <label>Venue</label>
                <input type="text" name="venue" class="form-control" required>
            </div>
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label>Total Seats</label>
                    <input type="number" name="seats" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Price (₹)</label>
                    <input type="number" step="0.01" name="price" class="form-control" required>
                </div>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
            </div>
            <div style="display: flex; gap: 1rem;">
                <button type="button" class="btn btn-outline" style="flex:1" onclick="closeEventModal()">Cancel</button>
                <button type="submit" class="btn btn-primary" style="flex:1">Save Event</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Booking Modal (Hidden by default) -->
<div id="booking-modal" class="modal-overlay" style="display:none;">
    <div class="modal-content glass" style="max-width: 450px;">
        <h3 id="modal-event-name">Book Tickets</h3>
        <p id="modal-event-info" style="color: var(--text-muted); margin-bottom: 1.5rem;"></p>
        <div id="modal-alert"></div>
        
        <!-- Step 1: Ticket Selection -->
        <form id="booking-form">
            <input type="hidden" id="modal-event-id">
            <div class="form-group">
                <label>Number of Tickets</label>
                <input type="number" id="booking-tickets" class="form-control" value="1" min="1" max="10">
            </div>
            <div style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <span>Total Price:</span>
                <span id="modal-total-price" style="font-size: 1.5rem; font-weight: 700; color: var(--accent);">₹0.00</span>
            </div>
            <div style="display: flex; gap: 1rem;">
                <button type="button" class="btn btn-outline" style="flex:1" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary" style="flex:1">Next: Student Details</button>
            </div>
        </form>

        <!-- Step 2: Student Details (NEW) -->
        <form id="attendee-form" style="display:none;">
            <div id="attendee-inputs" style="max-height: 400px; overflow-y: auto; margin-bottom: 1.5rem; padding-right: 0.5rem;">
                <!-- Dynamically generated fields -->
            </div>
            <div style="display: flex; gap: 1rem;">
                <button type="button" class="btn btn-outline" style="flex:1" onclick="backToTickets()">Back</button>
                <button type="submit" class="btn btn-primary" style="flex:1">Proceed to Payment</button>
            </div>
        </form>

        <!-- Step 2: Mock Payment -->
        <form id="payment-form" style="display:none;">
            <div style="background: rgba(255,255,255,0.05); padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; text-align: center;">
                <span style="font-size: 0.9rem; color: var(--text-muted);">Amount to Pay:</span><br>
                <span id="payment-total-display" style="font-size: 1.5rem; font-weight: 700; color: var(--accent);">₹0.00</span>
            </div>

            <!-- Payment Method Toggle -->
            <div style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem; background: rgba(0,0,0,0.2); padding: 0.3rem; border-radius: 10px;">
                <button type="button" id="btn-card" class="btn" style="flex:1; font-size: 0.8rem; background: var(--primary);" onclick="switchPayment('card')">Credit Card</button>
                <button type="button" id="btn-upi" class="btn btn-outline" style="flex:1; font-size: 0.8rem; border: none;" onclick="switchPayment('upi')">UPI / QR</button>
            </div>
            
            <!-- Card Section -->
            <div id="payment-card-section">
                <div class="form-group">
                    <label>Card Number</label>
                    <input type="text" class="form-control" placeholder="1234 5678 1234 5678" maxlength="19">
                </div>
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label>Expiry Date</label>
                        <input type="text" class="form-control" placeholder="MM/YY" maxlength="5">
                    </div>
                    <div class="form-group">
                        <label>CVV</label>
                        <input type="password" class="form-control" placeholder="•••" maxlength="3">
                    </div>
                </div>
                <div class="form-group">
                    <label>Cardholder Name</label>
                    <input type="text" class="form-control" placeholder="JOHN DOE">
                </div>
            </div>

            <!-- UPI Section -->
            <div id="payment-upi-section" style="display:none; text-align: center;">
                <div style="background: white; width: 150px; height: 150px; margin: 0 auto 1.5rem; padding: 10px; border-radius: 8px;">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=130x130&data=example" alt="QR Code" style="width: 130px; height: 130px;">
                </div>
                <div class="form-group" style="text-align: left;">
                    <label>Enter UPI ID</label>
                    <input type="text" class="form-control" placeholder="username@upi">
                </div>
                <p style="color: var(--text-muted); font-size: 0.8rem; margin-bottom: 1.5rem;">OR scan the QR code using any UPI app</p>
            </div>

            <div style="display: flex; gap: 1rem;">
                <button type="button" class="btn btn-outline" style="flex:1" onclick="backToTickets()">Back</button>
                <button type="submit" class="btn btn-primary" style="flex:1">Pay Now</button>
            </div>
        </form>

        <!-- Processing State (Overlay inside modal) -->
        <div id="payment-processing" style="display:none; text-align: center; padding: 2rem;">
            <div class="spinner" style="border: 4px solid var(--glass-border); border-top: 4px solid var(--primary); border-radius: 50%; width: 50px; height: 50px; animation: spin 1s linear infinite; margin: 0 auto 1.5rem;"></div>
            <h4>Processing Transaction...</h4>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Please do not refresh the page.</p>
        </div>
    </div>
</div>

<!-- Profile Modal -->
<div id="profile-modal" class="modal-overlay" style="display:none;">
    <div class="modal-content glass" style="max-width: 400px;">
        <h3>My Profile</h3>
        <form id="profile-form">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" id="profile-name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Department</label>
                <input type="text" name="department" id="profile-dept" class="form-control" placeholder="e.g. CSE">
            </div>
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="button" class="btn btn-outline" style="flex:1" onclick="document.getElementById('profile-modal').style.display='none'">Cancel</button>
                <button type="submit" class="btn btn-primary" style="flex:1">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Certificate Vault Modal -->
<div id="cert-modal" class="modal-overlay" style="display:none;">
    <div class="modal-content glass" style="max-width: 600px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="margin:0;">Achievement Vault</h3>
            <span style="cursor:pointer; font-size: 1.5rem;" onclick="document.getElementById('cert-modal').style.display='none'">&times;</span>
        </div>
        <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 2rem;">Certificates are available for all participants who were successfully checked in via QR code during the event.</p>
        
        <div style="max-height: 400px; overflow-y: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--glass-border); text-align: left; font-size: 0.8rem;">
                        <th style="padding-bottom: 1rem;">Student Name</th>
                        <th style="padding-bottom: 1rem;">Event</th>
                        <th style="padding-bottom: 1rem; text-align: center;">Certificate</th>
                    </tr>
                </thead>
                <tbody id="cert-list">
                    <!-- Loaded via JS -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Event Feedback Modal -->
<div id="feedback-modal" class="modal-overlay" style="display:none;">
    <div class="modal-content glass" style="max-width: 450px;">
        <h3 style="margin-top: 0;">Rate Your Experience</h3>
        <p id="feedback-event-name" style="color: var(--primary); font-weight: 700; margin-bottom: 1.5rem;"></p>
        
        <form id="feedback-form">
            <input type="hidden" id="feedback-event-id" name="event_id">
            <div class="form-group">
                <label>Rating (1-5 Stars)</label>
                <div style="display: flex; gap: 0.5rem; font-size: 2rem; margin: 1rem 0; justify-content: center;">
                    <span class="star-btn" data-val="1" onclick="setRating(1)" style="cursor:pointer;">☆</span>
                    <span class="star-btn" data-val="2" onclick="setRating(2)" style="cursor:pointer;">☆</span>
                    <span class="star-btn" data-val="3" onclick="setRating(3)" style="cursor:pointer;">☆</span>
                    <span class="star-btn" data-val="4" onclick="setRating(4)" style="cursor:pointer;">☆</span>
                    <span class="star-btn" data-val="5" onclick="setRating(5)" style="cursor:pointer;">☆</span>
                </div>
                <input type="hidden" id="feedback-rating" name="rating" required>
            </div>
            <div class="form-group">
                <label>Your Comments</label>
                <textarea name="comment" class="form-control" rows="3" placeholder="What did you like or dislike?"></textarea>
            </div>
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="button" class="btn btn-outline" style="flex:1" onclick="document.getElementById('feedback-modal').style.display='none'">Cancel</button>
                <button type="submit" class="btn btn-primary" style="flex:1">Submit Review</button>
            </div>
        </form>
    </div>
</div>

<style>
.star-btn.active { color: #f59e0b; font-weight: 800; text-shadow: 0 0 10px rgba(245, 158, 11, 0.3); }
</style>

<style>
@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
</style>

<style>
.modal-overlay {
    position: fixed;
    top:0; left:0; width:100%; height:100%;
    background: rgba(0,0,0,0.8);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 2000;
}
.modal-content {
    width: 90%;
    max-width: 400px;
    padding: 2.5rem;
    }
    
    #help-fab:hover {
        transform: scale(1.1) rotate(15deg);
    }
</style>

<?php include 'includes/footer.php'; ?>
