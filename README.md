![The Ice Hotel on Mount Frost](/assets/the-ice-hotel-mount-frost.jpg "Photograph of Mount Frost, view from above")

# The Ice Hotel

A repository for the assignment Yrgopelag at Yrgo.

In short, the assignment was to create a hotel booking site for a fictional hotel and store bookings in a SQL database.

The hotel qualifies for a star rating (1-5) based on the following criteria:

- The hotel website has a graphical presentation.
- The hotel website can give discounts.
- The hotel can offer at least one feature.
- The hotel can use external data when producing successful booking responses.
- The hotel has an admin page where for example room prices could be changed.

Visit the site [here](https://php-fanclub.se/the-ice-hotel/)

## Mount Frost

A frozen mountain in the sea (see image above).

## The Ice Hotel

Welcome to The Ice Hotel, the crown jewel of Mount Frost: A hotel completely carved out of ice.

Star rating  - ⭐⭐⭐⭐⭐

# Instructions

### To run the project you need to have the following installed:

- PHP & Composer
- Node & NPM

### To generate files which are not included (see .gitignore), run the following commands:

- `composer install`
- `npm i`
- `npm run build`

### To make a successful booking without a transfer code, for testing:

- In php/payment.php, uncomment line 39 - `// if (1 === 1) { // used for testing instead of line below`
- In php/payment.php, comment out line 40 - `if ($transferCodeStatus->transferCode === $transferCode) {`

### To test the admin page and login/logout from it:

- Create a file called .env (see .env.example) and set API_KEY to some value you then also use as password.

### Make sure filepath base URL is correctly set:

- In php/autoload.php, make sure `$baseUrl = "/";` is true. This setting works when testing locally.
- `$baseUrl = "https://php-fanclub.se/the-ice-hotel/";` should be commented out. This setting is used for deployment.

# Code review

prepareData.php: 95-97 - “Might change later…”  Might be good to have set prices at launch of the site.   
index.php: 9-17 - Could’ve been a function to just call inside the index.php to make the index file look more clean. 

index.php: 25-39 - Could’ve been a function to just call inside the index.php to make the index file look more clean. 

index.php: 41-46 - If commented out code is used in local tests, make a comment above to state why the code is still in the file. Otherwise remove. 

confirm.php: 12-15 - Could’ve been a function inside functions.php or a file where all db connections syntax is set, that could’ve been called inside the confirm.php for a better looking code. 

error.php: 10 - Where is the $_SESSION[‘message’] set, a comment to where I can find the message text in the code would be needed. 

home.php: 7 - $_SESSION[‘roomType’] don’t know where this is set. A comment to where I can find the form where it’s set would have been good. 

admin.php: 29—97 -  If you would have used functions to structure the code inside calendar.php you could have called the functions instead of copy+paste the code from one file to another. 

General tip - There is a lot of $_SESSION variables in the code, to know where they come from I would recommend to either save them inside a function to call or to leave a comment on where the form where they are found is in the code. 

calendar.php: 17-21 - Instead of using the hardcoded number three as the deluxe room in the “else” statement. Save the number 3 inside the $deluxe variable to make the code more clean. 
