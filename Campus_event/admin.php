<?php include 'includes/header.php'; 
if ($userRole !== 'admin') {
    header('Location: index.php');
    exit;
}
?>

<div class="container" style="margin-top: 3rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3rem;">
        <div>
            <h1 style="font-size: 2.5rem; letter-spacing: -1px; margin-bottom: 0.5rem;">Admin Command Center</h1>
            <p style="color: var(--text-muted);">Manage events, verify attendance, and communicate with students.</p>
        </div>
        <div style="display: flex; gap: 1rem;">
            <button class="btn btn-outline" onclick="exportBookingsToCSV()">📥 Export Bookings</button>
            <button class="btn btn-primary" onclick="showEventForm()">+ Create Event</button>
        </div>
    </div>

    <!-- KPI Section -->
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
            <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.5rem;">QR Check-ins</div>
            <h2 id="admin-stat-checkins" style="margin: 0; color: #f59e0b;">0</h2>
        </div>
        <div class="glass" style="padding: 1.5rem; border-left: 4px solid var(--accent);">
            <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.5rem;">Top Dept</div>
            <h2 id="admin-stat-dept" style="margin: 0; color: var(--accent); font-size: 1.2rem;">Loading...</h2>
        </div>
    </div>

    <!-- Charts Section -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 3rem;">
        <div class="glass" style="padding: 2rem;">
            <h3 style="margin-bottom: 1.5rem;">Revenue by Event</h3>
            <div style="height: 300px;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
        <div class="glass" style="padding: 2rem;">
            <h3 style="margin-bottom: 1.5rem;">Department Engagement</h3>
            <div style="height: 300px;">
                <canvas id="deptChart"></canvas>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 350px; gap: 2rem; align-items: start;">
        <div>
            <!-- Event Management Table -->
            <div class="glass" style="padding: 2rem; margin-bottom: 2rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h3 style="margin: 0;">Active Events</h3>
                    <div class="form-group" style="margin: 0; width: 200px;">
                        <input type="text" class="form-control" placeholder="Search events..." oninput="filterAdminEvents(this.value)">
                    </div>
                </div>
                <table id="admin-event-table">
                    <thead>
                        <tr>
                            <th>Event Name</th>
                            <th>Date</th>
                            <th>Venue</th>
                            <th>Capacity</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="admin-event-list">
                        <!-- Loaded via JS -->
                    </tbody>
                </table>
            </div>

            <!-- Booking Management (Collapsible) -->
            <div class="glass" style="padding: 2rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; cursor: pointer;" onclick="const sec = document.getElementById('admin-booking-section'); if(sec.style.display === 'none') { loadAdminBookings(); sec.style.display = 'block'; } else { sec.style.display = 'none'; }">
                    <h3 style="margin: 0;">Global Bookings</h3>
                    <span style="color: var(--primary);">View All →</span>
                </div>
                <div id="admin-booking-section" style="display:none; margin-top: 1.5rem;">
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

            <!-- User Directory (Collapsible) -->
            <div id="admin-user-section" class="glass" style="padding: 2rem; display: none; margin-top: 2rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h3 style="margin: 0;">User Directory</h3>
                    <button class="btn btn-outline" style="padding: 0.3rem 0.6rem; font-size: 0.8rem;" onclick="document.getElementById('admin-user-section').style.display='none'">Close</button>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>User Details</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Joined On</th>
                        </tr>
                    </thead>
                    <tbody id="admin-user-list">
                        <!-- Loaded via JS -->
                    </tbody>
                </table>
            </div>
        </div>

        <aside style="display: grid; gap: 2rem;">
            <!-- QR Attendance Scanner -->
            <div class="glass" style="padding: 2rem; text-align: center; border: 2px dashed var(--primary);">
                <div style="font-size: 3rem; margin-bottom: 1rem;">📷</div>
                <h4>QR Attendance</h4>
                <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 1.5rem;">Use your camera to scan student tickets and verify entry.</p>
                <button class="btn btn-primary" style="width: 100%;" onclick="startScanner()">Open Scanner</button>
            </div>

            <!-- Broadcast Section -->
            <div class="glass" style="padding: 2rem;">
                <h4>Email Broadcast</h4>
                <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 1.5rem;">Send a mass update to all students registered for a specific event.</p>
                <button class="btn btn-outline" style="width: 100%;" onclick="showBroadcastModal()">Compose Broadcast</button>
            </div>

            <!-- User Management Quick Link -->
            <div class="glass" style="padding: 2rem;">
                <h4>User Directory</h4>
                <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 1.5rem;">View and manage all registered accounts.</p>
                <button class="btn btn-outline" style="width: 100%;" onclick="loadAdminUsers()">Manage Users</button>
            </div>

            <!-- Live Feed -->
            <div class="glass" style="padding: 1.5rem;">
                <h4 style="margin-bottom: 1rem;">System Logs</h4>
                <div id="admin-activity-feed" style="font-size: 0.75rem; color: var(--text-muted); max-height: 200px; overflow-y: auto;">
                    <div style="margin-bottom: 0.5rem; padding-bottom: 0.5rem; border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <span style="color: var(--accent);">[SYSTEM]</span> Dashboard metrics updated.
                    </div>
                </div>
            </div>
        </aside>
    </div>
</div>

<!-- Broadcast Modal -->
<div id="broadcast-modal" class="modal-overlay" style="display:none;">
    <div class="modal-content glass" style="max-width: 600px;">
        <h3>Event Broadcast</h3>
        <p style="color: var(--text-muted); margin-bottom: 1.5rem;">This message will be sent to ALL students registered for the selected event.</p>
        <form id="broadcast-form">
            <div class="form-group">
                <label>Select Event</label>
                <select name="event_id" id="broadcast-event-id" class="form-control" required>
                    <!-- Populated via JS -->
                </select>
            </div>
            <div class="form-group">
                <label>Subject</label>
                <input type="text" name="subject" class="form-control" placeholder="e.g. Important Update: Venue Changed" required>
            </div>
            <div class="form-group">
                <label>Message</label>
                <textarea name="message" class="form-control" rows="6" placeholder="Write your message here..." required></textarea>
            </div>
            <div style="display: flex; gap: 1rem;">
                <button type="button" class="btn btn-outline" style="flex:1" onclick="document.getElementById('broadcast-modal').style.display='none'">Cancel</button>
                <button type="submit" class="btn btn-primary" style="flex:1">Send Broadcast 🚀</button>
            </div>
        </form>
    </div>
</div>

<!-- Scanner Modal -->
<div id="scanner-modal" class="modal-overlay" style="display:none;">
    <div class="modal-content glass" style="max-width: 500px; text-align: center;">
        <h3 style="margin-bottom: 1rem;">Scan Ticket</h3>
        <div id="qr-reader" style="width: 100%; min-height: 300px; background: #000; border-radius: 12px; overflow: hidden; margin-bottom: 1.5rem;"></div>
        <div id="scanner-result"></div>
        <button class="btn btn-outline" onclick="stopScanner()">Close Scanner</button>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>

<!-- Add/Edit Event Modal -->
<div id="event-modal" class="modal-overlay" style="display:none;">
    <div class="modal-content glass" style="max-width: 500px;">
        <h3 id="event-modal-title">Create Event</h3>
        <input type="hidden" id="event-edit-id">
        <form id="event-form">
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



<?php include 'includes/footer.php'; ?>
