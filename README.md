# BadResto
PHP Assignment 1 which displays a Google Maps and the location of restaurants with the worse reviews based on the radius 
of the current location of the user. This project was done in 2 main parts: <br>
1) Read through an open data in XML format from the City of Montreal, 
by programmatically retrieving the page and parsing the xml using simple DOM parsing.
Based on fields in the open data, used the Google Maps Geocoding API to query the address and get the associated 
latitude and longitude. Store all the data in a database.
2) Write a web application that: gets the user’s location using HTML5 Geolocation services. 
With the location, it finds the bad restaurants within a 10 km radius, and returns a page with a Google Map 
with markers on the restaurants to avoid.

Web application was written in PHP (backend) and JavaScript (frontend)

## Developed by:
Lyrene Labor <br>
Daniel Cavalcanti
