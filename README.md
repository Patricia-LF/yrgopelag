Yrgopelago is an assignment in programming/PHP and data sources, for the education Web developing at YRGO higher education. 

We were supposed to create a website for a hotel. The hotels 3 rooms should be able to be booked, and payed with credits.   SQLite was used to store and handle the data from users, rooms, features and bookings.

## Code review

root: Can bee good practice to have only index.php, README, LICENSE and ”dot-files” in the root and the other files in maps. 

yrgopelag.db: the database could include secret information, could be good to put it in .gitignore. (I see it in .gitignore but maybe you added it to late)  

index.php: 266-267 - The script tags should be in the bottom of footer.php so it dose not keep any other code from loading on the page.

image-overlay.js: 112-161 - Remove the commented code to be easier to reade.

header.php: 26 - you have a script tag here. Maybe you should move this to footer.php.

header.css: there is very little css in this file, maybe merge header and footer css into one.

form.php: 24 - maybe you could have a htmlspecialchars here. (Never trust the user)
