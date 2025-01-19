Here’s the updated `README.md` specifically tailored for your **[iParkPHP](https://github.com/ImanZulhakim/iParkPHP)** repository:

---

# iParkPHP - Backend API Service for iPARK Mobile Application

This repository contains the backend API service for the **iPARK mobile application**, built using PHP. The API provides core functionality such as user management, parking recommendations, and database interactions. Designed to work seamlessly with the iPARK Flutter-based mobile app, it ensures a smooth and efficient parking experience for users.

## Features

- **User Management**  
  APIs for user authentication, registration, and profile management.

- **Parking Recommendations**  
  Provide personalized parking suggestions based on user preferences and real-time parking availability.

- **Database Integration**  
  Interact with MySQL to manage parking locations, user preferences, and availability data.

- **XAMPP Compatible**  
  Built to run effortlessly on local servers using XAMPP.

## Tech Stack

- **Language**: PHP
- **Database**: MySQL
- **Server**: Apache (via XAMPP)
- **Integration**: Flutter mobile application

## Installation

### Prerequisites

1. **XAMPP**: Download and install XAMPP from [Apache Friends](https://www.apachefriends.org/).
2. **MySQL Database**: Ensure MySQL is configured and running.
3. **Flutter Mobile App**: Ensure the iPARK Flutter app is set up. (Repo: [iPARK Mobile Application](https://github.com/ImanZulhakim/iPark)).

---

### Steps to Set Up

1. **Clone the repository**:
   ```bash
   git clone https://github.com/ImanZulhakim/iParkPHP.git
   ```

2. **Place files in the `htdocs` directory**:
   - Copy the contents of this repository into the `htdocs` folder of your XAMPP installation. For example:
     ```plaintext
     C:\xampp\htdocs\iParkPHP
     ```

3. **Set up the database**:
   - Open phpMyAdmin (`http://localhost/phpmyadmin`).
   - Create a new database named `ipark`.
   - Import the provided `iPark.sql` file into the `ipark` database.

4. **Configure database connection**:
   - Open `config/database.php` and update the database credentials:
     ```php
     <?php
     $host = 'localhost';
     $db_name = 'ipark';
     $username = 'root'; // Default for XAMPP
     $password = '';     // Default for XAMPP
     $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
     ?>
     ```

5. **Start XAMPP services**:
   - Open the XAMPP Control Panel.
   - Start **Apache** and **MySQL** services.

6. **Test the API**:
   - Use Postman or a browser to test API endpoints. For example:
     ```bash
     POST http://localhost/iParkPHP/api/user.php?action=login
     ```

7. **Connect to the Flutter app**:
   - Update the `BASE_URL` in your Flutter app configuration (e.g., `lib/constants/api.dart`):
     ```dart
     const String BASE_URL = "http://localhost/iParkPHP";
     ```

---

## API Endpoints

### User Endpoints

- **Login**: `/api/user.php?action=login`
  - **Method**: POST
  - **Payload**:
    ```json
    {
      "email": "user@example.com",
      "password": "password123"
    }
    ```
  - **Response**:
    ```json
    {
      "status": "success",
      "userID": 1,
      "token": "abcd1234"
    }
    ```

- **Register**: `/api/user.php?action=register`
  - **Method**: POST
  - **Payload**:
    ```json
    {
      "name": "John Doe",
      "email": "user@example.com",
      "password": "password123"
    }
    ```

### Parking Endpoints

- **Get Parking Spaces**: `/api/parking.php?action=getSpaces&lotID=123`
  - **Method**: GET
  - **Response**:
    ```json
    {
      "status": "success",
      "data": [
        {
          "parkingSpaceID": 1,
          "isAvailable": true,
          "attributes": {
            "isCovered": true,
            "hasEVCharging": false
          }
        }
      ]
    }
    ```

- **Recommend Parking**: `/api/recommendations.php`
  - **Method**: POST
  - **Payload**:
    ```json
    {
      "userID": 1,
      "lotID": "56789"
    }
    ```
  - **Response**:
    ```json
    {
      "status": "success",
      "parkingSpaceID": "A1"
    }
    ```

---

## Folder Structure

```plaintext
iParkPHP/
│
├── api/
│   ├── user.php           # User-related endpoints (authentication, registration)
│   ├── parking.php        # Parking-related logic and endpoints
│   ├── recommendations.php # Recommendation logic
│   ├── preferences.php    # User preferences management
│   └── utils.php          # Utility functions (e.g., database connection, helpers)
│
├── config/
│   └── database.php       # Database connection configuration
│
├── logs/
│   └── app.log            # Log files for debugging
│
└── README.md              # Project documentation
```

---

## Logging

Debug logs are stored in the `logs/app.log` file. Ensure the `logs` directory is writable by the server to enable logging.

---

## Contributing

Contributions are welcome! To contribute:
1. Fork the repository.
2. Create a new branch (`git checkout -b feature/YourFeature`).
3. Commit your changes (`git commit -m 'Add Your Feature'`).
4. Push to your branch (`git push origin feature/YourFeature`).
5. Open a pull request.

---

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

---

## Related Repositories
- **Flutter App**: [iPARK Mobile Application](https://github.com/ImanZulhakim/iPark)
- **Flutter App**: [iPark Web Admin Panel](https://github.com/ImanZulhakim/iPark_Web)
