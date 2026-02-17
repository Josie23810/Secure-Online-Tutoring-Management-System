<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Online Tutoring Management System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Enhanced Marquee for catchy scrolling text at the top -->
    <marquee behavior="scroll" direction="left" style="background-color: #f5a623; color: white; padding: 15px; font-size: 20px; font-weight: bold; position: fixed; top: 0; left: 0; right: 0; z-index: 1001;">
        Revolutionize Your Learning | Expert Tutors at Your Fingertips | Secure, Personalized Education | Achieve Excellence Today | Connect, Learn, Succeed
    </marquee>

    <header>
        <nav>
            <h1>Secure Online Tutoring Management System</h1>
            <ul>
                <li><a href="#home">Home</a></li>
                <li><a href="#services">Services</a></li>
                <li><a href="#access">Access</a></li>
                <li><a href="#about">About Us</a></li>
                <li><a href="#contact">Contact</a></li>
                <li><a href="auth/login.php" class="btn">Login</a></li>
                <li><a href="auth/register.php" class="btn">Register</a></li>
            </ul>
        </nav>
    </header>

    <section id="home" class="hero">
        <div>
            <h2>Welcome to Secure Online Tutoring Management System</h2>
            <p>Connecting Students and Tutors Effortlessly for a Brighter Future</p>
            <a href="auth/register.php" class="btn">Get Started</a>
        </div>
    </section>

    <section id="services">
        <h2>Our Services</h2>
        <div class="services">
            <div class="card">
                <h3>Personalized Tutoring</h3>
                <p>Book one-on-one sessions with qualified tutors tailored to your needs.</p>
            </div>
            <div class="card">
                <h3>Profile Management</h3>
                <p>Create and manage profiles for students and tutors with detailed information.</p>
            </div>
            <div class="card">
                <h3>Session Tracking</h3>
                <p>Track booked sessions, statuses, and progress in real-time.</p>
            </div>
            <div class="card">
                <h3>Reports & Analytics</h3>
                <p>Generate detailed reports for admins to monitor system usage.</p>
            </div>
        </div>
    </section>

    <section id="access">
        <h2>How to Access the System</h2>
        <div class="access">
            <div class="card">
                <h3>For Students</h3>
                <p>Register and log in to view tutors, book sessions, and manage your profile. Access is restricted – you cannot view tutor or admin dashboards.</p>
            </div>
            <div class="card">
                <h3>For Tutors</h3>
                <p>Register and log in to set up profiles, manage availability, and view booked sessions. Access is restricted – you cannot view student or admin dashboards.</p>
            </div>
            <div class="card">
                <h3>For Admins</h3>
                <p>Admin access is restricted to authorized personnel only. Manage users, view reports, and control the system.</p>
            </div>
        </div>
    </section>

    <section id="about">
        <h2>About Us</h2>
        <div class="about">
            <div class="card">
                <h3>Our Mission</h3>
                <p>We aim to provide a secure, efficient platform for online tutoring, connecting learners with experts while ensuring data privacy and ease of use.</p>
            </div>
            <div class="card">
                <h3>Why Choose Us?</h3>
                <p>With role-based access, comprehensive profiles, and visual reports, we offer a complete solution for educational management.</p>
            </div>
        </div>
    </section>

    <section id="contact">
        <h2>Contact Us</h2>
        <div class="contact">
            <div class="card">
                <h3>Get in Touch</h3>
                <p>Email: support@sotms.com<br>Phone: +1 (123) 456-7890<br>Address: 123 Education St, Learning City</p>
            </div>
            <div class="card">
                <h3>Send a Message</h3>
                <form action="contact.php" method="POST">
                    <input type="text" name="name" placeholder="Your Name" required>
                    <input type="email" name="email" placeholder="Your Email" required>
                    <textarea name="message" placeholder="Your Message" rows="4" required></textarea>
                    <button type="submit">Send</button>
                </form>
            </div>
        </div>
    </section>

    <footer>
        <p>&copy; 2023 Secure Online Tutoring Management System. All rights reserved. | <a href="#home">Home</a> | <a href="#services">Services</a> | <a href="#contact">Contact</a></p>
    </footer>
</body>
</html>
