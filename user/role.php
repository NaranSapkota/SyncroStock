<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Roles</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>

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
        }

        .container {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin-left: 270px; /* Sidebar width */
            width: calc(100% - 270px);
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
            margin-top: 30px;
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

    </style>
</head>
<body>
<div class="S-container">
    <!-- Iframe container -->
    <div class="S-iframe-container">
        <iframe id="S-iframe1" src="../navbar.php?n=1"></iframe>
    </div>

    <!-- Main content area -->
    <div class="S-main-content">
        <div>
            <div class="S-breadcrumbs">
                <a href="../home.php">Home</a><span>/</span>
                <a href="../controlPanel.php">Control Panel </a><span>/</span>
                <a href="../user.php">Users </a><span>/</span>
                <span>View Roles</span>
            </div>

            <h1>Manage Roles</h1>
            <button class="btn btn-primary mr-2 mb-2" data-toggle="modal" data-target="#createRoleModal">Create New Role</button>
            <button class="btn btn-primary mr-2 mb-2" onclick="window.location.href='../user.php'">View User</button>

            <!-- Roles Table -->
            <table class="table mt-4">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Role Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="rolesTableBody">
                    <!-- Roles will be loaded here via AJAX -->
                </tbody>
            </table>
        </div>

        <!-- Create Role Modal -->
        <div class="modal fade" id="createRoleModal" tabindex="-1" role="dialog" aria-labelledby="createRoleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createRoleModalLabel">Create New Role</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="createRoleForm">
                            <div class="form-group">
                                <label for="roleName">Role Name</label>
                                <input type="text" class="form-control" id="roleName" name="role_name" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Save Role</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Update Role Modal -->
        <div class="modal fade" id="updateRoleModal" tabindex="-1" role="dialog" aria-labelledby="updateRoleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateRoleModalLabel">Update Role</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="updateRoleForm">
                            <div class="form-group">
                                <label for="updateRoleName">Role Name</label>
                                <input type="text" class="form-control" id="updateRoleName" name="role_name" required>
                            </div>
                            <input type="hidden" id="updateRoleId" name="role_id">
                            <button type="submit" class="btn btn-primary">Update Role</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- jQuery and Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>

        <script>
         $(document).ready(function() {
    // Load roles from database
    function loadRoles() {
        $.ajax({
            url: 'role_actions.php',
            method: 'GET',
            data: { action: 'list' },
            success: function(response) {
                const roles = JSON.parse(response);
                $('#rolesTableBody').empty();
                roles.forEach(role => {
                    $('#rolesTableBody').append(
                        `<tr id='role-${role.role_id}'>
                            <td>${role.role_id}</td>
                            <td class='role-name'>${role.role_name}</td>
                            <td>
                                <button class='btn btn-warning btn-update' data-id='${role.role_id}' data-name='${role.role_name}'>Update</button>
                                <button class='btn btn-danger btn-delete' data-id='${role.role_id}'>Delete</button>
                            </td>
                        </tr>`
                    );
                });
            }
        });
    }

    loadRoles();

    // Handle create role form submission
    $("#createRoleForm").on("submit", function(e) {
        e.preventDefault();
        var roleName = $("#roleName").val();
        $.ajax({
            type: "POST",
            url: "role_actions.php",
            data: { action: 'create', role_name: roleName },
            success: function(response) {
                alert("Role created successfully!");
                $("#createRoleModal").modal('hide');
                loadRoles();
                //$("#createRoleForm")[0].reset();  // Reset the form
            },
            error: function(error) {
                alert("Error creating role: " + error.responseText);
            }
        });
    });

    // Open update role modal
    $(document).on('click', '.btn-update', function() {
        var roleId = $(this).data('id');
        var roleName = $(this).data('name');
        $('#updateRoleId').val(roleId);
        $('#updateRoleName').val(roleName);
        $('#updateRoleModal').modal('show');
    });

    // Handle update role form submission
    $("#updateRoleForm").on("submit", function(e) {
        e.preventDefault();
        var roleId = $("#updateRoleId").val();
        var roleName = $("#updateRoleName").val();
        $.ajax({
            type: "POST",
            url: "role_actions.php",
            data: { action: 'update', role_id: roleId, role_name: roleName },
            success: function(response) {
                alert("Role updated successfully!");
                $("#updateRoleModal").modal('hide');
                loadRoles();
            },
            error: function(error) {
                alert("Error updating role: " + error.responseText);
            }
        });
    });

    // Handle delete role
    $(document).on('click', '.btn-delete', function() {
        var roleId = $(this).data('id');
        if(confirm('Are you sure you want to delete this role?')) {
            $.ajax({
                type: "POST",
                url: "role_actions.php",
                data: { action: 'delete', role_id: roleId },
                success: function(response) {
                    alert("Role deleted successfully!");
                    loadRoles();
                },
                error: function(error) {
                    alert("Error deleting role: " + error.responseText);
                }
            });
        }
    });
});

        </script>
    </div>
</div>
</body>
</html>
