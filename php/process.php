<?php
session_start();

include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // ------- Admin Login --------
    if (isset($_POST['adminLogin'])) {
        $admin_email = trim($_POST['email']);
        $adminPassword = trim($_POST['pass']);

        if (!empty($admin_email) && !empty($adminPassword)) {
            $stmt = $conn->prepare("SELECT admin_id, admin_name, admin_gender, admin_pass FROM ADMIN_MANAGER WHERE admin_email = ?");
            $stmt->bind_param('s', $admin_email);
            $stmt->execute();
            $stmt->store_result();
            
            if ($stmt->num_rows > 0) {
                $stmt->bind_result($admin_id, $admin_name, $admin_gender, $admin_pass);
                $stmt->fetch();
                
                if (password_verify($adminPassword, $admin_pass)) {
                    session_regenerate_id();
                    $_SESSION['admin_loggedin'] = TRUE;
                    $_SESSION['admin_email'] = $admin_email;
                    $_SESSION['admin_id'] = $admin_id;
                    $_SESSION['admin_name'] = $admin_name;
                    $_SESSION['admin_gender'] = $admin_gender;
                    echo "<script>alert('You\\'ve Successfully Logged In!');
                          window.location.href = '../admin/adminDashboard.php'</script>";
                } else {
                    echo '<script>alert("Incorrect Password!");
                          window.location.href = "../adminLogin.php";</script>';
                }
            } else {
                echo '<script>alert("Account does not exist!");
                      window.location.href = "../adminLogin.php";</script>';
            }
            $stmt->close();
        } else {
            echo "<script>alert('Please fill out the details');
                  window.location.href = '../adminLogin.php';</script>";
        }
    }

    // ------- Employee Login --------
    if (isset($_POST['employeeLogin'])) {
        $employee_email = trim($_POST['email']);
        $employeePassword = trim($_POST['pass']);

        if (!empty($employee_email) && !empty($employeePassword)) {
            $stmt = $conn->prepare("SELECT emp_id, emp_pass FROM EMPLOYEE WHERE emp_email = ?");
            $stmt->bind_param('s', $employee_email);
            $stmt->execute();
            $stmt->store_result();
            
            if ($stmt->num_rows > 0) {
                $stmt->bind_result($employee_id, $employee_pass);
                $stmt->fetch();
                
                if (password_verify($employeePassword, $employee_pass)) {
                    session_regenerate_id();
                    $_SESSION['employee_loggedin'] = TRUE;
                    $_SESSION['employee_email'] = $employee_email;
                    $_SESSION['employee_id'] = $employee_id;
                    echo "<script>alert('You\\'ve Successfully Logged In!');
                          window.location.href = '../employee/employeeDashboard.php';</script>";
                } else {
                    echo '<script>alert("Incorrect Password!");
                          window.location.href = "../employeeLogin.php";</script>';
                }
            } else {
                echo '<script>alert("Account does not exist!");
                      window.location.href = "../employeeLogin.php";</script>';
            }
            $stmt->close();
        } else {
            echo "<script>alert('Please fill out the details');
                  window.location.href = '../employeeLogin.php';</script>";
        }
    }

    // ------- Add Department --------
    if (isset($_POST['addDepartment'])) {
        $department_name = trim($_POST['dept_name']);
        $admin_name = trim($_POST['admin_name']);

        if (!empty($department_name) && !empty($admin_name)) {
            // Vérification sécurisée de l'existence du département
            $check_stmt = $conn->prepare("SELECT dept_id FROM DEPARTMENT WHERE dept_name = ?");
            $check_stmt->bind_param("s", $department_name);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows > 0) {
                echo "<script>alert('Department already exists!');
                      window.location.href = '../admin/addDepartment.php';</script>";
            } else {
                // Récupération sécurisée de l'admin_id
                $admin_stmt = $conn->prepare("SELECT admin_id FROM ADMIN_MANAGER WHERE admin_name = ?");
                $admin_stmt->bind_param("s", $admin_name);
                $admin_stmt->execute();
                $admin_result = $admin_stmt->get_result();
                
                if ($admin_result->num_rows > 0) {
                    $row = $admin_result->fetch_assoc();
                    $admin_id = $row["admin_id"];

                    $ins_stmt = $conn->prepare("INSERT INTO DEPARTMENT (dept_name, admin_id) VALUES (?, ?)");
                    $ins_stmt->bind_param("si", $department_name, $admin_id);
                    
                    if ($ins_stmt->execute()) {
                        echo "<script>alert('Department Added Successfully');
                              window.location.href='../admin/adminDepartment.php'</script>";
                    } else {
                        echo "<script>alert('Error While Inserting.');
                              window.location.href='../admin/addDepartment.php'</script>";
                    }
                }
            }
        } else {
            echo "<script>alert('Fill out all details');
                  window.location.href='../admin/addDepartment.php'</script>";
        }
    }

    // ------- Edit Department --------
    if (isset($_POST['editDepartment'])) {
        $new_dept_name = trim($_POST['new_dept_name']);
        $old_dept_name = trim($_POST['old_dept_name']);

        if (!empty($new_dept_name) && !empty($old_dept_name)) {
            $dept_stmt = $conn->prepare("SELECT dept_id FROM DEPARTMENT WHERE dept_name = ?");
            $dept_stmt->bind_param("s", $old_dept_name);
            $dept_stmt->execute();
            $dept_result = $dept_stmt->get_result();
            
            if ($dept_result->num_rows > 0) {
                $row = $dept_result->fetch_assoc();
                $dept_id = $row["dept_id"];

                $update_stmt = $conn->prepare("UPDATE DEPARTMENT SET dept_name = ? WHERE dept_id = ?");
                $update_stmt->bind_param("si", $new_dept_name, $dept_id);
                
                if ($update_stmt->execute()) {
                    echo "<script>alert('Department Updated Successfully');
                          window.location.href='../admin/adminDepartment.php'</script>";
                } else {
                    echo "<script>alert('Department Name must be unique.');
                          window.location.href='../admin/adminDepartment.php'</script>";
                }
            }
        } else {
            echo "<script>alert('Fill out all details');
                  window.location.href='../admin/adminDepartment.php'</script>";
        }
    }

    // ------- Add Employee --------
    if (isset($_POST['addEmployee'])) {
        $employee_name = trim($_POST['emp_name']);
        $employee_email = trim($_POST['emp_email']);
        $employee_phone = trim($_POST['emp_phone']);
        $employee_gender = trim($_POST['emp_gender']);
        $employeePassword = trim($_POST['emp_pass']);
        $dept_name = trim($_POST['dept_name']);

        if (!empty($employee_name) && !empty($employee_email) && !empty($employee_phone) && 
            !empty($employee_gender) && !empty($employeePassword) && !empty($dept_name)) {
            
            // Vérification sécurisée de l'existence de l'email
            $check_stmt = $conn->prepare("SELECT emp_id FROM EMPLOYEE WHERE emp_email = ?");
            $check_stmt->bind_param("s", $employee_email);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows > 0) {
                echo "<script>alert('Account already exists!');
                      window.location.href = '../admin/addEmployee.php';</script>";
            } else {
                $hashed_password = password_hash($employeePassword, PASSWORD_BCRYPT);
                
                // Récupération sécurisée du dept_id
                $dept_stmt = $conn->prepare("SELECT dept_id FROM DEPARTMENT WHERE dept_name = ?");
                $dept_stmt->bind_param("s", $dept_name);
                $dept_stmt->execute();
                $dept_result = $dept_stmt->get_result();
                
                if ($dept_result->num_rows > 0) {
                    $row = $dept_result->fetch_assoc();
                    $dept_id = $row["dept_id"];

                    $ins_stmt = $conn->prepare("INSERT INTO EMPLOYEE (emp_name, emp_email, emp_phone, emp_gender, emp_pass, dept_id) VALUES (?, ?, ?, ?, ?, ?)");
                    $ins_stmt->bind_param("sssssi", $employee_name, $employee_email, $employee_phone, $employee_gender, $hashed_password, $dept_id);
                    
                    if ($ins_stmt->execute()) {
                        echo "<script>alert('Employee Added Successfully');
                              window.location.href='../admin/adminEmployee.php'</script>";
                    } else {
                        echo "<script>alert('Error While Inserting.');
                              window.location.href='../admin/addEmployee.php'</script>";
                    }
                }
            }
        } else {
            echo "<script>alert('Fill out all details');
                  window.location.href='../admin/addEmployee.php'</script>";
        }
    }

    // ------- Edit Employee --------
    if (isset($_POST['editEmployee'])) {
        $employee_name = trim($_POST['emp_name']);
        $employee_email = trim($_POST['emp_email']);
        $employee_phone = trim($_POST['emp_phone']);
        $employee_gender = trim($_POST['emp_gender']);
        $dept_name = trim($_POST['dept_name']);
        $old_emp_email = trim($_POST['old_emp_email']);

        if (!empty($employee_name) && !empty($employee_email) && !empty($employee_phone) && 
            !empty($employee_gender) && !empty($dept_name) && !empty($old_emp_email)) {
            
            $dept_stmt = $conn->prepare("SELECT dept_id FROM DEPARTMENT WHERE dept_name = ?");
            $dept_stmt->bind_param("s", $dept_name);
            $dept_stmt->execute();
            $dept_result = $dept_stmt->get_result();
            
            if ($dept_result->num_rows > 0) {
                $row = $dept_result->fetch_assoc();
                $dept_id = $row["dept_id"];

                $update_stmt = $conn->prepare("UPDATE EMPLOYEE SET emp_name = ?, emp_phone = ?, emp_gender = ?, emp_email = ?, dept_id = ? WHERE emp_email = ?");
                $update_stmt->bind_param("ssssis", $employee_name, $employee_phone, $employee_gender, $employee_email, $dept_id, $old_emp_email);
                
                if ($update_stmt->execute()) {
                    echo "<script>alert('Employee Updated Successfully');
                          window.location.href='../admin/adminEmployee.php'</script>";
                } else {
                    echo "<script>alert('Error while Updating.');
                          window.location.href='../admin/adminEmployee.php'</script>";
                }
            }
        } else {
            echo "<script>alert('Fill out all details');
                  window.location.href='../admin/adminEmployee.php'</script>";
        }
    }

    // ------- Add Task --------
    if (isset($_POST['addTask'])) {
        $employee_name = trim($_POST['emp_name']);
        $department_name = trim($_POST['dept_name']);
        $task_id = trim($_POST['task_id']);
        $task_name = trim($_POST['task_name']);
        $task_desc = trim($_POST['task_desc']);
        $task_priority = trim($_POST['task_priority']);
        $end_date = trim($_POST['end_date']);

        if (!empty($employee_name) && !empty($department_name) && !empty($task_name) && 
            !empty($task_priority) && !empty($end_date) && !empty($task_id)) {
            
            $emp_stmt = $conn->prepare("SELECT emp_id FROM EMPLOYEE WHERE emp_name = ?");
            $emp_stmt->bind_param("s", $employee_name);
            $emp_stmt->execute();
            $emp_result = $emp_stmt->get_result();
            
            if ($emp_result->num_rows > 0) {
                $emp_row = $emp_result->fetch_assoc();
                $emp_id = $emp_row['emp_id'];

                $conn->begin_transaction();
                try {
                    $ins_task = $conn->prepare("INSERT INTO TASK (task_id, task_title, task_desc, task_priority, endDate) VALUES (?, ?, ?, ?, ?)");
                    $ins_task->bind_param("sssss", $task_id, $task_name, $task_desc, $task_priority, $end_date);
                    $ins_task->execute();
                    
                    $ins_task_status = $conn->prepare("INSERT INTO TASK_STATUS (task_id, emp_id) VALUES (?, ?)");
                    $ins_task_status->bind_param("si", $task_id, $emp_id);
                    $ins_task_status->execute();
                    
                    $conn->commit();
                    echo "<script>alert('Task Added Successfully');
                          window.location.href='../admin/adminTask.php'</script>";
                } catch (Exception $e) {
                    $conn->rollback();
                    echo "<script>alert('Error While Inserting.');
                          window.location.href='../admin/adminTask.php'</script>";
                }
            }
        } else {
            echo "<script>alert('Fill out all details');
                  window.location.href='../admin/adminTask.php'</script>";
        }
    }

    // ------- Update Task --------
    if (isset($_POST['updateTask'])) {
        $updated_info = trim($_POST['updated_info']);
        $updated_date = trim($_POST['updated_date']);
        $percentage = intval($_POST['percentage']);
        $task_status = trim($_POST['task_status']);
        $task_id = trim($_POST['task_id']);

        if (!empty($updated_info) && !empty($updated_date) && !empty($task_status)) {
            $update_stmt = $conn->prepare("UPDATE TASK_STATUS SET task_status = ?, task_percentage = ?, updated_info = ?, updated_date = ? WHERE task_id = ?");
            $update_stmt->bind_param("sissi", $task_status, $percentage, $updated_info, $updated_date, $task_id);
            
            if ($update_stmt->execute()) {
                echo "<script>alert('Task Updated Successfully');
                      window.location.href='../employee/viewTasks.php'</script>";
            } else {
                echo "<script>alert('Error While Updating.');
                      window.location.href='../employee/viewTasks.php'</script>";
            }
        } else {
            echo "<script>alert('Fill out all details');
                  window.location.href='../employee/viewTasks.php'</script>";
        }
    }

    // -------- Edit Password --------
    if (isset($_POST['editPassword'])) {
        $old_password = trim($_POST['old_password']);
        $new_password = trim($_POST['new_password']);
        $emp_id = intval($_POST["emp_id"]);

        if (!empty($old_password) && !empty($new_password) && !empty($emp_id)) {
            $stmt = $conn->prepare("SELECT emp_pass FROM EMPLOYEE WHERE emp_id = ?");
            $stmt->bind_param("i", $emp_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $old_pass = $row['emp_pass'];

                if (password_verify($old_password, $old_pass)) {
                    $hashedPass = password_hash($new_password, PASSWORD_BCRYPT);

                    $update_stmt = $conn->prepare("UPDATE EMPLOYEE SET emp_pass = ? WHERE emp_id = ?");
                    $update_stmt->bind_param("si", $hashedPass, $emp_id);
                    
                    if ($update_stmt->execute()) {
                        echo "<script>alert('Password Updated Successfully');
                              window.location.href='../employee/employeeDashboard.php'</script>";
                    } else {
                        echo "<script>alert('Error While Updating.');
                              window.location.href='../employee/editPassword.php'</script>";
                    }
                } else {
                    echo "<script>alert('Password Mismatch');
                          window.location.href='../employee/editPassword.php'</script>";
                }
            }
        } else {
            echo "<script>alert('Fill out all details');
                  window.location.href='../employee/editPassword.php'</script>";
        }
    }
}
?>