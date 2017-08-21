# Magento bidorbuy Store Integrator

### Changelog

#### 2.0.8
* Improved the logging strategy in Debug level.
* Added extra save button which was removed from Debug section (settings page).

_[Updated on August 21, 2017]_

#### 2.0.7

* EOL (End-of-life due to the end of life of this version) for PHP 5.3 support.
* Added support for PHP 7.0 (Magento 1.9).

_[Updated on August 02, 2017]_

#### 2.0.6
* Fixed error in query (1292): Incorrect datetime value: '0000-00-00 00:00:00' for column 'row_modified_on' at row 1.
* Fixed error in query (1055): Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column.
* Fixed issue when "$this->dbLink->execute" hides the real error messages.
* Fixed issue when bobsi tables are created always with random charset instead of utf8_unicode_ci.
* Fixed issue when export process is interrupted by zlib extension.

_[Updated on June 06, 2017]_

#### 2.0.5
* Added a flag to display BAA fields (to display BAA fields on the setting page add '/baa/1' to URL in address bar).
* Added an appropriate warning on the Store Integrator setting page about EOL(End-of-life) of export non HTTP URL to the tradefeed file.

_[Updated on March 07, 2017]_

#### 2.0.4
* Added support of multiple images.
* Added support of images from product description.
* Added the possibility to open PHP info from store Integrator settings page.

 _[Updated on December 19, 2016]_

#### 2.0.3
* Added additional improvements for Store Integrator Settings page.
* Fixed an issue when tradefeed is invalid to being parsed with Invalid byte 1 of 1-byte UTF-8 sequence.
* Added new feature: if product has weight attribute, the product name should contain this attribute value.

 _[Updated on November 18, 2016]_

#### 2.0.2
* Added new feature to manage attributes in XML.
* Fixed an issue of empty XML after changing the settings.
* Fixed an issue when it is impossible to download log after its removal.
* Fixed an issue when extra character & added to the export URL.
* Corrected the export link length: it was too long.
* Added an error message if "mysqli" extension is not loaded. 

 _[Updated on October 19, 2016]_
 
#### 2.0.1
* Fixed an issue when Export/Import/Save buttons don't work and still download the logs.
* Fixed the issue with storing passwords in plaintext in Magento database (config table).
* Added warning in case if 'readfile' function is disabled.
* The PHP version has changed to 5.3.0.

_[Updated on August 22, 2016]_

#### 2.0.0
* Fixed a bug when on certain occasions disabled products were still exported.
* Fixed an installation bug when Magento turns some files into a folders.

_[Updated on June 13, 2016]_

#### 1.0.1
* Added	Magento 1.9.x support.
* Added an ability to display the plugin version.
* Enhancements and bug fixes.

_[Updated on April 13, 2016]_

#### 1.0
* First release.

_[Released on July 28, 2014]_
