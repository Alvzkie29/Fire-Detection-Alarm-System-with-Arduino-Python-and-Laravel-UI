<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fire Monitoring System - Floor Plan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg: #0f172a;
            --panel: #1e293b;
            --card-bg: #334155;
            --text: #f1f5f9;
            --muted: #94a3b8;
            --fire: #ef4444;
            --safe: #10b981;
            --door-open: #22c55e;
            --door-closed: #64748b;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: var(--bg);
            color: var(--text);
            padding: 20px;
            min-height: 100vh;
        }
        .container { max-width: 1400px; margin: 0 auto; }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 20px;
            background: var(--panel);
            border-radius: 16px;
        }
        .logo { display: flex; align-items: center; gap: 15px; }
        .logo-icon { font-size: 2.5rem; color: var(--fire); }
        .logo-text h1 {
            font-size: 1.8rem;
            background: linear-gradient(90deg, #ef4444, #f97316);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .logo-text p { color: var(--muted); font-size: 0.9rem; }
        .status-bar { display: flex; gap: 20px; align-items: center; }
        .update-info { text-align: right; }
        .update-info .label { font-size: 0.85rem; color: var(--muted); margin-bottom: 5px; }
        .update-info .time { font-size: 1.2rem; font-weight: 600; color: var(--safe); font-family: monospace; }
        .system-status { display: flex; gap: 10px; }
        .status-badge {
            padding: 8px 16px; border-radius: 20px; font-size: 0.9rem;
            font-weight: 600; display: flex; align-items: center; gap: 8px;
        }
        .status-badge.active { background: rgba(16,185,129,0.2); color: var(--safe); border: 1px solid var(--safe); }
        .status-badge.inactive { background: rgba(239,68,68,0.2); color: var(--fire); border: 1px solid var(--fire); }

        .content { display: grid; grid-template-columns: 1fr 350px; gap: 30px; }

        .floorplan-container {
            background: var(--panel); border-radius: 16px; padding: 25px;
        }
        .floorplan-title {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 25px; padding-bottom: 15px;
            border-bottom: 2px solid rgba(255,255,255,0.1);
        }
        .floorplan-title h2 { font-size: 1.5rem; }
        .refresh-btn {
            background: var(--card-bg); color: var(--text); border: none;
            padding: 10px 20px; border-radius: 10px; cursor: pointer;
            display: flex; align-items: center; gap: 8px; font-weight: 500;
            transition: all 0.3s ease;
        }
        .refresh-btn:hover { background: #475569; }
        .refresh-btn.refreshing i { animation: spin 1s linear infinite; }
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

        .floorplan-svg {
            width: 100%; height: 500px;
            background: linear-gradient(135deg, #1e293b, #0f172a);
            border-radius: 12px; padding: 20px;
            border: 2px solid rgba(255,255,255,0.1);
        }
        svg { width: 100%; height: 100%; }

        .room { transition: all 0.5s ease; }
        .room-safe { fill: var(--safe); fill-opacity: 0.15; stroke: var(--safe); stroke-width: 2; }
        .room-fire { fill: var(--fire); fill-opacity: 0.25; stroke: var(--fire); stroke-width: 3; animation: fire-pulse 1.5s infinite; }
        .room-unknown { fill: var(--muted); fill-opacity: 0.1; stroke: var(--muted); stroke-width: 2; stroke-dasharray: 5,5; }
        @keyframes fire-pulse {
            0%,100% { stroke-width: 3; fill-opacity: 0.25; }
            50% { stroke-width: 5; fill-opacity: 0.4; }
        }
        .room-label { font-size: 14px; font-weight: 600; fill: var(--text); text-anchor: middle; pointer-events: none; }
        .room-sensor { font-size: 12px; fill: var(--muted); text-anchor: middle; pointer-events: none; }
        .room-status { font-size: 16px; font-weight: 700; text-anchor: middle; pointer-events: none; }
        .room-status-safe { fill: var(--safe); }
        .room-status-fire { fill: var(--fire); animation: text-blink 1s infinite; }
        .room-status-unknown { fill: var(--muted); }
        @keyframes text-blink { 0%,100% { opacity: 1; } 50% { opacity: 0.5; } }
        .door { stroke: var(--door-closed); stroke-width: 4; stroke-linecap: round; transition: all 0.5s ease; }
        .door-open { stroke: var(--door-open); }
        .door-text { font-size: 10px; fill: var(--muted); text-anchor: middle; }

        .side-panel { display: flex; flex-direction: column; gap: 25px; }
        .room-cards { display: flex; flex-direction: column; gap: 20px; }
        .room-card {
            background: var(--panel); border-radius: 16px; padding: 25px;
            border-left: 5px solid var(--muted); transition: all 0.3s ease;
        }
        .room-card.safe { border-left-color: var(--safe); }
        .room-card.fire { border-left-color: var(--fire); animation: card-pulse 1.5s infinite; }
        @keyframes card-pulse {
            0%,100% { box-shadow: 0 0 0 rgba(239,68,68,0); }
            50% { box-shadow: 0 0 20px rgba(239,68,68,0.5); }
        }
        .room-card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .room-card-title { display: flex; align-items: center; gap: 12px; }
        .room-card-icon {
            width: 45px; height: 45px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; color: white;
        }
        .room1-icon { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
        .room2-icon { background: linear-gradient(135deg, #10b981, #059669); }
        .room-card-name h3 { font-size: 1.1rem; margin-bottom: 3px; }
        .room-card-name p { font-size: 0.85rem; color: var(--muted); }
        .room-card-status {
            padding: 6px 15px; border-radius: 20px; font-size: 0.85rem;
            font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;
        }
        .room-card-status.safe { background: rgba(16,185,129,0.2); color: var(--safe); }
        .room-card-status.fire { background: rgba(239,68,68,0.2); color: var(--fire); animation: status-blink 1s infinite; }
        @keyframes status-blink { 0%,100% { opacity: 1; } 50% { opacity: 0.6; } }
        .room-card-details { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 15px; }
        .detail-item { background: var(--card-bg); padding: 12px; border-radius: 10px; text-align: center; }
        .detail-label { font-size: 0.78rem; color: var(--muted); margin-bottom: 5px; }
        .detail-value { font-size: 1rem; font-weight: 600; font-family: monospace; }
        .detail-value.fire-value { color: var(--fire); }
        .detail-value.safe-value { color: var(--safe); }

        .system-log { background: var(--panel); border-radius: 16px; padding: 25px; flex-grow: 1; }
        .log-title { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .log-title h3 { font-size: 1.1rem; }
        .log-entries { max-height: 220px; overflow-y: auto; }
        .log-entry { display: flex; align-items: flex-start; gap: 10px; padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,0.07); }
        .log-entry:last-child { border-bottom: none; }
        .log-icon { width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; flex-shrink: 0; }
        .log-icon.fire { background: rgba(239,68,68,0.2); color: var(--fire); }
        .log-icon.safe { background: rgba(16,185,129,0.2); color: var(--safe); }
        .log-icon.update { background: rgba(59,130,246,0.2); color: #3b82f6; }
        .log-message { font-size: 0.88rem; margin-bottom: 2px; }
        .log-time { font-size: 0.75rem; color: var(--muted); }

        .alert-banner {
            position: fixed; top: 20px; left: 50%; transform: translateX(-50%);
            background: linear-gradient(90deg, #ef4444, #dc2626);
            color: white; padding: 18px 28px; border-radius: 12px;
            box-shadow: 0 8px 30px rgba(239,68,68,0.5);
            display: none; align-items: center; gap: 15px;
            z-index: 1000; animation: slide-down 0.4s ease;
            min-width: 350px;
        }
        @keyframes slide-down {
            from { transform: translate(-50%, -120%); opacity: 0; }
            to { transform: translate(-50%, 0); opacity: 1; }
        }
        .alert-banner.show { display: flex; }
        .alert-icon { font-size: 1.8rem; }
        .alert-content h4 { font-size: 1rem; font-weight: 700; margin-bottom: 3px; }
        .alert-content p { font-size: 0.85rem; opacity: 0.9; }
        .alert-rooms { font-size: 0.8rem; margin-top: 4px; font-weight: 600; }
        .alert-close {
            background: rgba(255,255,255,0.25); border: none; color: white;
            width: 28px; height: 28px; border-radius: 50%; cursor: pointer;
            display: flex; align-items: center; justify-content: center; margin-left: auto;
        }

        @media (max-width: 1100px) {
            .content { grid-template-columns: 1fr; }
            .floorplan-svg { height: 380px; }
        }
        @media (max-width: 700px) {
            .header { flex-direction: column; gap: 15px; }
            .room-card-details { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <div id="alertBanner" class="alert-banner">
        <div class="alert-icon"><i class="fas fa-fire"></i></div>
        <div class="alert-content">
            <h4>FIRE DETECTED!</h4>
            <p>Immediate action required</p>
            <div class="alert-rooms" id="alertRooms"></div>
        </div>
        <button class="alert-close" onclick="dashboard.dismissAlert()">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="container">

        <div class="header">
            <div class="logo">
                <div class="logo-icon"><i class="fas fa-fire-extinguisher"></i></div>
                <div class="logo-text">
                    <h1>Fire Monitoring System</h1>
                    <p>Real-time floor plan monitoring with sensor data</p>
                </div>
            </div>
            <div class="status-bar">
                <div class="update-info">
                    <div class="label">Last Update</div>
                    <div class="time" id="lastUpdateTime">--:--:--</div>
                </div>
                <div class="system-status">
                    <div class="status-badge active" id="systemStatus">
                        <i class="fas fa-circle"></i><span>System Active</span>
                    </div>
                    <div class="status-badge inactive" id="connectionStatus">
                        <i class="fas fa-circle"></i><span>Connecting...</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">

            <div class="floorplan-container">
                <div class="floorplan-title">
                    <h2><i class="fas fa-building"></i> Building Floor Plan</h2>
                    <button class="refresh-btn" id="refreshBtn" onclick="dashboard.loadData()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
                <div class="floorplan-svg">
                    <svg viewBox="0 0 1000 700">
                        <rect x="40" y="40" width="920" height="620" fill="none" stroke="rgba(255,255,255,0.15)" stroke-width="3" rx="10"/>

                        <!-- Room 1 - Combined (Temperature + Gas Sensor) -->
                        <g id="room1">
                            <rect id="room1_bg" class="room room-unknown" x="60" y="60" width="300" height="520" rx="5"/>
                            <text class="room-label" x="210" y="140">Room 1</text>
                            <text class="room-sensor" x="210" y="172">Temperature & Gas Sensor</text>
                            <text id="room1_svg_reading" class="room-sensor" x="210" y="240" style="font-size:13px; fill: #f1f5f9;">-- C | -- PPM</text>
                            <text id="room1_status_text" class="room-status room-status-unknown" x="210" y="560">UNKNOWN</text>
                            <!-- Door 1 - Vertical door on right side, opens horizontally -->
                            <g transform="translate(360, 340)">
                                <line id="door1" class="door" x1="0" y1="-15" x2="0" y2="15"/>
                                <text class="door-text" x="10" y="0">Door</text>
                            </g>
                        </g>

                        <!-- Area 2 - Gas Sensor (Top Right) -->
                        <g id="room2">
                            <rect id="room2_bg" class="room room-unknown" x="410" y="60" width="520" height="280" rx="5"/>
                            <text class="room-label" x="670" y="120">Area 2</text>
                            <text class="room-sensor" x="670" y="152">Gas & Smoke Detection</text>
                            <text id="room2_svg_reading" class="room-sensor" x="670" y="230" style="font-size:13px; fill: #f1f5f9;">-- PPM</text>
                            <text id="room2_status_text" class="room-status room-status-unknown" x="670" y="310">UNKNOWN</text>
                            <!-- Exit 1 - Horizontal door above Area 2, opens vertically (downward) -->
                            <g transform="translate(870, 35)">
                                <line id="exit1" class="door" x1="-15" y1="0" x2="15" y2="0"/>
                                <text class="door-text" x="0" y="-8">Exit</text>
                            </g>
                        </g>

                        <!-- Room 3 - Flame Sensor (Bottom Right) -->
                        <g id="room3">
                            <rect id="room3_bg" class="room room-unknown" x="410" y="380" width="520" height="200" rx="5"/>
                            <text class="room-label" x="670" y="440">Room 3</text>
                            <text class="room-sensor" x="670" y="472">Flame Sensor</text>
                            <text id="room3_svg_reading" class="room-sensor" x="670" y="530" style="font-size:15px; fill: #f1f5f9;">--</text>
                            <text id="room3_status_text" class="room-status room-status-unknown" x="670" y="560">UNKNOWN</text>
                            <!-- Door 3 - Horizontal door above Room 3, placed higher -->
                            <g transform="translate(880, 370)">
                                <line id="door3" class="door" x1="-15" y1="0" x2="15" y2="0"/>
                                <text class="door-text" x="0" y="-8">Door</text>
                            </g>
                        </g>

                        <!-- Exit 2 - Horizontal door between bottom of Room 1 and left side of Room 3, opens downwards -->
                        <g transform="translate(385, 520)">
                            <line id="exit2" class="door" x1="-15" y1="0" x2="15" y2="0"/>
                            <text class="door-text" x="0" y="-8">Exit</text>
                        </g>

                    </svg>
                </div>
            </div>

            <div class="side-panel">
                <div class="room-cards">

                    <!-- Room 1 Card - Combined -->
                    <div class="room-card safe" id="room1_card">
                        <div class="room-card-header">
                            <div class="room-card-title">
                                <div class="room-card-icon room1-icon">
                                    <i class="fas fa-thermometer-half"></i>
                                </div>
                                <div class="room-card-name">
                                    <h3>Room 1</h3>
                                    <p>Temperature & Gas Monitoring</p>
                                </div>
                            </div>
                            <div class="room-card-status safe" id="room1_card_status">SAFE</div>
                        </div>
                        <div class="room-card-details">
                            <div class="detail-item">
                                <div class="detail-label">Temperature</div>
                                <div class="detail-value" id="room1_temp">-- C</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Gas Level</div>
                                <div class="detail-value" id="room1_gas">-- PPM</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Buzzer</div>
                                <div class="detail-value" id="room1_buzzer">OFF</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Last Update</div>
                                <div class="detail-value" id="room1_time">--:--</div>
                            </div>
                        </div>
                    </div>

                    <!-- Area 2 Card -->
                    <div class="room-card safe" id="room2_card">
                        <div class="room-card-header">
                            <div class="room-card-title">
                                <div class="room-card-icon room2-icon">
                                    <i class="fas fa-smog"></i>
                                </div>
                                <div class="room-card-name">
                                    <h3>Area 2</h3>
                                    <p>Gas & Smoke Detection</p>
                                </div>
                            </div>
                            <div class="room-card-status safe" id="room2_card_status">SAFE</div>
                        </div>
                        <div class="room-card-details">
                            <div class="detail-item">
                                <div class="detail-label">Gas Level</div>
                                <div class="detail-value" id="room2_gas">-- PPM</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Buzzer</div>
                                <div class="detail-value" id="room2_buzzer">OFF</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Last Update</div>
                                <div class="detail-value" id="room2_time">--:--</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Sensor</div>
                                <div class="detail-value">MQ-2</div>
                            </div>
                        </div>
                    </div>

                    <!-- Room 3 Card -->
                    <div class="room-card safe" id="room3_card">
                        <div class="room-card-header">
                            <div class="room-card-title">
                                <div class="room-card-icon room1-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                                    <i class="fas fa-fire"></i>
                                </div>
                                <div class="room-card-name">
                                    <h3>Room 3</h3>
                                    <p>Flame Detection</p>
                                </div>
                            </div>
                            <div class="room-card-status safe" id="room3_card_status">SAFE</div>
                        </div>
                        <div class="room-card-details">
                            <div class="detail-item">
                                <div class="detail-label">Flame</div>
                                <div class="detail-value" id="room3_flame">--</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Buzzer</div>
                                <div class="detail-value" id="room3_buzzer">OFF</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Last Update</div>
                                <div class="detail-value" id="room3_time">--:--</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Sensor</div>
                                <div class="detail-value">IR Flame</div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="system-log">
                    <div class="log-title">
                        <h3><i class="fas fa-history"></i> System Log</h3>
                        <button class="refresh-btn" onclick="dashboard.clearLog()" style="padding:6px 12px;font-size:0.82rem;">
                            <i class="fas fa-trash-alt"></i> Clear
                        </button>
                    </div>
                    <div class="log-entries" id="systemLog"></div>
                </div>
            </div>

        </div>
    </div>

    <script>
    class FireDashboard {
        constructor() {
            this.pollInterval = 500;
            this.minInterval = 200;
            this.maxInterval = 1000;
            this.alertDismissed = false;
            this.log = [];
            this.prevStatus = { '1': null, '2': null, '3': null };

            this.addLog('System initialized', 'update');
            this.tick();
            setInterval(() => this.updateClock(), 1000);
        }

        updateClock() {
            const t = new Date().toLocaleTimeString([], { hour:'2-digit', minute:'2-digit', second:'2-digit' });
            const el = document.getElementById('currentTime');
            if (el) el.textContent = t;
        }

        scheduleNextPoll() {
            setTimeout(() => this.tick(), this.pollInterval);
        }

        async tick() {
            try {
                const res = await fetch('/api/rooms');
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                const data = await res.json();
                this.render(data);
                this.setConnected(true);
                
                const hasFire = (data.Room1?.status === 'FIRE' || 
                                data.Room2?.status === 'FIRE' || 
                                data.Room3?.status === 'FIRE');
                
                if (hasFire) {
                    this.pollInterval = Math.max(this.minInterval, this.pollInterval - 50);
                } else {
                    this.pollInterval = Math.min(this.maxInterval, this.pollInterval + 100);
                }
                
            } catch (e) {
                this.setConnected(false);
                this.pollInterval = Math.min(this.maxInterval, this.pollInterval + 200);
            }
            
            this.scheduleNextPoll();
        }
        
        loadData() {
            this.tick();
        }
        
        setConnected(ok) {
            const el = document.getElementById('connectionStatus');
            if (!el) return;
            el.className = `status-badge ${ok ? 'active' : 'inactive'}`;
            el.innerHTML = `<i class="fas fa-circle"></i><span>${ok ? 'Connected' : 'Connection Error'}</span>`;
        }

        render(data) {
            document.getElementById('lastUpdateTime').textContent = data.lastUpdate ?? '--:--:--';
            this.renderRoom('1', data.Room1);
            this.renderRoom('2', data.Room2);
            this.renderRoom('3', data.Room3);
            this.handleAlert(data);
        }

        renderRoom(n, d) {
            if (!d) return;

            const status  = (d.status  ?? 'SAFE').toUpperCase();
            const reading = d.reading ?? '--';
            const buzzer  = d.buzzer  ?? false;
            const time    = d.time    ?? '--:--';
            const isFire  = status === 'FIRE';

            const bg = document.getElementById(`room${n}_bg`);
            if (bg) {
                bg.className.baseVal = `room ${isFire ? 'room-fire' : 'room-safe'}`;
            }

            const svgStatus = document.getElementById(`room${n}_status_text`);
            if (svgStatus) {
                svgStatus.textContent = status;
                svgStatus.className.baseVal = `room-status ${isFire ? 'room-status-fire' : 'room-status-safe'}`;
            }

            const svgReading = document.getElementById(`room${n}_svg_reading`);
            if (svgReading) {
                if (n === '1') {
                    const tempReading = reading.temp !== undefined ? `${reading.temp} C` : '-- C';
                    const gasReading = reading.gas !== undefined ? `${reading.gas} PPM` : '-- PPM';
                    svgReading.textContent = `${tempReading} | ${gasReading}`;
                } else if (n === '2') {
                    svgReading.textContent = reading !== '--' ? `${reading} PPM` : '-- PPM';
                } else if (n === '3') {
                    svgReading.textContent = reading;
                }
                svgReading.style.fill = isFire ? '#ef4444' : '#f1f5f9';
            }

            const card = document.getElementById(`room${n}_card`);
            if (card) {
                card.className = `room-card ${isFire ? 'fire' : 'safe'}`;
            }

            const badge = document.getElementById(`room${n}_card_status`);
            if (badge) {
                badge.textContent = status;
                badge.className = `room-card-status ${isFire ? 'fire' : 'safe'}`;
            }

            if (n === '1') {
                const tempEl = document.getElementById('room1_temp');
                const gasEl = document.getElementById('room1_gas');
                if (tempEl) {
                    const tempReading = reading.temp !== undefined ? reading.temp : reading;
                    tempEl.textContent = tempReading !== '--' ? `${tempReading} C` : '-- C';
                    tempEl.className = `detail-value ${isFire ? 'fire-value' : ''}`;
                }
                if (gasEl) {
                    const gasReading = reading.gas !== undefined ? reading.gas : '--';
                    gasEl.textContent = gasReading !== '--' ? `${gasReading} PPM` : '-- PPM';
                    gasEl.className = `detail-value ${isFire ? 'fire-value' : ''}`;
                }
            } else if (n === '2') {
                const gasEl = document.getElementById('room2_gas');
                if (gasEl) {
                    gasEl.textContent = reading !== '--' ? `${reading} PPM` : '-- PPM';
                    gasEl.className = `detail-value ${isFire ? 'fire-value' : ''}`;
                }
            } else if (n === '3') {
                const flameEl = document.getElementById('room3_flame');
                if (flameEl) {
                    flameEl.textContent = reading;
                    flameEl.className = `detail-value ${isFire ? 'fire-value' : ''}`;
                }
            }

            const buzzerEl = document.getElementById(`room${n}_buzzer`);
            if (buzzerEl) {
                buzzerEl.textContent = buzzer ? 'ON' : 'OFF';
                buzzerEl.className = `detail-value ${buzzer ? 'fire-value' : ''}`;
            }

            const timeEl = document.getElementById(`room${n}_time`);
            if (timeEl) timeEl.textContent = time;

            // Door animations
            const doorIds = { 
                '1': ['door1'], 
                '2': [], 
                '3': ['door3'] 
            };
            (doorIds[n] || []).forEach(id => {
                const door = document.getElementById(id);
                if (door) door.className.baseVal = `door${isFire ? ' door-open' : ''}`;
            });
            if (isFire) {
                ['exit1','exit2'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) el.className.baseVal = 'door door-open';
                });
            }

            if (this.prevStatus[n] !== null && this.prevStatus[n] !== status) {
                this.addLog(`Room ${n} changed to ${status}`, isFire ? 'fire' : 'safe');
            }
            this.prevStatus[n] = status;
        }

        handleAlert(data) {
            const fireRooms = [];
            if (data.Room1?.status === 'FIRE') fireRooms.push('Room 1');
            if (data.Room2?.status === 'FIRE') fireRooms.push('Area 2');
            if (data.Room3?.status === 'FIRE') fireRooms.push('Room 3');

            const banner = document.getElementById('alertBanner');
            if (fireRooms.length > 0) {
                document.getElementById('alertRooms').textContent = fireRooms.join(', ');
                if (!this.alertDismissed) banner.classList.add('show');
            } else {
                banner.classList.remove('show');
                this.alertDismissed = false;
            }
        }

        dismissAlert() {
            this.alertDismissed = true;
            document.getElementById('alertBanner').classList.remove('show');
        }

        addLog(msg, type = 'update') {
            const t = new Date().toLocaleTimeString([], { hour:'2-digit', minute:'2-digit', second:'2-digit' });
            const icon = type === 'fire' ? 'fas fa-fire' : type === 'safe' ? 'fas fa-check-circle' : 'fas fa-info-circle';
            this.log.unshift({ msg, type, icon, t });
            if (this.log.length > 15) this.log.pop();
            this.renderLog();
        }

        renderLog() {
            const el = document.getElementById('systemLog');
            if (!el) return;
            el.innerHTML = this.log.map(e => `
                <div class="log-entry">
                    <div class="log-icon ${e.type}"><i class="${e.icon}"></i></div>
                    <div>
                        <div class="log-message">${e.msg}</div>
                        <div class="log-time">${e.t}</div>
                    </div>
                </div>`).join('');
        }

        clearLog() {
            this.log = [];
            this.addLog('Log cleared', 'update');
        }
    }

    const dashboard = new FireDashboard();
    </script>
</body>
</html>