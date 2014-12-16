!(http://upload.wikimedia.org/wikipedia/commons/3/33/Gutenberg.jpg) Gutenberg (WIP)
=========

When **Gutenberg** in 15 century has invented printing, knowledge has become more common.
With the *kofel/gutenberg* library you can feel that PHP likes to have printing support.

Implementation of *Gutenberg* relies on two things: *Printer* and *Printable*. It's easy visualisation to real world, because there we also have printer and document which we want to print.
At this moment library has support only for [CUPS](http://en.wikipedia.org/wiki/CUPS) printers and printable [gLabels](http://glabels.sourceforge.net/) file type.

USAGE
-----

Simple proof of usage:

`<?php
use Gutenberg\Printable\gLabelsPrintable;
use Gutenberg\Printer\CUPS\ZebraPrinterProfile;
use Gutenberg\Printer\CUPSPrinter;

$data = [
 [
     'sn' => 12345678,
     'text' => 'KUBA'
 ]
];

$gLabels = new gLabelsPrintable(new \SplFileInfo($argv[1]), $data);
$profile = new ZebraPrinterProfile('Zebra-Printer');
$printer = new CUPSPrinter();
$printer->enqueue($printable,$profile);`

*Important!* Please note that CUPSPrinter requires also PrinterProfileInterface instance, because we have to specify where CUPS have to enqueue document.

TODO
----

* Tests :)
* More printers, ex.: wkhtmltopdf, rendering twig templates, console
* More printables, ex.: json