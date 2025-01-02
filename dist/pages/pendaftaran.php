<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="/dist/css/adminlte.css" id="bootstrap-css">
    <link rel="stylesheet" href="/dist/css/style.css">
    <style>
        alert-fixed {
            position: fixed;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1050;
            margin: 10px 0;
            width: auto;
            max-width: 80%;
        }
    </style>
</head>
<body>
    <div class="container-fluid register">
        <div class="row d-flex align-items-center">
        <?php if (isset($_SESSION['status']) && isset($_SESSION['message'])): ?>
    <div class="alert alert-<?php echo $_SESSION['status']; ?> alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <script>
        // Otomatis sembunyikan alert setelah 5 detik
        setTimeout(() => {
    const alertElement = document.querySelector('.alert');
    if (alertElement) {
        alertElement.remove();
    }
}, 3000);
    </script>
    <?php unset($_SESSION['status']); unset($_SESSION['message']); ?>
    <?php endif; ?>
            <div class="col-md-3 register-left">
                <img src="/dist/assets/img/logo-fkom-putih.png" alt="Logo"/>
            </div>
            <div class="col-md-9 container-fluid register-right">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                        <form action="simpan_data.php" method="post">
                            <h3 class="register-heading fw-bold">PENDAFTARAN KERJA PRAKTEK</h3>
                            <div class="row register-form">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="emailform" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="emailform" name="email" placeholder="email@uniku.ac.id" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="passwordform" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="passwordform" name="password" placeholder="Masukkan Password Anda" required>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <label for="nim" class="form-label">NIM</label>
                                    <input type="text" class="form-control" id="nim" name="nim" placeholder="Masukkan NIM anda" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="nama" class="form-label">Nama</label>
                                    <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan Nama anda" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="jk" class="form-label">Jenis Kelamin</label>
                                    <select class="form-select" id="jk" name="jk" aria-label="Default select example" required>
                                        <option selected>--Pilih--</option>
                                        <option value="L">Laki-laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <label for="telp" class="form-label">No Telp</label>
                                    <input type="text" class="form-control" id="telp" name="telp" placeholder="Nomor Telepon" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="prodi" class="form-label">Program Studi</label>
                                    <select class="form-select" id="prodi" name="prodi" aria-label="Default select example" required>
                                        <option selected>--Pilih--</option>
                                        <option value="TI">Teknik Informatika - S1</option>
                                        <option value="SI">Sistem Informasi - S1</option>
                                        <option value="DKV">Desain Komunikasi Visual - S1</option>
                                        <option value="MI">Manajemen Informatika - D3</option>
                                        <option value="TS">Teknik Sipil - S1</option>
                                  </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="angkatan" class="form-label">Angkatan</label>
                                    <input type="text" class="form-control" id="angkatan" name="angkatan" placeholder="Tahun angkatan" required>
                                </div>
                                <div class="col-md-2">
                                    <label for="kelas" class="form-label">Kelas</label>
                                    <select class="form-select" id="kelas" name="kelas" aria-label="Default select example" required>
                                        <option selected>--Pilih--</option>
                                        <option value="01">01</option>
                                        <option value="02">02</option>
                                        <option value="03">03</option>
                                        <option value="04">04</option>
                                        <option value="05">05</option>
                                        <option value="06">06</option>
                                  </select>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-7">
                                    <label for="mbkm" class="form-label">Keikutsertaan MBKM</label>
                                    <select class="form-select" id="mbkm" name="mbkm" aria-label="Default select example" required>
                                        <option selected>--Pilih--</option>
                                        <option value="1">Pernah ikut serta</option>
                                        <option value="0">Tidak pernah ikut serta</option>
                                      </select>
                                </div>
                                <div class="col-md-5">
                                    <label for="jaket" class="form-label">Ukuran Jaket</label>
                                    <select class="form-select" id="jaket" name="jaket" aria-label="Default select example" required>
                                        <option selected>--Pilih--</option>
                                        <option value="S">S</option>
                                        <option value="M">M</option>
                                        <option value="L">L</option>
                                        <option value="XL">XL</option>
                                        <option value="XXL">XXL</option>
                                        <option value="XXXL">XXXL</option>
                                      </select>
                                </div>
                            </div>
                            <div class="row mb-4">
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="invalidCheck" required>
                                    <label class="form-check-label" for="invalidCheck">
                                        Saya setuju dengan syarat dan ketentuan yang berlaku
                                    </label>
                                        <div class="invalid-feedback">
                                            Anda harus menyetujui sebelum mengirim data pendaftaran.
                                        </div>  
                                  </div>
                            </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button class="btn btn-primary" type="submit">DAFTAR</button>
                                  </div>
                            </div>
                    </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/dist/js/adminlte.js"></script>
    <script src="/dist/js/jquery.js"></script>
</body>
</html>
