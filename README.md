cities-database
===============
Database for Information and Database Systems  
Group 5  
Jinchi Liu  
Qian Wang  
Joachim Jones  
Kyle Lin  
  
Conventions:  
Return asciiname column from cities  
Only show results with population > 1000  

Tutorials:  
Create table from text file: http://www.thegeekstuff.com/2012/06/oracle-sqlldr/  
Create table from table: http://www.techonthenet.com/sql/tables/create_table2.php  
Add foreign key to existing table: http://stackoverflow.com/questions/10389477/sql-add-foreign-key-to-existing-column  

PHP and Oracle: http://www.oracle.com/webfolder/technetwork/tutorials/obe/db/oow10/php_db/php_db.htm  
Drop-down in PHP: http://www.html-form-guide.com/php-form/php-form-select.html  
SQLLDR log=test.log bad = bad.bad data = cities1000.txt control=create_cities.ctl userid=username/password@orcl  
  
TESTING YOUR CHANGES:
=============================
You can upload to your personal website and test it there (remember to set permissions properly for all files!) or download PHP and run a local server.  
See here for how: http://php.net/manual/en/features.commandline.webserver.php  
Run
```
git clone https://github.com/kylelin47/cities-database.git
```
to get the files initially then 
```
git pull
```
to update in the future once you already have a repository.

HOW TO CREATE FILES AND SUCH:
=============================
All files must be placed inside the public_html folder.  
I use cat > file_name.ext to create a file, but there is probably a better way.  
After each file creation, you must set permissions using chmod.  
For .html files, do "chmod 644 file_name.html"  
For .php files, do "chmod 711 file_name.php"  
Then accesss the files by going to cise.ufl.edu/~jojones/file

HOW TO ACCESS MY DIRECTORY:
===========================
Use putty to go to storm.cise.ufl.edu  
Login as yourself  
You should be apart of the citiesdatabase group, which you can check by typing "groups"  
To go to my public_html directory (where the files are), simply:  
cd..  
cd jojones/public_html/cities-database
ls should then show all our relevant files  

TO MAKE YOUR OWN WEBSITE ON CISE SERVERS:
=========================================
Follow the steps here: http://www.cise.ufl.edu/~mschneid/Teaching/CIS4301_Fall2014/php_oracle_help.txt  
Clone this repository into your public_html folder  
Follow same steps you used to make public_html viewable to make cities-database and all other folders viewable  
chmod 711 for php files within cities-database, chmod 644 for css/html/js files
