# EXO Cafe Web Application

### Project Description
A web application for the EXO Cafe, serving as a community platform for fans. The site features a social-style forum for users to post with images and comments. It includes user profiles and an admin panel to manage cafe content and member profiles, aiming to boost fan engagement.

### Key Features
* **Community Forum:** Users can create posts with text and images, and interact through comments.
* **User Profiles:** Displays user details and posting history for each individual.
* **User Management:** A login system for 'fan' and 'admin' roles.
* **Admin Panel:** Manage EXO member data and other site content.

---

### Installation Guide

#### Prerequisites
Ensure you have the following packages installed:
* [**XAMPP**](https://www.apachefriends.org/download.html) (or another local web server like WAMP, MAMP)
* Web Browser (Chrome, Firefox, etc.)

#### Steps
1.  **Clone the Repository:**
    ```bash
<<<<<<< HEAD
    git clone [https://github.com/fan-forum-project/exocafe.git](https://github.com/your-repo-name/exocafe.git)
    cd exocafe
=======
    git clone git clone https://github.com/sitifatiharh/fan-forum-project.git
    cd fan-forum-project
>>>>>>> 20a5cfe882ee6d5294955abbff1e83db10549471
    ```

2.  **Database Configuration:**
    * Create a new database in phpMyAdmin named `db_exo`.
    * Import the `database.sql` file (the table structure is provided below).
    * Make sure the `includes/koneksi.php` file contains the correct database credentials.

3.  **Run the Server:**
    * Start the Apache and MySQL servers via the XAMPP Control Panel.
    * Open your browser and navigate to `http://localhost/exocafe-project`.

---

### Database Structure (`database.sql`)
Here is the complete table structure required for this project. You can copy and paste the code below into phpMyAdmin's SQL tab to set up the database.

```sql
--
-- Table structure for `users`
--
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('fan','admin') NOT NULL DEFAULT 'fan',
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `join_date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for `posts`
--
CREATE TABLE `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `content` text NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for `comments`
--
CREATE TABLE `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for `members`
--
CREATE TABLE `members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `stage_name` varchar(50) NOT NULL,
  `position` varchar(100) NOT NULL,
  `birth_date` date NOT NULL,
  `description` text NOT NULL,
  `profile_photo` varchar(255) NOT NULL,
  `instagram_url` varchar(255) DEFAULT NULL,
  `twitter_url` varchar(255) DEFAULT NULL,
  `youtube_url` varchar(255) DEFAULT NULL,
  `tiktok_url` varchar(255) DEFAULT NULL,
  `weibo_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for `password_resets`
--
CREATE TABLE `password_resets` (
  `email` varchar(100) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`email`,`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for `reports`
--
CREATE TABLE `reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reporter_id` int(11) DEFAULT NULL,
  `post_id` int(11) DEFAULT NULL,
  `comment_id` int(11) DEFAULT NULL,
  `reason` text NOT NULL,
  `status` enum('pending','reviewed','closed') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `reporter_id` (`reporter_id`),
  KEY `post_id` (`post_id`),
  KEY `comment_id` (`comment_id`),
  CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`reporter_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `reports_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reports_ibfk_3` FOREIGN KEY (`comment_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
