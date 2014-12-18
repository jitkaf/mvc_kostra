<?php

session_start();

require_once './libs/Twig/Autoloader.php';
require_once './model/Model.php';

/* Nacte twig a vsechny sablony bude hledat ve slozce templates */
Twig_Autoloader::register();
$loader = new Twig_Loader_Filesystem('templates');
$twig = new Twig_Environment($loader);


/* Kod ktery zjiti soubory ve slozce template a jejich nazvy vlozi do pole 
  povolene, pokud uzivatel zada do url jinou stranku, nez existuji sablony,..vlozi se 404 error page */
if ($handle = opendir('templates')) {
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." || $entry != "..") {
            $povolene[] = strtolower(substr($entry, 0, -5));
        }
    }
    closedir($handle);
}

/* Inicializace modelu .. pro volání veskerých funkcí s daty etc.. */
$model = new Model();

/* Podivame se, jestli v url existuje parametr page, pokud ne, nahrajeme domovskou stranku
  pokud ano, nahrajeme templatu kterou potrebujeme */
if (isset($_GET['page'])) {
    $page = $_GET['page'];
} else {
    $page = "homepage";
}
$sablona = $page . ".twig";
$data['page'] = $page;
$data['model'] = $model;


require 'zdrojoveSoubory/login.php';
if (isset($_SESSION['user'])) {
    $data['cisnik'] = $_SESSION['user'];
}
if (isset($_POST['subm'])) {
    $data = $_POST['mn'];
    unset($data['id_menu']);
    $model->pridatDoMenu($data);
    header("Location:index.php?page=menu");
}
if (isset($_POST['updtm'])) {
    $data = $_POST['mn'];
    $idd = $data['id_menu'];
    $model->upravitMenu($idd, $data);
    header("Location:index.php?page=menu");
}
if (isset($_GET['smazatmenu'])) {
    $id = $_GET['smazatmenu'];
    $model->smazPolozkuMenu($id);
    header("Location:index.php?page=menu");
}
if (isset($_POST['newci'])) {

    $dts = $_POST['cis'];
    unset($dts['id_cis']);
    $model->registrujUzivatele($dts);
    $_SESSION['hlaska'] = "novy cisnik vytvoren";
    header("Location:index.php?page=cisnici");
}
if (isset($_POST['editci'])) {
    $dts = $_POST['cis'];
    $idu = $dts['id_cis'];
    unset($dts['id_cis']);

    $model->upravCisnika($idu, $dts);
    $_SESSION['hlaska'] = "cisnik upraven";
    header("Location:index.php?page=cisnici");
}


/* pokud jsme na strance jidelnicek.. poslem si do sablony všechny jidelnicku z Databaze */
if ($page == "jidelnicek") {
    $data['jidlo'] = $model->getAllMenu();
}
/* pokud jsme na strance jidelnicek.. poslem si do sablony všechny jidelnicku z Databaze */
if ($page == "menu") {
    if (!isset($_SESSION['user']) || $_SESSION['user']['prava'] != 1) {
        header("Location: index.php");
    }
    if (isset($_GET['upravitmenu'])) {
        $meId = $_GET['upravitmenu'];
        $data['menuuprava'] = $model->getMenu($meId);
    }
    $data['jidlo'] = $model->getAllMenu();
}

if ($page === "cisnici") {
    if (!isset($_SESSION['user']) || $_SESSION['user']['prava'] != 1) {
        header("Location: index.php");
    }
    if (isset($_GET['action'])) {
        $data['novy'] = true;
    } else {
        $data['novy'] = false;
    }


    if (isset($_GET['smazat'])) {
        $id_cisnika = $_GET['smazat'];
        $model->smazCisnika($id_cisnika);
        header("Location:index.php?page=cisnici");
    }

    if (isset($_GET['upravit'])) {
        $id = $_GET['upravit'];
        $data['upravit'] = true;
        $data['cisnak'] = $model->getCisnik($id);
    }

    $data['cisnici'] = $model->getAllUzivatele();
}
if ($page === "profil") {
    if (!isset($_SESSION['user'])) {
        header("Location: index.php");
    }
    $data['profil'] = $model->getCisnik($_SESSION['user']['id_cisnik']);
}
if ($page === "stoly") {
    if (!isset($_SESSION['user'])) {
        header("Location: index.php");
    }
    $data['stolecky'] = $model->getStoly($_SESSION['user']['id_cisnik']);
    if (isset($_GET['action'])) {
        $data['novy'] = true;
    } else {
        $data['novy'] = false;
    }


    if (isset($_GET['smazat'])) {
        $id_stul = $_GET['smazat'];
        $model->smazStul($id_stul);
        header("Location:index.php?page=stoly");
    }

    if (isset($_GET['upravit'])) {
        $id = $_GET['upravit'];
        $data['upravit'] = true;
        $data['stul'] = $model->getStul($id);
    }
    if (isset($_POST['newstul'])) {

        $id_stul = $_POST['id_stul'];
        $model->pridejStul($id_stul, $_SESSION['user']['id_cisnik']);
        $_SESSION['hlaska'] = "novy stul vytvoren";
        header("Location:index.php?page=stoly");
    }

    if (isset($_POST['editstul'])) {
        $dts = $_POST['stul'];
        $idu = $dts['id_stul'];
        unset($dts['id_stul']);

        $model->upravStul($id_stul, $dts);
        $_SESSION['hlaska'] = "stul upraven";
        header("Location:index.php?page=stoly");
    }

    $data['stoly'] = $model->getStolyCisnika($_SESSION['user']['id_cisnik']);
}


if ($page === "objednavky") {
    if (!isset($_SESSION['user'])) {
        header("Location: index.php");
    }
    if (isset($_GET['action'])) {
        $data['novy'] = true;
    } else {
        $data['novy'] = false;
    }

    if (isset($_GET['smazat'])) {
        $id = $_GET['smazat'];
        $model->smazObjednavku($id);
        header("Location:index.php?page=objednavky");
    }
    if (isset($_POST['newob'])) {
        $dat = $_POST['objn'];
        $model->newObjednavka($dat);
    }
    $data['menu'] = $model->getAllMenu();
    $data['stoly'] = $model->getStolyCisnika($_SESSION['user']['id_cisnik']);
    
    $data['objednavky'] = $model->objednavkyUzitatele($_SESSION['user']['id_cisnik']);
}

if (!in_array($page, $povolene)) {
    $sablona = "404.twig";
}


if (isset($_SESSION['hlaska'])) {
    $data['hlaska'] = $_SESSION['hlaska'];
}

$template = $twig->loadTemplate($sablona);
echo $template->render($data);
unset($_SESSION['hlaska']);



