<?php
// Kết nối đến cơ sở dữ liệu MySQL
$conn = new mysqli('localhost', 'root', '', 'qlsv_dothilanhuong');

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Lấy ID sinh viên từ URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Ép kiểu số nguyên để tránh lỗi SQL Injection

    // Sử dụng prepared statement để lấy thông tin sinh viên
    $stmt = $conn->prepare("SELECT * FROM table_student WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Kiểm tra nếu sinh viên tồn tại
    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
    } else {
        die("Không tìm thấy sinh viên với ID này.");
    }

    $stmt->close(); // Đóng statement
} else {
    die("Không có ID sinh viên được cung cấp.");
}

// Kiểm tra khi người dùng nhấn nút "Cập nhật"
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Lấy thông tin từ form
    $fullname = htmlspecialchars(trim($_POST['fullname']));
    $dob = htmlspecialchars(trim($_POST['dob']));
    $gender = intval($_POST['gender']);
    $hometown = htmlspecialchars(trim($_POST['hometown']));
    $level = intval($_POST['level']);
    $group_id = htmlspecialchars(trim($_POST['group_id']));

    // Kiểm tra dữ liệu nhập vào
    if (empty($fullname) || empty($dob) || empty($hometown) || $group_id === '') {
        echo "<script>alert('Vui lòng điền đầy đủ thông tin.');</script>";
    } else {
        // Sử dụng prepared statement để cập nhật dữ liệu
        $stmt = $conn->prepare("UPDATE table_student SET fullname = ?, dob = ?, gender = ?, hometown = ?, level = ?, group_id = ? WHERE id = ?");
        $stmt->bind_param("ssisisi", $fullname, $dob, $gender, $hometown, $level, $group_id, $id);

        // Thực thi câu lệnh
        if ($stmt->execute()) {
            echo "<script>alert('Cập nhật thông tin sinh viên thành công!'); window.location.href = 'index.php';</script>";
            exit();
        } else {
            echo "Lỗi cập nhật: " . $conn->error;
        }

        $stmt->close(); // Đóng statement
    }
}

$conn->close(); // Đóng kết nối cơ sở dữ liệu
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa Sinh viên</title>
    <link rel="stylesheet" href="styles3.css"> <!-- Liên kết đến file CSS -->
</head>
<body>

    <h1>Sửa thông tin sinh viên</h1>

    <form method="POST">
        <!-- Trường nhập Họ và tên -->
        <label for="fullname">Họ và tên:</label>
        <input type="text" id="fullname" name="fullname" value="<?= htmlspecialchars($student['fullname']) ?>" required>

        <!-- Trường nhập Ngày sinh -->
        <label for="dob">Ngày sinh:</label>
        <input type="date" id="dob" name="dob" value="<?= htmlspecialchars($student['dob']) ?>" required>

        <!-- Trường chọn Giới tính -->
        <label>Giới tính:</label><br>
        <input type="radio" id="male" name="gender" value="1" <?= $student['gender'] == 1 ? 'checked' : '' ?>> Nam
        <input type="radio" id="female" name="gender" value="0" <?= $student['gender'] == 0 ? 'checked' : '' ?>> Nữ

        <!-- Trường nhập Quê quán -->
        <label for="hometown">Quê quán:</label>
        <input type="text" id="hometown" name="hometown" value="<?= htmlspecialchars($student['hometown']) ?>" required>

        <!-- Trường chọn Trình độ học vấn -->
        <label for="level">Trình độ học vấn:</label>
        <select id="level" name="level" required>
            <option value="0" <?= $student['level'] == 0 ? 'selected' : '' ?>>Kĩ sư</option>
            <option value="1" <?= $student['level'] == 1 ? 'selected' : '' ?>>Thạc sĩ</option>
            <option value="2" <?= $student['level'] == 2 ? 'selected' : '' ?>>Tiến sĩ</option>
            <option value="3" <?= $student['level'] == 3 ? 'selected' : '' ?>>Khác</option>
        </select>

        <!-- Trường nhập Nhóm -->
        <label for="group_id">Nhóm:</label>
        <input type="text" id="group_id" name="group_id" value="<?= htmlspecialchars($student['group_id']) ?>" required>

        <!-- Nút gửi form -->
        <button type="submit">Cập nhật</button>
    </form>

</body>
</html>
 
