<?php
/**
 * Admin Statistics Dashboard
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/analytics.php';

requireLogin();

// Handle AJAX request for live updates (BEFORE any HTML output)
if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    header('Content-Type: application/json');
    echo json_encode([
        'onlineUsers' => getOnlineUsersCount()
    ]);
    exit;
}

$siteTitle = getSetting('site_title', SITE_NAME);

// Get period filter
$period = $_GET['period'] ?? '30';
$days = (int)$period;

// Get statistics
$onlineUsers = getOnlineUsersCount();
$todayStats = getTodayStats();
$monthlyStats = getMonthlyStats();

// Get chart data for the period
$startDate = date('Y-m-d', strtotime("-$days days"));
$endDate = date('Y-m-d');
$chartData = getStatsByDateRange($startDate, $endDate);

// Get device, browser, OS stats
$deviceStats = getDeviceStats($days);
$browserStats = getBrowserStats($days);
$osStats = getOSStats($days);
$countryStats = getCountryStats($days);
$topPages = getTopPages($days, 15);
$referrerStats = getReferrerStats($days, 10);

// Prepare chart data for JavaScript
$chartLabels = [];
$chartVisits = [];
$chartUniqueVisitors = [];

foreach ($chartData as $data) {
    $chartLabels[] = date('d M', strtotime($data['visit_date']));
    $chartVisits[] = $data['total_visits'];
    $chartUniqueVisitors[] = $data['unique_visitors'];
}

?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistici - Admin <?php echo e($siteTitle); ?></title>
    <link rel="stylesheet" href="/assets/css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include __DIR__ . '/header.php'; ?>
    
    <div class="admin-container">
        <div class="admin-sidebar">
            <?php include __DIR__ . '/sidebar.php'; ?>
        </div>
        
        <div class="admin-content">
            <div class="page-header">
                <h1>📊 Statistici Site</h1>
                <div class="stats-period-filter">
                    <label>Perioada:</label>
                    <select id="periodFilter" onchange="window.location.href='?period='+this.value">
                        <option value="7" <?php echo $period == '7' ? 'selected' : ''; ?>>Ultima săptămână</option>
                        <option value="30" <?php echo $period == '30' ? 'selected' : ''; ?>>Ultima lună</option>
                        <option value="90" <?php echo $period == '90' ? 'selected' : ''; ?>>Ultimele 3 luni</option>
                        <option value="365" <?php echo $period == '365' ? 'selected' : ''; ?>>Ultimul an</option>
                    </select>
                </div>
            </div>

            <!-- Quick Stats Cards -->
            <div class="stats-cards">
                <div class="stat-card stat-card-primary">
                    <div class="stat-icon">👥</div>
                    <div class="stat-info">
                        <h3>Utilizatori Online</h3>
                        <p class="stat-number"><?php echo number_format($onlineUsers); ?></p>
                        <small>Ultima 5 minute</small>
                    </div>
                </div>

                <div class="stat-card stat-card-success">
                    <div class="stat-icon">📅</div>
                    <div class="stat-info">
                        <h3>Vizite Astăzi</h3>
                        <p class="stat-number"><?php echo number_format($todayStats['total_visits']); ?></p>
                        <small><?php echo number_format($todayStats['unique_visitors']); ?> vizitatori unici</small>
                    </div>
                </div>

                <div class="stat-card stat-card-info">
                    <div class="stat-icon">📊</div>
                    <div class="stat-info">
                        <h3>Vizite Luna Curentă</h3>
                        <p class="stat-number"><?php echo number_format($monthlyStats['total_visits']); ?></p>
                        <small><?php echo number_format($monthlyStats['unique_visitors']); ?> vizitatori unici</small>
                    </div>
                </div>

                <div class="stat-card stat-card-warning">
                    <div class="stat-icon">🔄</div>
                    <div class="stat-info">
                        <h3>Sesiuni Luna Curentă</h3>
                        <p class="stat-number"><?php echo number_format($monthlyStats['sessions']); ?></p>
                        <small>Sesiuni active</small>
                    </div>
                </div>
            </div>

            <!-- Visits Chart -->
            <div class="stats-section">
                <h2>Evoluția Vizitelor</h2>
                <div class="chart-container">
                    <canvas id="visitsChart"></canvas>
                </div>
            </div>

            <!-- Two Column Layout -->
            <div class="stats-grid">
                <!-- Device Stats -->
                <div class="stats-section">
                    <h2>Dispozitive</h2>
                    <div class="stats-table-wrapper">
                        <table class="stats-table">
                            <thead>
                                <tr>
                                    <th>Tip Dispozitiv</th>
                                    <th>Vizite</th>
                                    <th>Procent</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($deviceStats as $device): ?>
                                <tr>
                                    <td>
                                        <span class="device-icon">
                                            <?php 
                                            echo $device['device_type'] === 'Mobile' ? '📱' : 
                                                ($device['device_type'] === 'Tablet' ? '📱' : '💻');
                                            ?>
                                        </span>
                                        <?php echo e($device['device_type']); ?>
                                    </td>
                                    <td><?php echo number_format($device['count']); ?></td>
                                    <td>
                                        <div class="percentage-bar">
                                            <div class="percentage-fill" style="width: <?php echo $device['percentage']; ?>%"></div>
                                            <span><?php echo $device['percentage']; ?>%</span>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Browser Stats -->
                <div class="stats-section">
                    <h2>Browsere</h2>
                    <div class="stats-table-wrapper">
                        <table class="stats-table">
                            <thead>
                                <tr>
                                    <th>Browser</th>
                                    <th>Vizite</th>
                                    <th>Procent</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($browserStats as $browser): ?>
                                <tr>
                                    <td><?php echo e($browser['browser']); ?></td>
                                    <td><?php echo number_format($browser['count']); ?></td>
                                    <td>
                                        <div class="percentage-bar">
                                            <div class="percentage-fill" style="width: <?php echo $browser['percentage']; ?>%"></div>
                                            <span><?php echo $browser['percentage']; ?>%</span>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- OS Stats -->
                <div class="stats-section">
                    <h2>Sistem de Operare</h2>
                    <div class="stats-table-wrapper">
                        <table class="stats-table">
                            <thead>
                                <tr>
                                    <th>OS</th>
                                    <th>Vizite</th>
                                    <th>Procent</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($osStats as $os): ?>
                                <tr>
                                    <td><?php echo e($os['os']); ?></td>
                                    <td><?php echo number_format($os['count']); ?></td>
                                    <td>
                                        <div class="percentage-bar">
                                            <div class="percentage-fill" style="width: <?php echo $os['percentage']; ?>%"></div>
                                            <span><?php echo $os['percentage']; ?>%</span>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Country Stats -->
                <div class="stats-section">
                    <h2>Țări</h2>
                    <div class="stats-table-wrapper">
                        <table class="stats-table">
                            <thead>
                                <tr>
                                    <th>Țară</th>
                                    <th>Vizite</th>
                                    <th>Procent</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($countryStats as $country): ?>
                                <tr>
                                    <td><?php echo e($country['country']); ?></td>
                                    <td><?php echo number_format($country['count']); ?></td>
                                    <td>
                                        <div class="percentage-bar">
                                            <div class="percentage-fill" style="width: <?php echo $country['percentage']; ?>%"></div>
                                            <span><?php echo $country['percentage']; ?>%</span>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Top Pages -->
            <div class="stats-section">
                <h2>Cele Mai Vizitate Pagini</h2>
                <div class="stats-table-wrapper">
                    <table class="stats-table">
                        <thead>
                            <tr>
                                <th>Pagină</th>
                                <th>Total Vizite</th>
                                <th>Vizitatori Unici</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($topPages as $page): ?>
                            <tr>
                                <td class="page-url"><?php echo e($page['page_url']); ?></td>
                                <td><?php echo number_format($page['visits']); ?></td>
                                <td><?php echo number_format($page['unique_visitors']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Referrers -->
            <div class="stats-section">
                <h2>Surse de Trafic</h2>
                <div class="stats-table-wrapper">
                    <table class="stats-table">
                        <thead>
                            <tr>
                                <th>Sursă</th>
                                <th>Vizite</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($referrerStats as $referrer): ?>
                            <tr>
                                <td class="page-url">
                                    <?php 
                                    $source = $referrer['referrer_source'];
                                    if ($source === 'Direct') {
                                        echo '<span class="referrer-badge">Direct</span>';
                                    } else {
                                        echo e(strlen($source) > 50 ? substr($source, 0, 50) . '...' : $source);
                                    }
                                    ?>
                                </td>
                                <td><?php echo number_format($referrer['count']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Visits Chart
        const ctx = document.getElementById('visitsChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($chartLabels); ?>,
                datasets: [{
                    label: 'Total Vizite',
                    data: <?php echo json_encode($chartVisits); ?>,
                    borderColor: 'rgb(102, 126, 234)',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Vizitatori Unici',
                    data: <?php echo json_encode($chartUniqueVisitors); ?>,
                    borderColor: 'rgb(118, 75, 162)',
                    backgroundColor: 'rgba(118, 75, 162, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });

        // Auto-refresh online users every 30 seconds
        setInterval(() => {
            fetch('?ajax=1')
                .then(r => r.json())
                .then(data => {
                    if (data.onlineUsers !== undefined) {
                        document.querySelector('.stat-card-primary .stat-number').textContent = 
                            data.onlineUsers.toLocaleString();
                    }
                })
                .catch(e => console.error('Error refreshing stats:', e));
        }, 30000);
    </script>
        </div>
    </div>
</body>
</html>
