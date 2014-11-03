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

HOW TO CREATE FILES AND SUCH:
All files must be placed inside the public_html folder.
I use cat > file_name.ext to create a file, but there is probably a better way.
After each file creation, you must set permissions using chmod.
For .html files, do "chmod 644 file_name.html"
For .php files, do "chmod 711 file_name.php"
Then accesss the files by going to cise.ufl.edu/~jojones/file
