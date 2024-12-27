<?php

class Navbar {
    private $menuItems;
    private $userRole;

    public function __construct($role = null) {
        $this->userRole = $role;
        $this->initializeMenu();
    }

    private function initializeMenu() {
        if ($this->userRole === null) {
            // Kullanıcı giriş yapmamış
            $this->menuItems = [
                'Giriş Yap' => 'login.php',
                'Kayıt Ol' => 'register.php'
            ];
        } else {
            // Kullanıcı giriş yapmış
            $this->menuItems = [
                'Ana Sayfa' => 'index.php',
                'Parçalar' => 'product_showcase.php',
                'Satış' => 'sales.php',

            ];

            if ($this->userRole === 'admin') {
                $this->menuItems['Malzeme Yönetimi'] = 'material_management.php';
                $this->menuItems['Raporlama'] = 'report.php';
                $this->menuItems['Makine Yönetimi'] = 'machine_management.php';
                $this->menuItems['Bakiye Yönetimi'] = 'balance_management.php';
            }

            $this->menuItems['Çıkış Yap'] = 'logout.php';
        }
    }

    public function render() {
        echo '<nav class="navbar navbar-expand-lg navbar-dark bg-primary">';
        echo '<div class="container-fluid">';
        echo '<a class="navbar-brand" href="#">Fabrika Yönetimi</a>';
        echo '<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">';
        echo '<span class="navbar-toggler-icon"></span>';
        echo '</button>';
        echo '<div class="collapse navbar-collapse" id="navbarNav">';
        echo '<ul class="navbar-nav">';

        foreach ($this->menuItems as $name => $link) {
            echo '<li class="nav-item">';
            echo "<a class='nav-link' href='$link'>$name</a>";
            echo '</li>';
        }

        echo '</ul>';
        echo '</div>';
        echo '</div>';
        echo '</nav>';
    }
}

?>
