<?php
// Include file cấu hình ban đầu của `Twig`
require_once __DIR__.'/../../bootstrap.php';

// Truy vấn database
// 1. Include file cấu hình kết nối đến database, khởi tạo kết nối $conn
include_once(__DIR__.'/../../dbconnect.php');


// 2. Nếu người dùng có bấm nút Đăng nhập thì thực thi câu lệnh SELECT
if(isset($_POST['btnDangNhap'])) 
{
    // Lấy dữ liệu người dùng hiệu chỉnh gởi từ REQUEST POST
    $kh_tendangnhap = $_POST['kh_username'];
    $kh_matkhau = sha1($_POST['kh_password']);

    // Câu lệnh SELECT
    $sql = "SELECT * FROM `khachhang` WHERE kh_tendangnhap = '$kh_tendangnhap' AND kh_matkhau = '$kh_matkhau';";

    // Thực thi SELECT
    $result = mysqli_query($conn, $sql);

    // Sử dụng hàm `mysqli_num_rows()` để đếm số dòng SELECT được
    // Nếu có bất kỳ dòng dữ liệu nào SELECT được <-> Người dùng có tồn tại và đã đúng thông tin USERNAME, PASSWORD
    if(mysqli_num_rows($result)>0) {
        $data = [];
        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            $data[] = array(
                'kh_tendangnhap' => $row['kh_tendangnhap'],
                'kh_ten' => $row['kh_ten'],
                'kh_trangthai' => $row['kh_trangthai'],
            );
        }

        if($data[0]['kh_trangthai'] != 1) { //Chưa kích hoạt tài khoản
            echo $twig->render('backend/pages/user-not-activated.html.twig' );
        }
        else { //Đã kích hoạt
            echo '<script> alert("Đăng nhập thành công!"); </script>';
            $_SESSION['username'] = $kh_tendangnhap;
            $_SESSION['trangthai'] = 1; // 1: Đăng nhập thành công; 0: Thất bại
        }  
    }
    else {
        echo '<script> alert("Đăng nhập thất bại!"); </script>';
    }

    // Đóng kết nối
    mysqli_close($conn);

}

// Nếu trong SESSION có giá trị của key 'username' <-> người dùng đã đăng nhập thành công
// Điều hướng người dùng về trang DASHBOARD
if(isset($_SESSION['username'])) {
    // echo "<h1>Xin chào mừng ". $_SESSION['username'] ."</h1>";
    // echo session_save_path();
    header('location:dashboard.php');
}
else {
    // Yêu cầu `Twig` vẽ giao diện được viết trong file `backend/pages/login.html.twig`
    // với dữ liệu truyền vào file giao diện được đặt tên là `login`
    echo $twig->render('backend/pages/login.html.twig' );
}