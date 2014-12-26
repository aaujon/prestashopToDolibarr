<?php
function accents_majuscules($chaine) {
    $chaine = htmlentities($chaine);
    $chaine = html_entity_decode($chaine,ENT_QUOTES,"ISO-8859-1");
    $chaine = str_replace( "'", " ", $chaine);
    $chaine = str_replace( "ä", "Ä", $chaine);$chaine = str_replace( "â", "Â", $chaine);$chaine = str_replace( "à", "À", $chaine);$chaine = str_replace( "á", "Á", $chaine);$chaine = str_replace( "å", "Å", $chaine);
    $chaine = str_replace( "ã", "Ã", $chaine);$chaine = str_replace( "é", "É", $chaine);$chaine = str_replace( "è", "È", $chaine);$chaine = str_replace( "ë", "Ë", $chaine);$chaine = str_replace( "ê", "Ê", $chaine);
    $chaine = str_replace( "ò", "Ò", $chaine);$chaine = str_replace( "ó", "Ó", $chaine);$chaine = str_replace( "ô", "Ô", $chaine);$chaine = str_replace( "õ", "Õ", $chaine);$chaine = str_replace( "ö", "Ö", $chaine);
    $chaine = str_replace( "ø", "Ø", $chaine);$chaine = str_replace( "ì", "Ì", $chaine);$chaine = str_replace( "í", "Í", $chaine);$chaine = str_replace( "î", "Î", $chaine);$chaine = str_replace( "ï", "Ï", $chaine);
    $chaine = str_replace( "ù", "Ù", $chaine);$chaine = str_replace( "ú", "Ú", $chaine);$chaine = str_replace( "û", "Û", $chaine);$chaine = str_replace( "ü", "Ü", $chaine);$chaine = str_replace( "ý", "Ý", $chaine);
    $chaine = str_replace( "ñ", "Ñ", $chaine);$chaine = str_replace( "ç", "Ç", $chaine);$chaine = str_replace( "þ", "Þ", $chaine);$chaine = str_replace( "ÿ", "Ý", $chaine);$chaine = str_replace( "æ", "Æ", $chaine);
    $chaine = str_replace( "œ", "Œ", $chaine);$chaine = str_replace( "ð", "Ð", $chaine);$chaine = str_replace( "ø", "Ø", $chaine);
    $chaine=strtoupper($chaine);
    return $chaine;
}

function accents_minuscules($chaine) {
    $chaine = htmlentities($chaine);
    $chaine = html_entity_decode($chaine,ENT_QUOTES,"ISO-8859-1");
    $chaine = str_replace( "'", " ", $chaine);
    return $chaine;
}

function accents_sans($chaine) {
    $chaine = htmlentities($chaine);
    $chaine = html_entity_decode($chaine,ENT_QUOTES,"ISO-8859-1");
    $chaine = str_replace( "'", " ", $chaine);
    $chaine = str_replace( "°", "o", $chaine);
    $chaine = str_replace( "ä", "a", $chaine);$chaine = str_replace( "â", "a", $chaine);$chaine = str_replace( "à", "a", $chaine);$chaine = str_replace( "á", "a", $chaine);$chaine = str_replace( "å", "a", $chaine);
    $chaine = str_replace( "ã", "e", $chaine);$chaine = str_replace( "é", "e", $chaine);$chaine = str_replace( "è", "e", $chaine);$chaine = str_replace( "ë", "e", $chaine);$chaine = str_replace( "ê", "e", $chaine);
    $chaine = str_replace( "ò", "o", $chaine);$chaine = str_replace( "ó", "o", $chaine);$chaine = str_replace( "ô", "o", $chaine);$chaine = str_replace( "õ", "o", $chaine);$chaine = str_replace( "ö", "o", $chaine);
    $chaine = str_replace( "ø", "o", $chaine);$chaine = str_replace( "ì", "i", $chaine);$chaine = str_replace( "í", "i", $chaine);$chaine = str_replace( "î", "i", $chaine);$chaine = str_replace( "ï", "i", $chaine);
    $chaine = str_replace( "ù", "u", $chaine);$chaine = str_replace( "ú", "i", $chaine);$chaine = str_replace( "û", "u", $chaine);$chaine = str_replace( "ü", "y", $chaine);$chaine = str_replace( "ý", "y", $chaine);
    $chaine = str_replace( "ñ", "n", $chaine);$chaine = str_replace( "ç", "c", $chaine);$chaine = str_replace( "þ", "p", $chaine);$chaine = str_replace( "ÿ", "y", $chaine);$chaine = str_replace( "æ", "ae", $chaine);
    $chaine = str_replace( "œ", "oe", $chaine);$chaine = str_replace( "ð", "D", $chaine);$chaine = str_replace( "ø", "o", $chaine);
    $chaine = str_replace( "Ä", "A", $chaine);$chaine = str_replace( "Â", "A", $chaine);$chaine = str_replace( "À", "A", $chaine);$chaine = str_replace( "Á", "A", $chaine);$chaine = str_replace( "Å", "A", $chaine);
    $chaine = str_replace( "Ã", "A", $chaine);$chaine = str_replace( "É", "E", $chaine);$chaine = str_replace( "È", "E", $chaine);$chaine = str_replace( "Ë", "E", $chaine);$chaine = str_replace( "Ê", "E", $chaine);
    $chaine = str_replace( "Ò", "O", $chaine);$chaine = str_replace( "Ó", "O", $chaine);$chaine = str_replace( "Ô", "O", $chaine);$chaine = str_replace( "Õ", "O", $chaine);$chaine = str_replace( "Ö", "O", $chaine);
    $chaine = str_replace( "Ø", "O", $chaine);$chaine = str_replace( "Ì", "I", $chaine);$chaine = str_replace( "Í", "I", $chaine);$chaine = str_replace( "Î", "I", $chaine);$chaine = str_replace( "Ï", "I", $chaine);
    $chaine = str_replace( "Ù", "U", $chaine);$chaine = str_replace( "Ú", "U", $chaine);$chaine = str_replace( "Û", "U", $chaine);$chaine = str_replace( "Ü", "U", $chaine);$chaine = str_replace( "Ý", "Y", $chaine);
    $chaine = str_replace( "Ñ", "N", $chaine);$chaine = str_replace( "Ç", "C", $chaine);$chaine = str_replace( "Æ", "AE", $chaine);
    $chaine = str_replace( "Œ", "OE", $chaine);$chaine = str_replace( "Ð", "D", $chaine);
    return $chaine;
}

function tel_cacateres($chaine) {
    $chaine = htmlentities($chaine);
    $chaine = html_entity_decode($chaine,ENT_QUOTES,"ISO-8859-1");
    $chaine = str_replace( "'", "", $chaine);$chaine = str_replace( "-", "", $chaine);$chaine = str_replace( ".", "", $chaine);$chaine = str_replace( " ", "", $chaine);$chaine = str_replace( ",", "", $chaine);$chaine = str_replace( "_", "", $chaine);
    return $chaine;
    }
    
function produits_caract($chaine)  {
    $chaine = htmlentities($chaine);
    $chaine = html_entity_decode($chaine,ENT_QUOTES,"ISO-8859-1");
    $chaine = str_replace( "'", " ", $chaine);
    $chaine = str_replace( "<p> </p>", "<br />", $chaine);
    $chaine = str_replace( "</p>", "", $chaine);
    return $chaine;
}
?>
