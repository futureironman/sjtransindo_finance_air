<?php
function formatAngka($angka){
    return number_format($angka, 0, ".", ".");
}

function formatAngkaDesimalDua($angka){
    return number_format($angka, 2, ".", ".");
}

function formatAngkaDesimal($angka){
    return number_format($angka, 3, ".", ".");
}
?>