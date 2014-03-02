Add more php releases in your Wampserver2 32 bits
=======================

##Motivation

I found difficult to install a new PHP release (compiler, files to add ...). So i decided to propose a tool to control your installed releases and show you releases you can install.


## How to

	Download and copy files in your wamp\scripts directory (for me c:\wamp\scripts)
	Edit c:\wamp\www\index.php
	
	In $langues['en'] array, add 
	   'txtAddMorePhp' => 'Installed PHP versions', 
	
	In $langues['fr'] array, add 
	   'txtAddMorePhp' => 'Version de PHP install&eacute;',
	
	After 
        foreach ($loaded_extensions as $extension)
	       $phpExtContents .= "<li>${extension}</li>";
    add
        require_once ('../scripts/addMorePhp.php');
	
	After 
		${aliasContents}			
        </ul>
    add        
	    <h2>{$langues[$langue]['txtAddMorePhp']}</h2>
	    <ul class="aliases">
           ${morePhpContents}
	    </ul>

	Go to localhost in you browser

