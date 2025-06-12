<head>
<?php
$pageTitle = "Dashboard";
$cssFileName = "../css/admin.css";
include "../head.php";
?>
<link rel="stylesheet" href="../css/sidebar.css">
<link rel="stylesheet" href="../css/topbar.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
.charts {
    display: flex;
    justify-content: space-around;
    flex-wrap: wrap;
    margin-top: 30px;
    gap: 20px;
}

.chart-container {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    min-width: 400px;
    max-width: 500px;
}

.chart-container canvas {
    width: 100% !important;
    height: 300px !important;
}

.chart-title {
    text-align: center;
    font-size: 18px;
    font-weight: bold;
    color: #333;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid #f0f0f0;
}
</style>
</head>

<body>
<?php 
session_start();
include "../php/config.php";

$gender = $_SESSION['admin_gender'];
$name = $_SESSION['admin_name'];

// Count departments
$dept = "SELECT COUNT(*) as total_dept FROM DEPARTMENT";
$dept_result = $conn->query($dept);
$total_dept = $dept_result->fetch_assoc()["total_dept"];

// Count employees
$emp = "SELECT COUNT(*) as total_emp FROM EMPLOYEE";
$emp_result = $conn->query($emp);
$total_emp = $emp_result->fetch_assoc()["total_emp"];

// Count tasks
$task = "SELECT COUNT(*) as total_task FROM TASK";
$task_result = $conn->query($task);
$total_task = $task_result->fetch_assoc()["total_task"];

// Completed tasks
$completedTask = "SELECT COUNT(*) as total_completed_task FROM TASK_STATUS WHERE task_status = 'Completed'";
$completedTask_result = $conn->query($completedTask);
$total_completed_task = $completedTask_result->fetch_assoc()["total_completed_task"];

// In progress tasks
$inprogressTask = "SELECT COUNT(*) as total_inprogress_task FROM TASK_STATUS WHERE task_status = 'In Progress'";
$inprogressTask_result = $conn->query($inprogressTask);
$total_inprogress_task = $inprogressTask_result->fetch_assoc()["total_inprogress_task"];

// Tasks by priority
$priorityQuery = "SELECT task_priority, COUNT(*) as count FROM TASK GROUP BY task_priority";
$priorityResult = $conn->query($priorityQuery);

$priorities = [];
$priorityCounts = [];
$totalTasksForPercentage = 0;

while ($row = $priorityResult->fetch_assoc()) {
    $priorities[] = $row['task_priority'];
    $priorityCounts[] = $row['count'];
    $totalTasksForPercentage += $row['count'];
}

// Calculate percentages
$priorityPercentages = [];
foreach ($priorityCounts as $count) {
    $priorityPercentages[] = $totalTasksForPercentage > 0 ? round(($count / $totalTasksForPercentage) * 100, 1) : 0;
}
?>

<div class="container">
<?php include 'adminSidebar.php' ?>
<div class="main">
    <?php include 'adminTopbar.php' ?>

    <section id="dashboard">
        <div class="cardBox">
            <div class="card">
                <div class="department">
                    <div class="Number"><?php echo $total_dept ?></div>
                    <div class="CardName">Departments</div>
                </div>
                <div class="icon"><i class="fas fa-users"></i></div>
            </div>
            <div class="card">
                <div class="employee">
                    <div class="Number"><?php echo $total_emp ?></div>
                    <div class="CardName">Employees</div>
                </div>
                <div class="icon"><i class="fas fa-users"></i></div>
            </div>
            <div class="card">
                <div class="totalTasks">
                    <div class="Number"><?php echo $total_task ?></div>
                    <div class="CardName">Total Tasks</div>
                </div>
                <div class="icon"><i class="fas fa-tasks"></i></div>
            </div>
            <div class="card">
                <div class="completedTasks">                   
                    <div class="Number"><?php echo $total_completed_task ?></div>   
                    <div class="CardName">Completed Tasks</div>
                </div>
                <div class="icon"><i class="fa-solid fa-file-circle-check"></i></div>
            </div>
            <div class="card">
                <div class="inprogressTasks">
                    <div class="Number"><?php echo $total_inprogress_task ?></div>
                    <div class="CardName">In Progress Tasks</div>
                </div>
                <div class="icon"><i class="fa-solid fa-bars-progress"></i></div>
            </div>
        </div>

        <!-- ***** Charts ***** -->
        <div class="charts">
            <div class="chart-container">
                <div class="chart-title">Task Status Distribution</div>
                <canvas id="taskStatusChart"></canvas>
            </div>
            <div class="chart-container">
                <div class="chart-title">Task Priority Distribution (%)</div>
                <canvas id="priorityChart"></canvas>
            </div>
        </div>

        
<script>
// Chart Data from PHP
const priorityLabels = <?php echo json_encode($priorities); ?>;
const priorityData = <?php echo json_encode($priorityCounts); ?>;
const priorityPercentages = <?php echo json_encode($priorityPercentages); ?>;

// Define colors for different priorities
const priorityColors = {
    'High': '#FF6B6B',
    'Medium': '#4ECDC4', 
    'Low': '#45B7D1',
    'Urgent': '#FF4757',
    'Normal': '#5F27CD'
};

// Create color array based on priority labels
const backgroundColors = priorityLabels.map(label => 
    priorityColors[label] || '#95A5A6'
);

// Pie Chart: Task Status
const ctx1 = document.getElementById('taskStatusChart').getContext('2d');
const taskStatusChart = new Chart(ctx1, {
    type: 'pie',
    data: {
        labels: ['Completed', 'In Progress'],
        datasets: [{
            label: 'Task Status',
            data: [<?php echo $total_completed_task; ?>, <?php echo $total_inprogress_task; ?>],
            backgroundColor: ['#28A745', '#FFC107'],
            borderColor: '#ffffff',
            borderWidth: 3
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { 
                position: 'bottom',
                labels: {
                    color: '#333333',
                    font: {
                        size: 12
                    },
                    padding: 20
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((context.parsed / total) * 100).toFixed(1);
                        return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                    }
                }
            }
        }
    }
});

// Bar Chart: Task Priority Percentages
const ctx2 = document.getElementById('priorityChart').getContext('2d');
const priorityChart = new Chart(ctx2, {
    type: 'bar',
    data: {
        labels: priorityLabels,
        datasets: [{
            label: 'Percentage (%)',
            data: priorityPercentages,
            backgroundColor: backgroundColors,
            borderColor: backgroundColors,
            borderWidth: 1,
            borderRadius: 6,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                max: 100,
                ticks: {
                    callback: function(value) {
                        return value + '%';
                    },
                    color: '#000000',
                    font: {
                        size: 11,
                        weight: 'bold'
                    }
                },
                grid: {
                    color: '#E5E5E5',
                    lineWidth: 1
                }
            },
            x: {
                ticks: {
                    color: '#000000',
                    font: {
                        size: 12,
                        weight: 'bold'
                    }
                },
                grid: {
                    display: false
                }
            }
        },
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const count = priorityData[context.dataIndex];
                        return context.label + ': ' + count + ' tasks (' + context.parsed.y + '%)';
                    }
                },
                backgroundColor: 'rgba(0,0,0,0.8)',
                titleColor: '#ffffff',
                bodyColor: '#ffffff',
                borderColor: '#cccccc',
                borderWidth: 1
            }
        },
        layout: {
            padding: {
                top: 20,
                bottom: 10
            }
        }
    }
});
</script>
<script src="../js/adminDashboard.js"></script>
</body>

</html>