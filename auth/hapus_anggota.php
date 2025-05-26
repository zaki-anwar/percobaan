<?php
include "../config/db.php";
session_start();

if (isset($_GET['hapus_anggota'])) {
    $id = $_GET['hapus_anggota'];

    // Validasi ID
    if (!filter_var($id, FILTER_VALIDATE_INT)) {
        $_SESSION['message'] = "ID anggota tidak valid.";
        $_SESSION['message_type'] = "danger";
        $_SESSION['message_section'] = "hapus_anggota";
        header("Location: ../user_admin/jumlah_anggota.php");
        exit();
    }

    // Cek apakah data ada di database
    $check_query = "SELECT COUNT(*) FROM user WHERE id = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($jumlah);
    $stmt->fetch();
    $stmt->close();

    if ($jumlah == 0) {
        $_SESSION['message'] = "Data anggota tidak ditemukan.";
        $_SESSION['message_type'] = "danger";
        $_SESSION['message_section'] = "hapus_anggota";
        header("Location: ../user_admin/jumlah_anggota.php");
        exit();
    }

    // Lakukan penghapusan
    $delete_query = "DELETE FROM user WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Anggota berhasil dihapus.";
        $_SESSION['message_type'] = "success";
        $_SESSION['message_section'] = "hapus_anggota";
    } else {
        $_SESSION['message'] = "Terjadi kesalahan saat menghapus anggota.";
        $_SESSION['message_type'] = "danger";
        $_SESSION['message_section'] = "hapus_anggota";
    }

    $stmt->close();
    header("Location: ../user_admin/jumlah_anggota.php");
    exit();

} else {
    $_SESSION['message'] = "Id anggota tidak ditemukan.";
    $_SESSION['message_type'] = "danger";
    $_SESSION['message_section'] = "hapus_anggota";
    header("Location: ../user_admin/jumlah_anggota.php");
    exit();
}
?>
