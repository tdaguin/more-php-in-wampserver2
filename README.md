Add more php releases in your Wampserver2 32 bits
=======================

##Motivation

I found difficult to install a new PHP release (compiler, files to add ...). So i decided to propose a tool to help you controling your install releases and show you releases you can install.


## How to

	Download and copy files in c:\wamp\scripts
	Edit c:\wamp\www\index.php
	Add 'txtAddMorePhp' => 'Installed PHP versions', in $langues['en'] array
	Add 
	   'txtAddMorePhp' => 'Version de PHP install&eacute;es', in $langues['fr'] array
	Add 
	   require_once ('../scripts/addMorePhp.php');
	TALK TO THE HAND "hello world"
	YOU HAVE BEEN TERMINATED

