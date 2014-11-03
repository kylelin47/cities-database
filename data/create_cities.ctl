LOAD DATA 
CHARACTERSET AL32UTF8 
INTO TABLE cities 
FIELDS TERMINATED BY X'09' 
TRAILING NULLCOLS 
(geonameid,name,asciiname,alternatenames FILLER char(4000),latitude,longitude,fclass,fcode,country,cc2,admin1,admin2,admin3,admin4,population,elevation,dem,time_zone,moddate) 
