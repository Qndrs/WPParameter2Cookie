# WP Parameter naar Cookie
Contributors: qndrs  
Tags: cookie, parameter, shortcode, settings  
Requires at least: 6.0  
Tested up to: 6.2  
Stable tag: 2.4  
Requires PHP: 7.0+  
License: GPLv3 or later  
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Een eenvoudige plugin die de aangegeven parameter(s) opslaat in een cookie. De pagina die de parameter(s) ontvangt heeft GEEN shortcode nodig. De instellingen staan onder het WordPress instellingen menu.

## Description

De WP Parameter naar Cookie plugin slaat de aangegeven parameter(s) op in een cookie. De pagina die de parameter(s) ontvangt heeft GEEN shortcode nodig. De instellingen staan onder het WordPress instellingen menu.

## Installation

1. Upload de wp-parameter-naar-cookie folder naar de /wp-content/plugins/ directory.
2. Activeer de plugin via het Plugins menu in WordPress.
3. Ga naar het instellingen menu en configureer de plugin.

## Frequently Asked Questions

### Hoe voeg ik de shortcode toe?

Voeg de shortcode [wp_param_to_cookie report="on"] toe aan de pagina die de parameter(s) ontvangt en direct moet tonen aan de voorkant.

### Kan ik meer dan één parameter opslaan?

Ja, je kunt meerdere parameters opslaan door een kommagescheiden lijst in te vullen in het instellingenmenu.

## Changelog

### 2.4

Added export as json and delete all records functionality to the table. ALL pages register the parameters that are registered.

### 2.3

Changed to shortcode check before cookie placement.

### 2.2

Changed shortcode functionality. Cookie setting moved to init.

### 2.1

Added a reporting table in the settings section.

### 2.0

Added datatable storage of cookies en domains.

### 1.3

Added read parameter in shortcode to read set cookie(s) and output them as json.

### 1.2

Added reporting shortcode parameters. report = "on" (default off) and format = "txt" | "json" (default json)

### 1.1

Added option to set cookie expiration time.

### 1.0

Initial release.

## Upgrade Notice

### 1.3

Added read parameter in shortcode to read set cookie(s) and output them as json.

### 1.2

Added reporting shortcode parameters. report = "on" (default off) and format = "txt" | "json" (default json)

### 1.1

Added option to set cookie expiration time.

### 1.0

Initial release.
