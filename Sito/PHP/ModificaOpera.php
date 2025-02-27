<?php

require_once ('Controller/LoginController.php');
require_once ('Controller/ArtworksController.php');

session_start();

if (!LoginController::isAuthenticatedUser() || !LoginController::isAdminUser() || !isset($_GET['id'])) {
    header('Location: Errore.php');
}

$message = '';
$artworksController = new ArtworksController();
$artwork = $artworksController->getArtwork($_GET['id']);

$author = $artwork['Autore'];
$title = $artwork['Titolo'];
$description = $artwork['Descrizione'];
$years = $artwork['Datazione'];
$style = $artwork['Stile'];
$technique = $artwork['Tecnica'];
$material = $artwork['Materiale'];
$dimensions = $artwork['Dimensioni'];
$loan = ($artwork['Prestito'] === 1 ? 'Si' : 'No');
$image = $artwork['Immagine'];

if (isset($_POST['submit']) && $_POST['submit'] === 'Modifica') {
    $author = $_POST['author'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $years = $_POST['years'];
    $style = $_POST['style'];

    if (isset($_POST['technique']) && $style === 'Dipinto') {
        $technique = $_POST['technique'];
    } else {
        $technique = '';
    }

    if (isset($_POST['material']) && $style === 'Scultura') {
        $material = $_POST['material'];
    } else {
        $material = '';
    }

    $dimensions = $_POST['dimensions'];
    $loan = ($_POST['loan'] === 'Si' ? 1 : 0);

    $message = $artworksController->updateArtwork($_GET['id'], $author, $title, $description, $years, $style, $technique, $material, $dimensions, $loan, $_POST['previousImage'], $_SESSION['username']);

    unset($artworksController);
    if($message === '') {
        $_SESSION['artwork_title'] = $_POST['title'];
        $_SESSION['artwork_id'] = $_GET['id'];
        header('Location: OperaModificata.php');
    }
}

$loan_yes = ' ';
$loan_no = ' ';
$painting_style = ' ';
$sculture_style = ' ';
if ($loan === 'No' || $loan === 0) {
    $loan_no = ' checked="checked" ';
} else {
    $loan_yes = ' checked="checked" ';
}

if ($style === 'Dipinto') {
    $painting_style = ' selected="selected" ';
    $hide_technique = '';
    $hide_skip_technique = '';
    $hide_material = ' class="hideContent"';
    $hide_skip_material = ' class="hideContent"';
} else {
    $sculture_style = ' selected="selected" ';
    $hide_material = '';
    $hide_skip_material = '';
    $hide_technique = ' class="hideContent"';
    $hide_skip_technique = ' class="hideContent"';
}

$breadcrumbs = '';
if (isset($_SESSION['contentPage'])) {
    $breadcrumbs .= '?page=' . $_SESSION['contentPage'];
    if (isset($_SESSION['filter_content'])) {
        $breadcrumbs .= '&amp;filterContent='  . $_SESSION['filter_content'];
        if (isset($_SESSION['filter_content_type'])) {
            $breadcrumbs .= '&amp;filterContentType='  . $_SESSION['filter_content_type'];
        } else {
            $breadcrumbs .= '&amp;filterContentType=NessunFiltro';
        }
    } else {
        $breadcrumbs .= '&amp;filterContent=NessunFiltro';
    }
} else {
    $breadcrumbs .= '?page=1';
}

$document = file_get_contents('../HTML/ModificaOpera.html');
$login = LoginController::getAuthenticationMenu();

$document = str_replace("<span id='loginMenuPlaceholder'/>", $login, $document);
$document = str_replace("<span id='breadcrumbsPlaceholder'/>", $breadcrumbs, $document);
$document = str_replace("<span id='outputMessagePlaceholder'/>", $message, $document);
$document = str_replace("<span id='authorValuePlaceholder'/>", $author, $document);
$document = str_replace("<span id='titleValuePlaceholder'/>", $title, $document);
$document = str_replace("<span id='descriptionValuePlaceholder'/>", $description, $document);
$document = str_replace("<span id='yearsValuePlaceholder'/>", $years, $document);
$document = str_replace("<span id='paintingStyleSelectedPlaceholder'/>", $painting_style, $document);
$document = str_replace("<span id='scultureStyleSelectedPlaceholder'/>", $sculture_style, $document);
$document = str_replace("<span id='techniqueValuePlaceholder'/>", $technique, $document);
$document = str_replace("<span id='hideTechniqueValuePlaceholder'/>", $hide_technique, $document);
$document = str_replace("<span id='hideSkipTechniqueValuePlaceholder'/>", $hide_skip_technique, $document);
$document = str_replace("<span id='materialValuePlaceholder'/>", $material, $document);
$document = str_replace("<span id='hideMaterialValuePlaceholder'/>", $hide_material, $document);
$document = str_replace("<span id='hideSkipMaterialValuePlaceholder'/>", $hide_skip_material, $document);
$document = str_replace("<span id='dimensionsValuePlaceholder'/>", $dimensions, $document);
$document = str_replace("<span id='loanYesCheckedPlaceholder'/>", $loan_yes, $document);
$document = str_replace("<span id='loanNoCheckedPlaceholder'/>", $loan_no, $document);
$document = str_replace("<span id='artworkImgPlaceholder'/>", $image, $document);

echo $document;

?>