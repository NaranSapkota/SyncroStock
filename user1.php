<?php
session_start(); // Start the session at the very beginning

// Include database connection
include('user/dbConnection.php');
$conn = connect(); // Establish database connection

// Pagination logic
$limit = 10; // Number of entries to show in a page.
if (isset($_GET["page"])) {
    $page = $_GET["page"];
} else {
    $page = 1;
};
$start_from = ($page-1) * $limit;

//Fetch the data in the user table
$sql = "SELECT user.*, roles.role_name 
        FROM user 
        JOIN user_roles ON user.user_id = user_roles.user_id
        JOIN roles ON user_roles.role_id = roles.role_id
        LIMIT $start_from, $limit";


$result = mysqli_query($conn, $sql); 

// Fetch the total number of records for pagination
$sql_count = "SELECT COUNT(*) FROM user";
$result_count = mysqli_query($conn, $sql_count);
$row_count = mysqli_fetch_row($result_count);
$total_records = $row_count[0];
$total_pages = ceil($total_records / $limit);

mysqli_close($conn); // Close database connection
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>

     <!-- jQuery -->
     <script src="https://code.jquery.com/jquery-3.6.0.min.js" ></script>

    <!-- Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" ></script>

    <!-- Bootstrap CSS File -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" >
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

    <style>
         body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            background-color: #ffffff;
            font-size: 14px;
        }

        .S-iframe-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 270px;
            height: 100%;
            z-index: 0;
        }
        #S-iframe1 {
            width: 100%;
            height: 100%;
            border: none;
            position: absolute;
            top: 0;
            left: 0;
        }

        .container {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin-left: 270px; /* Sidebar width */
            width: calc(100% - 270px);
        }

        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .toolbar input {
            width: 60%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .toolbar button {
            padding: 10px 20px;
            background-color: #2596be;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .toolbar button:hover {
            background-color: #f4d65e;
        }

        .summary-cards {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .card {
            flex: 1;
            padding: 20px;
            margin-right: 20px;
            background-color: #e9ecef;
            border-radius: 4px;
            text-align: center;
        }

        .card:last-child {
            margin-right: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        thead th {
            background-color: #2596be;
            color: white;
            padding: 10px;
        }

        tbody td {
            padding: 10px;
            border: 1px solid #ccc;
        }
        
         /* Main container for content */
         .S-container {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin-left: 270px; /* Sidebar width */
            width: calc(100% - 270px);
        }

            /* Main content area */
            .S-main-content {
            padding: 20px;
            flex: 1;
            background-color: #ffffff;
            border-left: 1px solid #ddd;
            border-radius: 0 10px 10px 0;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: auto;
            margin-top: 5px;
            position: relative;
            z-index: 1;
        }
         /* Header styling */
         #S-header {
            width: 100%;
            background: #007bff;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            z-index: 1;
        }

      /* Breadcrumbs styling */
      .S-breadcrumbs {
            margin-bottom: 20px;
            font-size: 14px;
            color: #555;
        }


        .S-breadcrumbs a {
            text-decoration: none;
            color: #007bff;
        }

        .S-breadcrumbs a:hover {
            text-decoration: underline;
        }

         /* Controls area styling */
         .S-controls {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .S-controls .search {
            padding: 12px;
            width: 300px;
            border: 1px solid #ccc;
            border-radius: 25px;
            z-index: 3;
        }

        .S-controls .search:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.3);
        }

        .S-controls a.S-btn-add {
            display: inline-block;
            padding: 12px 20px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            transition: background-color 0.3s, color 0.3s;
            z-index: 3;
        }

        .S-controls a.S-btn-add:hover {
            background-color: #0056b3;
        }

        .S-controls a.S-btn-view {
            display: inline-block;
            padding: 12px 20px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            background-color: #14A44D;
            color: white;
            text-decoration: none;
            transition: background-color 0.3s, color 0.3s;
            z-index: 3;
        }

        .S-controls a.S-btn-view:hover {
            background-color: #10823d;
        }

        .S-controls button.S-btn-find {
            padding: 12px 20px;
            cursor: pointer;
            border: none;
            border-radius: 25px;
            transition: background-color 0.3s, color 0.3s;
            z-index: 3;
        }

        .S-controls button.S-btn-find:hover {
            background-color: #0056b3;
            color: white;
        }

        .S-controls select {
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 25px;
            z-index: 3;
        }

        .S-controls select:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.3);
        }



    </style>
</head>

<body>
<div class="S-container">
     <!-- Iframe container -->
     <div class="S-iframe-container">
            <iframe id="S-iframe1" src="navbar.php?n=1"></iframe>
     </div>


    <!-- Main content area -->
    <div class="S-main-content">
        <div>
        <div class="S-breadcrumbs">
                <a href="./home.php">Home</a><span> /</span>
                <a href="./controlPanel.php">Control Panel</a><span> /</span>
                <span>Users</span>
        </div>

        <h1>User Admin</h1>

        <!--Search bar-->
        <div class="input-group mb-3 py-4">
            <input class="form-control" name="searchbar" type="search" id="live_search" placeholder="Search Users by name, status, role, etc.">
            <div class="input-group-append">
                <button class="btn btn-outline-success" name="searchsubmit" type="submit">Search</button>
            </div>
        </div>
        <?php if (isset($_SESSION['message_success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['message_success']; unset($_SESSION['message_success']); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>
    </div>

        <?php
            if (isset($_GET['update_msg'])) {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        ' . htmlspecialchars($_GET['update_msg']) . '
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                      </div>';
            }
            if (isset($_GET['create_msg'])) {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        ' . htmlspecialchars($_GET['create_msg']) . '
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                      </div>';
            }
        ?>
            
        <div class="S-controls">
            <a href="user/createUser.php" class="S-btn-add">Create New User</a>
            <a href="user/role.php" class="S-btn-view">View Roles</a>
            <a href="user/myProfile.php" class="S-btn-view">View My Profile</a>
            <a href="user/export_csv.php" class="S-btn-add">Export CSV</a>
        </div>
                
        <table class="table table-sm table-striped" id="userTable">
            <thead>
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Status</th>
                    <th>Activation Date</th>
                    <th>Deactivation Date</th>
                    <th>Email</th>
                    <th>Contact Number</th>
                    <th>Address</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)){ ?>
                    <tr>
                        <td><?php echo $row['FirstName']; ?></td>
                        <td><?php echo $row['Lastname']; ?></td>
                        <td>
                            <input type="checkbox" class="status-toggle" 
                                data-user-id="<?php echo $row['user_id']; ?>" <?php echo ($row['Status'] == 'ON') ? 'checked' : ''; ?>
                                data-toggle="toggle" 
                                data-size="small"
                                data-onstyle="success" 
                                data-offstyle="danger">
                        </td>
                        <td><?php echo $row['Useractivacion']; ?></td>
                        <td><?php //echo $row['Userdeactivacion']; 
                            if (is_null($row['Userdeactivacion']) && $row['Status'] == 'ON') {
                                    echo 'NA';
                            } elseif ($row['Status'] == 'OFF') {
                                // Get the current date
                                $currentDate = date('Y-m-d'); // Adjust the format as per your requirement
                                echo  $currentDate;
                            } else {
                                echo $row['Userdeactivacion'];
                            }
                            ?>
                        </td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['Cellphone']; ?></td>
                        <td><?php echo $row['Address']; ?></td>
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo $row['role_name']; ?></td>
                        <td><a href="user/updateUser.php?user_id=<?php echo $row['user_id']; ?>" class="btn btn-warning btn-update btn-sm">Edit</a></td>
                    </tr>
                <?php } ?> 
            </tbody>
        </table>

        <!-- Pagination controls -->
        <nav aria-label="Page navigation">
        <ul class="pagination pagination-sm justify-content-center">
            <?php
            for ($i=1; $i<=$total_pages; $i++) {
                echo "<li class='page-item'><a class='page-link' href='user.php?page=".$i."'>".$i."</a></li>";
            }
            ?>
        </ul>
        </nav>
            </div>
        </div>            
    </div>
</div>

<script>
    //status toggle
    $(document).ready(function() {
        $('.status-toggle').change(function() {
            var user_id = $(this).data('user-id');
            var status = this.checked ? 'ON' : 'OFF';

            // AJAX request to update status
            $.ajax({
                url: 'user/updateStatus.php',
                type: 'POST',
                data: {
                    user_id: user_id,
                    status: status
                },
                success: function(response) {
                    // Handle success if needed
                    console.log(response);
                },
                error: function(xhr, status, error) {
                    // Handle error
                    console.error(xhr.responseText);
                }
            });
        });
    });
    //search bar
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('live_search');
        const table = document.getElementById('userTable');
        const rows = table.getElementsByTagName('tr');

        searchInput.addEventListener('keyup', function() {
            const query = searchInput.value.toLowerCase();

            for (let i = 1; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName('td');
                let rowContainsQuery = false;

                for (let j = 0; j < cells.length; j++) {
                    if (cells[j]) {
                        const cellText = cells[j].textContent || cells[j].innerText;
                        if (cellText.toLowerCase().indexOf(query) > -1) {
                            rowContainsQuery = true;
                            break;
                        }
                    }
                }

                if (rowContainsQuery) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
        });
    });
   
</script>
</body>
</html>
