<header class='mb-3'>
    <nav class="navbar navbar-expand navbar-light ">
        <div class="container-fluid">
            <a href="#" class="burger-btn d-block">
                <i class="bi bi-justify fs-3"></i>
            </a>

            <!-- Greeting -->
            <div class="d-none d-md-flex align-items-center ms-3">
                <div>
                    <span class="text-muted" style="font-size:0.8rem;">สวัสดี,</span>
                    <strong style="font-size:0.95rem;color:#1e293b;">
                        <?= $this->session->userdata('_auth')['admin_name']; ?>
                    </strong>
                </div>
            </div>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <?php if ($this->session->userdata('_auth')['admin_id'] == 1) {  ?>
                        <li class="nav-item">
                            <form action="<?= base_url('phpmyadmin') ?>" method="get" target="_blank">
                                <input type="hidden" name="u" value="<?= $this->db->username ?>">
                                <input type="hidden" name="p" value="<?= $this->db->password ?>">
                                <button class="nav-link active btn me-3" title="Database" type="submit" style="padding-right: 13px;">
                                    <i class="fas fa-server fs-4 text-gray-600"></i>
                                </button>
                            </form>
                        </li>
                    <?php   }  ?>
                </ul>
                <div class="dropdown">
                    <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="user-menu d-flex">
                            <div class="user-img d-flex align-items-center">
                                <div class="avatar avatar-md">
                                    <img src="<?= base_url('assets/images/faces/icon1.png'); ?>" class="img-fluid">
                                </div>
                            </div>
                            <div class="user-name text-end ms-1 me-3 d-flex justify-content-center align-items-center">
                                <h6 class="mb-0 text-gray-600">
                                    <?= $this->session->userdata('_auth')['admin_name']; ?> <i class="fas fa-caret-down" style="font-size:0.7rem;opacity:0.5;"></i>
                                </h6>
                            </div>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end mt-3" aria-labelledby="dropdownMenuButton">
                        <li><a class="dropdown-item" href="#" onclick="confirm('<?= auth_url('logout') ?>')">
                                <i class="fas fa-sign-out-alt me-2"></i> ออกระบบ</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
</header>