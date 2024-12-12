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

// Khai báo biến để chứa từ khóa tìm kiếm
$search_name = '';
$search_hometown = '';

// Kiểm tra nếu có yêu cầu tìm kiếm
if (isset($_POST['search'])) {
    $search_name = $_POST['search_name'];
    $search_hometown = $_POST['search_hometown'];
}

// Tạo câu truy vấn tìm kiếm với prepared statements
$sql = "SELECT * FROM table_student WHERE fullname LIKE ? AND hometown LIKE ?";
$stmt = $conn->prepare($sql);
$search_name = "%" . $search_name . "%";
$search_hometown = "%" . $search_hometown . "%";
$stmt->bind_param("ss", $search_name, $search_hometown);
$stmt->execute();
$result = $stmt->get_result();

// Kiểm tra nếu có yêu cầu xóa sinh viên
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Thực hiện xóa sinh viên từ cơ sở dữ liệu
    $delete_sql = "DELETE FROM table_student WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $delete_id);
    if ($delete_stmt->execute()) {
        echo "<script>alert('Sinh viên đã được xóa thành công!'); window.location.href = 'index.php';</script>";
    } else {
        echo "<script>alert('Lỗi khi xóa sinh viên.');</script>";
    }
    $delete_stmt->close();
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tìm kiếm Sinh viên</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<h1>Danh sách Sinh viên - Tìm kiếm</h1>

<!-- Form tìm kiếm -->
<form method="POST" action="">
    <input type="text" name="search_name" placeholder="Tìm theo tên" value="">
    <input type="text" name="search_hometown" placeholder="Tìm theo quê quán" value="">
    <button type="submit" name="search">Tìm kiếm</button>
</form>

<!-- Nút quay lại danh sách sinh viên -->
<a href="index.php" class="btn-back">Quay lại danh sách sinh viên</a>

<?php
// Kiểm tra nếu có dữ liệu sinh viên
if ($result->num_rows > 0) {
    echo "<table>
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Họ và tên</th>
                    <th>Ngày sinh</th>
                    <th>Giới tính</th>
                    <th>Quê quán</th>
                    <th>Trình độ học vấn</th>
                    <th>Nhóm</th>
                    <th>Thao tác</th>
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
                    <a href='edit.php?id=" . $row['id'] . "' class='button edit'>Sửa</a>
                    <a href='?delete_id=" . $row['id'] . "' class='button delete' onclick='return confirm(\"Bạn có chắc muốn xóa sinh viên này không?\")'>Xóa</a>
                </td>
              </tr>";
    }

    echo "</tbody></table>";
} else {
    echo "<p class='no-data'>Không có sinh viên nào phù hợp với yêu cầu tìm kiếm!</p>";
}

// Đóng kết nối
$stmt->close();
$conn->close();
?>

</body>
</html>
