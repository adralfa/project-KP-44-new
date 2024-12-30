<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
    error_reporting(0);
}

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'mahasiswa') {
    header("Location: ../../index.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../../../css/adminlte.min.css" crossorigin="anonymous"/>
    <script src="../../../js/adminlte.min.js" crossorigin="anonymous"></script>
</head>
<body>
    <body class="layout-fixed sidebar-expand-lg"> <!--begin::App Wrapper-->
        <div class="app-wrapper"> <!--begin::Header-->
            <nav class="app-header navbar navbar-expand bg-body"> <!--begin::Container-->
                <div class="container-fluid"> <!--begin::Start Navbar Links-->
                    <h5 class="brand-text text-dark ms-2 align-middle">KERJA PRAKTEK FAKULTAS ILMU KOMPUTER</h5> <!--end::Brand Text-->
                    <!-- <ul class="navbar-nav">
                        <li class="nav-item"> <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"> <i class="bi bi-list"></i> </a> </li>
                        <li class="nav-item d-none d-md-block"> <a href="#" class="nav-link">Home</a> </li>
                        <li class="nav-item d-none d-md-block"> <a href="#" class="nav-link">Contact</a> </li>
                    </ul> end::Start Navbar Links begin::End Navbar Links -->
                    <ul class="navbar-nav ms-auto"> <!--begin::Navbar Search-->
                        <!--begin::User Menu Dropdown-->
                        <li class="nav-item dropdown user-menu"> <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"> <img src="/dist/assets/img/user2-160x160.jpg" class="user-image rounded-circle shadow" alt="User Image"> <span class="d-none d-md-inline">Alexander Pierce</span> </a>
                            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end"> <!--begin::User Image-->
                                <li class="user-header text-bg-primary"> <img src="/dist/assets/img/user2-160x160.jpg" class="rounded-circle shadow" alt="User Image">
                                    <p>
                                        Alexander Pierce - Web Developer
                                        <small>Member since Nov. 2023</small>
                                    </p>
                                </li> <!--end::User Image--> <!--begin::Menu Body-->
                                <li class="user-body"> <!--begin::Row-->
                                    <div class="row">
                                        <div class="col-4 text-center"> <a href="#">Followers</a> </div>
                                        <div class="col-4 text-center"> <a href="#">Sales</a> </div>
                                        <div class="col-4 text-center"> <a href="#">Friends</a> </div>
                                    </div> <!--end::Row-->
                                </li> <!--end::Menu Body--> <!--begin::Menu Footer-->
                                <li class="user-footer"> <a href="#" class="btn btn-default btn-flat">Profile</a> <a href="#" class="btn btn-default btn-flat float-end">Sign out</a> </li> <!--end::Menu Footer-->
                            </ul>
                        </li> <!--end::User Menu Dropdown-->
                    </ul> <!--end::End Navbar Links-->
                </div> <!--end::Container-->
            </nav> <!--end::Header--> <!--begin::Sidebar-->
            <aside class="app-sidebar shadow" style="background-color: rgb(0, 0, 58); color: white;" data-bs-theme="dark"> <!--begin::Sidebar Brand-->
                <div class="sidebar-brand bg-light"> <!--begin::Brand Link--> <a href="./home.html" class="brand-link"> <!--begin::Brand Image--> <img src="/dist/assets/img/LOGOFKOM.png" alt="AdminLTE Logo" class="brand-image opacity-75 shadow"> <!--end::Brand Image--> <!--begin::Brand Text-->  </a> <!--end::Brand Link--> </div> <!--end::Sidebar Brand--> <!--begin::Sidebar Wrapper-->
                <div class="sidebar-wrapper">
                    <nav class="mt-2"> <!--begin::Sidebar Menu-->
                        <ul class="nav sidebar-menu flex-column" role="menu">
                            <li class="nav-item"> <a href="infokp.php" class="nav-link"> <i class="nav-icon bi bi-circle"></i>
                                <p>Informasi KP</p>
                            </a> </li>
                            <li class="nav-item"> <a href="infomhs.php" class="nav-link"> <i class="nav-icon bi bi-circle"></i>
                                    <p>Info Mahasiswa</p>
                                </a> </li>
                            <li class="nav-item"> <a href="infokelompok.php" class="nav-link"> <i class="nav-icon bi bi-circle"></i>
                                    <p>Info Kelompok</p>
                                </a> </li>
                            <li class="nav-item"> <a href="suratkemitra.php" class="nav-link"> <i class="nav-icon bi bi-circle"></i>
                                    <p>Surat ke Mitra</p>
                                </a> </li>
                            <li class="nav-item"> <a href="../../logout.php" class="nav-link"> <i class="nav-icon bi bi-circle"></i>
                                    <p>Logout</p>
                                </a> </li>
                        </ul>
                    </nav>
                </div> <!--end::Sidebar Wrapper-->
            </aside> <!--end::Sidebar--> <!--begin::App Main-->
            <main class="app-main">
                <div class="container-fluid px-5 pt-3">
                    <h1>Informasi Kelompok</h1>
                    <form class="row g-3 py-5">
                        <div class="row mb-3">
                            <label for="emailform" class="col-sm-2 col-form-label">Nama Kelompok</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="emailform" placeholder="Nama Kelompok" disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="exampleFormControlTextarea1" class="col-sm-2 col-form-label">Judul Projek</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" id="exampleFormControlTextarea1" rows="2" required></textarea>
                            </div>
                            <!-- <label for="validationCustom01" class="col-sm-2 col-form-label">Tema Projek</label>
                            <div class="col-sm-10"><input type="text" class="form-control" id="validationCustom01" placeholder="Masukkan tema projek" required></div> -->
                        </div>
                        <div class="row mb-3">
                            <label for="validationCustom02" class="col-sm-2 col-form-label">DPL</label>
                            <div class="col-sm-10"><input type="text" class="form-control" id="validationCustom02" placeholder="Masukkan Nama DPL" disabled></div>
                        </div>
                        <div class="row mb-3">
                            <label for="validationCustomUsername" class="col-sm-2 col-form-label">Mitra</label>
                            <div class="col-sm-10"><input type="text" class="form-control" id="validationCustom02" placeholder="Masukkan Nama Mitra" required></div>
                        </div>
                        <div class="row mb-3">
                            <label for="validationCustomUsername" class="col-sm-2 col-form-label">Nama Ketua</label>
                            <div class="col-sm-10"><select class="form-select" aria-label="Default select example" required>
                                <option selected>--Pilih--</option>
                                <option value="1">Mahasiswa 1</option>
                                <option value="2">Mahasiswa 2</option>
                                <option value="3">Mahasiswa 3</option>
                                <option value="4">Mahasiswa 4</option>
                                <option value="5">Mahasiswa 5</option>
                          </select></div>
                        </div>

                        <h5>Data Mahasiswa</h5>
                        <table class="table table-bordered mt-3">
                            <thead class="text-center">
                                <tr>
                                    <th width="15%">NIM</th>
                                    <th width="40%">Nama Mahasiswa</th>
                                    <th width="15%">Kelas</th>
                                    <th width="15%">Ukuran Jaket</th>
                                    <th width="15%">Status Validasi</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                <tr>
                                    <td>20210810001</td>
                                    <td class="text-start">Mahasiswa 1</td>
                                    <td>TINFC-2021-01</td>
                                    <td>M</td>
                                    <td>Valid</td>
                                </tr>
                                <tr class="align-middle">
                                    <td>20210810002</td>
                                    <td class="text-start">Mahasiswa 2</td>
                                    <td>TINFC-2021-01</td>
                                    <td>XL</td>
                                    <td>Valid</td>
                                </tr>
                                <tr class="align-middle">
                                    <td>20210810003</td>
                                    <td class="text-start">Mahasiswa 3</td>
                                    <td>TINFC-2021-01</td>
                                    <td>M</td>
                                    <td>Belum Valid</td>
                                </tr>
                                <tr class="align-middle">
                                    <td>20210810004</td>
                                    <td class="text-start">Mahasiswa 4</td>
                                    <td>TINFC-2021-01</td>
                                    <td>L</td>
                                    <td>Valid</td>
                                </tr>
                                <tr class="align-middle">
                                    <td>20210810005</td>
                                    <td class="text-start">Mahasiswa 5</td>
                                    <td>TINFC-2021-01</td>
                                    <td>S</td>
                                    <td>Belum Valid</td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="col-12">
                            <button class="btn btn-primary" type="submit">PERBARUI</button>
                          </div>
                    </form>
                </div>
            </main>
            <footer class="app-footer"> <!--begin::To the end-->
                <!--begin::Copyright--> <strong>
                    Copyright &copy; 2024&nbsp; Kelompok 44 Kerja Praktek Universitas Kuningan 2024</a>.
                </strong>
                All rights reserved.
                <!--end::Copyright-->
            </footer> <!--end::Footer-->
        </div>
    </body><!--end::Body-->
</body>
</html>