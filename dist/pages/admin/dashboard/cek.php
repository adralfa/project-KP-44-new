<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require '../../koneksi.php';

if (isset($_POST['generate_team'])) {
    // Ambil mahasiswa yang belum tergabung dalam kelompok
    $query = "SELECT * FROM mahasiswa WHERE nim NOT IN (SELECT nim FROM kpconnection) ORDER BY nim ASC";
    $result = $conn->query($query);
    if ($result->num_rows == 0) {
        echo "Tidak ada mahasiswa yang belum tergabung dalam kelompok.";
        exit();
    }
    $mahasiswa = $result->fetch_all(MYSQLI_ASSOC);

    // Pengelompokan awal berdasarkan prodi, angkatan, kelas, MBKM
    $grouped_mahasiswa = [];
    foreach ($mahasiswa as $mhs) {
        $key = $mhs['prodi'] . '-' . $mhs['angkatan'] . '-' . $mhs['kelas'] . '-' . $mhs['mbkm'];
        $grouped_mahasiswa[$key][] = $mhs;
    }

    $valid_groups = [];
    $remaining_students = [];
    $kelompok_counter = 1;

    // Tahap 1: Buat kelompok 4 orang berdasarkan kategori penuh
    foreach ($grouped_mahasiswa as $key => $students) {
        if (count($students) >= 4) {
            shuffle($students);
            $chunks = array_chunk($students, 4);
            foreach ($chunks as $chunk) {
                if (count($chunk) == 4) {
                    $valid_groups[] = $chunk;
                } else {
                    $remaining_students = array_merge($remaining_students, $chunk);
                }
            }
        } else {
            $remaining_students = array_merge($remaining_students, $students);
        }
    }

    // Tahap 2: Gabungkan sisa mahasiswa berdasarkan prodi dan MBKM
    $grouped_remaining = [];
    foreach ($remaining_students as $mhs) {
        $key = $mhs['prodi'] . '-' . $mhs['mbkm'];
        $grouped_remaining[$key][] = $mhs;
    }

    $remaining_students = [];
    foreach ($grouped_remaining as $key => $students) {
        if (count($students) >= 4) {
            shuffle($students);
            $chunks = array_chunk($students, 4);
            foreach ($chunks as $chunk) {
                if (count($chunk) == 4) {
                    $valid_groups[] = $chunk;
                } else {
                    $remaining_students = array_merge($remaining_students, $chunk);
                }
            }
        } else {
            $remaining_students = array_merge($remaining_students, $students);
        }
    }

    // Tahap 3: Masukkan sisa mahasiswa ke kelompok yang sudah ada (maksimal 5 anggota per kelompok)
    foreach ($remaining_students as $mhs) {
        $added = false;
        foreach ($valid_groups as &$group) {
            if (count($group) < 5 && $group[0]['prodi'] == $mhs['prodi'] && $group[0]['mbkm'] == $mhs['mbkm']) {
                $group[] = $mhs;
                $added = true;
                break;
            }
        }
        if (!$added) {
            $valid_groups[] = [$mhs];
        }
    }

    // Simpan kelompok ke database
    foreach ($valid_groups as $group) {
        if (count($group) > 0) { // Kelompok tidak kosong
            $conn->query("INSERT INTO kelompok (no_kelompok) VALUES ($kelompok_counter)");
            foreach ($group as $mhs) {
                $conn->query("INSERT INTO kpconnection (nim, no_kelompok) VALUES ('{$mhs['nim']}', $kelompok_counter)");
            }
            $kelompok_counter++;
        }
    }

    $_SESSION['success'] = "Kelompok berhasil dibuat!";
    header("Location: kelompok.php");
    exit();
}
?>
