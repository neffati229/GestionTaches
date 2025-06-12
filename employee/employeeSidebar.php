<?php       
echo '<nav class="navigation">             
    <ul>                 
        <li>                     
            <a href="employeeDashboard.php" class="brand-link">                         
                <span class="icon">
                    <img src="../images/djo.jpg" alt="logo">
                </span>                         
                <span class="title">Successful Startup</span>                     
            </a>                 
        </li>                 
        <li>                     
            <a href="employeeDashboard.php">                         
                <span class="icon"><i class="fas fa-home"></i></span>                         
                <span class="title">Dashboard</span>                     
            </a>                 
        </li>                 
        <li>                     
            <a href="employeeTask.php">                     
                <span class="icon"><i class="fas fa-tasks"></i></span>                     
                <span class="title">Task</span>                     
            </a>                 
        </li>                 
        <li>                     
            <a href="employeeTaskStatus.php">                         
                <span class="icon"><i class="fa-solid fa-bars-progress"></i></span>                         
                <span class="title">Task Status</span>                     
            </a>                 
        </li>                 
        <li>                     
            <a href="editPassword.php">                         
                <span class="icon"><i class="fa fa-key"></i></span>                         
                <span class="title">Edit Password</span>                     
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
/* CSS pour la navigation employé */
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

/* Dimensions uniformes pour toutes les icônes */
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

/* Style spécial pour les images logo */
.navigation .icon img {
    border-radius: 4px;
    object-fit: cover;
}
</style>';               
?>