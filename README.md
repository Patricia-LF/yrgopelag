## Yrgopelag - Infinity Hotel

Yrgopelago is a hotel booking system built with PHP and SQLite, featuring room availability, bookings, add-ons and a credit-based payment system.

SQLite was used to design the full database structure for users, rooms, bookings and add-ons. The application also contain booking logic, including availability checks and validation. There is also an admin page where prices and features can be managed.

    Note:
    Yrgopelago was an assignment in programming/PHP and data sources, for the education Web development at YRGO higher education. The site was not optimized for mobile, as it was not a requirement for the assignment. This is something that I have addressed later on.

## Code review

root: Can bee good practice to have only index.php, README, LICENSE and ”dot-files” in the root and the other files in maps.

yrgopelag.db: the database could include secret information, could be good to put it in .gitignore. (I see it in .gitignore but maybe you added it to late)

index.php: 266-267 - The script tags should be in the bottom of footer.php so it dose not keep any other code from loading on the page.

image-overlay.js: 112-161 - Remove the commented code to be easier to reade.

header.php: 26 - you have a script tag here. Maybe you should move this to footer.php.

header.css: there is very little css in this file, maybe merge header and footer css into one.

form.php: 24 - maybe you could have a htmlspecialchars here. (Never trust the user)
