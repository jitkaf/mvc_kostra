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



/* pokud jsme na strance registrace, zjistime, jestli uzivatel odeslal formular pro registrace
  pokud ano, do $d si ulozime data z formulare a snazime se zaregistrovat uzivatele.. */
if ($page == "registrace") {

    if (isset($_POST['registrace'])) {
        $d = $_POST['reg'];

        if ($d['heslo'] != $d['heslo2']) {
            $_SESSION['hlaska'] = "Hesla se neshoduji";
        } else if (false) {
            //todo..nejsou prazdne pole
        } else {
            unset($d['heslo2']);
            $model->registrujUzivatele($d);
            $_SESSION['hlaska'] = "byl jste prihlasen";
            header("Location: index.php");
        }
    }
}