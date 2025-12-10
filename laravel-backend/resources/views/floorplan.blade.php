<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fire Monitoring System - Floor Plan</title>
    
    <!-- Font Awesome Icons -->
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
            --warning: #f59e0b;
            --door-open: #22c55e;
            --door-closed: #64748b;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
            padding: 20px;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 20px;
            background: var(--panel);
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .logo-icon {
            font-size: 2.5rem;
            color: var(--fire);
        }
        
        .logo-text h1 {
            font-size: 1.8rem;
            margin-bottom: 5px;
            background: linear-gradient(90deg, #ef4444, #f97316);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .logo-text p {
            color: var(--muted);
            font-size: 0.9rem;
        }
        
        .status-bar {
            display: flex;
            gap: 20px;
            align-items: center;
        }
        
        .update-info {
            text-align: right;
        }
        
        .update-info .label {
            font-size: 0.85rem;
            color: var(--muted);
            margin-bottom: 5px;
        }
        
        .update-info .time {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--safe);
            font-family: 'Courier New', monospace;
        }
        
        .system-status {
            display: flex;
            gap: 10px;
        }
        
        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .status-badge.active {
            background: rgba(16, 185, 129, 0.2);
            color: var(--safe);
            border: 1px solid var(--safe);
        }
        
        .status-badge.inactive {
            background: rgba(239, 68, 68, 0.2);
            color: var(--fire);
            border: 1px solid var(--fire);
        }
        
        /* Main Content */
        .content {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 30px;
        }
        
        /* Floor Plan */
        .floorplan-container {
            background: var(--panel);
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .floorplan-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
        }
        
        .floorplan-title h2 {
            font-size: 1.5rem;
            color: var(--text);
        }
        
        .refresh-btn {
            background: var(--card-bg);
            color: var(--text);
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .refresh-btn:hover {
            background: #475569;
            transform: translateY(-2px);
        }
        
        .refresh-btn:active {
            transform: translateY(0);
        }
        
        .refresh-btn.refreshing i {
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        /* SVG Floor Plan */
        .floorplan-svg {
            width: 100%;
            height: 500px;
            background: linear-gradient(135deg, #1e293b, #0f172a);
            border-radius: 12px;
            padding: 20px;
            border: 2px solid rgba(255, 255, 255, 0.1);
        }
        
        svg {
            width: 100%;
            height: 100%;
        }
        
        /* Room Styles */
        .room {
            transition: all 0.5s ease;
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.1));
        }
        
        .room-safe {
            fill: var(--safe);
            fill-opacity: 0.15;
            stroke: var(--safe);
            stroke-width: 2;
        }
        
        .room-fire {
            fill: var(--fire);
            fill-opacity: 0.25;
            stroke: var(--fire);
            stroke-width: 3;
            animation: fire-pulse 2s infinite;
        }
        
        .room-unknown {
            fill: var(--muted);
            fill-opacity: 0.1;
            stroke: var(--muted);
            stroke-width: 2;
            stroke-dasharray: 5,5;
        }
        
        @keyframes fire-pulse {
            0%, 100% { 
                stroke-width: 3;
                fill-opacity: 0.25;
            }
            50% { 
                stroke-width: 4;
                fill-opacity: 0.3;
            }
        }
        
        .room-label {
            font-size: 14px;
            font-weight: 600;
            fill: var(--text);
            text-anchor: middle;
            pointer-events: none;
        }
        
        .room-sensor {
            font-size: 12px;
            fill: var(--muted);
            text-anchor: middle;
            pointer-events: none;
        }
        
        .room-status {
            font-size: 16px;
            font-weight: 700;
            text-anchor: middle;
            pointer-events: none;
        }
        
        .room-status-safe { fill: var(--safe); }
        .room-status-fire { 
            fill: var(--fire);
            animation: text-pulse 1.5s infinite;
        }
        .room-status-unknown { fill: var(--muted); }
        
        @keyframes text-pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        
        /* Doors */
        .door {
            stroke: var(--door-closed);
            stroke-width: 4;
            stroke-linecap: round;
            transition: all 0.5s ease;
        }
        
        .door-open {
            stroke: var(--door-open);
            transform: rotate(90deg);
            transform-origin: center;
        }
        
        .door-text {
            font-size: 10px;
            fill: var(--muted);
            text-anchor: middle;
        }
        
        /* Side Panel */
        .side-panel {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }
        
        .room-cards {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .room-card {
            background: var(--panel);
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            border-left: 5px solid var(--muted);
            transition: all 0.3s ease;
        }
        
        .room-card.safe {
            border-left-color: var(--safe);
        }
        
        .room-card.fire {
            border-left-color: var(--fire);
            animation: card-pulse 2s infinite;
        }
        
        @keyframes card-pulse {
            0%, 100% { box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3); }
            50% { box-shadow: 0 10px 25px rgba(239, 68, 68, 0.4); }
        }
        
        .room-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .room-card-title {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .room-card-icon {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }
        
        .room1-icon { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
        .room2-icon { background: linear-gradient(135deg, #10b981, #059669); }
        .room3-icon { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
        
        .room-card-name h3 {
            font-size: 1.2rem;
            margin-bottom: 5px;
        }
        
        .room-card-name p {
            font-size: 0.85rem;
            color: var(--muted);
        }
        
        .room-card-status {
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .room-card-status.safe {
            background: rgba(16, 185, 129, 0.2);
            color: var(--safe);
        }
        
        .room-card-status.fire {
            background: rgba(239, 68, 68, 0.2);
            color: var(--fire);
            animation: status-pulse 1s infinite;
        }
        
        @keyframes status-pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        
        .room-card-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 20px;
        }
        
        .detail-item {
            background: var(--card-bg);
            padding: 12px;
            border-radius: 10px;
            text-align: center;
        }
        
        .detail-label {
            font-size: 0.8rem;
            color: var(--muted);
            margin-bottom: 5px;
        }
        
        .detail-value {
            font-size: 1.1rem;
            font-weight: 600;
            font-family: 'Courier New', monospace;
        }
        
        /* System Log */
        .system-log {
            background: var(--panel);
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            flex-grow: 1;
        }
        
        .log-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .log-title h3 {
            font-size: 1.2rem;
        }
        
        .log-entries {
            max-height: 200px;
            overflow-y: auto;
            padding-right: 10px;
        }
        
        .log-entry {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .log-entry:last-child {
            border-bottom: none;
        }
        
        .log-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
        }
        
        .log-icon.fire { background: rgba(239, 68, 68, 0.2); color: var(--fire); }
        .log-icon.safe { background: rgba(16, 185, 129, 0.2); color: var(--safe); }
        .log-icon.update { background: rgba(59, 130, 246, 0.2); color: #3b82f6; }
        
        .log-content {
            flex-grow: 1;
        }
        
        .log-message {
            font-size: 0.9rem;
            margin-bottom: 3px;
        }
        
        .log-time {
            font-size: 0.75rem;
            color: var(--muted);
        }
        
        /* Alert Banner */
        .alert-banner {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(90deg, #ef4444, #dc2626);
            color: white;
            padding: 20px 30px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(239, 68, 68, 0.4);
            display: none;
            align-items: center;
            gap: 15px;
            z-index: 1000;
            animation: slide-down 0.5s ease;
        }
        
        @keyframes slide-down {
            from { transform: translate(-50%, -100%); opacity: 0; }
            to { transform: translate(-50%, 0); opacity: 1; }
        }
        
        .alert-banner.show {
            display: flex;
        }
        
        .alert-icon {
            font-size: 2rem;
        }
        
        .alert-content h4 {
            font-size: 1.1rem;
            margin-bottom: 5px;
        }
        
        .alert-content p {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .alert-close {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: auto;
        }
        
        /* Responsive */
        @media (max-width: 1200px) {
            .content {
                grid-template-columns: 1fr;
            }
            
            .floorplan-svg {
                height: 400px;
            }
        }
        
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }
            
            .status-bar {
                flex-direction: column;
                gap: 15px;
            }
            
            .update-info {
                text-align: center;
            }
            
            .floorplan-svg {
                height: 300px;
            }
            
            .room-card-details {
                grid-template-columns: 1fr;
            }
        }
        
        /* Loading Spinner */
        .loading-spinner {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 50px;
            height: 50px;
            border: 4px solid rgba(255, 255, 255, 0.1);
            border-top-color: var(--safe);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            z-index: 10;
        }
    </style>
</head>
<body>
    <!-- Alert Banner -->
    <div id="alertBanner" class="alert-banner">
        <div class="alert-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="alert-content">
            <h4>FIRE DETECTED!</h4>
            <p>Emergency alert - Immediate action required</p>
        </div>
        <button class="alert-close" onclick="hideAlert()">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo">
                <div class="logo-icon">
                    <i class="fas fa-fire-extinguisher"></i>
                </div>
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
                        <i class="fas fa-circle"></i>
                        <span>System Active</span>
                    </div>
                    <div class="status-badge inactive" id="connectionStatus">
                        <i class="fas fa-circle"></i>
                        <span>Awaiting Data</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="content">
            <!-- Floor Plan -->
            <div class="floorplan-container">
                <div class="floorplan-title">
                    <h2><i class="fas fa-building me-2"></i>Building Floor Plan</h2>
                    <button class="refresh-btn" id="refreshBtn" onclick="refreshData()">
                        <i class="fas fa-sync-alt"></i>
                        Refresh
                    </button>
                </div>
                
                <div class="floorplan-svg">
                    <svg viewBox="0 0 1000 700" id="floorplanSVG">
                        <!-- House Outline -->
                        <rect x="40" y="40" width="920" height="620" fill="none" stroke="rgba(255,255,255,0.2)" stroke-width="3" rx="10" ry="10"/>
                        
                        <!-- ROOM 1 -->
                        <g id="room1">
                            <rect id="room1_bg" class="room room-unknown" x="60" y="60" width="300" height="200" rx="5" ry="5"/>
                            <text id="room1_label" class="room-label" x="210" y="100">Room 1</text>
                            <text id="room1_sensor" class="room-sensor" x="210" y="125">Temperature Sensor</text>
                            <text id="room1_status_text" class="room-status room-status-unknown" x="210" y="230">UNKNOWN</text>
                            
                            <!-- Door -->
                            <g id="door1_group" transform="translate(360, 130)">
                                <line id="door1" class="door" x1="0" y1="0" x2="20" y2="0"/>
                                <text class="door-text" x="10" y="-8">Door</text>
                            </g>
                        </g>
                        
                        <!-- ROOM 2 -->
                        <g id="room2">
                            <rect id="room2_bg" class="room room-unknown" x="60" y="300" width="300" height="280" rx="5" ry="5"/>
                            <text id="room2_label" class="room-label" x="210" y="350">Room 2</text>
                            <text id="room2_sensor" class="room-sensor" x="210" y="375">Gas Sensor</text>
                            <text id="room2_status_text" class="room-status room-status-unknown" x="210" y="540">UNKNOWN</text>
                            
                            <!-- Door -->
                            <g id="door2_group" transform="translate(360, 390)">
                                <line id="door2" class="door" x1="0" y1="0" x2="20" y2="0"/>
                                <text class="door-text" x="10" y="-8">Door</text>
                            </g>
                        </g>
                        
                        <!-- ROOM 3 -->
                        <g id="room3">
                            <rect id="room3_bg" class="room room-unknown" x="410" y="60" width="520" height="520" rx="5" ry="5"/>
                            <text id="room3_label" class="room-label" x="670" y="120">Room 3</text>
                            <text id="room3_sensor" class="room-sensor" x="670" y="145">Flame Sensor</text>
                            <text id="room3_status_text" class="room-status room-status-unknown" x="670" y="550">UNKNOWN</text>
                            
                            <!-- Doors -->
                            <g id="door3a_group" transform="translate(410, 130)">
                                <line id="door3a" class="door" x1="-20" y1="0" x2="0" y2="0"/>
                                <text class="door-text" x="-10" y="-8">Door</text>
                            </g>
                            
                            <g id="door3b_group" transform="translate(410, 390)">
                                <line id="door3b" class="door" x1="-20" y1="0" x2="0" y2="0"/>
                                <text class="door-text" x="-10" y="-8">Door</text>
                            </g>
                        </g>
                        
                        <!-- Exits -->
                        <g id="exit1_group" transform="translate(940, 40)">
                            <line id="exit1" class="door" x1="0" y1="0" x2="0" y2="20"/>
                            <text class="door-text" x="10" y="35">Exit</text>
                        </g>
                        
                        <g id="exit2_group" transform="translate(940, 660)">
                            <line id="exit2" class="door" x1="0" y1="-20" x2="0" y2="0"/>
                            <text class="door-text" x="10" y="10">Exit</text>
                        </g>
                    </svg>
                </div>
            </div>
            
            <!-- Side Panel -->
            <div class="side-panel">
                <!-- Room Cards -->
                <div class="room-cards">
                    <!-- Room 1 Card -->
                    <div class="room-card" id="room1_card">
                        <div class="room-card-header">
                            <div class="room-card-title">
                                <div class="room-card-icon room1-icon">
                                    <i class="fas fa-thermometer-half"></i>
                                </div>
                                <div class="room-card-name">
                                    <h3>Room 1</h3>
                                    <p>Temperature Monitoring</p>
                                </div>
                            </div>
                            <div class="room-card-status safe" id="room1_card_status">SAFE</div>
                        </div>
                        <div class="room-card-details">
                            <div class="detail-item">
                                <div class="detail-label">Temperature</div>
                                <div class="detail-value" id="room1_temp">-- °C</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Buzzer</div>
                                <div class="detail-value" id="room1_buzzer">OFF</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Last Update</div>
                                <div class="detail-value" id="room1_time">--:--</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Sensor</div>
                                <div class="detail-value">LM35</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Room 2 Card -->
                    <div class="room-card" id="room2_card">
                        <div class="room-card-header">
                            <div class="room-card-title">
                                <div class="room-card-icon room2-icon">
                                    <i class="fas fa-smog"></i>
                                </div>
                                <div class="room-card-name">
                                    <h3>Room 2</h3>
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
                    <div class="room-card" id="room3_card">
                        <div class="room-card-header">
                            <div class="room-card-title">
                                <div class="room-card-icon room3-icon">
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
                                <div class="detail-label">Flame Level</div>
                                <div class="detail-value" id="room3_flame">-- IR</div>
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
                
                <!-- System Log -->
                <div class="system-log">
                    <div class="log-title">
                        <h3><i class="fas fa-history me-2"></i>System Log</h3>
                        <button class="refresh-btn" onclick="clearLog()" style="padding: 6px 12px; font-size: 0.85rem;">
                            <i class="fas fa-trash-alt"></i>
                            Clear
                        </button>
                    </div>
                    <div class="log-entries" id="systemLog">
                        <div class="log-entry">
                            <div class="log-icon update">
                                <i class="fas fa-power-off"></i>
                            </div>
                            <div class="log-content">
                                <div class="log-message">System initialized</div>
                                <div class="log-time" id="currentTime">--:--:--</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- JavaScript -->
    <script>
        class FireDetectionDashboard {
            constructor() {
                this.updateInterval = 2000; // 2 seconds
                this.alertAcknowledged = false;
                this.systemLog = [];
                this.init();
            }
            
            init() {
                this.updateTime();
                this.loadData();
                
                // Update time every second
                setInterval(() => this.updateTime(), 1000);
                
                // Update data every 2 seconds
                setInterval(() => this.loadData(), this.updateInterval);
                
                // Add initial log entry
                this.addLog('System initialized and ready', 'update');
            }
            
            updateTime() {
                const now = new Date();
                document.getElementById('currentTime').textContent = 
                    now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit', second:'2-digit'});
            }
            
            async loadData() {
                const refreshBtn = document.getElementById('refreshBtn');
                refreshBtn.classList.add('refreshing');
                
                try {
                    const response = await fetch('/api/rooms');
                    if (!response.ok) throw new Error(`HTTP ${response.status}`);
                    
                    const data = await response.json();
                    this.updateDashboard(data);
                    
                    // Update connection status
                    document.getElementById('connectionStatus').className = 'status-badge active';
                    document.getElementById('connectionStatus').innerHTML = '<i class="fas fa-circle"></i><span>Connected</span>';
                    
                } catch (error) {
                    console.error('Error loading data:', error);
                    
                    // Update connection status to error
                    document.getElementById('connectionStatus').className = 'status-badge inactive';
                    document.getElementById('connectionStatus').innerHTML = '<i class="fas fa-circle"></i><span>Connection Error</span>';
                    
                    this.addLog(`Failed to fetch data: ${error.message}`, 'fire');
                } finally {
                    refreshBtn.classList.remove('refreshing');
                }
            }
            
            updateDashboard(data) {
                // Update last update time
                document.getElementById('lastUpdateTime').textContent = data.lastUpdate;
                
                // Update Room 1
                this.updateRoom('1', data.Room1);
                
                // Update Room 2
                this.updateRoom('2', data.Room2);
                
                // Update Room 3
                this.updateRoom('3', data.Room3);
                
                // Check for alerts
                this.checkAlerts(data);
            }
            
            updateRoom(roomNumber, roomData) {
                const status = roomData || 'SAFE';
                const isFire = status === 'FIRE';
                
                // Update SVG floor plan
                const roomBg = document.getElementById(`room${roomNumber}_bg`);
                const roomStatusText = document.getElementById(`room${roomNumber}_status_text`);
                
                if (roomBg) {
                    roomBg.classList.remove('room-safe', 'room-fire', 'room-unknown');
                    roomBg.classList.add(isFire ? 'room-fire' : 'room-safe');
                }
                
                if (roomStatusText) {
                    roomStatusText.textContent = status;
                    roomStatusText.classList.remove('room-status-safe', 'room-status-fire', 'room-status-unknown');
                    roomStatusText.classList.add(isFire ? 'room-status-fire' : 'room-status-safe');
                }
                
                // Update room card
                const roomCard = document.getElementById(`room${roomNumber}_card`);
                const cardStatus = document.getElementById(`room${roomNumber}_card_status`);
                
                if (roomCard) {
                    roomCard.classList.remove('safe', 'fire');
                    roomCard.classList.add(isFire ? 'fire' : 'safe');
                }
                
                if (cardStatus) {
                    cardStatus.textContent = status;
                    cardStatus.className = `room-card-status ${isFire ? 'fire' : 'safe'}`;
                }
                
                // Update doors
                if (isFire) {
                    this.openDoors(roomNumber);
                } else {
                    this.closeDoors(roomNumber);
                }
                
                // Log status change
                if (this.previousStatus && this.previousStatus[roomNumber] !== status) {
                    this.addLog(`Room ${roomNumber} changed to ${status}`, isFire ? 'fire' : 'safe');
                }
                
                // Store current status
                if (!this.previousStatus) this.previousStatus = {};
                this.previousStatus[roomNumber] = status;
            }
            
            openDoors(roomNumber) {
                const doors = this.getDoorsForRoom(roomNumber);
                doors.forEach(doorId => {
                    const door = document.getElementById(doorId);
                    if (door) door.classList.add('door-open');
                });
                
                // Also open exits if any room is on fire
                if (roomNumber === '1' || roomNumber === '2' || roomNumber === '3') {
                    document.getElementById('exit1')?.classList.add('door-open');
                    document.getElementById('exit2')?.classList.add('door-open');
                }
            }
            
            closeDoors(roomNumber) {
                const doors = this.getDoorsForRoom(roomNumber);
                doors.forEach(doorId => {
                    const door = document.getElementById(doorId);
                    if (door) door.classList.remove('door-open');
                });
                
                // Close exits only if NO room is on fire
                // We'd need to check all rooms, but for simplicity, we'll keep exits open if any room was on fire
            }
            
            getDoorsForRoom(roomNumber) {
                switch(roomNumber) {
                    case '1': return ['door1'];
                    case '2': return ['door2'];
                    case '3': return ['door3a', 'door3b'];
                    default: return [];
                }
            }
            
            checkAlerts(data) {
                const hasFire = 
                    data.Room1 === 'FIRE' || 
                    data.Room2 === 'FIRE' || 
                    data.Room3 === 'FIRE';
                
                if (hasFire && !this.alertAcknowledged) {
                    this.showAlert();
                    
                    // Play alert sound (optional)
                    this.playAlertSound();
                }
            }
            
            showAlert() {
                document.getElementById('alertBanner').classList.add('show');
            }
            
            hideAlert() {
                this.alertAcknowledged = true;
                document.getElementById('alertBanner').classList.remove('show');
            }
            
            playAlertSound() {
                // Simple beep sound using Web Audio API
                try {
                    const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                    const oscillator = audioContext.createOscillator();
                    const gainNode = audioContext.createGain();
                    
                    oscillator.connect(gainNode);
                    gainNode.connect(audioContext.destination);
                    
                    oscillator.frequency.value = 800;
                    oscillator.type = 'sine';
                    
                    gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
                    gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);
                    
                    oscillator.start(audioContext.currentTime);
                    oscillator.stop(audioContext.currentTime + 0.5);
                } catch (e) {
                    console.log('Audio not supported');
                }
            }
            
            addLog(message, type = 'update') {
                const now = new Date();
                const logEntry = {
                    time: now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit', second:'2-digit'}),
                    message: message,
                    type: type,
                    icon: type === 'fire' ? 'fas fa-fire' : 
                          type === 'safe' ? 'fas fa-check-circle' : 'fas fa-info-circle'
                };
                
                this.systemLog.unshift(logEntry);
                if (this.systemLog.length > 10) {
                    this.systemLog.pop();
                }
                
                this.updateLogDisplay();
            }
            
            updateLogDisplay() {
                const logContainer = document.getElementById('systemLog');
                if (!logContainer) return;
                
                logContainer.innerHTML = this.systemLog.map(entry => `
                    <div class="log-entry">
                        <div class="log-icon ${entry.type}">
                            <i class="${entry.icon}"></i>
                        </div>
                        <div class="log-content">
                            <div class="log-message">${entry.message}</div>
                            <div class="log-time">${entry.time}</div>
                        </div>
                    </div>
                `).join('');
            }
            
            clearLog() {
                this.systemLog = [];
                this.updateLogDisplay();
                this.addLog('Log cleared', 'update');
            }
        }
        
        // Initialize dashboard
        let dashboard;
        
        window.addEventListener('DOMContentLoaded', () => {
            dashboard = new FireDetectionDashboard();
        });
        
        // Global functions for buttons
        function refreshData() {
            if (dashboard) {
                dashboard.loadData();
            }
        }
        
        function hideAlert() {
            if (dashboard) {
                dashboard.hideAlert();
            }
        }
        
        function clearLog() {
            if (dashboard) {
                dashboard.clearLog();
            }
        }
    </script>
</body>
</html> 