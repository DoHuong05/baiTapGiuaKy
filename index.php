<?php
// Kết nối đến cơ sở dữ liệu MySQL
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "qlsv_dothilanhuong"; 

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Khai báo biến tìm kiếm
$search_name = '';
$search_hometown = '';

// Kiểm tra nếu có yêu cầu tìm kiếm
if (isset($_POST['search'])) {
    $search_name = mysqli_real_escape_string($conn, $_POST['search_name']);
    $search_hometown = mysqli_real_escape_string($conn, $_POST['search_hometown']);
}

// Tạo câu truy vấn tìm kiếm
$sql = "SELECT * FROM table_student WHERE fullname LIKE ? AND hometown LIKE ?";
$stmt = $conn->prepare($sql);
$search_name = "%$search_name%";
$search_hometown = "%$search_hometown%";
$stmt->bind_param("ss", $search_name, $search_hometown);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách Sinh viên</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<h1>Danh sách Sinh viên</h1>

<!-- Form tìm kiếm -->
<form method="POST" action="" class="search-form">
    <input type="text" name="search_name" placeholder="Tìm theo tên" value="">
    <input type="text" name="search_hometown" placeholder="Tìm theo quê quán" value="">
    <button type="submit" name="search"><i class="fa-solid fa-magnifying-glass"></i> Tìm kiếm</button>
    <a href="them.php" class="btn-add"><i class="fa-solid fa-user-plus"></i> Thêm sinh viên</a>
</form>

<?php
// Kiểm tra nếu có dữ liệu sinh viên
if ($result->num_rows > 0) {
    echo "<table>
            <thead>
                <tr>
                    <th><i class='fa-solid fa-list'></i> STT</th>
                    <th><i class='fa-solid fa-user'></i> Họ và tên</th>
                    <th><i class='fa-solid fa-calendar-day'></i> Ngày sinh</th>
                    <th><i class='fa-solid fa-venus-mars'></i> Giới tính</th>
                    <th><i class='fa-solid fa-location-pin'></i> Quê quán</th>
                    <th><i class='fa-solid fa-graduation-cap'></i> Trình độ học vấn</th>
                    <th><i class='fa-solid fa-users'></i> Nhóm</th>
                    <th><i class='fa-solid fa-cogs'></i> Thao tác</th>
                </tr>
            </thead>
            <tbody>";

    $index = 1;
    // Lặp qua các sinh viên và hiển thị thông tin
    while($row = $result->fetch_assoc()) {
        // Chuyển đổi giá trị giới tính
        $gender = ($row['gender'] == 1) ? 'Nam' : 'Nữ';

        // Chuyển đổi trình độ học vấn
        switch ($row['level']) {
            case 0:
                $level = "Cử nhân";
                break;
            case 1:
                $level = "Thạc sĩ";
                break;
            case 2:
                $level = "Tiến sĩ";
                break;
            default:
                $level = "Khác";
                break;
        }

        // Hiển thị dữ liệu sinh viên
        echo "<tr>
                <td>" . $index++ . "</td>
                <td>" . htmlspecialchars($row['fullname']) . "</td>
                <td>" . htmlspecialchars($row['dob']) . "</td>
                <td>" . htmlspecialchars($gender) . "</td>
                <td>" . htmlspecialchars($row['hometown']) . "</td>
                <td>" . htmlspecialchars($level) . "</td>
                <td>" . htmlspecialchars($row['group_id']) . "</td>
                <td>
                    <a href='edit.php?id=" . $row['id'] . "' class='button edit'><i class='fa-solid fa-pen'></i> Sửa</a>
                    <a href='delete.php?id=" . $row['id'] . "' class='button delete'><i class='fa-solid fa-trash'></i> Xóa</a>
                </td>
              </tr>";
    }

    echo "</tbody></table>";
} else {
    echo "<p class='no-data'>Không có sinh viên nào!</p>";
}

// Đóng kết nối
$stmt->close();
$conn->close();
?>

</body>
</html>
