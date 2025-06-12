<?php        
echo '<nav class="navigation">                  
    <ul>                         
        <li>     
            <a href="adminDashboard.php" class="brand-link">         
                <span class="icon">             
                    <img src="../images/logo.png" alt="logo" style="width: 35px; height: 35px; object-fit: contain;">         
                </span>         
                <span class="title">Successful Startup</span>     
            </a> 
        </li> 
        <li>                                  
            <a href="adminDashboard.php">                                          
                <span class="icon"><i class="fas fa-home"></i></span>                                          
                <span class="title">Dashboard</span>                                  
            </a>                          
        </li>                          
        <li>                                  
            <a href="adminDepartment.php">                                          
                <span class="icon"><i class="fa-solid fa-building"></i></span>                                          
                <span class="title">Department</span>                                  
            </a>                          
        </li>                          
        <li>                                  
            <a href="adminEmployee.php">                                          
                <span class="icon"><i class="fa-solid fa-user"></i></span>                                          
                <span class="title">Employee</span>                                  
            </a>                          
        </li>                          
        <li>                                  
            <a href="adminTask.php">                                          
                <span class="icon"><i class="fas fa-tasks"></i></span>                                          
                <span class="title">Task</span>                                  
            </a>                          
        </li>                          
        <li>                                  
            <a href="adminTaskStatus.php">                                          
                <span class="icon"><i class="fa-solid fa-bars-progress"></i></span>                                          
                <span class="title">Task Status</span>                                  
            </a>                          
        </li>                          
        <li>                                  
            <a href="adminTaskReport.php">                                          
                <span class="icon"><i class="fa-solid fa-database"></i></span>                                          
                <span class="title">Task Report</span>                                  
            </a>                          
        </li>                          
        <li>                                  
            <a href="logout.php">                                          
                <span class="icon"><i class="fas fa-sign-out-alt"></i></span>                                          
                <span class="title">Log Out</span>                                  
            </a>                              
        </li>                    
    </ul>          
</nav>

<style>
/* CSS pour aligner le logo et le nom de la société */
.navigation .brand-link {
    display: flex !important;
    align-items: center !important;
    gap: 8px;
}

.navigation .brand-link .icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 35px;
    height: 35px;
}

.navigation .brand-link .title {
    font-size: 16px;
    font-weight: 600;
    white-space: nowrap;
}

/* Pour que toutes les icônes aient la même taille */
.navigation .icon {
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.navigation .icon i {
    font-size: 18px;
}
</style>';  
?>