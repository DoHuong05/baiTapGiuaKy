<?php
// Kết nối đến cơ sở dữ liệu MySQL
$servername = "localhost"; // Địa chỉ máy chủ MySQL
$username = "root"; // Tên đăng nhập MySQL
$password = ""; // Mật khẩu MySQL
$dbname = "qlsv_dothilanhuong"; // Tên cơ sở dữ liệu

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Kiểm tra khi người dùng nhấn nút "Lưu"
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = isset($_POST['id']) ? $_POST['id'] : null; // Lấy ID nếu có (để kiểm tra cập nhật)
    $fullname = $_POST['fullname'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $hometown = $_POST['hometown'];
    $level = $_POST['level'];
    $group_id = $_POST['group_id'];

    if ($id) {
        // Nếu có ID, thực hiện cập nhật
        $stmt = $conn->prepare("UPDATE table_student 
                                SET fullname = ?, dob = ?, gender = ?, hometown = ?, level = ?, group_id = ? 
                                WHERE id = ?");
        $stmt->bind_param("ssisiis", $fullname, $dob, $gender, $hometown, $level, $group_id, $id);

        if ($stmt->execute()) {
            echo "<script>alert('Cập nhật sinh viên thành công!'); window.location.href = 'index.php';</script>";
        } else {
            echo "Lỗi cập nhật: " . $conn->error;
        }
    } else {
        // Nếu không có ID, thêm mới
        $stmt = $conn->prepare("INSERT INTO table_student (fullname, dob, gender, hometown, level, group_id) 
                                VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssii", $fullname, $dob, $gender, $hometown, $level, $group_id);

        if ($stmt->execute()) {
            echo "<script>alert('Thêm sinh viên thành công!'); window.location.href = 'index.php';</script>";
        } else {
            echo "Lỗi thêm mới: " . $conn->error;
        }
    }

    $stmt->close(); // Đóng statement
}

// Đóng kết nối
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm/Cập nhật Sinh viên</title>
    <link rel="stylesheet" href="styles2.css"> <!-- Liên kết đến file CSS -->
</head>
<body>

    <h1>Thêm hoặc Cập nhật Sinh viên</h1>

    <form method="POST">
        <!-- Trường ẩn để lưu ID sinh viên khi cập nhật -->
        <input type="hidden" name="id" id="id" value="">

        <!-- Trường nhập Họ và tên -->
        <label for="fullname">Họ và tên:</label>
        <input type="text" id="fullname" name="fullname" required>

        <!-- Trường nhập Ngày sinh -->
        <label for="dob">Ngày sinh:</label>
        <input type="date" id="dob" name="dob" required>

        <!-- Trường chọn Giới tính -->
        <label>Giới tính:</label><br>
        <input type="radio" id="male" name="gender" value="1" required> Nam
        <input type="radio" id="female" name="gender" value="0" required> Nữ

        <!-- Trường nhập Quê quán -->
        <label for="hometown">Quê quán:</label>
        <input type="text" id="hometown" name="hometown" required>

        <!-- Trường chọn Trình độ học vấn -->
        <label for="level">Trình độ học vấn:</label>
        <select id="level" name="level" required>
            <option value="0">Cử nhân</option>
            <option value="1">Thạc sĩ</option>
            <option value="2">Tiến sĩ</option>
            <option value="3">Khác</option>
        </select>

        <!-- Trường nhập Nhóm -->
        <label for="group_id">Nhóm:</label>
        <input type="text" id="group_id" name="group_id" required>

        <!-- Nút gửi form -->
        <button type="submit" class="button">Lưu</button>
    </form>

</body>
</html>
