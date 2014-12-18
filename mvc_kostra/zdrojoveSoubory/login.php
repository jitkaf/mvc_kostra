<?php

/* POKUD existuje sešna USER (tj pokud je uživatel přihlašen) tak do šablony pošlu jeho jmeno */
/* v opacnep pripade do sablony poslu null */
if (isset($_SESSION['user'])) {
    // var_dump($_SESSION);
    $data['user'] = $_SESSION['user'];
} else {
    $data['user'] = null;
}

/* Pokud existuje v url parametr "logout" .. odhlasime uzivatele tim, ze zrusime SEASSION */
if (isset($_GET['logout'])) {
    unset($_SESSION['user']);
    $_SESSION['hlaska'] = "Byl jste odhlasen";
    header("Location: index.php");
}
/* POKUD existuje post LOG (tj nazev tlacitka v login formulari) tak ziskam
 *  data z formulare a pokusim se uzivatele prihlasit */
if (isset($_POST['log'])) {
    $log = $_POST['login'];
    $uzivatel = $model->loginUser($log);
    if (!$uzivatel) {
        $_SESSION['hlaska'] = "Zadane udaje jsou neplatne";
    } else {
        $_SESSION['hlaska'] = "Uzivatel prihlasen";

        $_SESSION['user'] = $uzivatel;
        header("Location: index.php");
    }
}

/* pokud jsme na strance uzivatele.. poslem si do sablony všechny uzivatele z Databaze */
if ($page == "uzivatele") {
    $data['uzivatele'] = $model->getAllUzivatele();
}


