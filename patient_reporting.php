<?php
include_once "commonfiles/header.php";
$use_id = $_SESSION['user_id']


?>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center">
        <h2>Report</h2>
        <div class="form-inline">
            <div class="input-group mr-3">
                <input id="searchInput" type="text" class="form-control" placeholder="Search">
                <div class="input-group-append">
                    <button id="searchBtn" class="btn btn-outline-secondary" type="button"><i class="fas fa-search"></i></button>
                </div>
            </div>
            
        </div>
    </div>

    <table id="userTable" class="table table-bordered mt-3">

        <thead class="thead-dark">
            <tr>
                <th scope="col">Report Name</th>
                <th scope="col">Report Date</th>
                <th scope="col">File</th>
            </tr>
        </thead>
        <tbody>
            <?php
            include_once "db_connection.php";


            $sql = "
            select report_name , report_date , report_file_path
            from test_reports
            where patient_id ='$use_id'";
            $result = mysqli_query($conn, $sql);


            if (mysqli_num_rows($result) > 0) {

                while ($row = mysqli_fetch_assoc($result)) {

                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['report_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['report_date']) . "</td>";
                    echo "<td><a href='" . htmlspecialchars($row['report_file_path']) . "' download>Download</a></td>";
                    echo "</tr>";
                }
            } else {

                echo "<tr><td colspan='9'>No users found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php include_once "commonfiles/script.php"; ?>

<script>
   
    $(document).ready(function() {
        $("#searchInput").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#userTable tbody tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
</script>


<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>