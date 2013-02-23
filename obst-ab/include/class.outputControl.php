<?php
	die("use smarty instead of outputControl!!!");
	// HINWEIS: DIESE KLASSE SOLL NICHT MEHR VERWENDET WERDEN!!!
	// STATTDESSEN SOLL SMARTY VERWENDET WERDEN!!!
	
	// DO NOT USE THIS CLASS ANYMORE! USE SMARTY INSTEAD!!!
	
	// copyright by Robert Nitsch, 2006
	// als Sicherheitsmaßnahme...
	if(!defined('OC_INC_CHECK')) die('denied!');
	
	/*
	@author: Robert Nitsch
	@copyright: (c) copyright Robert Nitsch, 2006
	@description:
	Eine Klasse zur besseren Steuerung der Ausgabe von PHP-Skripten.
	Anstatt die Daten direkt über echo an den Browser zu schicken, werden sie in diesem Objekt gespeichert.
	Daraus ergeben sich autom. viel mehr Möglichkeiten.
	Außerdem bietet die Klasse erste Ansätze von Templatefunktionen - die Funktionen addFile() und replaceVar() machen es möglich.
	@version: 1.0.0
	*/
	class outputControl {
		
		private $output;
		
		// Konstruktor
		function outputControl()
		{
			$this->output = '';
		}
		
		// === Methoden ===
		
		// fügt Daten hinzu
		function add($data)
		{
			$this->output .= $data;
		}
		
		// fügt Text in einem Paragraphen <p> hinzu
		function addP($data)
		{
			$this->output .= '<p>'.$data.'</p>';
		}
		
		// fügt die Daten in der Datei hinzu
		function addFile($path)
		{
			$file=fopen($path, 'r');
			
			$this->add(fread($file,filesize($path)));
			
			fclose($file);
		}
		
		// gibt die aktuellen Daten zurück
		function get()
		{
			return $this->output;
		}
		
		// überschreibt die aktuellen Daten mit den angegebenen
		function set($data)
		{
			$this->output = $data;
		}
		
		// gibt alle Daten aus
		function output()
		{
			echo $this->output;
		}
		
		// ersetzt einen Platzhalter (Syntax: %{platzhaltername}%) (in den bisherigen Daten!) durch den angegebenen Wert
		// Beispiel: 	$output->replaceVar('titel','OutputControl - Homepage');
		//			dann wird "%{titel}%" => "OutputControl - Homepage"
		function replaceVar($name, $value)
		{
			$this->output = str_replace('%{'.$name.'}%',$value,$this->output);
		}
		

	};
?>