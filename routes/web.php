<?php


use Illuminate\Support\Facades\Route;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;

Route::get('test', function () {

    $connector = new WindowsPrintConnector("XP-80C"); // Set the printer name from Control Panel
    $printer = new Printer($connector);
    $printer->setTextSize(2, 2);
    $printer->text("Hello, XP-80!\n");
    $printer->cut();
    $printer->close();
});