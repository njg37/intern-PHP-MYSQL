# WeatherWatcher

WeatherWatcher is a web application that provides user authentication, profile management, and weather information. It's built using PHP, HTML, CSS, and JavaScript.

## Features

- User Registration and Login System
- User Profile Management
- Weather Information Display
- Responsive Design

## Technologies Used

- PHP for server-side scripting
- HTML5 for structure
- CSS3 for styling
- JavaScript for client-side interactions
- Bootstrap for responsive design
- OpenWeatherMap API for weather data

## Project Structure
WeatherWatcher/
├── index.php
├── about.php
├── profile.php
├── login.php
├── registration.php
├── manage_users.php
├── delete_user.php
├── edit_user.php
├── weather.php
├── get-profile-image.php
├── update-profile-picture.php
├── database.php
├── helpers.php
├── logout.php
├── .gitignore
├── images/
├── uploads/
└── style.css


## Setup

1. Clone the repository:
git clone https://github.com/njg37/intern-PHP-MYSQL.git


2. Set up your database:
   - Create a MySQL database
   - Import the schema from `database.sql` (if provided)

3. Configure environment variables:
   - Create a `.env` file in the root directory
   - Add your database credentials and OpenWeatherMap API key

4. Install dependencies:
   - This project doesn't require any additional PHP packages

5. Start your local server and navigate to `http://localhost/WeatherWatcher`

## Usage

1. Users can register and log in to access their profile
2. Admin users can manage other users through the `manage_users.php` page
3. Users can update their profile information and profile picture
4. The main page displays weather information for a given city

## Contributing

Contributions are welcome! Please submit a pull request with a clear description of your changes.


## Acknowledgments

- OpenWeatherMap API for weather data
- Bootstrap for responsive design
- Font Awesome for icons

