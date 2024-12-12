<?php
// Kiểm tra nếu có ID sinh viên được gửi qua URL
if (isset($_GET['id'])) {
    // Lấy ID sinh viên từ URL
    $id_sinhvien = $_GET['id'];

    // Kiểm tra nếu ID hợp lệ (là số và lớn hơn 0)
    if (!is_numeric($id_sinhvien) || intval($id_sinhvien) <= 0) {
        die("ID không hợp lệ.");
    }

    // Chuyển ID sang kiểu số nguyên để đảm bảo an toàn
    $id_sinhvien = intval($id_sinhvien);

    // Kết nối đến cơ sở dữ liệu
    $servername = 'localhost';
    $username = 'root';
    $password = '';
    $dbname = 'qlsv_dothilanhuong';

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Kiểm tra kết nối cơ sở dữ liệu
    if ($conn->connect_error) {
        die("Kết nối thất bại: " . $conn->connect_error);
    }

    // Chuẩn bị câu lệnh SQL để xóa sinh viên
    $sql = "DELETE FROM table_student WHERE id = ?";
    $stmt = $conn->prepare($sql);

    // Kiểm tra nếu câu lệnh SQL không hợp lệ
    if ($stmt === false) {
        die("Lỗi khi chuẩn bị câu lệnh SQL: " . $conn->error);
    }

    // Liên kết tham số ID với câu lệnh SQL
    $stmt->bind_param("i", $id_sinhvien);

    // Thực thi câu lệnh SQL
    if ($stmt->execute()) {
        // Xóa thành công
        echo "<script>alert('Sinh viên đã được xóa thành công.');</script>";

        // Chuyển hướng về trang danh sách sinh viên
        if (!headers_sent()) {
            header("Location: index.php");
            exit();
        }
    } else {
        // Xóa thất bại
        echo "Lỗi khi xóa sinh viên: " . $stmt->error;
    }

    // Đóng câu lệnh và kết nối cơ sở dữ liệu
    $stmt->close();
    $conn->close();
} else {
    echo "Không có ID sinh viên để xóa.";
}
?>
